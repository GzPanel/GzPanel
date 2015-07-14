<?php
namespace Data_Building\Entities;

use Data_Building\Exceptions\InvalidParameterException;

class ServersEntity
{
	/**
	 * The ID of the server.
	 * @var int
	 */
	private $ID;
	/**
	 * The server's client ID.
	 * @var int
	 */
	private $Owner;
	/**
	 * The creation time of the server.
	 * @var \DateTime
	 */
	private $Creation;
	/**
	 * The unique name of the server
	 * @var string
	 */
	private $Name;
	/**
	 * The hostname of the server
	 * @var string
	 */
	private $Host;
	/**
	 * The App ID of the server running on this node
	 * @var int
	 */
	private $App_ID;
	/**
	 * The node which the server is being hosted on
	 * @var int
	 */
	private $Node;

	/**
	 * Return an array representation of the Server's properties
	 * @return array
	 */
    public function getArrayCopy()
    {
        return array(
			'ID' => $this->ID,
			'Owner' => $this->Owner,
			'Creation' => $this->Creation,
			'Name' => $this->Name,
			'Host' => $this->Host,
			'App_ID' => $this->App_ID,
			'Node' => $this->Node,
        );
    }
 
    public function exchangeArray(array $array)
    {
		$this->ID = $array['ID'];
		$this->Owner = $array['Owner'];
		$this->Creation = $array['Creation'];
		$this->Name = $array['Name'];
		$this->Host = $array['Host'];
		$this->App_ID = $array['App_ID'];
		$this->Node = $array['Node'];
    }
	public function getNode(){
		return $this->Node;
	}
	public function setNode($node){
		if (is_int($node))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integer. Input was: ' . $node);
		else
			$this->Node = $node;
	}
	public function getApp(){
		return $this->App_ID;
	}
	public function setApp($app){
		if (is_int($app))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integer. Input was: ' . $app);
		else
			$this->App_ID = $app;
	}
	public function getHost(){
		return $this->Host;
	}
	public function setHost($host){
		if (is_string($host))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $host);
		else
			$this->Host = $host;
	}
	public function getName(){
		return $this->Name;
	}
	public function setName($name){
		if (is_string($name))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $name);
		else
			$this->Name = $name;
	}
	public function getCreation(){
		return $this->Creation;
	}
	public function setCreation($creation){
		if (is_string($creation))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $creation);
		else
			$this->Creation = $creation;
	}

	public function getOwner(){
		return $this->Owner;
	}
	public function setOwner($owner){
		if (is_int($owner))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $owner);
		else
			$this->Owner = $owner;
	}
	public function getID(){
		return $this->ID;
	}
	public function setID($id){
		if (is_int($id))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $id);
		else
			$this->ID = $id;
	}

}
