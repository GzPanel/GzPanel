<?php
namespace Data_Building;
use Data_Building\Data_Query\Data_Deleter;
use Data_Building\Data_Query\Data_Fetcher;
use Data_Building\Data_Query\Data_Poster;
use Data_Building\Entities\Nodes_StatusEntity;

require_once('Entities/Nodes_StatusEntity.php');
require_once('Data_Query/Data_Fetcher.php');
require_once('Data_Query/Data_Poster.php');
require_once('Data_Query/Data_Deleter.php');


class Nodes_StatusInfo {
    private $Interface = "nodes_status";

    public function fetchStatusByPingID($ID)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface . "/ping/" . $ID);
        $entity = new Nodes_StatusEntity();
        if ($entityData['status'] == 200) {
            $entity->exchangeArray($entityData['results']);
            return $entity;
        }
        return null;
    }

    public function fetchStatusByNodeID($ID)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface . "/node/" . $ID);
        $entity = new Nodes_StatusEntity();

        if ($entityData['status'] == 200) {
            $entity->exchangeArray($entityData['results']);
            return $entity;
        }

        return null;
    }


    public function fetchStatuses($dataFilter = array())
    {
        $dataFetcher = new Data_Fetcher();
        $data = $dataFetcher->fetchData($this->Interface, $dataFilter);
        $array = array();
        if (isset($data['results'])) {
            $data = $data['results'];
            for ($var = 0; $var < count($data); $var++) {
                $entity = new Nodes_StatusEntity();
                $entity->exchangeArray($data[$var]);
                array_push($array, $entity);
            }
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