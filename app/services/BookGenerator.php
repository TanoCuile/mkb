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
//        NodeTitleCompatibility::FINALE_1_2_PAGE => [
//            'callback' => 'addFinale',
//            'fileTitle' => 'finale1'
//        ],
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
     * @param \Service\NodeGenerator $nodeGenerator
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

        $imageTitle = NodeTitleCompatibility::getRealTitle(NodeTitleCompatibility::START_PAGE);
        $resultImagePath = FileSystemUtils::getFileRealPath($this->getPagesDirPath($uniqueId) . $imageTitle . '.png');
        FileSystemUtils::mkpath(dirname($resultImagePath));
        $text_options['saved_path'] = $resultImagePath;
        $path = FileSystemUtils::getPagePath($gender, $age, $imageTitle, 1, 'L');

        $this->putTextToImage($path . '/' . $imageTitle . '.png', Utils::strtoupper($firstNameString), $uniqueId, $font, $text_options);
        $imageData = FileSystemUtils::getImageData($resultImagePath);
        $book->addImage($resultImagePath, $alt, $title, $imageData[0], $imageData[1], NodeTitleCompatibility::START_PAGE);
        return true;
    }

    protected function addIntro0(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $alt = 'intro0';
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $lastNameString = trim(mb_strtolower($book->getLastName()));
        $imageTitle = NodeTitleCompatibility::INTRO_0_PAGE;

        $variant = 1;
        $this->saveJustCopy($book, $uniqueId, $gender, $age, $imageTitle, 1, 'L', $alt, $title);

        $resultImagePath = $this->getPagesDirPath($uniqueId)
            . $this->getPageImageFile($gender, $age, $imageTitle, $variant, 'R') . '.png';
        $path = $this->getPageImagePath($gender, $age, $imageTitle, $variant, 'R');
        $im = imagecreatefrompng($path);

        $font_path = FileSystemUtils::getNadiriiFontPath();
        $black = imagecolorallocate($im, 0, 0, 0);
        imagettftext($im, 36, 0, 280, 145, $black, $font_path, $lastNameString);

        $a = imagepng($im, $resultImagePath);
        if (!empty($im)) {
            imagedestroy($im);
        }
        $imageData = FileSystemUtils::getImageData($resultImagePath);
        $book->addImage($resultImagePath, $alt, $title, $imageData[0], $imageData[$variant], $imageTitle);
        return true;
    }

    protected function addIntro(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $imageTitle = $step;
        $alt = 'intro0';
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $this->saveJustCopy($book, $uniqueId, $gender, $age, $imageTitle, 1, 'L', $alt, $title);
        $this->saveJustCopy($book, $uniqueId, $gender, $age, $imageTitle, 1, 'R', $alt, $title);
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
        $lastNameString = trim(mb_strtolower($book->getLastName()));
        $imageTitle = isset($stepData['fileTitle']) ? $stepData['fileTitle'] : $step;

        $this->saveJustCopy($book, $uniqueId, $gender, $age, $imageTitle, 1, 'L', $alt, $title);

        if ($imageTitle != 'finale4') {
            $this->saveJustCopy($book, $uniqueId, $gender, $age, $imageTitle, 1, 'R', $alt, $title);
        }
        return true;
    }

    protected function addLastNameStory(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $alt = 'intro0';
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $imageTitle = 'finale1';

        // Trinkets
        $sourceImages = [];
        foreach ($book->trinkets as $trinket) {
            $sourceImages[] = imagecreatefrompng(FileSystemUtils::getTrinketsImagesPath() . '/resized/' . $trinket . '.png');
        }

        // Background
        if (!file_exists($this->getPageImagePath($gender, $age, 'finale2', 1, 'L'))) {
            $path = FileSystemUtils::getABCPath() . '/background.jpg';
        }
        else {
            $path = $this->getPageImagePath($gender, $age, 'finale2', 1, 'L');
        }
        $book->setBackground($path);
        $im = imagecreatefrompng($path);

        // Set the margins for the stamp and get the height/width of the stamp image.
        $y = imagesy($im);
        $x = imagesx($im) - 100;
        $letter_width = self::LASTNAME_LETTER_WIDTH;
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
        }
        elseif($countSourceImages <= 6) {
            $all_letters_width = count($sourceImages) * $letter_width;
            $marge_left = 0.8*($x * 0.8 - $all_letters_width) / 2;
        }
        else {
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
            imagesavealpha($image_p, TRUE);
            $color = imagecolorallocatealpha($image_p, 0x00, 0x00, 0x00, 127);
            imagefill($image_p, 0, 0, $color);
            imagecopyresampled($image_p, $img, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);

            // Copy the stamp image onto our photo using
            // the margin offsets and the photo width to
            // calculate positioning of the stamp.
            imagecopy($im, $image_p, $marge_left, $y / 2 - $new_height / 2, 0, 0, $new_width, $new_height);
            $marge_left += $letter_width;
        }
        // Output and free memory.
        $time = time();
        $string = trim(preg_replace('/[-\s]+/', '-', $book->getLastName()), '-');
        $path =  $this->getPagesDirPath($uniqueId) . 'Finale2_01_L.png';
        $a = imagepng($im, $path);
        $imageData = FileSystemUtils::getImageData($path);
        $book->addImage(
            $path,
            $alt,
            $title,
            $imageData[0],
            $imageData[1],
            'Finale2_01_L'
        );
        if (!empty($im)) {
            imagedestroy($im);
        }
        return true;
    }

    protected function addLastNameStoryPair(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $alt = 'surname page pair';
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();

        $path = $this->getPageImagePath($gender, $age, 'finale2', 1, 'R');
        $im = imagecreatefrompng($path);

        $time = time();
        $string = ucfirst(trim(preg_replace('/[-\s]+/', '-', $book->getFirstName()), '-'));

        $font_path = FileSystemUtils::getKOMTXTFontPath();
        $black = imagecolorallocate($im, 0, 0, 0);
        if ($age == '14') {
            imagettftext($im, 35, 0, 105, 210, $black, $font_path, '');
        }
        else {
            // TODO: translate
            imagettftext($im, 35, 0, 105, 210, $black, $font_path, 'Prince ' . $string);
        }

        $file = $this->getPagesDirPath($uniqueId) . 'Finale2_01_R.png';
        $a = imagepng($im, $file);
        $imageData = FileSystemUtils::getImageData($file);
        $book->addImage(
            $file,
            $alt,
            $title,
            $imageData[0],
            $imageData[1],
            'Finale2_01_R'
        );

        return true;
    }

    protected function addFinale3(JsonBook $book, $step, $stepData, $uniqueId):bool {
        $alt = $step;
        $title = '';
        $gender = $book->getGender();
        $age = $book->getAgeCategory();
        $path = $this->getPageImagePath($gender, $age, 'finale3', 1, 'L');
        $im = imagecreatefrompng($path);

        $time = time();
        //$string = ucfirst(trim(preg_replace('/[-\s]+/', '-', $form_state['values']['field_book_last_name']['und'][0]['value']), '-'));
        $string = ucfirst(trim($book->getLastName()));

        $font_path = FileSystemUtils::getNadiriiFontPath();
        $black = imagecolorallocate($im, 255, 255, 255);
        imagettftext($im, 100, 0, 845, 865, $black, $font_path, $string);

        $path = $this->getPagesDirPath($uniqueId);
        $fileName = $this->getPageImageFile($gender, $age, 'finale3', 1, 'L');
        $a = imagepng($im, $path . '/' . $fileName . '.png');
        if (!empty($im)) {
            imagedestroy($im);
        }
        $imageData = FileSystemUtils::getImageData($path. '/' . $fileName . '.png');
        $book->addImage(
            $path . '/' . $fileName . '.png',
            $alt,
            $title,
            $imageData[0],
            $imageData[1],
            $fileName
        );
        return true;
    }

    public function addLetterToBook($iconName, $time, JsonBook $book)
    {
        $handle = fopen(FileSystemUtils::getIconsPath() . '/' . $iconName . '.png', 'r');
        $file = Utils::saveFileFromData($handle, FileSystemUtils::getFileRealPath($this->getSmallLettersPath($time) . $iconName . '.png'));
        $imageData = FileSystemUtils::getImageData($file);
        $book->addSmallLetter($file, '', '', $imageData[0], $imageData[1]);
    }

    function putTextToImage($path, $text, $uniqueId, $font, $options = array()) {
        // Create image.
        $image = imagecreatefrompng($path);
        // Image width and height.
        $image_width = imagesx($image);
        $image_height = imagesy($image);
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
            $shadow_colour = imagecolorallocate($image, $shadow_colours[0], $shadow_colours[1], $shadow_colours[2]);
            imagettftext($image, $font_size, $angle, $x, $y + $shadow_weight, $shadow_colour, $font['font'], $text);
        }
        // Adding text.
        $font_colours = (!empty($font['colour'])) ? $font['colour'] : array(0, 0, 0);
        $font_colour = imagecolorallocate($image, $font_colours[0], $font_colours[1], $font_colours[2]);
        imagettftext($image, $font_size, $angle, $x, $y, $font_colour, $font['font'], $text);
        // Save image.
        if (!empty($options['saved_path'])) {
            $saved_path = $options['saved_path'];
        }
        else {
            throw new GenerationException("No path send", $options);
        }
        $changedImageHandle = imagepng($image, $saved_path);
        imagedestroy($image);

        if ($changedImageHandle) {
            return $saved_path;
        }
        else {
            throw new \RuntimeException('Can not generate image');
        }
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
}