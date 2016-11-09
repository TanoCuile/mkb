<?php

namespace Service;

use Model\File;

class DatabaseUtils
{
    const EMPTY_IMAGE_FID = 177;
    public static $prefix;
    /**
     * @var \mysqli
     */
    protected static $db;

    public static function setDrupalDB(\mysqli $db)
    {
        self::$db = $db;
    }

    public static function saveNode()
    {

    }

    public static function loadNode()
    {

    }

    public static function saveField()
    {

    }

    public static function loadField()
    {

    }

    public static function saveFile(File $file)
    {
        return $file;
    }

    public static function findFileByUri($uri)
    {

    }

    public static function loadFile($fid)
    {
        $file = new File();
        return $file;
    }

    public static function getLetterPagesQuery($gender, $age)
    {
        return "SELECT fm.uri AS uri, fi.delta AS delta, node.nid AS nid, node.title AS title, pic_name.field_name_of_picture_on_backend_value AS name, story_type.field_story_type as type FROM file_managed AS fm 
          INNER JOIN field_data_field_page_images AS fi ON fi.field_image_fid = fm.fid, 
          INNER JOIN node AS n ON n.nid = fi.entity_id
          INNER JOIN field_data_field_age AS fa ON fa.entity_id = n.nid
          INNER JOIN field_data_field_client_gender AS fcg ON fcg.entity_id = n.nid
          LEFT JOIN field_data_field_name_of_picture_on_backend AS pic_name ON pic_name.entity_id = fi.entity_id
          LEFT JOIN field_data_field_story_type AS story_type ON story_type.entity_id = fi.entity_id  
      WHERE fa.field_age_value = {$age} AND fcg.field_client_gender_value = '{$gender}' AND n.status = 1 AND n.type = 'letter_images' ORDER BY fi.delta ASC";
    }

    public static function loadLetterPages()
    {
        $query = "SELECT fm.uri AS uri, fm.fid AS fid, fi.delta AS delta, n.nid AS nid, n.title AS title, 
                      fcg.field_client_gender_value AS gender,
                      fa.field_age_value AS age,
                      pic_name.field_name_of_picture_on_backend_value AS name,
                      pic2_name.field_name_of_second_picture_value AS name2, 
                      pic3_name.field_name_of_third_picture_value AS name3, 
                      pic4_name.field_name_of_fourth_picture_value AS name4, 
                      story_type.field_story_type_value as type FROM file_managed AS fm
            INNER JOIN field_data_field_page_images AS fi ON fi.field_page_images_fid = fm.fid
            INNER JOIN node AS n ON n.nid = fi.entity_id
            INNER JOIN field_data_field_age AS fa ON fa.entity_id = n.nid
            INNER JOIN field_data_field_client_gender AS fcg ON fcg.entity_id = n.nid
            LEFT JOIN field_data_field_name_of_picture_on_backend AS pic_name ON pic_name.entity_id = fi.entity_id
            LEFT JOIN field_data_field_name_of_second_picture AS pic2_name ON pic2_name.entity_id = fi.entity_id
            LEFT JOIN field_data_field_name_of_third_picture AS pic3_name ON pic3_name.entity_id = fi.entity_id
            LEFT JOIN field_data_field_name_of_fourth_picture AS pic4_name ON pic4_name.entity_id = fi.entity_id
            LEFT JOIN field_data_field_story_type AS story_type ON story_type.entity_id = fi.entity_id
            WHERE n.status = 1 AND n.type = 'letter_images' ORDER BY fi.delta ASC;";
        $images = [];
        $result = self::$db->query($query);
        if ($result) {
            $images = $result->fetch_all(MYSQLI_ASSOC);
            $grouped = [];
            foreach ($images as $image) {
                $key = trim(mb_strtolower($image['title']));
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [];
                }

                $nid = $image['nid'];
                if (!isset($grouped[$key][$nid])) {
                    $grouped[$key][$image['nid']] = [
                        'age' => $image['age'],
                        'gender' => $image['gender'],
                        'images' => [],
                        'names' => []
                    ];
                }

                $grouped[$key][$nid]['images'][$image['delta']] = $image['uri'];
                if ($image['name']) {
                    $grouped[$key][$nid]['names'][0] = $image['name'];
                }
                if ($image['name2']) {
                    $grouped[$key][$nid]['names'][1] = $image['name2'];
                }
                if ($image['name3']) {
                    $grouped[$key][$nid]['names'][2] = $image['name3'];
                }
                if ($image['name4']) {
                    $grouped[$key][$nid]['names'][3] = $image['name4'];
                }
            }
            return $grouped;
        }
        return $images;
    }
}