<?php

namespace Service;

class Synchronize {
    public static function downloadImage($filePath, $destination)
    {
        $ch = curl_init($filePath);

        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($retcode != 200) {
            print '<pre>' . htmlspecialchars(print_r($filePath,1)) . '</pre>';
            print '<pre>' . htmlspecialchars(print_r('NOTFOUND',1)) . '</pre>';
            return false;
        }

        $dirPath = dirname($destination);
        FileSystemUtils::mkpath($dirPath);
        if (!file_exists($dirPath)) {
            return false;
        }
        file_put_contents($destination, fopen($filePath, '-r'));

        return true;
    }
}