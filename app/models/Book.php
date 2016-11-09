<?php

namespace Model;

class Book extends Node {
    protected $firstName;
    protected $lastName;
    protected $gender;
    protected $ageCategory;
    protected $images;
    protected $message;
    protected $printSiteImageNames;
    protected $letters;
    protected $backgroundImage;
    protected $orderId;
    const FIELD_IMAGES_MACHINENAME = 'field_images';
    const FIELD_AGE_CATEGORY_MACHINENAME = 'field_age_category';
    const FIELD_BOOK_FIRST_NAME_MACHINENAME = 'field_book_first_name';
    const FIELD_BOOK_LAST_NAME_MACHINENAME = 'field_book_last_name';
    const FIELD_BOOK_GENDER_NAME_MACHINENAME = 'field_book_gender';
    const FIELD_MESSAGE_MACHINENAME = 'field_message';
    const FIELD_PRINTSITE_IMAGE_NAMES_MACHINENAME = 'field_printsite_image_names';
    const FIELD_SMALL_LETTERS_MACHINENAME = 'field_small_letters';
    const FIELD_BACKGROUND_IMAGE_MACHINENAME = 'field_background_image';
    const FIELD_CUSTOM_BOOK_ORDER_ID_MACHINENAME = 'field_custom_book_order_id';

    /**
     * Book constructor.
     */
    public function __construct($firstName, $lastName, $gender, $ageCategory, $images, $message, $printSiteImageNames, $letters, $backgroundImage, $orderId)
    {
        parent::__construct('custom_book', 'und', 'My kingdom Book ' . date('d/m/Y - H:i'), 0, 1);

        $this->ageCategory = new Field(self::FIELD_AGE_CATEGORY_MACHINENAME, $ageCategory);
        $this->firstName = new Field(self::FIELD_BOOK_FIRST_NAME_MACHINENAME, $firstName);
        $this->lastName = new Field(self::FIELD_BOOK_LAST_NAME_MACHINENAME, $lastName);
        $this->gender = new Field(self::FIELD_BOOK_GENDER_NAME_MACHINENAME, $gender);

        $this->images = [];
        foreach ($images as $img) {
            $this->images[] = new ImageField(self::FIELD_IMAGES_MACHINENAME, $img);
        }

        $this->message = new Field(self::FIELD_MESSAGE_MACHINENAME, $message);

        $this->printSiteImageNames = [];
        foreach ($printSiteImageNames as $name) {
            $this->printSiteImageNames[] = new Field(self::FIELD_PRINTSITE_IMAGE_NAMES_MACHINENAME, $name);
        }

        $this->letters = [];
        foreach ($letters as $letter) {
            $this->letters[] = new ImageField(self::FIELD_SMALL_LETTERS_MACHINENAME, $letter);
        }

        $this->backgroundImage = new Field(self::FIELD_BACKGROUND_IMAGE_MACHINENAME, $backgroundImage);

        $this->backgroundImage = new Field(self::FIELD_CUSTOM_BOOK_ORDER_ID_MACHINENAME, $orderId);
    }

    /**
     * @return Field
     */
    public function getFirstName(): Field
    {
        return $this->firstName;
    }

    /**
     * @param Field $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return Field
     */
    public function getLastName(): Field
    {
        return $this->lastName;
    }

    /**
     * @param Field $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return Field
     */
    public function getGender(): Field
    {
        return $this->gender;
    }

    /**
     * @param Field $gender
     *
     * @return $this
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return Field
     */
    public function getAgeCategory(): Field
    {
        return $this->ageCategory;
    }

    /**
     * @param Field $ageCategory
     *
     * @return $this
     */
    public function setAgeCategory($ageCategory)
    {
        $this->ageCategory = $ageCategory;
        return $this;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array $images
     *
     * @return $this
     */
    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return Field
     */
    public function getMessage(): Field
    {
        return $this->message;
    }

    /**
     * @param Field $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return array
     */
    public function getPrintSiteImageNames(): array
    {
        return $this->printSiteImageNames;
    }

    /**
     * @param array $printSiteImageNames
     *
     * @return $this
     */
    public function setPrintSiteImageNames($printSiteImageNames)
    {
        $this->printSiteImageNames = $printSiteImageNames;
        return $this;
    }

    /**
     * @return array
     */
    public function getLetters(): array
    {
        return $this->letters;
    }

    /**
     * @param array $letters
     *
     * @return $this
     */
    public function setLetters($letters)
    {
        $this->letters = $letters;
        return $this;
    }

    /**
     * @return Field
     */
    public function getBackgroundImage(): Field
    {
        return $this->backgroundImage;
    }

    /**
     * @param Field $backgroundImage
     *
     * @return $this
     */
    public function setBackgroundImage($backgroundImage)
    {
        $this->backgroundImage = $backgroundImage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function addSmallLetter($fid, $alt, $title, $width, $height) {
        $this->letters[] = new ImageField(self::FIELD_SMALL_LETTERS_MACHINENAME, $fid, null, count($this->letters), $width, $height, $alt, $title);
    }

    public function addImage($fid, $alt, $title, $width, $height) {
        $this->images[] = new ImageField(self::FIELD_IMAGES_MACHINENAME, $fid, null, count($this->images), $width, $height, $alt, $title);
    }

    public function addImageServerName($name) {
        $this->printSiteImageNames[] = new Field(self::FIELD_IMAGES_MACHINENAME, $name, null, count($this->printSiteImageNames));
    }

    public function getSaveSQLString()
    {
        return '';
    }

    public function getImagesData() {
        return [];
    }

    public function getFields()
    {
        return parent::getFields();
    }
}