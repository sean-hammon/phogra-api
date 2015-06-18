<?php
namespace Phogra\File;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * Class ImageFaker
 *
 * @package Phogra\File
 *
 *          A helper class to generate random images for seeding dummy data and for testing.
 */
class ImageFaker
{
    /**
     * Generate a randomized image and return the path to the file.
     *
     * @return string
     */
    public static function generateImage()
    {
        $height = 108;
        $width = 192;
        $scale = 10;
        $filename = Config::get('phogra.photoTempDir') . DIRECTORY_SEPARATOR . 'test_' . bin2hex(openssl_random_pseudo_bytes(8)) . ".jpg";

        $colors = array();
        for ($i =0; $i < $height; $i++) {
            $colors[] = self::randomColor();
        }

        $img = Image::canvas($width, $height, '#000');
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $img->pixel($colors[$y], $x, $y);
            }
            $first = array_shift($colors);
            $colors[] = $first;
        }
        $img->resize($width * $scale, $height * $scale);
        $jpg = $img->encode('jpg');
        $jpg->save($filename);

        return $filename;
    }

    /**
     * Generate random rgba color
     *
     * @return array
     */
    private static function randomColor()
    {
        return array(
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 255),
            1
        );
    }
}