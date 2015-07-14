<?php
namespace Data_Building;

use Data_Building\Data_Query\Data_Deleter;
use Data_Building\Data_Query\Data_Fetcher;
use Data_Building\Data_Query\Data_Poster;
use Data_Building\Entities\TokensEntity;

require_once('Entities/TokensEntity.php');
require_once('Data_Query/Data_Fetcher.php');
require_once('Data_Query/Data_Poster.php');
require_once('Data_Query/Data_Deleter.php');


class TokensInfo
{
    public $Interface = "tokens";

    public function validateToken($type, $token)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface . "/type/" . $type . "/token/" . $token, array());
        $entity = new TokensEntity();
        if ($entityData['status'] == 200) {
            return true;
        }
        return false;
    }


    public function fetchNewToken($tokenType)
    {
        $dataPoster = new Data_Poster();
        $entityData = $dataPoster->postData($this->Interface, array("Type" => $tokenType));
        $entity = new TokensEntity();
        if ($entityData['status'] == 201) {
            $entity->exchangeArray($entityData['results']);
        }

        return $entity;
    }

    public function removeToken($data)
    {
        $dataPoster = new Data_Deleter();
        $data = $dataPoster->deleteData($this->Interface, $data);
        return $data;
    }

}