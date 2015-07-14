<?php
namespace Data_Building;

use Data_Building\Data_Query\Data_Deleter;
use Data_Building\Data_Query\Data_Fetcher;
use Data_Building\Data_Query\Data_Poster;
use Data_Building\Entities\ApplicationSupportedEntity;

require_once(dirname(dirname(__FILE__)) . '/Data_Building/Entities/ApplicationSupportedEntity.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Fetcher.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Poster.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Deleter.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

class ApplicationSupportInfo
{
    private $Interface = "application_support";

    public function fetchApplicationsSupportedByID($id)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface . "/app/" . $id, array());
        $entity = new ApplicationSupportedEntity();
        print_r($entityData);
        if (isset($entityData['results'])) {
            $entity->exchangeArray($entityData['results']);
        }

        return $entity;
    }

    public function addApplicationSupport(ApplicationSupportedEntity $supportedServerEntity = null)
    {
        $dataPoster = new Data_Poster();
        $postParams = array();
        if ($supportedServerEntity instanceof ApplicationSupportedEntity)
            $postParams = $supportedServerEntity->getArrayCopy();
        $data = $dataPoster->postData($this->Interface, $postParams);
        return $data;
    }

    /**
     * Remove support for an application by ID
     * @param $appID
     * @return array|mixed
     */
    public function removeApplicationSupportByID($appID)
    {
        $dataPoster = new Data_Deleter();
        $data = $dataPoster->deleteData($this->Interface, array("App_ID" => $appID));
        return $data;
    }

    /**
     * Remove support for an application by Name
     * @param $appName
     * @return array|mixed
     */
    public function removeApplicationSupportByName($appName)
    {
        $dataPoster = new Data_Deleter();
        $data = $dataPoster->deleteData($this->Interface, array("Name" => $appName));
        return $data;
    }

    /**
     * Fetch all supported servers either by filtering for a specific operating system. If no parameters are provided, all servers will be fetched.
     * @param string $osRequested
     * @return array|mixed
     */
    public function getSupportedServers($osRequested = "ALL")
    {
        $osRequested = strtoupper($osRequested);
        if ($osRequested === "ALL")
            return $this->fetchApplicationsSupported();
        else
            return $this->fetchApplicationsSupportedByOS($osRequested);
    }

    public function fetchApplicationsSupported($dataFilter = array())
    {
        $dataFetcher = new Data_Fetcher();
        $data = $dataFetcher->fetchData($this->Interface, $dataFilter);
        $array = array();
        if (isset($data['results'])) {
            $data = $data['results'];
            for ($var = 0; $var < count($data); $var++) {
                $entity = new ApplicationSupportedEntity();
                $entity->exchangeArray($data[$var]);
                array_push($array, $entity);
            }
        } else {
            $array = $data;
        }

        return $array;
    }

    public function fetchApplicationsSupportedByOS($os)
    {
        $dataFetcher = new Data_Fetcher();
        $data = $dataFetcher->fetchData($this->Interface . "/os/" . $os, array());
        $array = array();
        if (isset($data['results'])) {
            $data = $data['results'];
            for ($var = 0; $var < count($data); $var++) {
                $entity = new ApplicationSupportedEntity();
                $entity->exchangeArray($data[$var]);
                array_push($array, $entity);
            }
        } else {
            $array = $data;
        }

        return $array;
    }

}