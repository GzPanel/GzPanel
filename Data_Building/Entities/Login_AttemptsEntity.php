<?php
namespace Data_Building\Entities;

class Login_AttemptsEntity
{
	/*
	 * This file will not be worked on, as it is currently unused in the panel.
	 * Currently it just has the basics to allow for integration if I choose I need it.
	 */
	public $Attempt_ID;
 	public $IP;
	public $Attempt_Date;

    public function getArrayCopy()
    {
        return array(
			'Attempt_ID'		=> $this->Attempt_ID,
			'IP'				=> $this->IP,
			'Attempt_Date'		=> $this->Attempt_Date,
        );
    }
 
    public function exchangeArray(array $array)
    {
		$this->Attempt_ID		= $array['Attempt_ID'];
		$this->IP				= $array['IP'];
		$this->Attempt_Date		= $array['Attempt_Date'];
    }
}
