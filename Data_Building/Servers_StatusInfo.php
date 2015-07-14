<?php
namespace Data_Building;
use Data_Building\Data_Query\Data_Deleter;
use Data_Building\Data_Query\Data_Fetcher;
use Data_Building\Data_Query\Data_Poster;
use Data_Building\Entities\Servers_StatusEntity;

require_once('Entities/Servers_StatusEntity.php');
require_once('Data_Query/Data_Fetcher.php');
require_once('Data_Query/Data_Poster.php');
require_once('Data_Query/Data_Deleter.php');


class Servers_StatusInfo {
    public $Interface = "servers_status";

    public function fetchStatusByID($ID)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface . "/ping/" . $ID, array());
        $entity = new Servers_StatusEntity();
        if (isset($entityData['results'])) {
            $entity->exchangeArray($entityData);
        }

        return $entity;
    }

    public function fetchStatusByServerID($ID)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface . "/server/" . $ID, array());
        $entity = new Servers_StatusEntity();
        if (isset($entityData['results'])) {
            $entity->exchangeArray($entityData);
        }

        return $entity;
    }

    public function fetchStatus($data)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface, $data);
        $entity = new Servers_StatusEntity();
        if (isset($entityData['results'])) {
            $entity->exchangeArray($entityData);
        }

        return $entity;
    }

    public function fetchStatuses($dataFilter = array())
    {
        $dataFetcher = new Data_Fetcher();
        $data = $dataFetcher->fetchData($this->Interface, $dataFilter);
        $array = array();
        if (isset($data['results'])) {
            $data = $data['results'];
            for ($var = 0; $var < count($data); $var++) {
                $entity = new Servers_StatusEntity();
                $entity->exchangeArray($data[$var]);
                array_push($array, $entity);
            }
        }
        else {
            $array = $data;
        }

        return $array;
    }

    public function addStatus($data)
    {
        $dataPoster = new Data_Poster();
        $data = $dataPoster->postData($this->Interface, $data);
        return $data;
    }

    public function removeStatus($data)
    {
        $dataPoster = new Data_Deleter();
        $data = $dataPoster->deleteData($this->Interface, $data);
        return $data;
    }

}