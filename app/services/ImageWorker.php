<?php

namespace Service;

use Model\JsonBook;
use \void;

class ImageWorker
{
    /**
     * @param $path
     * @param $text
     * @param $font
     * @param array $options
     * @param string $resultImagePath
     */
    static function saveImageWithText(
        $path,
        $text,
        $font,
        $options = array(),
        $resultImagePath = ''
    ) {
        // Create image.
        $image = imagecreatefrompng($path);

        // Image width and height.
        $image_width = imagesx($image);
        $font_size = (!empty($font['size'])) ? $font['size'] : 12;
        $angle = 0;
        $text_box = imagettfbbox($font_size, $angle, $font['font'], $text);

        // Text width and height.
        $text_width = $text_box[2] - $text_box[0];
        $text_height = $text_box[1] - $text_box[7];

        // Calculate coordinates of the text.
        $x = ($image_width / 2) - ($text_width / 2);
        $y = (!empty($options['y'])) ? $options['y'] + $text_height : $text_height;

        // Adding shadow if needed.
        if (!empty($font['shadow'])) {
            $shadow_weight = (!empty($font['shadow']['weight'])) ? $font['shadow']['weight'] : 1;
            $shadow_colours = (!empty($font['shadow']['colour'])) ? $font['shadow']['colour'] : array(
                0,
                0,
                0,
            );
            $shadow_colour = imagecolorallocate(
                $image,
                $shadow_colours[0],
                $shadow_colours[1],
                $shadow_colours[2]
            );
            imagettftext(
                $image,
                $font_size,
                $angle,
                $x,
                $y + $shadow_weight,
                $shadow_colour,
                $font['font'],
                $text
            );
        }

        // Adding text.
        $font_colours = (!empty($font['colour'])) ? $font['colour'] : array(0, 0, 0);
        $font_colour = imagecolorallocate(
            $image,
            $font_colours[0],
            $font_colours[1],
            $font_colours[2]
        );
        imagettftext($image, $font_size, $angle, $x, $y, $font_colour, $font['font'], $text);

        $changedImageHandle = imagepng($image, $resultImagePath);
        imagedestroy($image);

        if (!$changedImageHandle) {
            throw new \RuntimeException('Can not generate image');
        }
    }

    /**
     * @param $path
     * @param $lastNameString
     * @param $resultImagePath
     */
    static function saveImageWithLabel($path, $lastNameString, $resultImagePath) {
        $im = imagecreatefrompng($path);

        $font_path = FileSystemUtils::getNadiriiFontPath();
        $black = imagecolorallocate($im, 0, 0, 0);
        imagettftext($im, 36, 0, 280, 145, $black, $font_path, $lastNameString);

        imagepng($im, $resultImagePath);
        if (!empty($im)) {
            imagedestroy($im);
        }
    }
    /**
     * @param JsonBook $book
     * @param $uniqueId
     * @param $gender
     * @param $age
     * @param $realImageTitle
     * @return string
     */
    static function addTestToLastnameStoryPair(
        JsonBook $book,
        $uniqueId,
        $gender,
        $age,
        $realImageTitle,
        $path,
        $resultImagePath
    ):string
    {
        $im = imagecreatefrompng($path);

        $string = ucfirst(trim(preg_replace('/[-\s]+/', '-', $book->getFirstName()), '-'));

        $font_path = FileSystemUtils::getKOMTXTFontPath();
        $black = imagecolorallocate($im, 0, 0, 0);
        if ($age == '14') {
            imagettftext($im, 35, 0, 105, 210, $black, $font_path, '');
        } else {
            // TODO: translate
            imagettftext($im, 35, 0, 105, 210, $black, $font_path, 'Prince ' . $string);
        }

        imagepng($im, $resultImagePath);
        return $resultImagePath;
    }

    /**
     * @param $trinkets
     * @return array
     */
    static function getLetterImages($trinkets):array
    {
        $sourceImages = [];
        foreach ($trinkets as $trinket) {
            $sourceImages[] = imagecreatefrompng(
                FileSystemUtils::getTrinketsImagesPath() . '/resized/' . $trinket . '.png'
            );
        }
        return $sourceImages;
    }

    /**
     * @param $path
     * @param $sourceImages
     */
    static function addLastNameFinale($path, $sourceImages)
    {
        $im = imagecreatefrompng($path);

        // Set the margins for the stamp and get the height/width of the stamp image.
        $y = imagesy($im);
        $x = imagesx($im) - 100;
        $letter_width = BookGenerator::LASTNAME_LETTER_WIDTH;
        $countSourceImages = count($sourceImages);

        // Reduce width and margin.
        $percent = 1;
        if ($countSourceImages < 6) {
            $percent = 0.6;
        }
        if ($countSourceImages == 5) {
            $percent = 0.45;
        }
        if ($countSourceImages >= 6) {
            $percent = 0.4;
        }
        $letter_width = ceil($letter_width * $percent);

        if ($countSourceImages * $letter_width > $x) {
            $letter_width = ceil(($x / count($sourceImages)) - ($x / count($sourceImages) / 15));
            $marge_left = 0;
        } elseif ($countSourceImages <= 6) {
            $all_letters_width = count($sourceImages) * $letter_width;
            $marge_left = 0.8 * ($x * 0.8 - $all_letters_width) / 2;
        } else {
            $all_letters_width = count($sourceImages) * $letter_width;
            $marge_left = ($x - $all_letters_width) / 8;
        }
        foreach ($sourceImages as $img) {
            // Resize image.
            $old_width = imagesx($img);
            $old_height = imagesy($img);
            $new_width = $old_width * $percent;
            $new_height = $old_height * $percent;
            $image_p = imagecreatetruecolor($new_width, $new_height);
            imagesavealpha($image_p, true);
            $color = imagecolorallocatealpha($image_p, 0x00, 0x00, 0x00, 127);
            imagefill($image_p, 0, 0, $color);
            imagecopyresampled(
                $image_p,
                $img,
                0,
                0,
                0,
                0,
                $new_width,
                $new_height,
                $old_width,
                $old_height
            );

            // Copy the stamp image onto our photo using
            // the margin offsets and the photo width to
            // calculate positioning of the stamp.
            imagecopy(
                $im,
                $image_p,
                $marge_left,
                $y / 2 - $new_height / 2,
                0,
                0,
                $new_width,
                $new_height
            );
            $marge_left += $letter_width;
        }

        // Output and free memory.
        imagepng($im, $path);
        if (!empty($im)) {
            imagedestroy($im);
        }
    }

    /**
     * @param $path
     * @param $string
     * @param $resultImagePath
     */
    static function prepareThirdFinaleImage($path, $string, $resultImagePath)
    {
        $im = imagecreatefrompng($path);

        $font_path = FileSystemUtils::getNadiriiFontPath();
        $black = imagecolorallocate($im, 255, 255, 255);
        imagettftext($im, 100, 0, 845, 865, $black, $font_path, $string);
        imagepng($im, $resultImagePath);
        if (!empty($im)) {
            imagedestroy($im);
        }
    }
}