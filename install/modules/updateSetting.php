<?php
/**
 * User: Samer
 * Date: 2015-04-25
 * Time: 8:43 PM
 * Description: In this file, database connection will commence, and more functions will be listed here...
 */
use api\Entities\DbConnection;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
require_once('../../api/Entities/DbConnection.php');
require_once('../../Data_Building/MembersInfo.php');


$name = $_POST['name'];
$value = $_POST['value'];
$jsonDecoded = json_decode($value, true);
switch (strtolower($name)) {
    case "check_db":
        if (isset($jsonDecoded['Username']) && isset($jsonDecoded['Password']) && isset($jsonDecoded['Host']) && isset($jsonDecoded['Port']) && isset($jsonDecoded['Database_Name'])) {
            $dbConnection = new DbConnection($jsonDecoded['Username'], $jsonDecoded['Password'], $jsonDecoded['Database_Name'], $jsonDecoded['Host'], $jsonDecoded['Port']);
            if ($dbConnection->DbConnection != null)
                echo true;
            else
                echo 0;
        } else
            echo 0;
        break;
    case "set_dbinfo":
        if (isset($jsonDecoded['Username']) && isset($jsonDecoded['Password']) && isset($jsonDecoded['Host']) && isset($jsonDecoded['Port']) && isset($jsonDecoded['Database_Name'])) {
            $dbConnection = new DbConnection($jsonDecoded['Username'], $jsonDecoded['Password'], $jsonDecoded['Database_Name'], $jsonDecoded['Host'], $jsonDecoded['Port']);
            if ($dbConnection->DbConnection != null) {
                // Save DB info and build Db.

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Login_Attempts`(
                    `Attempt_ID` INT( 11 ) AUTO_INCREMENT,
                     `IP_Address` VARCHAR ( 100 ) NOT NULL,
                     `Attempt_Date` DATETIME NOT NULL,
                     PRIMARY KEY (`Attempt_ID`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Configuration`(
                    `ID` INT( 11 ) AUTO_INCREMENT,
                     `Name` VARCHAR( 100 ) NOT NULL,
                     `Value` VARCHAR( 2048 ) NOT NULL,
                     PRIMARY KEY (`ID`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Accounts`(
                    `User_ID` INT( 11 ) AUTO_INCREMENT,
                     `Username` VARCHAR( 100 ) NOT NULL,
                     `Email_Address` VARCHAR( 100 ) NOT NULL,
                     `Password` VARCHAR( 60 ) NOT NULL,
                     PRIMARY KEY (`User_ID`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Accounts_Data`(
                    `User_ID` INT( 11 ) AUTO_INCREMENT,
                     `Creation_Date` DATETIME NOT NULL,
                     `Last_Login_Date` DATETIME NOT NULL,
                     PRIMARY KEY (`User_ID`))", array());

                // This session table will be cleared regulary to avoid overload - It keeps records of all sessions.
                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Sessions_Data`(
                    `Session_ID` INT( 11 ) AUTO_INCREMENT,
                     `User_ID` INT ( 11 ) NOT NULL,
                     `Session_Start` DATETIME NOT NULL,
                     `Session_Key` VARCHAR( 60 ) NOT NULL,
                     `Session_IP` VARCHAR( 45 ) NOT NULL,
                     PRIMARY KEY (`Session_ID`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Support_Tickets`(
                    `Ticket_ID` INT( 11 ) AUTO_INCREMENT,
                     `Ticket_Author` INT ( 11 ) NOT NULL,
                     `Ticket_Creation` DATETIME NOT NULL,
                     `Ticket_Title` VARCHAR( 200 ) NULL,
                     `Ticket_Content` VARCHAR( 10000 ) NOT NULL,
                     `Ticket_Priority` VARCHAR( 1 ) NULL,
                     `Ticket_Parent` INT( 11 ) NULL,
                     PRIMARY KEY (`Ticket_ID`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Servers`(
                    `ID` INT( 11 ) AUTO_INCREMENT,
                     `Owner` INT ( 11 ) NOT NULL,
                     `Creation` DATETIME NOT NULL,
                     `Name` VARCHAR( 200 ) NOT NULL,
                     `Host` VARCHAR( 100 ) NOT NULL,
                     `App_ID` INT( 11 ) NOT NULL,
                     `Node` INT( 11 ) NOT NULL,
                     PRIMARY KEY (`ID`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Servers_Status`(
                    `Ping_ID` INT( 11 ) AUTO_INCREMENT,
                    `Server_ID` INT( 11 ) NOT NULL,
                     `Time_Pinged` DATETIME NOT NULL,
                     `Data` VARCHAR (1000) NULL,
                     PRIMARY KEY (`Ping_ID`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Nodes`(
                    `Node` INT( 11 ) AUTO_INCREMENT,
                     `Name` VARCHAR ( 100 ) NOT NULL,
                     `Creation` DATETIME NOT NULL,
                     `Host` VARCHAR( 200 ) NOT NULL,
                     `Port` VARCHAR( 5 ) NOT NULL,
                     `Password` VARCHAR( 100 ) NOT NULL,
                     `Directory` VARCHAR( 100 ) NOT NULL,
                     `OS` VARCHAR( 50 ) NOT NULL,
                     `Active` VARCHAR( 1 ) NOT NULL,
                     `Online` VARCHAR( 1 ) NOT NULL,
                     PRIMARY KEY (`Node`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Nodes_Status`(
                    `Ping_ID` INT( 11 ) AUTO_INCREMENT,
                    `Node` INT( 11 ) NOT NULL,
                     `Time_Pinged` DATETIME NOT NULL,
                     `Ping` VARCHAR (4) NOT NULL,
                     `Load_AVG` VARCHAR( 10 ) NOT NULL,
                     `HDD_Space` VARCHAR( 150 ) NOT NULL,
                     `RAM` VARCHAR( 100 ) NOT NULL,
                     PRIMARY KEY (`Ping_ID`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Special_Tokens`(
                    `Token_ID` INT( 11 ) AUTO_INCREMENT,
                     `Generated` VARCHAR ( 100 ) NOT NULL,
                     `Creation` DATETIME NOT NULL,
                     `Type` VARCHAR ( 20 ) NOT NULL,
                     `Linked` INT ( 11 ) DEFAULT NULL,
                     PRIMARY KEY (`Token_ID`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `Supported_Servers` (
                    `App_ID` INT( 11 ) AUTO_INCREMENT,
                    `Name` VARCHAR(128),
                    `Description` VARCHAR(512),
                    `Installation` VARCHAR(2048),
                    `Execution` VARCHAR(2048),
                    `OS` VARCHAR(50),
                    PRIMARY KEY (`App_ID`))", array());

                $dbConnection->query("CREATE TABLE IF NOT EXISTS `API_Keys` (
                    `API_ID` INT( 11 ) AUTO_INCREMENT,
                    `API_Key` VARCHAR(60),
                    `Access_Filter` VARCHAR(512),
                    `Creation` DATETIME NOT NULL,
                    `Capabilities` VARCHAR(128),
                    PRIMARY KEY (`API_ID`))", array());
                $apiKey = get_key();
                $array = array($apiKey, json_encode(array()), json_encode(array("Master")));
                $dbConnection->query("INSERT INTO API_Keys (API_Key, Access_Filter, Creation, Capabilities) VALUES (?,?,NOW(),?)", $array);
                $confData = array("username" => $jsonDecoded['Username'], "password" => $jsonDecoded['Password'], "name" => $jsonDecoded['Database_Name'], "host" => $jsonDecoded['Host'], "port" => $jsonDecoded['Port'], "api-key" => $apiKey);

                $fp = fopen('../../Configuration/internal_data.json', 'w');
                fwrite($fp, json_encode($confData));
                fclose($fp);
                $options = array(
                    'cost' => 11,
                );
                $password = password_hash("admin", PASSWORD_BCRYPT, $options);
                $memberValues = array("admin", "admin@example.com", $password);
                $dbConnection->query('INSERT INTO Accounts (Username, Email_Address, Password) VALUES (?,?,?)', $memberValues);
                $dbConnection->query('INSERT INTO Configuration (Name, Value) VALUES (?,?)', array("ring_data", '[{"data_name":"online_servers"},{"data_name":"offline_servers"},{"data_name":"total_members"},{"data_name":"open_tickets"},{"data_name":"total_servers"}]'));
            }
        }
        break;
    default:
        echo 0;
        break;
}

#http://stackoverflow.com/questions/637278/what-is-the-best-way-to-generate-a-random-key-within-php
#Generate a random key from /dev/random
function get_key($bit_length = 128)
{
    $fp = @fopen('/dev/random', 'rb');
    if ($fp !== FALSE) {
        $key = substr(base64_encode(@fread($fp, ($bit_length + 7) / 8)), 0, (($bit_length + 5) / 6) - 2);
        @fclose($fp);
        return $key;
    }
    return null;
}
