<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-07-06
 * Time: 12:59 PM
 */

namespace api\Entities;
use PDO;
use PDOException;


class DbConnection {
    public $DbConnection;
    public $Username;
    public $Password;
    public $Host;
    public $Port;
    public $Database_Name;
    public $errorException;

    public function __construct($Username, $Password, $Database_Name, $Host = "localhost", $Port = 3306)
    {
        $this->Username			= $Username;
        $this->Password			= $Password;
        $this->Database_Name = $Database_Name;
        $this->Host	            = $Host;
        $this->Port			    = $Port;
        $limit = 10;
        $counter = 0;
        while (true) {
            try {
                $this->DbConnection = new PDO('mysql:host=' . $this->Host . ';dbname=' . $this->Database_Name, $this->Username, $this->Password);
                $this->DbConnection->exec( "SET CHARACTER SET utf8" );
                $this->DbConnection->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
                $this->DbConnection->setAttribute( PDO::ATTR_PERSISTENT, true );
                $this->DbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                break;
            } catch (PDOException $e) {
                $DbConnection = null;
                $counter++;
                if ($counter == $limit) {
                    $this->errorException = $e;
                    break;
                }
            }
        }
    }

    public function getArrayCopy()
    {
        return array(
            'Username' => $this->Username,
            'Password' => $this->Password,
            'Database_Name' => $this->Database_Name,
            'Host' => $this->Host,
            'Port' => $this->Port,
        );
    }

    public function query($queryString){
        try {
            $stmt = $this->DbConnection->prepare($queryString);

            $arg_list = func_get_args();


            for ($i = 1; $i < func_num_args(); $i++) {
                if (is_array($arg_list[1])) {

                    $array = (array)$arg_list[1];

                    for ($arrayEntry = 0; $arrayEntry < count($array); $arrayEntry++) {
                        $stmt->bindValue($arrayEntry + 1, $array[$arrayEntry]);
                    }

                } else
                    $stmt->bindValue($i, $arg_list[$i]);
            }

            $stmt->execute();
            $result = null;
            if (strpos(strtolower($queryString), "select ") !== false) {
                $result = $stmt->fetchAll();
            }

            return $result;
        } catch (PDOException $e) {
            echo("ERROR: " . $e->getMessage());
        }
        return null;
    }


    public function exchangeArray(array $array)
    {
        $this->Username			= $array['Username'];
        $this->Password			= $array['Password'];
        $this->Host	            = $array['Host'];
        $this->Port			    = $array['Port'];
        $this->Database_Name = $array['Database_Name'];
    }
}