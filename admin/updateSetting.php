<?php
/**
 * User: Samer
 * Date: 2015-04-25
 * Time: 8:43 PM
 * Description: In this file, database connection will commence, and more functions will be listed here...
 */
use Data_Building\Entities\ApplicationSupportedEntity;
use DataHandlers\Main;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
require('../DataHandlers/Main.php');
require_once('../Data_Building/Entities/ApplicationSupportedEntity.php');
require_once('../Data_Building/Entities/ServersEntity.php');
set_include_path(get_include_path() . PATH_SEPARATOR . '../libs/phpseclib');
include('../libs/phpseclib/Net/SSH2.php');

$Main = new Main();

$name = $_POST['name'];
$value = $_POST['value'];
$jsonDecoded = json_decode($value, true);
switch (strtolower($name)) {
    case "add_application_support":
        if (isset($jsonDecoded['Name']) && isset($jsonDecoded['Install_Commands']) && isset($jsonDecoded['Execute_Commands']) && isset($jsonDecoded['OS'])) {
            $entryData = array("Name" => $jsonDecoded['Name'], "Description" => $jsonDecoded['Description'], "Installation" => json_encode($jsonDecoded['Install_Commands']), "Execution" => json_encode($jsonDecoded['Execute_Commands']), "OS" => json_encode($jsonDecoded['OS']));
            $applicationEntity = new ApplicationSupportedEntity();
            $applicationEntity->exchangeArray($entryData);
            $Main->applicationSupportInfo->addApplicationSupport($applicationEntity);
        }

        break;
    case "deploy_server":
        if (isset($jsonDecoded['Name']) && isset($jsonDecoded['Node']) && isset($jsonDecoded['Application'])) {
            $chosenNode = $Main->nodesInfo->fetchNodeByID($jsonDecoded['Node']);
            $server = $Main->serversInfo->addServer($jsonDecoded['Name'], $chosenNode->getHost(), $chosenNode->getID(), $Main->clientInfo->getClient()->getUserID(), $jsonDecoded['Application']);
            define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
            $applicationEntity = $Main->applicationSupportInfo->fetchApplicationsSupportedByID($jsonDecoded['Application']);
            $ssh = new Net_SSH2($chosenNode->getHost());
            if (!$ssh->login('PanelAgent', $chosenNode->getPassword())) {
                exit('Login Failed');
            }

            /*
             * We must create all folders in-case they were not created in Templates and Servers - then copy templates folder into servers
             *
             * I was going to use ->write however it is buggy and requires the use of ->read which really is annoying. Exec will run better and not wait for response.
             */
            $ssh->exec("[ ! -d ~/templates ] && mkdir ~/templates");
            $ssh->exec("[ ! -d ~/servers ] && mkdir ~/servers");
            // If there is already a template, then simply just copy it.
            $ssh->exec("[ -d ~/templates/".$applicationEntity->getID()." ] && cp -r ~/templates/".$applicationEntity->getID()."/. ~/servers/".$server->getID());
            // If there is no template, then we will build it and copy it.
            $commands = "[ ! -d ~/templates/".$applicationEntity->getID()." ] && mkdir ~/templates/" . $applicationEntity->getID()." && cd ~/templates/" . $applicationEntity->getID() . " && ";
            foreach ($applicationEntity->getInstallation() as $installCommand) {
                $commands .= $installCommand . " && ";
            }
            $commands .= "mkdir ~/servers/".$server->getID()." && cp -r ~/templates/".$applicationEntity->getID()."/. ~/servers/".$server->getID();
            //$commands = substr($commands, 0, strlen($commands) - 4);
            $ssh->exec($commands);
        }
        break;
    default:
        $Main->configInfo->addConfig($name, $value);
        break;
}
