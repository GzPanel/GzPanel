<?php
namespace Data_Building\Data_Query;
use Data_Building\Configuration;
use Data_Building\Exceptions\FailedDatabaseConnection;
use Data_Building\Exceptions\FailedFetchException;
use xPaw\SourceQuery\Exception\TimeoutException;

require_once(dirname(dirname(__FILE__)).'/Configuration.php');
require_once(dirname(dirname(__FILE__)) . '/Exceptions/FailedFetchException.php');
class Data_Fetcher
{
    public function fetchData($interfaceFetch, $fetchParams = array())
    {
        $configuration = new Configuration();
        $url = $configuration->getAPI() . $interfaceFetch;

        $key = array('key' => $configuration->getAPI_Key());
        $params = array_merge($key, $fetchParams);
        $params = http_build_query($params);
        $url .= "?".$params;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING,  '');
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = null;
        try {
            $data = curl_exec($ch);
        } catch (TimeoutException $e) {
            throw new FailedFetchException("Failed to connect to the API @ '$configuration->getAPI()'");
        }

        curl_close($ch);

        $response = json_decode($data, true);
        $data = array("interface" => $interfaceFetch, "data" => $fetchParams);

        if (!is_array($response))
            throw new FailedFetchException("Failed to fetch data in an array format. Interface in question is '$interfaceFetch'");
        if ($response['status'] == 418) {
            // Database not connected.
            throw new FailedDatabaseConnection($response['msg']);
        }
        $response = array_merge($response, $data);
        return $response;
    }
}