<?php
namespace Data_Building\Entities;

use Data_Building\Exceptions\InvalidParameterException;

class ConfigurationEntity
{
    /**
     * A unique ID for this config entry - Not required, might be removed.
     * @var int
     */
    public $ID;
    /**
     * A name representation of the configuration
     * @var string
     */
    public $Name;
    /**
     * A value representation for the config-property.
     * @var string
     */
    public $Value;

    /**
     * Returns the representation of the properties via array.
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'ID' => $this->ID,
            'Name' => $this->Name,
            'Value' => $this->Value
        );
    }

    /**
     * Allows the exchange of array data to initialize object properties.
     * @deprecated Old way of setting data - The data however is not verified, and can lead to issues.
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        $this->ID = $array['ID'];
        $this->Name = $array['Name'];
        $this->Value = $array['Value'];
    }

    /**
     * Returns the ID of the config-property.
     * @return int
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * Set the ID of the config-property.
     * @param $id
     * @throws InvalidParameterException
     */
    public function setID($id)
    {
        if (!is_int($id))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $id);
        else
            $this->ID = $id;
    }

    /**
     * Returns the name of the config-property.
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Set the name of the config-property.
     * @param $name
     * @throws InvalidParameterException
     */
    public function setName($name)
    {
        if (!is_string($name))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $name);
        else
            $this->Name = $name;
    }

    /**
     * Returns the value of the config-property.
     * @return string
     */
    public function getValue()
    {
        return $this->Value;
    }

    /**
     * Set the value of the config-property.
     * @param $value
     * @throws InvalidParameterException
     */
    public function setValue($value)
    {
        if (!is_string($value))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $value);
        else
            $this->Value = $value;
    }
}
