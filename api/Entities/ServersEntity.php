<?php
namespace api\Entities;

class ServersEntity
{
    public $ID;
    public $Owner;
    public $Creation;
    public $Name;
    public $Host;
    public $App_ID;
    public $Node;

    public function getArrayCopy()
    {
        return array(
            'ID' => $this->ID,
            'Owner' => $this->Owner,
            'Creation' => $this->Creation,
            'Name' => $this->Name,
            'Host' => $this->Host,
            'App_ID' => $this->App_ID,
            'Node' => $this->Node,
        );
    }

    public function exchangeArray(array $array)
    {
        if (isset($array['ID']))
            $this->ID = $array['ID'];
        if (isset($array['Owner']))
            $this->Owner = $array['Owner'];
        if (isset($array['Creation']))
            $this->Creation = $array['Creation'];
        if (isset($array['Name']))
            $this->Name = $array['Name'];
        if (isset($array['Host']))
            $this->Host = $array['Host'];
        if (isset($array['App_ID']))
            $this->App_ID = $array['App_ID'];
        if (isset($array['Node']))
            $this->Node = $array['Node'];
    }
}
