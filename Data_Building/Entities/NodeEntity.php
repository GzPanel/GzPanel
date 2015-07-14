<?php
namespace Data_Building\Entities;

use Data_Building\Exceptions\InvalidParameterException;

class NodeEntity
{
    /**
     * A unique ID to identify this node/system.
     * @var int
     */
    private $Node;
    /**
     * A unique name to identify this node/system.
     * @var string
     */
    private $Name;
    /**
     * A timestamp of when this node/system was added.
     * @var \DateTime
     */
    private $Creation;
    /**
     * The host at which this node resides.
     * @var string
     */
    private $Host;
    /**
     * A port number (could be from 0-65536) 2^16
     * @var int
     */
    private $Port;
    /**
     * The password used to access this system - May soon be depreciated
     * @var string
     */
    private $Password;
    /**
     * The directory at which the home is located - May soon be depreciated.
     * @var string
     */
    private $Directory;
    /**
     * The OS that operates on this system/node.
     * @var string
     */
    private $OS;
    /**
     * If node/system could be used for server deployment.
     * @var boolean
     */
    private $Active;
    /**
     * Is the server online - May soon be depreciated (use node status for accurate data)
     * @var boolean
     */
    private $Online;

    /**
     * Return an array representation of the node properties.
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'Node' => $this->Node,
            'Name' => $this->Name,
            'Creation' => $this->Creation,
            'Host' => $this->Host,
            'Port' => $this->Port,
            'Password' => $this->Password,
            'Directory' => $this->Directory,
            'OS' => $this->OS,
			'Active'			=> $this->Active,
			'Online'			=> $this->Online,
        );
    }

    /**
     * A quick way to set data, however it is not recommended to be used as it lacks type checking.
     * @param array $array
     * @deprecated
     */
    public function exchangeArray(array $array)
    {
        $this->Node = $array['Node'];
        $this->Name = $array['Name'];
        $this->Creation = $array['Creation'];
        $this->Host = $array['Host'];
        $this->Port = $array['Port'];
        $this->Password = $array['Password'];
        $this->Directory = $array['Directory'];
        $this->OS = $array['OS'];
		$this->Active			= $array['Active'];
		$this->Online			= $array['Online'];
    }

    /**
     * Set the ID of the node/system.
     * @param $node
     * @throws InvalidParameterException
     */
    public function setID($node)
    {
        if (!is_int($node))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers. Input was: ' . $node);
        else
            $this->Node = $node;
    }

    /**
     * Get the node/system ID
     * @return int
     */
    public function getID()
    {
        return $this->Node;
    }

    /**
     * Get the node/system unique name.
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Set the name of the node/system.
     * @param $name
     * @throws InvalidParameterException
     */
    public function setName($name)
    {
        if (!is_string($name))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $name);
        else
            $this->Name = $name;
    }

    /**
     * Get the DateTime of creation of the node/system.
     * @return \DateTime
     */
    public function getCreation()
    {
        return $this->Creation;
    }

    /**
     * Set the creation time of the node/system.
     * @param $creation
     * @throws InvalidParameterException
     */
    public function setCreation($creation)
    {
        if (!is_string($creation))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $creation);// TODO: Date checking
        else
            $this->Creation = $creation;
    }

    /**
     * Get the host of the node/system.
     * @return string
     */
    public function getHost()
    {
        return $this->Host;
    }

    /**
     * Set the host of the node/system.
     * @param $host
     * @throws InvalidParameterException
     */
    public function setHost($host)
    {
        if (!is_string($host))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $host);
        else
            $this->Host = $host;
    }

    /**
     * Get the port of the node/system.
     * @return int
     */
    public function getPort()
    {
        return $this->Port;
    }

    /**
     * Set the port of the node/system.
     * @param $port
     * @throws InvalidParameterException
     */
    public function setPort($port)
    {
        if (!is_int($port) || (intval($port) > 65536 || intval($port) < 0))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integers between 0 and 65,536. Input was: ' . $port);
        else
            $this->Port = $port;
    }

    /**
     * Get the password of the node/system.
     * @return string
     */
    public function getPassword()
    {
        return $this->Password;
    }

    /**
     * Set the password of the node/system.
     * @param $password
     * @throws InvalidParameterException
     */
    public function setPassword($password)
    {
        if (!is_string($password))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $password);
        else
            $this->Password = $password;
    }

    /**
     * Get the home directory of the node/system.
     * @return string
     */
    public function getDirectory()
    {
        return $this->Directory;
    }

    /**
     * Set the directory at which the home directory resides - Soon to be depreciated.
     * @param $directory
     * @throws InvalidParameterException
     */
    public function setDirectory($directory)
    {
        if (!is_string($directory))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $directory);
        else
            $this->Directory = $directory;
    }

    /**
     * Get the active status of the node/system.
     * @return bool
     */
    public function getActive()
    {
        return $this->Active;
    }

    /**
     * Set the active status of the node/system.
     * @param $active
     * @throws InvalidParameterException
     */
    public function setActive($active)
    {
        if (!is_bool($active))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts booleans. Input was: ' . $active);
        else
            $this->Active = $active;
    }

    /**
     * Get the online status of the node/system.
     * @return bool
     */
    public function getOnline()
    {
        return $this->Online;
    }

    /**
     * Set the online status of the node/system - Soon to be depreciated.
     * @param $online
     * @throws InvalidParameterException
     */
    public function setOnline($online)
    {
        if (!is_bool($online))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts booleans. Input was: ' . $online);
        else
            $this->Online = $online;
    }

    /**
     * Get the OS on the node/system.
     * @return string
     */
    public function getOS()
    {
        return $this->OS;
    }

    /**
     * Set the OS of the node/system.
     * @param $OS
     * @throws InvalidParameterException
     */
    public function setOS($OS)
    {
        if (!is_string($OS))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $OS);
        else
            $this->OS = $OS;
    }
}
