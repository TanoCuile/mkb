<?php

namespace Service;

class NodeTitleCompatibility {
    const START_PAGE = 'start page';
    const INTRO_0_PAGE = 'intro0';
    const INTRO_1_PAGE = 'intro1';
    const INTRO_2_PAGE = 'intro2';
    const INTRO_3_PAGE = 'intro3';
    const FINALE_1_1_PAGE = 'finale11';
    const FINALE_1_2_PAGE = 'finale12';
    const FINALE_3_1_PAGE = 'finale31';
    const FINALE_3_2_PAGE = 'finale32';
    const FINALE_4_1_PAGE = 'finale41';
    const titles_map = [
        'start page' => 'start_page',
        'last page' => 'last_page'
    ];

    public static function getRealTitle($originalTitle) {
        $titlesMap = self::titles_map;
        return isset($titlesMap[$originalTitle]) ? $titlesMap[$originalTitle] : $originalTitle;
    }
}