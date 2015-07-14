<?php
namespace Data_Building\Entities;

use Data_Building\Exceptions\InvalidParameterException;

class Nodes_StatusEntity
{
	/**
	 * A unique ID to identify the ping query.
	 * @var int
	 */
	public $Ping_ID;
	/**
	 * The node which this query represents.
	 * @var int
	 */
	public $Node;
	/**
	 * The time at which this query was done.
	 * @var \DateTime
	 */
	public $Time_Pinged;
	/**
	 * The total amount of milliseconds between the panel and the node.
	 * @var int
	 */
	public $Ping;
	/**
	 * The load average at which the node is currently operating at.
	 * This represents the total load on the node.
	 * @var int
	 */
	public $Load_AVG;
	/**
	 * A string representation of the current Hard-drive space available.
	 * @var string
	 */
	public $HDD_Space;
	/**
	 * A string representation of the current RAM available.
	 * @var string
	 */
	public $RAM;

	/**
	 * Return an array representation of the node status query.
	 * @deprecated
	 * @return array
	 */
    public function getArrayCopy()
    {
        return array(
			'Ping_ID' => $this->Ping_ID,
			'Node' => $this->Node,
			'Time_Pinged' => $this->Time_Pinged,
			'Ping' => $this->Ping,
			'Load_AVG' => $this->Load_AVG,
			'HDD_Space' => $this->HDD_Space,
			'RAM'				=> $this->RAM,
        );
    }

	/**
	 * Set the properties of node status via Array. Not recommended to be used, as there is no type checking (Thx php :P)
	 * @param array $array
	 */
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

	public function getID(){
		return $this->Ping_ID;
	}

	public function setID($id){
		if (!is_int($id))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $id);
		else
			$this->Ping_ID = $id;
	}

	public function getRAM(){
		return $this->RAM;
	}

	public function setRAM($RAM){
		if (!is_string($RAM))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $RAM);
		else
			$this->RAM = $RAM;
	}

	public function getHDDSpace(){
		return $this->HDD_Space;
	}

	public function setHDDSpace($space){
		if (!is_string($space))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $space);
		else
			$this->HDD_Space = $space;
	}

	public function getLoad(){
		return $this->Load_AVG;
	}

	public function setLoad($load){
		if (!is_double($load) && !is_int($load))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers and doubles. Input was: ' . $load);
		else
			$this->Load_AVG = $load;
	}

	public function getPing(){
		return $this->Ping;
	}

	public function setPing($ping){
		if (!is_int($ping))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $ping);
		else
			$this->Ping = $ping;
	}

	public function getTime(){
		return $this->Time_Pinged;
	}

	public function setTime($time){
		if (!is_string($time))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $time);
		else
			$this->Time_Pinged = $time;
	}

	public function getNode(){
		return $this->Node;
	}

	public function setNode($id){
		if (!is_int($id))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $id);
		else
			$this->Node = $id;
	}

}
