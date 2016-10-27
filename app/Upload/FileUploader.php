<?php

namespace App\Upload;

class FileUploader
{
    const TYPE_IMG = ['png', 'jpg', 'jpeg', 'gif'];

    const TYPE_DOC = ['doc', 'docx', 'docm', 'odt', 'ods', 'xls', 'xlsx', 'pdf', 'txt'];

    private $file;

    private $maxSize;

    private $authorizedExtensions;

    private $uploadDir;

    public function __construct(UploadedFile $file, $size, $dir, array $extensions = array())
    {
        $this->file = $file;
        $this->maxSize = $size;
        $this->authorizedExtensions = $extensions;
        $this->uploadDir = $dir;
    }

    public function upload($fileName)
    {
        if($this->file->isValid() && $this->checkFileSize() && $this->checkExtension()) {
            return $this->move($fileName);
        }

        return false;
    }

    public function checkFileSize()
    {
        return $this->file->getSize() <= $this->maxSize;
    }

    public function checkExtension()
    {
        return empty($this->authorizedExtensions) || in_array($this->file->getExtension(), $this->authorizedExtensions);
    }

    private function move($fileName)
    {
        $path = $this->uploadDir . '/' . $fileName . '.' . $this->file->getExtension();
        return move_uploaded_file($this->file->getTempName(), $path);
    }

    public function getAuthorizedExtensions()
    {
        return $this->authorizedExtensions;
    }

    public function setAuthorizedExtensions(array $authorizedExtensions)
    {
        $this->authorizedExtensions = $authorizedExtensions;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getMaxSize()
    {
        return $this->maxSize;
    }

    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
    }

    public function getUploadDir()
    {
        return $this->uploadDir;
    }

    public function setUploadDir($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }
}
