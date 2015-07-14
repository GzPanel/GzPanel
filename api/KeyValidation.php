<?php
namespace GzPanel;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use ZF\ApiProblem\ApiProblem;

class KeyValidation
{
    protected $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function validateKey($params = null, $capabilities = null)
    {
        // An array of capabilities
        $requestIP = $_SERVER['REMOTE_ADDR'];

        if (isset($params['key'])) {
            $apiKey = $params['key'];

            $apiExists = $this->dbConnection->query('SELECT * FROM API_Keys WHERE API_Key = ? LIMIT 1', $apiKey);

            /*
                API exists more than once in the database must mean trouble.
            */
            if (count($apiExists) == 1) {
                $keyCapabilities = $apiExists[0]['Capabilities'];
                $keyAccess = $apiExists[0]['Access_Filter'];


                if (!is_null($keyAccess)) {
                    $keyAccessFilter = json_decode($keyAccess);
                    /*
                        Check security of the key before processing the request.
                    */
                    foreach ($keyAccessFilter as $filter) {
                        if ($filter['Type'] == "IP")
                            if (!in_array($requestIP, json_decode($filter['ValidIPList'])))
                                return false;
                    }
                }

                if (!is_null($keyCapabilities)) {
                    $allowedCapabilities = json_decode($keyCapabilities);
                    /*
                        Check capabilities of the key before processing the request.
                    */
                    if (in_array('Master', $allowedCapabilities))
                        return true;
                    if ($capabilities != null) {
                        foreach ($capabilities as $capability) {
                            if (!in_array($capability, $allowedCapabilities))
                                return false;
                        }
                        return true;
                    }
                }
            }
        } else {
            // Secret key used.... We must check if it is a valid token, and make sure it is limited in use.
            $dataSet = $this->dbConnection->query('SELECT * FROM Special_Tokens WHERE Generated = ? AND Type = ?', $params['secret'], "NODE");
            if (!empty($dataSet)) {
                if ($dataSet[0]['Linked'] == null) {
                    // Server was not linked yet....
                    // How reliable this method is... I am uncertain, however it should protect against simple attacks.
                    $serversToLink = $this->dbConnection->query('SELECT * FROM Nodes WHERE Host = ?', $_SERVER['REMOTE_ADDR']);
                    if (!empty($serversToLink))
                        $this->dbConnection->query('UPDATE Special_Tokens SET Linked = ? WHERE Generated = ? AND Type = ?', $serversToLink[0]['Node'], $params['secret'], "NODE");
                    return true;
                } else {
                    // Server has been linked to filter all requests and make sure they include Node = 'Linked Val' otherwise deny them.
                    if (isset($params['Node']))
                        if ($params['Node'] == $dataSet[0]['Linked'])
                            return true;
                }
            }
        }
        return false;
    }
}