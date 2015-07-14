<?php
namespace Data_Building;
use Data_Building\Data_Query\Data_Deleter;
use Data_Building\Data_Query\Data_Fetcher;
use Data_Building\Data_Query\Data_Poster;
use Data_Building\Entities\NodeEntity;

require_once(dirname(dirname(__FILE__)).'/Data_Building/Entities/NodeEntity.php');
require_once(dirname(dirname(__FILE__)).'/Data_Building/Data_Query/Data_Fetcher.php');
require_once(dirname(dirname(__FILE__)).'/Data_Building/Data_Query/Data_Poster.php');
require_once(dirname(dirname(__FILE__)).'/Data_Building/Data_Query/Data_Deleter.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

class NodesInfo {
    public $Interface = "nodes";

    public function fetchNodeByID($ID)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface . "/node", array("Node" => $ID));
        $entity = new NodeEntity();

        if (isset($entityData['results'])) {
            $entity->exchangeArray($entityData['results']);
            return $entity;
        }
        return $entity;
    }

    public function fetchNodes($dataFilter = array())
    {
        $dataFetcher = new Data_Fetcher();
        $data = $dataFetcher->fetchData($this->Interface, $dataFilter);
        $array = array();
        if (isset($data['results'])) {
            $data = $data['results'];
            for ($var = 0; $var < count($data); $var++) {
                $entity = new NodeEntity();
                $entity->exchangeArray($data[$var]);
                array_push($array, $entity);
            }
        }

        return $array;
    }

//$dataParams = array($params['Node_Name'], $params['Node_Host'], $params['Node_Port'], $params['Node_Password'], $params['Node_Directory'], $params['Node_OS']);

    public function addNode($name, $host, $port, $password, $directory, $OS)
    {
        $nodeArray = array(
            "Name" => $name,
            "Host" => $host,
            "Port" => $port,
            "Password" => $password,
            "Directory" => $directory,
            "OS" => $OS
        );
        $dataPoster = new Data_Poster();
        $data = $dataPoster->postData($this->Interface, $nodeArray);
        return $data;
    }
    public function removeNode($data)
    {
        $dataPoster = new Data_Deleter();
        $data = $dataPoster->deleteData($this->Interface, $data);
        return $data;
    }

}