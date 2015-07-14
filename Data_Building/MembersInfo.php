<?php
namespace Data_Building;

use Data_Building\Data_Query\Data_Deleter;
use Data_Building\Data_Query\Data_Fetcher;
use Data_Building\Data_Query\Data_Poster;
use Data_Building\Entities\MembersEntity;
use Data_Building\Entities\SessionEntity;

require_once(dirname(dirname(__FILE__)) . '/Data_Building/Entities/MembersEntity.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Entities/SessionEntity.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Fetcher.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Poster.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Deleter.php');


class MembersInfo
{
    public $Interface = "members";

    public function fetchUserByID($ID)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface . "/user/" . $ID, array());
        if (isset($entityData['results'])) {
            $entity = new MembersEntity();
            $entity->exchangeArray($entityData['results']);
        } else
            $entity = $entityData;

        return $entity;
    }

    public function fetchUserByEmail($Email)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface . "/email/" . $Email, array());
        if (isset($entityData['results'])) {
            $entity = new MembersEntity();
            $entity->exchangeArray($entityData['results']);
        } else
            $entity = $entityData;

        return $entity;
    }


    public function fetchMember($data)
    {
        $dataFetcher = new Data_Fetcher();
        $entityData = $dataFetcher->fetchData($this->Interface, $data);
        if (isset($entityData['results'])) {
            $entity = new MembersEntity();
            $entity->exchangeArray($entityData['results']);
        } else
            $entity = $entityData;

        return $entity;
    }

    public function fetchMembers($dataFilter = array())
    {
        $dataFetcher = new Data_Fetcher();
        $data = $dataFetcher->fetchData($this->Interface, $dataFilter);
        $array = array();
        if (isset($data['results'])) {
            $data = $data['results'];
            for ($var = 0; $var < count($data); $var++) {
                $entity = new MembersEntity();
                $entity->exchangeArray($data[$var]);
                array_push($array, $entity);
            }
        } else {
            $array = $data;
        }

        return $array;
    }

    public function checkPasswordFromUsername($userAuth, $userPass, $userIP)
    {
        $dataPoster = new Data_Poster();
        $entityData = $dataPoster->postData($this->Interface . "/login", array("Username" => $userAuth, "Password" => $userPass, "IP_Address" => $userIP));
        $entity = new MembersEntity();
        if (isset($entityData['results'])) {
            $entity->exchangeArray($entityData['results']);
        } else
            $entity = $entityData;

        return $entity;
    }

    public function checkPasswordFromEmail($userAuth, $userPass, $userIP)
    {
        $dataPoster = new Data_Poster();
        $entityData = $dataPoster->postData($this->Interface . "/login", array("Email_Address" => $userAuth, "Password" => $userPass, "IP_Address" => $userIP));
        $entity = new MembersEntity();
        if (isset($entityData['results'])) {
            $entity->exchangeArray($entityData['results']);
        }

        return $entity;
    }

    public function fetchNewSession($userID, $userIP)
    {
        $dataPoster = new Data_Poster();
        $data = $dataPoster->postData($this->Interface . "/sessions", array("User_ID" => $userID, "Session_IP" => $userIP));
        if ($data['status'] == 201) {
            $sessionEntity = new SessionEntity();
            $sessionEntity->exchangeArray($data['results']);
            return $sessionEntity->Session_Key;
        }
        return null;
    }

    public function checkSession($sessionKey)
    {
        $dataFetcher = new Data_Fetcher();
        $data = $dataFetcher->fetchData($this->Interface . "/sessions", array("Session_Key" => $sessionKey));
        if ($data['status'] == 201) {
            $sessionEntity = new SessionEntity();
            $sessionEntity->exchangeArray($data['results']);
            return $sessionEntity;
        }
        return null;
    }

    public function addMember($Username, $Email_Address, $Password)
    {
        $dataPoster = new Data_Poster();
        $data = $dataPoster->postData($this->Interface, array("Username" => $Username, "Email_Address" => $Email_Address, "Password" => $Password));
        if ($data['status'] == 200) {
            return true;
        } else
            return $data['msg'];
    }

    public function removeMember($data)
    {
        $dataPoster = new Data_Deleter();
        $data = $dataPoster->deleteData($this->Interface, $data);
        if ($data['status'] == 200) {
            return true;
        } else
            return $data['msg'];
    }

}