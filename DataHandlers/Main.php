<?php
namespace DataHandlers;

use Data_Building\ApplicationSupportInfo;
use Data_Building\ClientInfo;
use Data_Building\ConfigInfo;
use Data_Building\MembersInfo;
use Data_Building\Nodes_StatusInfo;
use Data_Building\NodesInfo;
use Data_Building\Servers_StatusInfo;
use Data_Building\ServersInfo;
use Data_Building\TokensInfo;


date_default_timezone_set('UTC');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once(dirname(dirname(__FILE__)) . '/Data_Building/MembersInfo.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/ConfigInfo.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/ApplicationSupportInfo.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/TokensInfo.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/ClientInfo.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/ServersInfo.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Servers_StatusInfo.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/NodesInfo.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Nodes_StatusInfo.php');

class Main
{
    /**
     * Handles any operations regarding member management.
     * @var MembersInfo
     */
    public $memberInfo;
    /**
     * Handles any operations regarding configuration management.
     * @var ConfigInfo
     */
    public $configInfo;
    /**
     * Handles any operations regarding application support management.
     * @var ApplicationSupportInfo
     */
    public $applicationSupportInfo;
    /**
     * Handles any operations regarding token management.
     * @var TokensInfo
     */
    public $tokensInfo;
    /**
     * Manages the current client's information.
     * @var ClientInfo
     */
    public $clientInfo;
    /**
     * Handles any operations regarding server management.
     * @var ServersInfo
     */
    public $serversInfo;
    /**
     * Handles any operations regarding servers statuses.
     * @var Servers_StatusInfo
     */
    public $serversStatusInfo;
    /**
     * Handles any operations regarding node management.
     * @var NodesInfo
     */
    public $nodesInfo;
    /**
     * Handles any operations regarding node status management.
     * @var Nodes_StatusInfo
     */
    public $nodesStatusInfo;


    public function __construct()
    {
        $this->memberInfo = new MembersInfo();
        $this->configInfo = new ConfigInfo();
        $this->applicationSupportInfo = new ApplicationSupportInfo();
        $this->tokensInfo = new TokensInfo();
        $this->clientInfo = new ClientInfo();
        $this->serversInfo = new ServersInfo();
        $this->serversStatusInfo = new Servers_StatusInfo();
        $this->nodesInfo = new NodesInfo();
        $this->nodesStatusInfo = new Nodes_StatusInfo();
        $this->checkLogin();
    }

    public function checkLogin()
    {
        if (!$this->clientInfo->isLoggedIn()) {
            // We only allow the 'logging-in' to happen if NO-ONE is logged in.
            if (isset($_POST['adminEmail']) && isset($_POST['adminPassword'])) {
                $adminEmail = $_POST['adminEmail'];
                $adminPassword = $_POST['adminPassword'];
                try {
                    $entityData = $this->memberInfo->checkPasswordFromEmail($adminEmail, $adminPassword, $_SERVER['REMOTE_ADDR']);
                } catch (\Exception $e){

                }
                if (isset($entityData)) {
                    $session = $this->memberInfo->fetchNewSession($entityData->User_ID, $_SERVER['REMOTE_ADDR']);
                    setcookie("accountSession", $session);
                    $this->clientInfo->checkUserSession($session);
                }
            }
        }
    }

    public function getClientInfo()
    {
        return $this->clientInfo;
    }
}