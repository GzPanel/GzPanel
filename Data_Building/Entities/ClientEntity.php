<?php
namespace Data_Building\Entities;

use Data_Building\Exceptions\InvalidParameterException;
use InvalidArgumentException;

class ClientEntity
{
    /**
     * Unique Identifier of the client.
     * @var integer
     */
    private $User_ID;
    /**
     * Username of the client.
     * @var string
     */
    private $Username;
    /**
     * Email Address of the client.
     * @var string
     */
    private $Email_Address;
    /**
     * Password of the client.
     * @var string
     */
    private $Password;
    /**
     * IP Address of the client.
     * @var string
     */
    private $IPAddress;
    /**
     * Unique session key for the current session of the client.
     * @var string
     */
    private $Session_Key;

    /**
     * Construct using the IP of the client - We don't need a setter.
     */
    public function __construct()
    {
        $this->IPAddress = $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Retrieve the array interpretation of the information.
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'User_ID' => $this->User_ID,
            'Username' => $this->Username,
            'Email_Address' => $this->Email_Address,
            'Password' => $this->Password,
            'IPAddress' => $this->IPAddress,
            'Session_Key' => $this->Session_Key,
        );
    }

    /**
     * Returns the User ID
     * @return int
     */
    public function getUserID()
    {
        return $this->User_ID;
    }

    /**
     * Sets the User ID
     * @param $id
     * @throws InvalidParameterException
     */
    public function setUserID($id)
    {
        if (!is_int($id))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $id);
        else
            $this->User_ID = $id;
    }

    /**
     * Returns the Username
     * @return string
     */
    public function getUsername()
    {
        return $this->Username;
    }

    /**
     * Sets the Username
     * @param $name
     * @throws InvalidParameterException
     */
    public function setUsername($name)
    {
        if (!is_string($name))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $name);
        else
            $this->Username = $name;
    }

    /**
     * Returns the Email Address
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->Email_Address;
    }

    /**
     * Sets the Email Address
     * @param $emailAddress
     * @throws InvalidParameterException
     */
    public function setEmailAddress($emailAddress)
    {
        if (!is_string($emailAddress))
            throw new InvalidArgumentException(__FUNCTION__ . ' function only accepts string. Input was: ' . $emailAddress);
        else
            $this->Email_Address = $emailAddress;
    }

    /**
     * Returns the Password (Unhashed)
     * @return string
     */
    public function getPassword()
    {
        return $this->Password;
    }

    /**
     * Sets the Password (Unhashed)
     * @param $password
     * @throws InvalidParameterException
     */
    public function setPassword($password)
    {
        if (!is_string($password))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts string. Input was: ' . $password);
        else
            $this->Password = $password;
    }

    /**
     * Returns the current session key
     * @return string
     */
    public function getSessionKey()
    {
        return $this->Session_Key;
    }

    /**
     * Sets the current session key
     * @param $sessionKey
     * @throws InvalidParameterException
     */
    public function setSessionKey($sessionKey)
    {
        if (!is_string($sessionKey))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts string. Input was: ' . $sessionKey);
        else
            $this->Session_Key = $sessionKey;
    }

    /**
     * Returns the client's IP Address
     * @return string
     */
    public function getIPAddress()
    {
        return $this->IPAddress;
    }

}