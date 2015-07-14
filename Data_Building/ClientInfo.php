<?php
namespace Data_Building;

use Data_Building\Entities\ClientEntity;
use Data_Building\Exceptions\InvalidParameterException;

require_once(dirname(dirname(__FILE__)) . '/Data_Building/Entities/ClientEntity.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/MembersInfo.php');


class ClientInfo
{
    private $clientEntity;
    private $loggedIn;
    private $memberInfo;

    public function __construct()
    {
        $this->memberInfo = new MembersInfo();
        $this->clientEntity = new ClientEntity();
        $this->loggedIn = false;
        $this->checkUserSession();
    }

    public function checkUserSession($session = null)
    {
        $loggedIn = $this->isLoggedIn();
        if (!$loggedIn) {
            if (isset($_COOKIE['accountSession']) || $session != null) {
                // User has some sort of session saved...
                if (isset($_COOKIE['accountSession']))
                    $hashedCookie = $_COOKIE['accountSession'];
                if ($session != null)
                    $hashedCookie = $session;
                try {
                    $checkSession = $this->memberInfo->checkSession($hashedCookie);
                } catch (\Exception $e){

                }
                if (isset($checkSession)) {
                    // User has a valid session - We must now build his user-info
                    $userInfo = $this->memberInfo->fetchUserByID($checkSession->User_ID);
                    $this->clientEntity->setUserID(intval($userInfo->User_ID));
                    $this->clientEntity->setUsername($userInfo->Username);
                    $this->clientEntity->setEmailAddress($userInfo->Email_Address);
                    $this->clientEntity->setPassword($userInfo->Password);
                    $this->clientEntity->setSessionKey($checkSession->Session_Key);
                    $this->loggedIn = true;
                }
            }
        }
    }

    public function isLoggedIn()
    {
        return $this->loggedIn;
    }

    public function getClient()
    {
        return $this->clientEntity;
    }

    public function setClient(ClientEntity $clientEntity)
    {
        if ($clientEntity instanceof ClientEntity)
            $this->clientEntity = $clientEntity;
        else
            throw new InvalidParameterException("The provided parameter is not a valid ClientEntity.");
    }
}