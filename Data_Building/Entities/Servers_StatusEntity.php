<?php
namespace Data_Building\Entities;

use Data_Building\Exceptions\InvalidParameterException;

class Servers_StatusEntity
{
    /**
     * Status ID of the data query.
     * @var int
     */
    public $Ping_ID;
    /**
     * Server ID for the data query.
     * @var int
     */
    public $Server_ID;
    /**
     * Time the data query was done.
     * @var \DateTime
     */
    public $Time_Pinged;
    /**
     * Data retrieved from the data query.
     * @var string
     */
    public $Data;

    /**
     * Return an array form of the properties of the data query.
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'Ping_ID' => $this->Ping_ID,
            'Server_ID' => $this->Server_ID,
            'Time_Pinged' => $this->Time_Pinged,
            'Data' => $this->Data,
        );
    }

    /**
     * Set the data from array format to the properties of the Status.
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        $this->Ping_ID = $array['Ping_ID'];
        $this->Server_ID = $array['Server_ID'];
        $this->Time_Pinged = $array['Time_Pinged'];
        $this->Data = $array['Data'];
    }

    /**
     * Set the ping query ID.
     * @param $id
     * @throws InvalidParameterException
     */
    public function setID($id){
        if (is_int($id))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $id);
        else
            $this->Ping_ID = $id;
    }
    /**
     * Set the server ID queried.
     * @param $id
     * @throws InvalidParameterException
     */
    public function setServer($id){
        if (is_int($id))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $id);
        else
            $this->Server_ID = $id;
    }

    /**
     * Set the time the server was queried.
     * @param $time
     * @throws InvalidParameterException
     */
    public function setTime($time){
        if (is_string($time))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $time);
        else
            $this->Time_Pinged = $time;
    }

    /**
     * Set the data retrieved from the query.
     * @param $data
     * @throws InvalidParameterException
     */
    public function setData($data){
        if (is_string($data))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $data);
        else
            $this->Data = $data;
    }

    /**
     * Return the ping ID.
     * @return int
     */
    public function getID(){
        return $this->Ping_ID;
    }

    /**
     * Return the server ID.
     * @return int
     */
    public function getServer(){
        return $this->Server_ID;
    }

    /**
     * Return the time the query was sent.
     * @return \DateTime
     */
    public function getTime(){
        return $this->Time_Pinged;
    }

    /**
     * Return the data retrieved from the query
     * @return string
     */
    public function getData(){
        return $this->Data;
    }
}
