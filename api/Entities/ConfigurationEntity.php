<?php
namespace api\Entities;

class ConfigurationEntity
{
    public $ID;
    public $Name;
    public $Value;

    public function getArrayCopy()
    {
        return array(
            'ID' => $this->ID,
            'Name' => $this->Name,
            'Value' => $this->Value,
        );
    }

    public function exchangeArray(array $array)
    {
        $this->ID = $array['ID'];
        $this->Name = $array['Name'];
        $this->Value = $array['Value'];
    }
}
