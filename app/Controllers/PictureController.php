<?php

namespace App\Controllers;

use App\Models\PictureRating;
use Intervention\Image\Image;
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


    public function changeProfilePicture($request,$response)
    {
        $user = $this->auth->user();
        $redirectUrl = $this->router->pathFor('user.profile',['slug' => $user->user_slug]);
        $file = new UploadedFile('picture-file', true);
        $path = 'uploads/images/users/';

        if (!$file->isValid()) {
            $this->flash->addMessage('error', 'An error occured while attempting to upload the image.');
            return $response->withRedirect($redirectUrl);
        }

        if ($file->isUploaded()) {
            $fileUploader = new FileUploader($file, 2000000, $path, FileUploader::TYPE_IMG);

            if (!$fileUploader->checkExtension()) {
                $this->flash->addMessage('error', 'Unknown file extension.');
                return $response->withRedirect($redirectUrl);
            }

            if (!$fileUploader->checkFileSize()) {
                $this->flash->addMessage('error', 'The file is too large. Max file size: 2MB.');
                return $response->withRedirect($redirectUrl);
            }

            $image = $this->makeImage($file->getTempName());
            $image->save($path . $user->user_id . '.jpg');

            $this->flash->addMessage('success', 'Picture edited successfully!');
            return $response->withRedirect($redirectUrl);
        }

        $this->flash->addMessage('error', 'An image file is required.');
        return $response->withRedirect($redirectUrl);
    }

    public function postAdd($request, $response)
    {
        $redirectUrl = $this->router->pathFor('picture.add');
        $caption = $request->getParam('caption');

        if (!v::notEmpty()->validate($caption)) {
            $this->flash->addMessage('error', 'The caption cannot be empty.');
            return $response->withRedirect($redirectUrl);
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

            $tags = array();
            preg_match_all('/#(\w+)/', $caption, $tags);

            $picture = new Picture();
            $picture->description = $caption;
            if (!empty($tags[1])) {
                $picture->tags = json_encode($tags[1]);
            }
            $picture->user()->associate($this->auth->user());
            $picture->save();

            $image = $this->makeImage($file->getTempName());

            if (
                isset($_POST['crop-pic']) &&
                isset($_POST['x']) &&
                isset($_POST['y']) &&
                isset($_POST['x2']) &&
                isset($_POST['y2']) &&
                isset($_POST['width']) &&
                isset($_POST['height']) &&
                isset($_POST['original-width']) &&
                isset($_POST['original-height'])
            ) {
                $image = $this->crop(
                    $image,
                    (int) $_POST['x'],
                    (int) $_POST['y'],
                    (int) $_POST['x2'],
                    (int) $_POST['y2'],
                    (int) $_POST['width'],
                    (int) $_POST['height'],
                    (int) $_POST['original-width'],
                    (int) $_POST['original-height']
                );

                if ($_POST['width'] != $_POST['height']) {
                    $image = $this->resize($image);
                }
            } else {
                $image = $this->resize($image);
            }

            $image->save('uploads/images/kebabs/' . $picture->id . '.jpg');

            $this->flash->addMessage('success', 'Picture added successfully!');
            return $response->withRedirect($this->router->pathFor('home'));
        }

        $this->flash->addMessage('error', 'An image file is required.');
        return $response->withRedirect($redirectUrl);
    }

    private function makeImage($src)
    {
        $manager = new ImageManager(array('driver' => 'gd'));

        return $manager->make($src);
    }

    private function crop(Image $image, $x1, $y1, $x2, $y2, $w, $h, $ow, $oh)
    {
        $x = $x1 < $x2 ? $x1 : $x2;
        $y = $y1 < $y2 ? $y1 : $y2;

        $square = $w == $h;

        $x = (int) ceil(($x * $image->getWidth()) / $ow);
        $y = (int) ceil(($y * $image->getHeight()) / $oh);

        $w = (int) ceil(($w * $image->getWidth()) / $ow);
        $h = (int) ceil(($h * $image->getHeight()) / $oh);

        if ($square && $w != $h) {
            if ($w > $h) {
                $h = $w;
            } else {
                $w = $h;
            }
        }

        return $image->crop($w, $h, $x, $y);
    }

    private function resize(Image $image)
    {
        $size = $image->height() > $image->width() ? $image->height() : $image->width();

        return $image->resizeCanvas($size, $size, 'center', false, '#000000');
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
