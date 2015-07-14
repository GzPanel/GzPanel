<?php
namespace Data_Building\Entities;

class TicketsEntity
{
	/*
	 * This file will not be worked on, as it is currently unused in the panel.
	 * Currently it just has the basics to allow for integration if I choose I need it.
	 */
	public $Ticket_ID;
 	public $Ticket_Author;
	public $Ticket_Creation;
	public $Ticket_Title;
	public $Ticket_Content;
	public $Ticket_Priority;
	public $Ticket_Parent;

    public function getArrayCopy()
    {
        return array(
			'Ticket_ID'					=> $this->Ticket_ID,
			'Ticket_Author'				=> $this->Ticket_Author,
			'Ticket_Creation'			=> $this->Ticket_Creation,
			'Ticket_Title'				=> $this->Ticket_Title,
			'Ticket_Content'			=> $this->Ticket_Content,
			'Ticket_Priority'			=> $this->Ticket_Priority,
			'Ticket_Parent'				=> $this->Ticket_Parent,
        );
    }
 
    public function exchangeArray(array $array)
    {
		$this->Ticket_ID				= $array['Ticket_ID'];
		$this->Ticket_Author			= $array['Ticket_Author'];
		$this->Ticket_Creation			= $array['Ticket_Creation'];
		$this->Ticket_Title				= $array['Ticket_Title'];
		$this->Ticket_Content			= $array['Ticket_Content'];
		$this->Ticket_Priority			= $array['Ticket_Priority'];
		$this->Ticket_Parent			= $array['Ticket_Parent'];
    }
}
