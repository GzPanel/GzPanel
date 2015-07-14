<?php
namespace Data_Building\Data_Query;

use Data_Building\Configuration;
use Data_Building\Exceptions\FailedDatabaseConnection;
use Data_Building\Exceptions\FailedPostException;
use xPaw\SourceQuery\Exception\TimeoutException;

require_once(dirname(dirname(__FILE__)) . '/Configuration.php');
require_once(dirname(dirname(__FILE__)) . '/Exceptions/FailedPostException.php');
require_once(dirname(dirname(__FILE__)) . '/Exceptions/FailedDatabaseConnection.php');

class Data_Poster
{

    public function postData($interfacePost, $postParams = array())
    {
        $configuration = new Configuration();
        $url = $configuration->getAPI() . $interfacePost;

        $key = array('key' => $configuration->getAPI_Key());
        $params = array_merge($key, $postParams);
        $postParams = http_build_query($params);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POST, sizeof($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = null;
        try {
            $data = curl_exec($ch);
        } catch (TimeoutException $e) {
            throw new FailedPostException("Failed to connect to the API @ '$configuration->getAPI()'");
        }

        curl_close($ch);

        $response = json_decode($data, true);
        $data = array("interface" => $interfacePost, "data" => $postParams);

        if (!is_array($response))
            throw new FailedPostException("Failed to fetch data in an array format. Interface in question is '$interfacePost'");
        if ($response['status'] == 418) {
            // Database not connected.
            throw new FailedDatabaseConnection($response['msg']);
        }
        $response = array_merge($response, $data);
        return $response;
    }
}