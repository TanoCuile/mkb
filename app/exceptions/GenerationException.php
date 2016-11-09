<?php

namespace Exception;

use Exception;

class GenerationException extends \Exception {
    protected $data = [];

    public function __construct($message, $data)
    {
        parent::__construct($message);
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}