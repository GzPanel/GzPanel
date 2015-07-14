<?php
namespace api\Entities;

class Nodes_StatusEntity
{
    public $Ping_ID;
    public $Node;
    public $Time_Pinged;
    public $Ping;
    public $Load_AVG;
    public $HDD_Space;
    public $RAM;

    public function getArrayCopy()
    {
        return array(
            'Ping_ID' => $this->Ping_ID,
            'Node' => $this->Node,
            'Time_Pinged' => $this->Time_Pinged,
            'Ping' => $this->Ping,
            'Load_AVG' => $this->Load_AVG,
            'HDD_Space' => $this->HDD_Space,
            'RAM' => $this->RAM,
        );
    }

    public function exchangeArray(array $array)
    {
        $this->Ping_ID = $array['Ping_ID'];
        $this->Node = $array['Node'];
        $this->Time_Pinged = $array['Time_Pinged'];
        $this->Ping = $array['Ping'];
        $this->Load_AVG = $array['Load_AVG'];
        $this->HDD_Space = $array['HDD_Space'];
        $this->RAM = $array['RAM'];
    }
}
