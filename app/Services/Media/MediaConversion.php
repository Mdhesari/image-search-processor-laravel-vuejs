<?php

namespace App\Services\Media;

//TODO: make conversions specific by composition design pattern and give more flexibility
class MediaConversion
{
    private ?\GdImage $image;

    private string $imageType;

    public function __construct(
        public int $Width,
        public int $Height
    )
    {
        //
    }

    /**
     * @param $path
     * @return MediaConversion
     * @throws \Exception
     */
    public function load($path)
    {
        $image_info = getimagesize($path);
        if (! $image_info) {

            throw new \Exception('Invalid source image.');
        }
        $this->imageType = $image_info[2];

        switch ($this->imageType) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($path);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($path);
                break;
            default:
                //TODO: more extensions?
        }

        return $this;
    }

    /**
     * @param string $path
     * @param int $quality
     * @throws \Exception
     */
    public function save(string $path, int $quality)
    {
        $res = false;

        if ($this->imageType == IMAGETYPE_JPEG) {
            $res = imagejpeg($this->image, $path, $quality);
        } elseif ($this->imageType == IMAGETYPE_GIF) {

            $res = imagegif($this->image, $path);
        } elseif ($this->imageType == IMAGETYPE_PNG) {

            $res = imagepng($this->image, $path, $quality);
        }

        if (! $res) {
            throw new \Exception('Could not manipulate image.');
        }
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function apply()
    {
        $new_image = imagecreatetruecolor($this->Width, $this->Height);

        $res = imagecopyresampled(
            $new_image,
            $this->image,
            0,
            0,
            0,
            0,
            $this->getWidth($new_image),
            $this->getHeight($new_image),
            $this->getWidth($this->image),
            $this->getHeight($this->image)
        );
        if (! $res) {
            throw new \Exception("Imagecopyresapled did not work.");
        }

        $this->image = $new_image;

        return $this;
    }

    /**
     * @param $image
     * @return false|int
     */
    private function getWidth($image)
    {
        return imagesx($image);
    }

    /**
     * @param $image
     * @return false|int
     */
    private function getHeight($image)
    {
        return imagesy($image);
    }
}
