<?php
namespace Data_Building;

use Data_Building\Data_Query\Data_Deleter;
use Data_Building\Data_Query\Data_Fetcher;
use Data_Building\Data_Query\Data_Poster;
use Data_Building\Entities\ConfigurationEntity;

require_once(dirname(dirname(__FILE__)) . '/Data_Building/Entities/ConfigurationEntity.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Fetcher.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Poster.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Deleter.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

class ConfigInfo
{
    public $Interface = "configuration";

    public function fetchConfigByName($Name)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface . "/config/" . $Name, array());
        $entity = new ConfigurationEntity();
        if (isset($entityData['results'])) {
            $entity->exchangeArray($entityData['results']);
        }

        return $entity;
    }


    public function fetchConfigs($dataFilter = array())
    {
        $dataFetcher = new Data_Fetcher();
        $data = $dataFetcher->fetchData($this->Interface, $dataFilter);
        $array = array();
        if (isset($data['results'])) {
            $data = $data['results'];
            for ($var = 0; $var < count($data); $var++) {
                $entity = new ConfigurationEntity();
                $entity->exchangeArray($data[$var]);
                array_push($array, $entity);
            }
        } else {
            $array = $data;
        }

        return $array;
    }

    public function addConfig($name, $value)
    {
        $dataPoster = new Data_Poster();
        $data = $dataPoster->postData($this->Interface, array("Name" => $name, "Value" => $value));
        return $data;
    }

    public function removeConfig($name)
    {
        $dataPoster = new Data_Deleter();
        $data = $dataPoster->deleteData($this->Interface, array("Name" => $name));
        return $data;
    }

}