<?php

namespace Model;

use Service\FileSystemUtils;

class JsonBook {
    protected $data = [
        'age' => 14,
        'first_name' => '',
        'last_name' => '',
        'gender' => 'boy',
        'images' => [],
        'letters' => [],
        'background' => []
    ];

    public $trinkets = [];

    /**
     * Book constructor.
     */
    public function __construct($firstName, $lastName, $gender, $ageCategory, $images, $letters, $background = [], $uniqueId = null)
    {
        $this->data = [
            'age' => $ageCategory,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'gender' => $gender,
            'images' => $images,
            'letters' => $letters,
            'background' => $background,
            'unique_id' => $uniqueId
        ];
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->data['first_name'];
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->data['first_name'] = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->data['last_name'];
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->data['last_name'] = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->data['gender'];
    }

    /**
     * @param string $gender
     *
     * @return $this
     */
    public function setGender($gender)
    {
        $this->data['gender'] = $gender;
        return $this;
    }

    /**
     * @return string
     */
    public function getAgeCategory()
    {
        return $this->data['age'];
    }

    /**
     * @param string $ageCategory
     *
     * @return $this
     */
    public function setAgeCategory($ageCategory)
    {
        $this->data['age'] = $ageCategory;
        return $this;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->data['images'];
    }

    /**
     * @param array $images
     *
     * @return $this
     */
    public function setImages($images)
    {
        $this->data['images'] = $images;
        return $this;
    }

    /**
     * @return array
     */
    public function getLetters(): array
    {
        return $this->data['letters'];
    }

    /**
     * @param array $letters
     *
     * @return $this
     */
    public function setLetters($letters)
    {
        $this->data['letters'] = $letters;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBackground()
    {
        return $this->data['background'];
    }/**
     * @param mixed $background
     *
     * @return $this
     */
    public function setBackground($background)
    {
        $this->data['background'] = [
            'url' => FileSystemUtils::getWebPath($background),
            'alt' => '',
            'title' => '',
            'type' => '',
            'size' => 0,
            'width' => 0,
            'height' => 0
        ];
        return $this;
    }

    public function addSmallLetter($url, $alt, $title, $width, $height) {
        $this->data['letters'][] = [
            'url' => FileSystemUtils::getWebPath($url),
            'alt' => $alt,
            'title' => $title,
            'width' => $width,
            'height' => $height,
            'type' => '',
            'size' => '',
            'delta' => count($this->data['letters']),
        ];
    }

    public function addImage($url, $alt, $title, $width, $height, $smallImagePath, $serverImageName) {
        $this->data['images'][] = [
            'url' => FileSystemUtils::getWebPath($url),
            'small_image' => FileSystemUtils::getWebPath($smallImagePath),
            'alt' => $alt,
            'title' => $title,
            'width' => $width,
            'height' => $height,
            'type' => '',
            'size' => '',
            'delta' => count($this->data['images']),
            'server_image_name' => $serverImageName
        ];
    }

    public function getImagesData() {
        return $this->data;
    }
}