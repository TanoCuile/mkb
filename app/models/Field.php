<?php

namespace Model;

class Field {
    protected $machineName;
    protected $delta;
    protected $entityId;
    protected $entityType;
    protected $bundle;
    protected $deleted;
    protected $revisionId;
    protected $language;
    protected $data;
    protected $dataFieldName = null;

    /**
     * Field constructor.
     * @param $machineName
     * @param $data
     */
    public function __construct($machineName, $data, $entityId = null, $delta = 0, $dataFieldName = null, $bundle = '', $entityType = 'node', $deleted = 0, $revisionId = 0, $language = 'und')
    {
        $this->machineName = $machineName;
        $this->data = $data;
        $this->dataFieldName = $dataFieldName;
        $this->entityId = $entityId;
        $this->delta = $delta;
        $this->bundle = $bundle;
        $this->entityType = $entityType;
        $this->deleted = $deleted;
        $this->revisionId = $revisionId;
        $this->language = $language;
    }

    /**
     * @return mixed
     */
    public function getMachineName()
    {
        return $this->machineName;
    }

    /**
     * @param mixed $machineName
     *
     * @return $this
     */
    public function setMachineName($machineName)
    {
        $this->machineName = $machineName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return null
     */
    public function getDataFieldName()
    {
        return $this->dataFieldName;
    }

    /**
     * @param null $dataFieldName
     *
     * @return $this
     */
    public function setDataFieldName($dataFieldName)
    {
        $this->dataFieldName = $dataFieldName;
        return $this;
    }

    public function getSaveSQLString() {
        $dataFieldName = $this->dataFieldName ? $this->dataFieldName : $this->machineName . '_value';
        return "INSERT INTO field_data_{$this->machineName} (entity_type, bundle, entity_id, language, {$dataFieldName}, delta, deleted, revision_id)
VALUES ({$this->entityType}, {$this->bundle}, {$this->entityId}, {$this->language}, {$this->data}, {$this->delta}, {$this->deleted}, {$this->revisionId})";
    }
}