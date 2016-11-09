<?php

namespace Service;

class FileSystemUtils {
    const ALLOWED_SCHEMES = [
        'temporary' => SITE_ROOT . '/temp',
        'public' => SITE_ROOT . '/files'
    ];

    public static function loadLetterImage() {

    }

    public static function loadSmallImage() {

    }

    public static function saveBookImage() {

    }

    public static function getInkBanditsFontPath() {
        return SITE_ROOT . '/book_assets/fonts/InkBandits_Script.ttf';
    }

    public static function getNadiriiFontPath() {
        return SITE_ROOT . '/book_assets/fonts/Nadirii-Bold.otf';
    }

    public static function getKOMTXTFontPath() {
        return SITE_ROOT . '/book_assets/fonts/KOMTXT.ttf';
    }

    public static function getImagesPath()
    {
        return SITE_ROOT . '/book_assets/pages';
    }

    public static function getOutputBooksPath()
    {
        return SITE_ROOT . '/files/mkbooks';
    }

    public static function getIconsPath()
    {
        return SITE_ROOT . '/book_assets/abc-icons';
    }

    public static function getABCPath()
    {
        return SITE_ROOT . '/book_assets/abc';
    }

    public static function getTrinketsImagesPath() {
        return SITE_ROOT . '/book_assets/trinkets';
    }

    public static function getPagePath($gender, $age = 14, $title = null, $variant = null, $side = 'L') {
        return self::getImagesPath() . '/' . $age . '/' . $gender . ($title ? '/' . $title . ($variant ? '/' . $variant . ($side ? '/' . $side : '') : '') : '');
    }

    public static function getUriScheme($uri) {
        $position = strpos($uri, '://');
        return $position ? substr($uri, 0, $position) : false;
    }

    public static function isValidUri($uri, $checkFileExist = true) {
        $uriScheme = self::getUriScheme($uri);
        if ($uriScheme && !self::ALLOWED_SCHEMES[$uriScheme]) return false;

        $path = $uriScheme ? str_replace($uriScheme, self::ALLOWED_SCHEMES[$uriScheme], $uri) : $uri;

        if ($checkFileExist && (!file_exists($path) || !is_file($path))) return false;

        return true;
    }

    /**
     * Port: https://api.drupal.org/api/drupal/includes!file.inc/function/drupal_basename/7.x
     * @param $uri
     * @param $suffix
     * @return mixed|string
     */
    public static function getBaseName($uri, $suffix = null) {
        $separators = '/';
        // Remove right-most slashes when $uri points to directory.
        $uri = rtrim($uri, $separators);
        // Returns the trailing part of the $uri starting after one of the directory
        // separators.
        $filename = preg_match('@[^' . preg_quote($separators, '@') . ']+$@', $uri, $matches) ? $matches[0] : '';
        // Cuts off a suffix from the filename.
        if ($suffix) {
            $filename = preg_replace('@' . preg_quote($suffix, '@') . '$@', '', $filename);
        }
        return $filename;
    }

    /**
     * @param $handle
     * @param $destination
     * @return bool|string
     */
    public static function saveFileTo($handle, $destination) {
        $path = self::getFileRealPath($destination);
        FileSystemUtils::mkpath(dirname($path));
        if (!$path || !file_put_contents($path, $handle)) return false;

        return $destination;
    }

    /**
     * @param $destination
     * @return mixed
     */
    public static function getFileRealPath($destination)
    {
        $scheme = self::getUriScheme($destination);
        if ($scheme) {
            if (!self::ALLOWED_SCHEMES[$scheme]) {
                return false;
            }

            $path = str_replace($scheme . ':/', self::ALLOWED_SCHEMES[$scheme], $destination);
            return $path;
        }
        return $destination;
    }

    public static function getWebPath($absolutePath) {
        return 'http://' . SITE_DOMAIN . str_replace(SITE_ROOT, '', $absolutePath);
    }

    /**
     * @param $uri
     * @return array
     */
    public static function getImageData($uri) {
        $path = self::getFileRealPath($uri);
        
        return getimagesize($path);
    }

    public static function mkpath($path)
    {
        umask(0);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}