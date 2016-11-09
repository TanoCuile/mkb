<?php

namespace Model;

class ImageField extends Field {
    public $alt;
    public $title;
    public $width;
    public $height;

    /**
     * Field constructor.
     * @param $machineName
     * @param $data
     */
    public function __construct($machineName, $data, $entityId = null, $delta = 0, $width = 0, $height = 0, $alt = '', $title = '', $dataFieldName = null, $bundle = '', $entityType = 'node', $deleted = 0, $revisionId = 0, $language = 'und')
    {
        parent::__construct($machineName, $data, $entityId, $delta, $dataFieldName, $bundle, $entityType, $deleted, $revisionId, $language);
        $this->alt = $alt;
        $this->title = $title;
        $this->width = $width;
        $this->height = $height;
    }

    public function getSaveSQLString() {
        $dataFieldName = $this->dataFieldName ? $this->dataFieldName : $this->machineName . '_fid';
        $alt = $this->machineName . '_alt';
        $title = $this->machineName . '_title';
        $width = $this->machineName . '_width';
        $height = $this->machineName . '_height';
        return "INSERT INTO field_data_{$this->machineName} (entity_type, bundle, entity_id, language, {$dataFieldName}, {$alt}, {$title}, {$width}, {$height}, delta, deleted, revision_id)
VALUES ({$this->entityType}, {$this->bundle}, {$this->entityId}, {$this->language}, {$this->data}, {$this->data}, {$this->alt}, {$this->title}, {$this->width}, {$this->height}, {$this->deleted}, {$this->revisionId})";
    }
}