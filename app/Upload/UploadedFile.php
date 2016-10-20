<?php

namespace App\Upload;

class UploadedFile
{
    private $field;

    private $name;

    private $extension;

    private $size;

    private $tempName;

    private $required;

    public function __construct($field, $required = false)
    {
        $this->field = $field;
        $this->required = $required;

        if ($this->isValid() && $this->isUploaded()) {
            $this->size = $_FILES[$this->field]['size'];
            $this->tempName = $_FILES[$this->field]['tmp_name'];
            $this->guessName();
        }
    }

    public function isValid()
    {
        if ($this->required) {
            return isset($_FILES[$this->field]) && $_FILES[$this->field]['error'] == UPLOAD_ERR_OK;
        } else {
            return isset($_FILES[$this->field]) && ($_FILES[$this->field]['error'] == UPLOAD_ERR_NO_FILE || $_FILES[$this->field]['error'] == UPLOAD_ERR_OK);
        }
    }

    public function isUploaded()
    {
        return $_FILES[$this->field]['error'] != UPLOAD_ERR_NO_FILE;
    }

    private function guessName()
    {
        $pathInfo = pathinfo($_FILES[$this->field]['name']);
        $this->name = $pathInfo['filename'];
        $this->extension = $pathInfo['extension'];
    }

    public static function getFileList($required = false)
    {
        $list = array();

        if (isset($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $list[] = new UploadedFile($key, $required);
            }
        }

        return $list;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getTempName()
    {
        return $this->tempName;
    }
}