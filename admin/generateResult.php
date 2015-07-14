<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-06-09
 * Time: 9:47 PM
 */
use DataHandlers\Main;

require '../DataHandlers/Main.php';
$Main = new Main();

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (isset($_GET['keyType'])) {
    $keyTypeToGenerate = $_GET['keyType'];

    if ($Main->getClientInfo() != null) {
        if ($keyTypeToGenerate == "dlScripts") {
            echo $Main->tokensInfo->fetchNewToken("DL_SCRIPT")->Generated;
        }
    }
}
else if (isset($_GET['valueSet'])){
    $valueSet = $_GET['valueSet'];
    if ($valueSet == "dashboardData"){
        $dataList = json_decode($Main->configInfo->fetchConfigByName("ring_data")->Value, true);
        $dataSet = array();
        for ($count = 0; $count < count($dataList); $count++) {
            $dataEntry = array(
                "Name" => $dataList[$count]['data_name'],
                "Value" => rand(0, 4),
            );
            array_push($dataSet, $dataEntry);
        }
        echo json_encode($dataSet);
    }
}