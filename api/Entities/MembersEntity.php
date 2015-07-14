<?php
namespace api\Entities;

class MembersEntity
{
    public $User_ID;
    public $Username;
    public $Email_Address;

    public function getArrayCopy()
    {
        return array(
            'User_ID' => $this->User_ID,
            'Username' => $this->Username,
            'Email_Address' => $this->Email_Address,
        );
    }

    public function exchangeArray(array $array)
    {
        $this->User_ID = $array['User_ID'];
        $this->Username = $array['Username'];
        $this->Email_Address = $array['Email_Address'];
    }
}