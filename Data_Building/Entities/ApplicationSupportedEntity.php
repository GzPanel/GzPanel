<?php
namespace Data_Building\Entities;

use Data_Building\Exceptions\InvalidParameterException;

class ApplicationSupportedEntity
{
    /**
     * Application ID as found in the database.
     * @var int
     */
    private $App_ID;
    /**
     * The name of the application.
     * @var string
     */
    private $Name;
    /**
     * A short description of the application.
     * @var string
     */
    private $Description;
    /**
     * A set of instructions followed when installing this application.
     * @var array
     */
    private $Installation;//TODO: Add support for linux and windows machines
    /**
     * A set of instructions followed to begin the application.
     * @var array
     */
    private $Execution;//TODO: Add support for linux and windows machines.
    /**
     * A list of operating systems which are supported for this application.
     * @var string
     */
    private $OS;
    /**
     * An array of Operating Systems supported and their execution, installation and other steps.
     * @var array
     */
    private $Deployment;//TODO: Future feature


    /**
     * In the event of Installation and Execution scripts are not configured, we will
     * assume that the user did not set-up any instructions.
     */
    public function __construct()
    {
        $this->Installation = array();
        $this->Execution = array();
    }

    /**
     * Returns an array representation of the properties of this application.
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'App_ID' => $this->App_ID,
            'Name' => $this->Name,
            'Description' => $this->Description,
            'Installation' => $this->Installation,
            'Execution' => $this->Execution,
            'OS' => $this->OS,
        );
    }

    /**
     * Sets the properties of this application via Array with the keys being string
     * representation of the variable. Hopefully will be depreciated, as it is unsafe.
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        if (isset($array['App_ID']))
            $this->App_ID = $array['App_ID'];
        if (isset($array['Name']))
            $this->Name = $array['Name'];
        if (isset($array['Description']))
            $this->Description = $array['Description'];
        if (isset($array['Installation']))
            $this->Installation = json_decode($array['Installation'], true);
        if (isset($array['Execution']))
            $this->Execution = json_decode($array['Execution'], true);
        if (isset($array['OS']))
            $this->OS = json_decode($array['OS'], true);
    }

    /**
     * Return the app ID
     * @return string
     */
    public function getID()
    {
        return $this->App_ID;
    }

    public function setID($id){
        if (!is_int($id))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts integer. Input was: ' . $id);
        else
            $this->App_ID = $id;
    }

    /**
     * Return the installation instructions in array format.
     * @return array
     */
    public function getInstallation()
    {
        return $this->Installation;
    }

    /**
     * Sets the installation instructions in Array format.
     * @param array $array
     */
    public function setInstallation(array $array)
    {
        $this->Installation = $array;
    }

    /**
     * Return the execution instructions in array format.
     * @return array
     */
    public function getExecution()
    {
        return $this->Execution;
    }

    /**
     * Sets the exection instructions in Array format.
     * @param array $array
     */
    public function setExecution(array $array)
    {
        $this->Execution = $array;
    }

    /**
     * Returns the name of the application.
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Sets the name of the application.
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
     * Returns the description of the application.
     * @return string
     */
    public function getDescription()
    {
        return $this->Description;
    }

    /**
     * Sets the description of the application.
     * @param $description
     * @throws InvalidParameterException
     */
    public function setDescription($description)
    {
        if (!is_string($description)) {
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $description);
        } else
            $this->Description = $description;
    }

    /**
     * Returns the array of OS's supported.
     * @return string
     */
    public function getOS()
    {
        return $this->OS;
    }

    /**
     * Add a new OS to be marked as 'supported'.
     * @param $OS
     * @throws InvalidParameterException
     */
    public function addOS($OS)
    {
        if (!is_string($OS))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $OS);
        else
            array_push($this->OS, $OS);
    }

    /**
     * Remove an OS from being marked as 'supported'.
     * @param $OS
     * @throws InvalidParameterException
     */
    public function removeOS($OS)
    {
        if (!is_string($OS))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $OS);
        else {
            unset($this->OS[$OS]);// Cleared the entry... We must now re-index the array.
            $this->OS = array_values($this->OS);
        }
    }

    /**
     * Clear the list of OS's supported.
     */
    public function clearOS()
    {
        $this->OS = array();
    }


    /*
     * Below is some code I have not yet implemented on the web-panel...
     * On implementation this file will need some reworking.
     */

    /**
     * Adds a new execution step for OS - Revamped edition
     * @param $OS
     * @param $step
     * @throws InvalidParameterException
     */
    public function addExecutionStep($OS, $step)
    {
        if (!is_string($OS) || !is_string($step))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $OS);
        else {
            //addDeploymentStep($OS, $step);
        }
    }

    /**
     * Removes an execution step for OS - Revamped edition
     * @param $OS
     * @param $step
     * @throws InvalidParameterException
     */
    public function removeExecutionStep($OS, $step)
    {
        if (!is_string($OS) || !is_string($step))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $OS);
        else {
            //removeDeploymentStep($OS, $step);
        }
    }

    /**
     * Clears execution steps for OS.
     * @param $OS
     * @throws InvalidParameterException
     */
    public function clearExecutionSteps($OS)
    {
        if (!is_string($OS))
            throw new InvalidParameterException(__FUNCTION__ . ' function only accepts strings. Input was: ' . $OS);
        else {
            //clearDeploymentSteps($OS);
        }
    }
}
