<?php
namespace api\Entities;

class Login_AttemptsEntity
{
    public $Attempt_ID;
    public $IP;
    public $Attempt_Date;

    public function getArrayCopy()
    {
        return array(
            'Attempt_ID' => $this->Attempt_ID,
            'IP' => $this->IP,
            'Attempt_Date' => $this->Attempt_Date,
        );
    }

    public function exchangeArray(array $array)
    {
        $this->Attempt_ID = $array['Attempt_ID'];
        $this->IP = $array['IP'];
        $this->Attempt_Date = $array['Attempt_Date'];
    }
}
