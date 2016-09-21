<?php

/**
 * Created by PhpStorm.
 * User: Esdras
 * Date: 23/07/2016
 * Time: 22:27
 */
namespace Lib\Tools;

class HandleImage
{
    /** @var  string */
    private $size;
    /** @var  string */
    private $type;
    /** @var  string */
    private $name;
    /** @var  string */
    private $ext;
    /** @var  string */
    private $tmp;
    /** @var  boolean */
    private $error = false;
    /** @var  string */
    private $message = '';

    /* ===============================
                GETTERS
    ================================ */

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * @return string
     */
    public function getTmp()
    {
        return $this->tmp;
    }

    /**
     * @return boolean
     */
    public function isError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /* ===============================
                SETTERS
    ================================ */

    /**
     * @param string $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $ext
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
    }

    /**
     * @param string $tmp
     */
    public function setTmp($tmp)
    {
        $this->tmp = $tmp;
    }

    /**
     * @param boolean $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }


    /**
     * @param $file
     * @param $path
     * @param string $inputname
     * @return bool
     */
    public function sampleUpload($file, $path, $inputname='imagem', $newname='')
    {
        if(isset($file)){
            $this->size = $file[$inputname]['size'];
            $this->tmp  = $file[$inputname]['tmp_name'];
            $this->type = $file[$inputname]['type'];
            $this->ext  = pathinfo($file[$inputname]['name'], PATHINFO_EXTENSION);

            if(!empty($newname)) $this->name = $newname . '.' . $this->ext;
            else $this->name = sha1_file($this->tmp).'.'.$this->ext;

            if(move_uploaded_file($this->tmp, sprintf($path.'%s', $this->name))){
                $this->error = false;
                $this->message = "Imagem carregada com sucesso!";
                return true;
            }
        }

        $this->error = true;
        $this->message = "Erro ao tentar carregar esta imagem!";
        return false;
    }

    /**
     * @param $filename
     * @param string $savepath
     * @param string $width
     * @param string $height
     * @param int $top
     * @param int $left
     * @param string $quality
     * @return bool
     */
    public function createThumb($filename, $savepath='_tmb/', $width='100', $height='100', $top=0, $left=0, $quality='50')
    {
        $ext  = pathinfo($filename, PATHINFO_EXTENSION);
        $name = $filename;
        $newfilename = $savepath.(pathinfo($filename, PATHINFO_FILENAME)).'.'.$ext;

        list($realWidth, $realHeight) = getimagesize($name);

        $scale = min($width/$realWidth, $height/$realHeight);

        $new_width = floor($scale * $realWidth);
        $new_height = floor($scale * $realHeight);

        $image_p    = imagecreatetruecolor($width, $height);
        $image      = self::createTmpImage($name, $ext);
        $h_align    = floor(($width-$new_width)/2);

        if(imagecopyresized($image_p, $image, $h_align, 0, 0, 0, $new_width, $new_height, $realWidth, $realHeight)){
            if(self::render($image_p, $newfilename, $ext, $quality)){
                imagedestroy($image_p);
                $this->error = false;
                $this->message = "Thumb gerado.";
                return true;
            }
        }

        $this->error = true;
        $this->message = "Erro ao tentar gerar o thumb.";
        return false;
    }

    /**
     * @param string $sourceimage
     * @param null $path
     * @param string $ext
     * @param int $quality
     * @return bool
     */
    private function render($sourceimage, $path=null, $ext='jpeg', $quality=100)
    {
        $im = false;
        if($sourceimage!=''){
            switch($ext){
                case 'gif' :
                    $im = imagegif($sourceimage, $path);
                    break;
                case 'png' :
                    imagesavealpha($sourceimage, true);
                    $trans_colour = imagecolorallocatealpha($sourceimage, 0, 0, 0, 127);
                    imagefill($sourceimage, 0, 0, $trans_colour);

                    $red = imagecolorallocate($sourceimage, 255, 0, 0);
                    imagefilledellipse($sourceimage, 400, 300, 400, 300, $red);

                    $im = imagepng($sourceimage, $path, round($quality/10));
                    break;
                default:
                    $im = imagejpeg($sourceimage, $path, $quality);
            }
        }

        return $im;
    }

    /**
     * @param $imgname
     * @param $ext
     * @return resource
     */
    private function createTmpImage($imgname, $ext)
    {
        switch($ext){
            case 'gif' :
                $im = @imagecreatefromgif($imgname); /* Attempt to open */
                break;
            case 'png' :
                $im = @imagecreatefrompng($imgname); /* Attempt to open */
                break;
            default:
                $im = @imagecreatefromjpeg($imgname); /* Attempt to open */
        }

        if (!$im) { /* See if it failed */
            $im  = imagecreate(150, 30); /* Create a black image */
            $bgc = imagecolorallocate($im, 255, 255, 255);
            $tc  = imagecolorallocate($im, 0, 0, 0);
            imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
            /* Output an errmsg */
            imagestring($im, 1, 5, 5, "Erro no carregamento $imgname", $tc);
        }
        return $im;
    }

    public function clear()
    {
        $this->setError(false);
        $this->setExt('');
        $this->setMessage('');
        $this->setName('');
        $this->setSize('');
        $this->setTmp('');
        $this->setType('');
    }

}