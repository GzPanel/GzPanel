<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-07-05
 * Time: 6:20 PM
 */

namespace Data_Building;


class Configuration
{
    private $api;
    private $api_key;

    // TODO: Work with more than just the root folder... Fetch info from other scripts.
    public function __construct()
    {
        if (file_exists("../Configuration/internal_data.json")) {
            $string = file_get_contents("../Configuration/internal_data.json");
            $jsonConf = json_decode($string, true);
            $this->api_key = $jsonConf['api-key'];
        } else
            $this->api_key = "invalidKey";

        $this->api = "http://localhost/api/v1/";
    }

    public function getAPI()
    {
        return $this->api;
    }

    public function getAPI_Key()
    {
        return $this->api_key;
    }
}