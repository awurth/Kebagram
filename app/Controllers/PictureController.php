<?php

namespace App\Controllers;

use App\Models\PictureRating;
use Respect\Validation\Validator as v;
use Intervention\Image\ImageManager;
use App\Models\Picture;
use App\Upload\FileUploader;
use App\Upload\UploadedFile;
use App\Controllers\ProfileController;

class PictureController extends Controller
{

    public function getAdd($request, $response)
    {
        return $this->view->render($response, 'picture/new.twig');
    }

    public function postAdd($request, $response)
    {
        $redirectUrl = $this->router->pathFor('picture.add');
        $caption = $request->getParam('caption');

        if (!v::notEmpty()->validate($caption)) {
            $this->flash->addMessage('error', 'The caption cannot be empty.');
            return $request->withRedirect($redirectUrl);
        }

        $file = new UploadedFile('picture-file', true);
         if (!$file->isValid()) {
             $this->flash->addMessage('error', 'An error occured while attempting to upload the image.');
             return $response->withRedirect($redirectUrl);
         }

        if ($file->isUploaded()) {
            $fileUploader = new FileUploader($file, 2000000, 'uploads/images/kebabs', FileUploader::TYPE_IMG);

            if (!$fileUploader->checkExtension()) {
                $this->flash->addMessage('error', 'Unknown file extension.');
                return $response->withRedirect($redirectUrl);
            }

            if (!$fileUploader->checkFileSize()) {
                $this->flash->addMessage('error', 'The file is too large. Max file size: 2MB.');
                return $response->withRedirect($redirectUrl);
            }

            $picture = new Picture();
            $picture->description = $caption;
            $picture->user()->associate($this->auth->user());
            $picture->save();

            $this->resize($file->getTempName(), 'uploads/images/kebabs/' . $picture->id . '.jpg');

            $this->flash->addMessage('success', 'Picture added successfully!');
            return $response->withRedirect($this->router->pathFor('home'));
        }

        $this->flash->addMessage('error', 'An image file is required.');
        return $response->withRedirect($redirectUrl);
    }

    public function resize($src, $dest)
    {
        $manager = new ImageManager(array('driver' => 'gd'));

        $image = $manager->make($src);

        $size = $image->height() > $image->width() ? $image->height() : $image->width();

        $image->resizeCanvas($size, $size, 'center', false, '#000000')->save($dest);
    }


    public function likeDispatcher($request,$response)
    {
        $action = $request->getParam('what');
        $idPhoto = $request->getParam('idPhoto');
        $userTarget = $request->getParam('userTarget');
        $idUser = $this->auth->user()->user_id;

        if ($action && $idPhoto && $idUser && $userTarget){
            switch($action) {
                case 'like' :
                    $this->likePhoto($idUser,$idPhoto);
                    $this->flash->addMessage('success', '<i class="material-icons">thumb_up</i> Photo liked');
                    return $response->withRedirect('user/'.$userTarget);
                case 'dislike' :
                    $this->dislikePhoto($idUser,$idPhoto);
                    $this->flash->addMessage('error', '<i class="material-icons">thumb_down</i> Photo disliked');
                    return $response->withRedirect('user/'.$userTarget);
            }
        }

        $this->flash->addMessage('error', 'You just tried a weird thing.');
        return $response->withRedirect('.');
    }

    private function isLiked($idUser,$idPhoto)
    {
        $query = PictureRating::where('user_id','=',$idUser)
                    ->where('picture_id','=',$idPhoto)
                    ->first();

        if ($query == NULL) {
            return false;
        }

        return true;
    }

    private function dislikePhoto($idUser,$idPhoto)
    {
        if ( ($this->isLiked($idUser,$idPhoto)) ) {

            $like = PictureRating::where('user_id','=',$idUser)
                ->where('picture_id','=',$idPhoto)
                ->first();

            $like->delete();
        }
    }

    private function likePhoto($idUser,$idPhoto)
    {
        if ( !($this->isLiked($idUser,$idPhoto)) ) {
            $pictureRate = new PictureRating;
            $pictureRate->liker($idUser,$idPhoto);
            $pictureRate->save();
        }
    }
}
