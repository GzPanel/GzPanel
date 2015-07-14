<?php
namespace Data_Building\Data_Query;
use Data_Building\Configuration;
use Data_Building\Exceptions\FailedDatabaseConnection;
use Data_Building\Exceptions\FailedDeleteException;
use xPaw\SourceQuery\Exception\TimeoutException;

require_once(dirname(dirname(__FILE__)).'/Configuration.php');
require_once(dirname(dirname(__FILE__)) . '/Exceptions/FailedDeleteException.php');
require_once(dirname(dirname(__FILE__)) . '/Exceptions/FailedDatabaseConnection.php');

class Data_Deleter
{

    public function deleteData($interfaceDelete, $deleteParams = array())
    {
        $configuration = new Configuration();
        $url = $configuration->getAPI() . $interfaceDelete;

        $key = array('key' => $configuration->getAPI_Key());
        $params = array_merge($key, $deleteParams);
        $params = http_build_query($params);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params))
        );
        $data = null;
        try {
            $data = curl_exec($ch);
        } catch (TimeoutException $e) {
            throw new FailedDeleteException("Failed to connect to the API @ '$configuration->getAPI()'");
        }
        curl_close($ch);
        $response = json_decode($data, true);
        $data = array("interface" => $interfaceDelete, "data" => $deleteParams);
        if (!is_array($response))
            throw new FailedDeleteException("Failed to fetch data in an array format. Interface in question is '$interfaceDelete'");
        if ($response['status'] == 418) {
            // Database not connected.
            throw new FailedDatabaseConnection($response['msg']);
        }
        $response = array_merge($response, $data);
        return $response;
    }
}