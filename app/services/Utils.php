<?php

namespace Service;

use Model\File;

class Utils {
    public static function strtoupper($text) {
        if (is_callable('mb_strtoupper')) {
            return mb_strtoupper($text);
        } else {
            // Use C-locale for ASCII-only uppercase
            $text = strtoupper($text);
            // Case flip Latin-1 accented letters
            $text = preg_replace_callback('/\xC3[\xA0-\xB6\xB8-\xBE]/', '_unicode_caseflip', $text);
            return $text;
        }
    }

    /**
     * @param $data
     * @param null $destination
     * @param bool $replace
     * @return string
     */
    public static function saveFileFromData($data, $destination = NULL) {
        if (empty($destination)) {
            $destination = 'public://';
        }
        if (!FileSystemUtils::isValidUri($destination, false)) {
            throw new \RuntimeException("File {$destination} not found");
        }

        if (!$uri = FileSystemUtils::saveFileTo($data, $destination)) {
            throw new \RuntimeException("Can't save file {$destination}.");
        }

        return $uri ? $uri : $destination;
    }
}