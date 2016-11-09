<?php

use Service\DatabaseUtils;
use Service\FileSystemUtils;
use Service\Synchronize;

require_once '../app_console.php';

$data = DatabaseUtils::loadLetterPages();

$titlesMap = \Service\NodeTitleCompatibility::titles_map;

foreach ($data as $title => $versions) {
    $sourceServer = 'http://dev.mykingdombooks.com/sites/default/files/';
    foreach ($versions as $nid => $version) {
        $age = $version['age'];
        $gender = $version['gender'];
        foreach ($version['images'] as $delta => $image) {
            $fileName = basename($image);
            if (isset($version['names'][$delta])) {
                $fileName = $version['names'][$delta];
            }

            if (isset($titlesMap[$title])) {
                $fileName = str_replace($title, $titlesMap[$title], $fileName);
                $title = $titlesMap[$title];
            }

            $matches = [];
            $localImage = str_replace('male_1_4', '', str_replace('female_1_4/', '', $image));

            $path = dirname(str_replace('public://', FileSystemUtils::getImagesPath() . '/', $localImage)) . '/' . $age . '/' . $gender . '/' . $title . '/';
            $variant = 1;
            $side = $delta % 2 == 1 ? 'R' : 'L';
            if (preg_match('/^([\w]|Filler)(\d).+_(\w)\.\w+$/', $fileName, $matches)) {
                list(, , $variant, $side) = $matches;
            }
            $path .= $variant . '/' . $side . '/' . $fileName;
            if (!file_exists($path)) {
                $source = str_replace('public:/', $sourceServer, str_replace('//', '/', $image));
                print 'Prepare: ' . $source . '
';
                $status = Synchronize::downloadImage(str_replace(' ', '%20', $source), $path);
                if ($status) {
                    print 'Download: ' . $path . '
';
                } else {
                    print 'Fail: ' . $path . '
';
                }
            }
        }
    }
}