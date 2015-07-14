<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
use DataHandlers\Main;

require('../DataHandlers/Main.php');
$Main = new Main();

$originalCopy = '../scripts/node_setup.sh';
$newCopy = '../tmp/node_setup.sh';

$serverPath = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$serverPath = str_replace('admin/generateNodeScript.php','', $serverPath);
if (strpos($serverPath, "?tempKey"))
$serverPath = explode("?tempKey", $serverPath)[0];


if (!copy($originalCopy, $newCopy)) {
    echo "failed to copy $originalCopy...\n";
}
else {
    $tempSession = false;
    if (isset($_GET['tempKey'])) {
        $tempKey = $_GET['tempKey'];
        if ($Main->tokensInfo->validateToken("DL_SCRIPT", $tempKey)) {
            $tempSession = true;
        }
    }


    //TODO: Add permissions system - Link up this section with permissions - Also debug this area.
    if ($Main->clientInfo->isLoggedIn() || $tempSession == true) {

        $token = $Main->tokensInfo->fetchNewToken("NODE");
        $fhandle = fopen($newCopy, "r");
        $content = fread($fhandle, filesize($newCopy));
        $content = str_replace("SECRETKEY=\"\"", "SECRETKEY=\"" . $token->Generated . "\"", $content);
        $content = str_replace("WEBSITEURL=\"\"", "WEBSITEURL=\"" . $serverPath . "\"", $content);

        $fhandle = fopen($newCopy, "w");
        fwrite($fhandle, $content);
        fclose($fhandle);
        if (file_exists($newCopy)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($newCopy));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($newCopy));
            readfile($newCopy);
            unlink($newCopy);
            exit;
        }
    }
}
?>
