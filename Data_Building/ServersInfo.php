<?php
namespace Data_Building;

use Data_Building\Data_Query\Data_Deleter;
use Data_Building\Data_Query\Data_Fetcher;
use Data_Building\Data_Query\Data_Poster;
use Data_Building\Entities\ServersEntity;

require_once(dirname(dirname(__FILE__)) . '/Data_Building/Entities/ServersEntity.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Fetcher.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Poster.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Deleter.php');


class ServersInfo
{
    public $Interface = "servers";

    public function fetchServerByID($ID)
    {
        return $this->fetchServer(array("ID" => $ID));
    }

    public function fetchServer($data)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface, $data);
        $entity = new ServersEntity();

        if (isset($entityData['results'])) {
            $entity->exchangeArray($data);
        }

        return $entity;
    }

    public function fetchServerByOwner($ID, $page = 1)
    {
        return $this->fetchServers(array("Owner" => $ID, "page" => $page));
    }

    public function fetchServers($dataFilter = array())
    {
        $dataFetcher = new Data_Fetcher();
        $data = $dataFetcher->fetchData($this->Interface, $dataFilter);
        $array = array();
        if (isset($data['results'])) {
            $data = $data['results'];
            for ($var = 0; $var < count($data); $var++) {
                $entity = new ServersEntity();
                $entity->exchangeArray($data[$var]);
                array_push($array, $entity);
            }
        } else {
            $array = $data;
        }

        return $array;
    }

    public function fetchServerOnNode($ID, $page = 1)
    {
        return $this->fetchServers(array("Node" => $ID, "page" => $page));
    }

    public function fetchAllServers($page = 1)
    {
        return $this->fetchServers(array("page" => $page));
    }

    //$dataParams = array($params['Owner'], $params['Name'], $params['Host'], $params['App_ID'], $params['Node']);

    public function addServer($name, $host, $node, $owner, $app_id)
    {
        $nodeArray = array(
            "Name" => $name,
            "Host" => $host,
            "Owner" => $owner,
            "App_ID" => $app_id,
            "Node" => $node
        );
        $dataPoster = new Data_Poster();
        $data = $dataPoster->postData($this->Interface, $nodeArray);
        print_r($data);
        $entity = new ServersEntity();

        if (isset($data['results'])) {
            $entity->exchangeArray($data['results']);
        }
        return $entity;
    }

    public function removeServerByID($id)
    {
        $nodeArray = array(
            "ID" => $id
        );
        $dataPoster = new Data_Deleter();
        $data = $dataPoster->deleteData($this->Interface, $nodeArray);
        return $data;
    }

    public function removeServerByOwner($owner)
    {
        $nodeArray = array(
            "Owner" => $owner
        );
        $dataPoster = new Data_Deleter();
        $data = $dataPoster->deleteData($this->Interface, $nodeArray);
        return $data;
    }

    public function removeServerByNode($id)
    {
        $nodeArray = array(
            "Node" => $id
        );
        $dataPoster = new Data_Deleter();
        $data = $dataPoster->deleteData($this->Interface, $nodeArray);
        return $data;
    }
}