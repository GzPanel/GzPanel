<?php
namespace api\Entities;

class Supported_ServersEntity
{
	public $App_ID;
 	public $Name;
    public $Description;
	public $Installation;
	public $Execution;
	public $OS;

    public function getArrayCopy()
    {
        return array(
			'App_ID'			=> $this->App_ID,
			'Name'				=> $this->Name,
            'Description' => $this->Description,
            'Installation' => $this->Installation,
			'Execution'			=> $this->Execution,
			'OS'				=> $this->OS,
        );
    }
 
    public function exchangeArray(array $array)
    {
		$this->App_ID			= $array['App_ID'];
		$this->Name				= $array['Name'];
        $this->Description = $array['Description'];
        $this->Installation = $array['Installation'];
		$this->Execution		= $array['Execution'];
		$this->OS				= $array['OS'];
    }
}
