<?php
namespace api\Entities;

class Servers_StatusEntity
{
    public $Ping_ID;
    public $Server_ID;
    public $Time_Pinged;
    public $Data;

    public function getArrayCopy()
    {
        return array(
            'Ping_ID' => $this->Ping_ID,
            'Server_ID' => $this->Server_ID,
            'Time_Pinged' => $this->Time_Pinged,
            'Data' => $this->Data,
        );
    }

    public function exchangeArray(array $array)
    {
        $this->Ping_ID = $array['Ping_ID'];
        $this->Server_ID = $array['Server_ID'];
        $this->Time_Pinged = $array['Time_Pinged'];
        $this->Data = $array['Data'];
    }
}
