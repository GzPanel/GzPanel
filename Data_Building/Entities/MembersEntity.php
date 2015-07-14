<?php
namespace Data_Building\Entities;

use Data_Building\Exceptions\InvalidParameterException;

class MembersEntity
{
	/**
	 * A unique ID used to identify a member.
	 * @var int
	 */
	public $User_ID;
	/**
	 * A unique username used to allow users identify each other.
	 * @var string
	 */
 	public $Username;
	/**
	 * An email for the user - Will be used in later stages.
	 * @var string
	 */
	public $Email_Address;
	/**
	 * A hashed/secured password - May get removed as it is not required.
	 * IT IS NOT SUPPOSED TO BE SAVED IN PLAIN FORMAT.
	 * @var string
	 */
	public $Password;

	/**
	 * Return an array representation of the properties listed here, that the member has.
	 * @return array
	 */
    public function getArrayCopy()
    {
        return array(
			'User_ID'			=> $this->User_ID,
			'Username'			=> $this->Username,
			'Email_Address'		=> $this->Email_Address,
			'Password'			=> $this->Password,
        );
    }

	/**
	 * A quick way to set data - No validation is done, therefore not recommended
	 * to be used unless retrieving data from database (which should already be doing the validation).
	 * @param array $array
	 */
    public function exchangeArray(array $array)
    {
		$this->User_ID			= $array['User_ID'];
		$this->Username			= $array['Username'];
		$this->Email_Address	= $array['Email_Address'];
		$this->Password			= $array['Password'];
    }

	/**
	 * Set the unique ID for the user.
	 * @param $id
	 * @throws InvalidParameterException
	 */
	public function setID($id)
	{
		if (!is_int($id))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $id);
		else
			$this->User_ID = $id;
	}

	/**
	 * Return the user ID.
	 * @return int
	 */
	public function getID()
	{
		return $this->User_ID;
	}

	/**
	 * Return the username.
	 * @return string
	 */
	public function getUsername()
	{
		return $this->Username;
	}

	/**
	 * Set the username for the user.
	 * @param $username
	 * @throws InvalidParameterException
	 */
	public function setUsername($username)
	{
		if (!is_int($username))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $username);
		else
			$this->Username = $username;
	}

	/**
	 * Return the email address.
	 * @return string
	 */
	public function getEmailAddress()
	{
		return $this->Email_Address;
	}

	/**
	 * Set the email address for the user.
	 * @param $email
	 * @throws InvalidParameterException
	 */
	public function setEmailAddress($email)
	{
		if (!is_string($email))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $email);
		else
			$this->Email_Address = $email;
	}

	/**
	 * Return the password.
	 * @return string
	 */
	public function getPassword()
	{
		return $this->Password;
	}

	/**
	 * Set the password for the user.
	 * @param $password
	 * @throws InvalidParameterException
	 */
	public function setPassword($password)
	{
		if (!is_string($password))
			throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $password);
		else
			$this->$password = $password;
	}

}