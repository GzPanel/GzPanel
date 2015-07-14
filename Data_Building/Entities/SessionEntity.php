<?php
namespace Data_Building\Entities;

class SessionEntity
{
    public $User_ID;
    public $Session_Start;
    public $Session_Key;
    public $Session_IP;

    public function getArrayCopy()
    {
        return array(
            'User_ID' => $this->User_ID,
            'Session_Start' => $this->Session_Start,
            'Session_Key' => $this->Session_Key,
            'Session_IP' => $this->Session_IP,
        );
    }

    public function exchangeArray(array $array)
    {
        $this->User_ID = $array['User_ID'];
        $this->Session_Start = $array['Session_Start'];
        $this->Session_Key = $array['Session_Key'];
        $this->Session_IP = $array['Session_IP'];
    }
}
