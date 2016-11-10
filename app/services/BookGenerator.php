<?php

namespace Service;

use Exception\GenerationException;
use Model\JsonBook;

/**
 * Class BookGenerator
 * Author Stri <strifinder@gmail.com>
 * @package Service
 */
class BookGenerator {
    const SMALL_LETTERS = 'small_letters';
    const FIRSTNAME_STORY = 'firstname_story';
    const LASTNAME_STORY = 'lastname_story';
    const LASTNAME_STORY_PAIR = 'lastname_story_pair';
    const FILLERS = 'fillers';
    const LASTNAME_LETTER_WIDTH = 360;
    const LASTNAME_LETTER_HEIGHT = 360;

    private $scenario = [
        self::SMALL_LETTERS => [
            'callback' => 'initializeSmallImages'
        ],
        NodeTitleCompatibility::START_PAGE => [
            'callback' => 'addStartPage'
        ],
        NodeTitleCompatibility::INTRO_0_PAGE => [
            'callback' => 'addIntro0'
        ],
        NodeTitleCompatibility::INTRO_1_PAGE => [
            'callback' => 'addIntro'
        ],
        NodeTitleCompatibility::INTRO_2_PAGE => [
            'callback' => 'addIntro'
        ],
        NodeTitleCompatibility::INTRO_3_PAGE => [
            'callback' => 'addIntro'
        ],
        self::FIRSTNAME_STORY => [
            'callback' => 'addFirstNameStory'
        ],
        self::FILLERS => [
            'callback' => 'addFillers'
        ],
        NodeTitleCompatibility::FINALE_1_1_PAGE => [
            'callback' => 'addFinale',
            'fileTitle' => 'finale1'
        ],
        self::LASTNAME_STORY => [
            'callback' => 'addLastNameStory'
        ],
        self::LASTNAME_STORY_PAIR => [
            'callback' => 'addLastNameStoryPair'
        ],
        NodeTitleCompatibility::FINALE_3_1_PAGE => [
            'callback' => 'addFinale3',
        ],
        NodeTitleCompatibility::FINALE_3_2_PAGE => [
            'callback' => 'addFinale',
            'fileTitle' => 'finale3'
        ],
        NodeTitleCompatibility::FINALE_4_1_PAGE => [
            'callback' => 'addFinale',
            'fileTitle' => 'finale4'
        ],
    ];

    /**
     * BookGenerator constructor.
     * 
     */
    public function __construct()
    {
    }

    public function generateBook($firstName, $lastName, $gender, $age = 14, $uniqueId = null) {
        if (!$uniqueId) {
            $uniqueId = uniqid(time());
            $path = $this->getPagesDirPath($uniqueId);
            FileSystemUtils::mkpath($path);
        }
        $book = new JsonBook($firstName, $lastName, $gender, $age, [], [], '', $uniqueId);

        foreach ($this->scenario as $step => $stepInfo) {
            if (is_callable([$this, $stepInfo['callback']])) {
                $result = call_user_func([$this, $stepInfo['callback']], $book, $step, $stepInfo, $uniqueId);
                if (!$result) {
                    throw new GenerationException('Step was failed: ' . $step, $stepInfo);
                }
            } else
                throw new GenerationException('Invalid callback: ' . $stepInfo['callback'], $stepInfo);
        }

        return $book->getImagesData();
    }

    protected function initializeSmallImages(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $this->addLetterToBook('start', $uniqueId, $book);

        $firstNameString = trim(mb_strtolower($book->getFirstName()));

        // Go throught all letters.
        foreach (str_split($firstNameString) as $letter) {
            // Add letter icon.
            $this->addLetterToBook($letter . '32', $uniqueId, $book);
        }

        $this->addLetterToBook('end', $uniqueId, $book);
        return true;
    }

    protected function addStartPage(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $alt = 'start page';
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $firstNameString = trim(mb_strtolower($book->getFirstName()));

        $font = array(
            'font' => FileSystemUtils::getInkBanditsFontPath(),
            'size' => 120,
            'colour' => array(255, 255, 255),
            'shadow' => array(
                'colour' => array(45, 45, 45),
                'weight' => 10,
            ),
        );
        $text_options = array(
            'y' => 235,
        );
        if ($gender == 'girl') {
            $text_options = array(
                'y' => 232,
            );
        }

        $imageUniquePath = NodeTitleCompatibility::getRealTitle(NodeTitleCompatibility::START_PAGE);
        $resultImagePath = FileSystemUtils::getFileRealPath($this->getPagesDirPath($uniqueId) . $imageUniquePath . '.png');

        $path = FileSystemUtils::getPagePath($gender, $age, $imageUniquePath, 1, 'L') . '/' . $imageUniquePath . '.png';
        $serverImageName = NodeTitleCompatibility::START_PAGE . '.png';

        ImageWorker::saveImageWithText($path, Utils::strtoupper($firstNameString), $font, $text_options, $resultImagePath);

        $this->addImageToBook($book, $resultImagePath, $alt, $title, $serverImageName);
        return true;
    }

    protected function addIntro0(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $variant = 1;
        $alt = $step;
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $lastNameString = trim(mb_strtolower($book->getLastName()));
        $imageUniquePath = NodeTitleCompatibility::INTRO_0_PAGE;

        $this
            ->saveJustCopy($book, $uniqueId, $gender, $age, $imageUniquePath, $variant, 'L', $alt, $title);

        $realImageName =
            $this->getPageImageFile($gender, $age, $imageUniquePath, $variant, 'R') . '.png';

        $resultImagePath = $this->getPagesDirPath($uniqueId) . $realImageName;
        $path = $this->getPageImagePath($gender, $age, $imageUniquePath, $variant, 'R');

        ImageWorker::saveImageWithLabel($path, $lastNameString, $resultImagePath);

        $this->addImageToBook($book, $resultImagePath, $alt, $title, $imageUniquePath);

        return true;
    }

    protected function addIntro(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $imageUniquePath = $step;
        $alt = 'intro0';
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();

        $this->saveJustCopy($book, $uniqueId, $gender, $age, $imageUniquePath, 1, 'L', $alt, $title);
        $this->saveJustCopy($book, $uniqueId, $gender, $age, $imageUniquePath, 1, 'R', $alt, $title);

        return true;
    }

    protected function addFirstNameStory(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $firstNameString = trim(mb_strtolower($book->getFirstName()));

        $usedLetters = [];

        foreach (str_split($firstNameString) as $letter) {
            if (!isset($usedLetters[$letter])) $usedLetters[$letter] = 0;
            $usedLetters[$letter]++;

            $book->trinkets[] = strtoupper($letter) . $usedLetters[$letter];

            $this->fulfillChapter($book, $uniqueId, $gender, $age, $letter, $usedLetters, $title);
        }
        return true;
    }

    protected function addFillers(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $firstNameString = trim(mb_strtolower($book->getFirstName()));

        $diff = 4 - strlen($firstNameString);

        if ($diff >= 1) {
            for ($i = 0; $i < $diff; ++$i) {
                $this->fulfillChapter($book, $uniqueId, $gender, $age, 'filler' . $i, [], $title);
            }
        }

        return true;
    }

    protected function addFinale(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $alt = $step;
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $uniqueImagePath = isset($stepData['fileTitle']) ? $stepData['fileTitle'] : $step;

        $this->saveJustCopy($book, $uniqueId, $gender, $age, $uniqueImagePath, 1, 'L', $alt, $title);

        if ($uniqueImagePath != 'finale4') {
            $this->saveJustCopy($book, $uniqueId, $gender, $age, $uniqueImagePath, 1, 'R', $alt, $title);
        }
        return true;
    }

    protected function addLastNameStory(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $alt = 'intro0';
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $serverImageName = 'Finale2_01_L';
        $finalImagePath =  $this->getPagesDirPath($uniqueId) . $serverImageName . '.png';

        // Trinkets
        $sourceImages = ImageWorker::getLetterImages($book->trinkets);

        // Background
        $path = $this->setBookBackground($book, $gender, $age);
        ImageWorker::addLastNameFinale($path, $sourceImages);

        $this->addImageToBook($book, $finalImagePath, $alt, $title, $serverImageName);

        return true;
    }

    protected function addLastNameStoryPair(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $alt = 'surname page pair';
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $realImageTitle = 'Finale2_01_R';

        $path = $this->getPageImagePath($gender, $age, 'finale2', 1, 'R');
        $resultImagePath = $this->getPagesDirPath($uniqueId) . $realImageTitle . '.png';

        $resultImagePath = ImageWorker::addTestToLastnameStoryPair(
            $book,
            $uniqueId,
            $gender,
            $age,
            $realImageTitle,
            $path,
            $resultImagePath
        );
        $this->addImageToBook($book, $resultImagePath, $alt, $title, $realImageTitle);

        return true;
    }

    protected function addFinale3(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $alt = $step;
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $path = $this->getPagesDirPath($uniqueId);
        $fileName = $this->getPageImageFile($gender, $age, 'finale3', 1, 'L');
        $resultImagePath = $path . '/' . $fileName . '.png';

        $string = ucfirst(trim($book->getLastName()));

        ImageWorker::prepareThirdFinaleImage($path, $string, $resultImagePath);

        $this->addImageToBook($book, $resultImagePath, $alt, $title, $fileName);

        return true;
    }

    public function addLetterToBook($iconName, $time, JsonBook $book)
    {
        $handle = fopen(FileSystemUtils::getIconsPath() . '/' . $iconName . '.png', 'r');
        $file = Utils::saveFileFromData($handle, FileSystemUtils::getFileRealPath($this->getSmallLettersPath($time) . $iconName . '.png'));
        $imageData = FileSystemUtils::getImageData($file);
        $book->addSmallLetter($file, '', '', $imageData[0], $imageData[1]);
    }

    /**
     * @param $uniqueId
     * @return string
     */
    protected function getPagesDirPath($uniqueId):string
    {
        return FileSystemUtils::getFileRealPath('public://mkbooks/' . $uniqueId . '/pages/');
    }

    /**
     * @param $time
     * @return string
     */
    protected function getSmallLettersPath($time):string
    {
        return 'public://mkbooks/' . $time . '/small_letters/';
    }

    /**
     * @param $gender
     * @param $age
     * @param $imageTitle
     * @return mixed
     */
    protected function getPageImagePath($gender, $age, $imageTitle, $variant, $side , $fileTitle = null)
    {
        $path = FileSystemUtils::getFileRealPath(
            FileSystemUtils::getPagePath($gender, $age, $imageTitle, $variant, $side)
        );
        if (!$fileTitle) {
            $files = $this->getPageDirectoryContent($gender, $age, $imageTitle, $variant, $side);
            $path .= '/' . $files[2];
        } else {
            $path .= '/' . $fileTitle;
        }
        return $path;
    }

    protected function getPageImageFile($gender, $age, $imageTitle, $variant, $side)
    {
        $files = $this->getPageDirectoryContent($gender, $age, $imageTitle, $variant, $side);
        return $files[2];
    }

    /**
     * @param JsonBook $book
     * @param $uniqueId
     * @param $gender
     * @param $age
     * @param $imageTitle
     * @param $variant
     * @param $side
     * @param $alt
     * @param $title
     * @return array
     */
    protected function saveJustCopy(
        JsonBook $book,
        $uniqueId,
        $gender,
        $age,
        $imageTitle,
        $variant,
        $side,
        $alt,
        $title,
        $fileTitle = null
    )
    {
        $fileTitle = $fileTitle ? $fileTitle : $this->getPageImageFile(
            $gender,
            $age,
            $imageTitle,
            $variant,
            $side
        );
        $resultImagePath = $this->getPagesDirPath($uniqueId) . $fileTitle;
        $path = $this->getPageImagePath($gender, $age, $imageTitle, $variant, $side, $fileTitle);
        FileSystemUtils::saveFileTo(fopen($path, 'r'), $resultImagePath);

        $imageData = FileSystemUtils::getImageData($resultImagePath);
        $book->addImage(
            $resultImagePath,
            $alt,
            $title,
            $imageData[0],
            $imageData[1],
            $fileTitle ? $fileTitle : $imageTitle
        );
    }

    /**
     * @param $gender
     * @param $age
     * @param $imageTitle
     * @param $variant
     * @param $side
     * @return array
     */
    protected function getPageDirectoryContent($gender, $age, $imageTitle, $variant, $side):array
    {
        $path = FileSystemUtils::getFileRealPath(
            FileSystemUtils::getPagePath($gender, $age, $imageTitle, $variant, $side)
        );
        $files = scandir($path);
        return $files;
    }

    /**
     * @param JsonBook $book
     * @param $uniqueId
     * @param $gender
     * @param $age
     * @param $letter
     * @param $usedLetters
     * @param $title
     */
    protected function fulfillChapter(
        JsonBook $book,
        $uniqueId,
        $gender,
        $age,
        $letter,
        $usedLetters,
        $title
    )
    {
        $leftSidePages = $this->getPageDirectoryContent(
            $gender,
            $age,
            $letter,
            $usedLetters[$letter],
            'L'
        );
        $rightSidePages = $this->getPageDirectoryContent(
            $gender,
            $age,
            $letter,
            $usedLetters[$letter],
            'R'
        );

        if ($leftSidePages[2]) {
            $this->saveJustCopy(
                $book,
                $uniqueId,
                $gender,
                $age,
                $letter,
                $usedLetters[$letter],
                'L',
                $leftSidePages[2],
                $title,
                $leftSidePages[2]
            );
        }
        if ($rightSidePages[2]) {
            $this->saveJustCopy(
                $book,
                $uniqueId,
                $gender,
                $age,
                $letter,
                $usedLetters[$letter],
                'R',
                $rightSidePages[2],
                $title,
                $rightSidePages[2]
            );
        }
        if ($leftSidePages[3]) {
            $this->saveJustCopy(
                $book,
                $uniqueId,
                $gender,
                $age,
                $letter,
                $usedLetters[$letter],
                'L',
                $leftSidePages[3],
                $title,
                $leftSidePages[3]
            );
        }
        if ($rightSidePages[3]) {
            $this->saveJustCopy(
                $book,
                $uniqueId,
                $gender,
                $age,
                $letter,
                $usedLetters[$letter],
                'R',
                $rightSidePages[3],
                $title,
                $rightSidePages[3]
            );
        }
    }

    /**
     * @param JsonBook $book
     * @param $resultImagePath
     * @param $alt
     * @param $title
     * @param $serverImageName
     */
    protected function addImageToBook(
        JsonBook $book,
        $resultImagePath,
        $alt,
        $title,
        $serverImageName
    )
    {
        $imageData = FileSystemUtils::getImageData($resultImagePath);
        $book->addImage(
            $resultImagePath,
            $alt,
            $title,
            $imageData[0],
            $imageData[1],
            $serverImageName
        );
    }

    /**
     * @param JsonBook $book
     * @param $gender
     * @param $age
     * @return mixed
     */
    protected function setBookBackground(JsonBook $book, $gender, $age)
    {
        if (!file_exists($this->getPageImagePath($gender, $age, 'finale2', 1, 'L'))) {
            $path = FileSystemUtils::getABCPath() . '/background.jpg';
        } else {
            $path = $this->getPageImagePath($gender, $age, 'finale2', 1, 'L');
        }
        $book->setBackground($path);
        return $path;
    }

}