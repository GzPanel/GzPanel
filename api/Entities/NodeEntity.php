<?php
namespace api\Entities;

class NodeEntity
{
    public $Node;
    public $Name;
    public $Creation;
    public $Host;
    public $Port;
    public $Password;
    public $Directory;
    public $OS;
    public $Active;
    public $Online;

    public function getArrayCopy()
    {
        return array(
            'Node' => $this->Node,
            'Name' => $this->Name,
            'Creation' => $this->Creation,
            'Host' => $this->Host,
            'Port' => $this->Port,
            'Password' => $this->Password,
            'Directory' => $this->Directory,
            'OS' => $this->OS,
            'Active' => $this->Active,
            'Online' => $this->Online,
        );
    }

    public function exchangeArray(array $array)
    {
        $this->Node = $array['Node'];
        $this->Name = $array['Name'];
        $this->Creation = $array['Creation'];
        $this->Host = $array['Host'];
        $this->Port = $array['Port'];
        $this->Password = $array['Password'];
        $this->Directory = $array['Directory'];
        $this->OS = $array['OS'];
        $this->Active = $array['Active'];
        $this->Online = $array['Online'];
    }
}
