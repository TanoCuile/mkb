<?php

namespace Model;

class Node {
    protected $nid;
    protected $vid;
    protected $type;
    protected $language;
    protected $title;
    protected $uid;
    protected $status;
    protected $created;
    protected $changed;
    protected $comment;
    protected $promote;
    protected $sticky;
    protected $tnid;
    protected $translate;

    /**
     * Node constructor.
     * @param $type
     * @param $language
     * @param $title
     * @param $uid
     * @param $status
     */
    public function __construct($type, $language, $title, $uid, $status)
    {
        $this->type = $type;
        $this->language = $language;
        $this->title = $title;
        $this->uid = $uid;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getSaveSQLString() {
        return '';
    }

    /**
     * @return array
     */
    public function getFields() {
        return [];
    }
}