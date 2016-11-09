<?php

namespace Service;

use mysqli;

class NodeGenerator {
    private $fieldGenerator;

    /**
     * NodeGenerator constructor.
     * @param FieldGenerator $fieldGenerator
     * @param mysqli $db
     */
    public function __construct(FieldGenerator $fieldGenerator, mysqli $db)
    {
        $this->fieldGenerator = $fieldGenerator;
    }


}