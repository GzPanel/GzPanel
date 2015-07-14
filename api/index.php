<?php
require 'Slim/Slim.php';
require 'Slim/Middleware.php';
require 'Entities/DbConnection.php';
require 'Entities/NodeEntity.php';
require 'Entities/TokensEntity.php';
require 'Entities/Nodes_StatusEntity.php';
require 'Entities/ServersEntity.php';
require 'Entities/Servers_StatusEntity.php';
require 'Entities/Supported_ServersEntity.php';
require 'Entities/MembersPrivateEntity.php';
require 'Entities/MembersEntity.php';
require 'Entities/Login_AttemptsEntity.php';
require 'Entities/SessionEntity.php';
require 'Entities/ConfigurationEntity.php';
require 'KeyValidation.php';
\Slim\Slim::registerAutoloader();

use api\Entities\ConfigurationEntity;
use api\Entities\DbConnection;
use api\Entities\Login_AttemptsEntity;
use api\Entities\MembersEntity;
use api\Entities\MembersPrivateEntity;
use api\Entities\NodeEntity;
use api\Entities\Nodes_StatusEntity;
use api\Entities\Servers_StatusEntity;
use api\Entities\ServersEntity;
use api\Entities\SessionEntity;
use api\Entities\Supported_ServersEntity;
use api\Entities\TokensEntity;
use GzPanel\KeyValidation;

date_default_timezone_set('UTC');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
$app = new \Slim\Slim();
$app->view(new JsonApiView());
$app->add(new JsonApiMiddleware());
// Set-up the database info, if it fails then do NOT set-up the api.
$dbConnection = null;
if (!file_exists("../Configuration/internal_data.json"))
    $failMessage = 'Failed to connect to database. Verify your database information in the configuration.';
else {
    $string = file_get_contents("../Configuration/internal_data.json");
    $jsonConf = json_decode($string, true);

    $database = $jsonConf;//Needs fixes.
    if (!isset($database['name']) || !isset($database['host']) || !isset($database['port']) || !isset($database['username']) || !isset($database['password']))
        $failMessage = 'Failed to connect to database. Verify your database information in the configuration.';

    $dbConnection = new DbConnection($database['username'], $database['password'], $database['name'], $database['host'], $database['port']);
    if ($dbConnection->DbConnection == null)
        $failMessage = 'Failed to connect to database. Verify your database information in the configuration.';

    $rows = $dbConnection->query("SELECT COUNT(*) AS totalTables FROM information_schema.tables WHERE table_schema = '".$database['name']."' and TABLE_TYPE='BASE TABLE'")[0]['totalTables'];
    if ($rows == 0) {
        $failMessage = 'Databases are not set-up. Please re-initiate installation.';
        $dbConnection = null;
    }
}

if ($dbConnection != null) {
// Version group
    $app->group('/v1', function () use ($app, $dbConnection) {
        // Check API key
        $keyValidate = new KeyValidation($dbConnection);
        $app->group('/nodes', function () use ($app, $keyValidate, $dbConnection) {

            $params = $app->request->post();

            $app->post('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Name']) && isset($params['Host']) && isset($params['Port']) && isset($params['Password']) && isset($params['Directory']) && isset($params['OS'])) {
                            $dataParams = array($params['Name'], $params['Host'], $params['Port'], $params['Password'], $params['Directory'], $params['OS']);

                            $dataSet = $dbConnection->query("SELECT * FROM Nodes WHERE `Host` = ?", $dataParams[1]);

                            if (!empty($dataSet))
                                $app->render(409, array(
                                    'msg' => 'This record is already in the database.',
                                ));
                            else
                                $dbConnection->query('INSERT INTO Nodes (Name, Creation, Host, Port, Password, Directory, OS, Active, Online) VALUES (?, Now(), ?, ?, ?, ?, ?, 1, 1)', $dataParams);
                            $dataSet = $dbConnection->query("SELECT * FROM Nodes WHERE `Host` = ?", $dataParams[1]);
                            $entity = new NodeEntity();
                            $entity->exchangeArray($dataSet[0]);
                            $app->render(200, array("results" => $entity->getArrayCopy()));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->delete();
            $app->delete('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Node'])) {
                            $dataParams = array($params['Node']);

                            $dataSet = $dbConnection->query("SELECT * FROM Nodes WHERE Node = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Nodes WHERE Node = ?', $dataParams);
                                $dbConnection->query('DELETE FROM Servers WHERE Node = ?', $dataParams);

                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else if (isset($params['Host'])) {
                            $dataParams = array($params['Host']);
                            $dataSet = $dbConnection->query("SELECT * FROM Nodes WHERE Host = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $nodeID = $dataSet[0]['Node'];
                                $dbConnection->query('DELETE FROM Nodes WHERE Host = ?', $dataParams);
                                $dbConnection->query('DELETE FROM Servers WHERE Node = ?', array($nodeID));

                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->get();
            $app->get('/node', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Node'])) {
                            $dataSet = $dbConnection->query("SELECT * FROM Nodes WHERE `Node` = ?", array($params['Node']));
                            if (!empty($dataSet)) {
                                $entity = new NodeEntity();
                                $entity->exchangeArray($dataSet[0]);
                                $app->render(200, array("results" => $entity->getArrayCopy()));
                            }

                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (!isset($params['sortby']))
                    $params['sortby'] = "Node";
                if (!isset($params['sortorder']))
                    $params['sortorder'] = "asc";
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {

                        $orders = array("Node");
                        $key = array_search($params['sortby'], $orders);
                        $order = $orders[$key];

                        $directions = array("desc", "asc");
                        $key = array_search($params['sortorder'], $directions);
                        $direction = $directions[$key];
                        // TODO: PAGE SYSTEM
                        $dataSet = $dbConnection->query("SELECT * FROM Nodes ORDER BY $order $direction", $params['sortby']);
                        $dataArray = array();
                        foreach ($dataSet as $data) {
                            $entity = new NodeEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
        });
        //TODO
        $app->group('/tickets', function () use ($app, $keyValidate, $dbConnection) {
            $app->get('/', function () use ($app, $keyValidate, $dbConnection) {
                $app->render(200, array(
                    'msg' => 'This API has not been deployed yet.',
                ));
            });
        });
        $app->group('/tokens', function () use ($app, $keyValidate, $dbConnection) {

            $params = $app->request->post();

            $app->post('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Type'])) {
                            $tokenValue = md5(uniqid(rand(), TRUE));
                            $date = date('Y-m-d H:i:s');
                            $linked = null;
                            if (isset($params['Linked']) && is_int($params['Linked']))
                                $linked = $params['Linked'];

                            $tokenValues = array($tokenValue, $date, $params['Type'], $linked);
                            $dbConnection->query('INSERT INTO Special_Tokens (Generated, Creation, Type, Linked) VALUES (?,?,?,?)', $tokenValues);

                            $entityData = array(
                                'Generated' => $tokenValue,
                                'Creation' => $date,
                                'Type' => $params['Type'],
                                'Linked' => $linked,
                            );
                            $entity = new TokensEntity();
                            $entity->exchangeArray($entityData);
                            $app->render(201, array("results" => $entity->getArrayCopy()));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });


            $params = $app->request->delete();
            $app->delete('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Generated']) && isset($params['Type'])) {
                            $dataParams = array($params['Generated'], $params['Type']);
                            $dataSet = $dbConnection->query("SELECT * FROM Special_Tokens WHERE Generated = ? AND Type = ?", $dataParams);
                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Special_Tokens WHERE Generated = ? AND Type = ?', $params);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            } else
                                $app->render(409, array(
                                    'msg' => 'This record does not exist.',
                                ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->get();
            $app->get('/type/:type/token/:token', function ($type, $token) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $dataSet = $dbConnection->query("SELECT * FROM Special_Tokens WHERE `Type` = ? AND `Generated` = ?", array($type, $token));
                        if (!empty($dataSet)) {
                            $app->render(200, array(
                                'msg' => 'The record is valid.',
                            ));
                        }
                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
        });
        $app->group('/nodes_status', function () use ($app, $keyValidate, $dbConnection) {

            $params = $app->request->post();

            $app->post('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Load_AVG']) && isset($params['HDD_Space']) && isset($params['RAM']) && isset($params['Node'])) {

                            $dataSet = $dbConnection->query("SELECT * FROM Nodes WHERE Node = ?", $params['Node']);
                            if (!empty($dataSet)) {
                                $host = $dataSet[0]['Host'];
                                $dataParams = array($params['Node'], fetchPing($host), $params['Load_AVG'], $params['HDD_Space'], $params['RAM']);
                                $dbConnection->query('INSERT INTO Nodes_Status (Node, Time_Pinged, Ping, Load_AVG, HDD_Space, RAM) VALUES (?,Now(), ?, ?, ?, ?)', $dataParams);
                                $app->render(201, array(
                                    'msg' => 'The record has been created.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->delete();
            $app->delete('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($data['Ping_ID'])) {
                            $dataParams = array($data['Ping_ID']);
                            $dataSet = $dbConnection->query("SELECT * FROM Nodes_Status WHERE Ping_ID = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Nodes_Status WHERE Ping_ID = ?', $dataParams);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else if (isset($data['Node'])) {
                            $dataParams = array($data['Node']);
                            $dataSet = $dbConnection->query("SELECT * FROM Nodes_Status WHERE Node = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Nodes_Status WHERE Node = ?', $params);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->get();
            $app->get('/node/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        // TODO: PAGE SYSTEM - NOT NEEDED BUT OPTIONAL
                        $dataSet = $dbConnection->query("SELECT * FROM Nodes_Status WHERE `Node` = ? ORDER BY Ping_ID DESC LIMIT 1", array($id));
                        if (!empty($dataSet)) {
                            $entity = new Nodes_StatusEntity();
                            $entity->exchangeArray($dataSet[0]);
                            $app->render(200, array("results" => $entity->getArrayCopy()));
                        }

                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/ping/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $dataSet = $dbConnection->query("SELECT * FROM Nodes_Status WHERE `Ping_ID` = ?", array($id));
                        if (!empty($dataSet)) {
                            $entity = new Nodes_StatusEntity();
                            $entity->exchangeArray($dataSet[0]);
                            $app->render(200, array("results" => $entity->getArrayCopy()));
                        }

                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (!isset($params['sortby']))
                    $params['sortby'] = "Ping_ID";
                if (!isset($params['sortorder']))
                    $params['sortorder'] = "asc";
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {

                        $orders = array("Ping_ID", "Load_AVG", "HDD_Space", "Time_Pinged", "Node", "Ping", "RAM");
                        $key = array_search($params['sortby'], $orders);
                        $order = $orders[$key];

                        $directions = array("desc", "asc");
                        $key = array_search($params['sortorder'], $directions);
                        $direction = $directions[$key];
                        // TODO: PAGE SYSTEM
                        $dataSet = $dbConnection->query("SELECT * FROM Nodes_Status ORDER BY $order $direction LIMIT 100", $params['sortby']);
                        $dataArray = array();
                        foreach ($dataSet as $data) {
                            $entity = new Nodes_StatusEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
        });
        $app->group('/servers', function () use ($app, $keyValidate, $dbConnection) {

            $params = $app->request->post();

            $app->post('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Owner']) && isset($params['Name']) && isset($params['Host']) && isset($params['App_ID']) && isset($params['Node'])) {
                            $dataParams = array($params['Owner'], $params['Name'], $params['Host'], $params['App_ID'], $params['Node']);
                            $dataSet = $dbConnection->query("SELECT * FROM Servers WHERE Owner = ? AND Name = ?", array($dataParams[0], $dataParams[1]));

                            if (!empty($dataSet))
                                $app->render(409, array(
                                    'msg' => 'This record is already in the database.',
                                ));
                            else {
                                // We must now deploy the server on the node selected.
                                $dbConnection->query('INSERT INTO Servers (Owner, Creation, Name, Host, App_ID, Node) VALUES (?,NOW(),?,?,?,?)', $dataParams);
                                $dataSet = $dbConnection->query("SELECT * FROM Servers WHERE Owner = ? AND Name = ?", array($dataParams[0], $dataParams[1]));
                                $entity = new ServersEntity();
                                $entity->exchangeArray($dataSet[0]);
                                $app->render(200, array("results" => $entity->getArrayCopy()));
                            }
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->delete();
            $app->delete('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($data['ID'])) {
                            $dataParams = array($data['ID']);
                            $dataSet = $dbConnection->query("SELECT * FROM Servers WHERE ID = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Servers WHERE ID = ?', $dataParams);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            } else
                                $app->render(409, array(
                                    'msg' => 'This record does not exist.',
                                ));
                        } else if (isset($data['Node'])) {
                            $dataParams = array($data['Node']);
                            $dataSet = $dbConnection->query("SELECT * FROM Servers WHERE Node = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Servers WHERE Node = ?', $dataParams);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            } else
                                $app->render(409, array(
                                    'msg' => 'This record does not exist.',
                                ));
                        } else if (isset($data['Owner'])) {
                            $dataParams = array($data['Owner']);
                            $dataSet = $dbConnection->query("SELECT * FROM Servers WHERE Owner = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Servers WHERE Owner = ?', $dataParams);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            } else
                                $app->render(409, array(
                                    'msg' => 'This record does not exist.',
                                ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->get();
            $app->get('/server/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        // TODO: PAGE SYSTEM - NOT NEEDED BUT OPTIONAL
                        $dataSet = $dbConnection->query("SELECT * FROM Servers WHERE `ID` = ? LIMIT 1", array($id));
                        if (!empty($dataSet)) {
                            $entity = new Nodes_StatusEntity();
                            $entity->exchangeArray($dataSet[0]);
                            $app->render(200, array("results" => $entity->getArrayCopy()));
                        }

                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/node/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $dataSet = $dbConnection->query("SELECT * FROM Servers WHERE `Node` = ?", array($id));
                        $dataArray = array();
                        foreach ($dataSet as $data) {
                            $entity = new ServersEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/user/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $dataSet = $dbConnection->query("SELECT * FROM Servers WHERE `Owner` = ?", array($id));
                        $dataArray = array();
                        foreach ($dataSet as $data) {
                            $entity = new ServersEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (!isset($params['sortby']))
                    $params['sortby'] = "ID";
                if (!isset($params['sortorder']))
                    $params['sortorder'] = "asc";
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {

                        $orders = array("ID", "Owner", "Server_Creation", "Name", "Host", "App_ID", "Node");
                        $key = array_search($params['sortby'], $orders);
                        $order = $orders[$key];

                        $directions = array("desc", "asc");
                        $key = array_search($params['sortorder'], $directions);
                        $direction = $directions[$key];
                        // TODO: PAGE SYSTEM
                        $dataSet = $dbConnection->query("SELECT * FROM Servers ORDER BY $order $direction LIMIT 100", $params['sortby']);
                        $dataArray = array();
                        foreach ($dataSet as $data) {
                            $entity = new ServersEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
        });
        $app->group('/servers_status', function () use ($app, $keyValidate, $dbConnection) {

            $params = $app->request->post();

            $app->post('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Server_ID']) && isset($params['Data'])) {
                            $dataParams = array($params['Server_ID'], $params['Data']);
                            $dbConnection->query('INSERT INTO Servers_Status (Server_ID, Time_Pinged, Data) VALUES (?,Now(), ?)', $dataParams);
                            $app->render(201, array(
                                'msg' => 'The record has been created.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->delete();
            $app->delete('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($data['Server_ID'])) {
                            $dataParams = array($data['Server_ID']);
                            $dataSet = $dbConnection->query("SELECT * FROM Servers_Status WHERE Server_ID = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Servers_Status WHERE Server_ID = ?', $dataParams);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else if (isset($data['Ping_ID'])) {
                            $dataParams = array($data['Ping_ID']);
                            $dataSet = $dbConnection->query("SELECT * FROM Servers_Status WHERE Ping_ID = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Servers_Status WHERE Ping_ID = ?', $dataParams);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->get();
            $app->get('/server/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        // TODO: PAGE SYSTEM - NOT NEEDED BUT OPTIONAL
                        $dataSet = $dbConnection->query("SELECT * FROM Servers_Status WHERE `Server_ID` = ? LIMIT 1", array($id));
                        if (!empty($dataSet)) {
                            $entity = new Servers_StatusEntity();
                            $entity->exchangeArray($dataSet[0]);
                            $app->render(200, array("results" => $entity->getArrayCopy()));
                        }

                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/ping/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $dataSet = $dbConnection->query("SELECT * FROM Servers_Status WHERE `Ping_ID` = ?", array($id));
                        if (!empty($dataSet)) {
                            $dataArray = array();
                            foreach ($dataSet as $data) {
                                $entity = new Servers_StatusEntity();
                                $entity->exchangeArray($data);
                                array_push($dataArray, $entity->getArrayCopy());
                            }
                            $app->render(200, array("results" => $dataArray));
                        }
                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (!isset($params['sortby']))
                    $params['sortby'] = "Server_ID";
                if (!isset($params['sortorder']))
                    $params['sortorder'] = "asc";
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $orders = array("Ping_ID", "Server_ID", "Time_Pinged");
                        $key = array_search($params['sortby'], $orders);
                        $order = $orders[$key];

                        $directions = array("desc", "asc");
                        $key = array_search($params['sortorder'], $directions);
                        $direction = $directions[$key];
                        // TODO: PAGE SYSTEM
                        $dataSet = $dbConnection->query("SELECT * FROM Servers_Status ORDER BY $order $direction LIMIT 100", $params['sortby']);
                        $dataArray = array();
                        foreach ($dataSet as $data) {
                            $entity = new Servers_StatusEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
        });
        $app->group('/application_support', function () use ($app, $keyValidate, $dbConnection) {

            $params = $app->request->post();

            $app->post('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($executeCommands))
                            $executeCommands = $params['Execution'];
                        else
                            $executeCommands = json_encode(array());
                        if (isset($executeCommands))
                            $installCommands = json_encode($params['Installation']);
                        else
                            $installCommands = json_encode(array());


                        if (isset($params['Name']) && isset($params['OS']) && isset($params['Description'])) {
                            $dataParams = array($params['Name'], $params['Description'], $installCommands, $executeCommands, $params['OS']);
                            $dbConnection->query('INSERT INTO Supported_Servers (Name, Description, Installation, Execution, OS) VALUES (?, ?, ?, ?, ?)', $dataParams);
                            $app->render(201, array(
                                'msg' => 'The record has been created.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->delete();
            $app->delete('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($data['App_ID'])) {
                            $dataParams = array($data['App_ID']);
                            $dataSet = $dbConnection->query("SELECT * FROM Supported_Servers WHERE App_ID = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Supported_Servers WHERE App_ID = ?', $dataParams);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else if (isset($data['Name'])) {
                            $dataParams = array($data['Name']);
                            $dataSet = $dbConnection->query("SELECT * FROM Supported_Servers WHERE Name = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Supported_Servers WHERE Name = ?', $dataParams);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->get();
            $app->get('/app/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        // TODO: PAGE SYSTEM - NOT NEEDED BUT OPTIONAL
                        $dataSet = $dbConnection->query("SELECT * FROM Supported_Servers WHERE `App_ID` = ? LIMIT 1", array($id));
                        if (!empty($dataSet)) {
                            $entity = new Supported_ServersEntity();
                            $entity->exchangeArray($dataSet[0]);
                            $app->render(200, array("results" => $entity->getArrayCopy()));
                        }
                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $app->get('/os/:os', function ($os) use ($app, $keyValidate, $dbConnection, $params) {
                if (!isset($params['sortby']))
                    $params['sortby'] = "App_ID";
                if (!isset($params['sortorder']))
                    $params['sortorder'] = "asc";
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $orders = array("App_ID", "OS", "Name");
                        $key = array_search($params['sortby'], $orders);
                        $order = $orders[$key];

                        $directions = array("desc", "asc");
                        $key = array_search($params['sortorder'], $directions);
                        $direction = $directions[$key];
                        // TODO: PAGE SYSTEM
                        $dataSet = $dbConnection->query("SELECT * FROM Supported_Servers WHERE OS LIKE ? ORDER BY $order $direction LIMIT 100", array("%" . $os . "%"));
                        $dataArray = array();
                        foreach ($dataSet as $data) {
                            $entity = new Supported_ServersEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $app->get('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (!isset($params['sortby']))
                    $params['sortby'] = "App_ID";
                if (!isset($params['sortorder']))
                    $params['sortorder'] = "asc";
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $orders = array("App_ID", "OS", "Name");
                        $key = array_search($params['sortby'], $orders);
                        $order = $orders[$key];

                        $directions = array("desc", "asc");
                        $key = array_search($params['sortorder'], $directions);
                        $direction = $directions[$key];
                        // TODO: PAGE SYSTEM
                        $dataSet = $dbConnection->query("SELECT * FROM Supported_Servers ORDER BY $order $direction LIMIT 100", $params['sortby']);
                        $dataArray = array();
                        foreach ($dataSet as $data) {
                            $entity = new Supported_ServersEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
        });
        $app->group('/members', function () use ($app, $keyValidate, $dbConnection) {

            $params = $app->request->post();

            $app->post('/login', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if ((isset($params['Username']) || isset($params['Email_Address'])) && isset($params['Password']) && isset($params['IP_Address'])) {

                            $dataSet = $dbConnection->query("SELECT * FROM Configuration WHERE Name = ? LIMIT 1", "failed_login_limit");

                            $timeLimit = 5;
                            $attemptLimit = 5;
                            if (!empty($dataSet)) {
                                $dataSetArray = json_decode($dataSet[0]);
                                if (isset($dataSetArray['time_limit']))
                                    $timeLimit = $dataSetArray['time_limit'];
                                if (isset($dataSetArray['attempt_limit']))
                                    $attemptLimit = $dataSetArray['attempt_limit'];
                            }
                            if ($attemptLimit != 0) {
                                $dataSet = $dbConnection->query("SELECT * FROM Login_Attempts WHERE IP_Address = ? AND Attempt_Date > NOW() - INTERVAL ? MINUTE", array($params['IP_Address'], $timeLimit));
                                if (count($dataSet) > $attemptLimit) {
                                    $app->render(409, array(
                                        'msg' => 'This record does not exist.',
                                    ));
                                }
                            }


                            if (isset($params['Email_Address']))
                                $dataSet = $dbConnection->query("SELECT * FROM Accounts WHERE Email_Address = ?", array($params['Email_Address']));
                            else if (isset($params['Username']))
                                $dataSet = $dbConnection->query("SELECT * FROM Accounts WHERE Username = ?", array($params['Username']));

                            if (!empty($dataSet)) {
                                // TODO: Apply lock check...
                                $entity = new MembersPrivateEntity();
                                $entity->exchangeArray($dataSet[0]);
                                // Handle the password checking here...
                                if (password_verify($params['Password'], $entity->Password) == 1) {
                                    // Valid password
                                    $app->render(200, array("results" => $entity->getArrayCopy()));
                                }
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));

                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->post('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Username']) && isset($params['Email_Address']) && isset($params['Password'])) {
                            $memberCheck = array($params['Username'], $params['Email_Address']);
                            $options = array(
                                'cost' => 11,
                            );
                            $password = password_hash($params['Password'], PASSWORD_BCRYPT, $options);
                            $memberValues = array($params['Username'], $params['Email_Address'], $password);
                            $dataSet = $dbConnection->query("SELECT * FROM Accounts WHERE Username = ? OR Email_Address = ?", $memberCheck);

                            if (!empty($dataSet))
                                $app->render(409, array(
                                    'msg' => 'This record is already in the database.',
                                ));
                            else
                                $dbConnection->query('INSERT INTO Accounts (Username, Email_Address, Password) VALUES (?,?,?)', $memberValues);
                            $app->render(201, array(
                                'msg' => 'The record has been created.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            // This request will take 0.3 seconds to complete TODO: Find a faster/safe way of hashing a session.
            $app->post('/sessions', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['User_ID']) && isset($params['Session_IP'])) {

                            $dataSet = $dbConnection->query("SELECT * FROM Accounts WHERE User_ID = ?", array($params['User_ID']));

                            if (!empty($dataSet)) {
                                $options = [
                                    'cost' => 11,
                                ];
                                $generatedKey = password_hash($params['User_ID'] . ":" . time(), PASSWORD_BCRYPT, $options);
                                $dataParams = array($params['User_ID'], $generatedKey, $params['Session_IP']);
                                $dbConnection->query('INSERT INTO Sessions_Data (User_ID, Session_Start, Session_Key, Session_IP) VALUES (?, NOW(),?,?)', $dataParams);
                                $dataResult = $dbConnection->query('SELECT * FROM Sessions_Data WHERE Session_Key = ?', $generatedKey);
                                $entity = new SessionEntity();
                                $entity->exchangeArray($dataResult[0]);
                                $app->render(201, array("results" => $entity->getArrayCopy()));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->delete();
            $app->delete('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($data['User_ID'])) {
                            $dataParams = array($params['User_ID']);
                            $dataSet = $dbConnection->query("SELECT * FROM Accounts WHERE User_ID = ?", $dataParams);
                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Accounts WHERE User_ID = ?', $params);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        }
                        $app->render(400, array(
                            'msg' => 'Parameters must be present.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->get();
            $app->get('/sessions', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Session_Key'])) {
                            $dataParams = array($params['Session_Key']);
                            $dataSet = $dbConnection->query("SELECT * FROM Sessions_Data WHERE `Session_Key` = ? LIMIT 1", $dataParams);
                            if (!empty($dataSet)) {
                                $entity = new SessionEntity();
                                $entity->exchangeArray($dataSet[0]);
                                $app->render(201, array("results" => $entity->getArrayCopy()));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        }
                        $app->render(400, array(
                            'msg' => 'Parameters must be present.',
                        ));

                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/user/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $dataSet = $dbConnection->query("SELECT * FROM Accounts WHERE `User_ID` = ? LIMIT 1", array($id));
                        if (!empty($dataSet)) {
                            $entity = new MembersEntity();
                            if ($keyValidate->validateKey($params))
                                $entity = new MembersPrivateEntity();
                            $entity->exchangeArray($dataSet[0]);
                            $app->render(200, array("results" => $entity->getArrayCopy()));
                        }
                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/email/:email', function ($email) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $dataSet = $dbConnection->query("SELECT * FROM Accounts WHERE `Email_Address` = ? LIMIT 1", array($email));
                        if (!empty($dataSet)) {
                            $entity = new MembersEntity();
                            if ($keyValidate->validateKey($params))
                                $entity = new MembersPrivateEntity();

                            $entity->exchangeArray($dataSet[0]);
                            $app->render(200, array("results" => $entity->getArrayCopy()));
                        }
                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $app->get('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                $private = false;
                if ($keyValidate->validateKey($params))
                    $private = true;
                if (!isset($params['sortby']))
                    $params['sortby'] = "User_ID";
                if (!isset($params['sortorder']))
                    $params['sortorder'] = "asc";
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $orders = array("User_ID", "Email_Address", "Username");
                        $key = array_search($params['sortby'], $orders);
                        $order = $orders[$key];

                        $directions = array("desc", "asc");
                        $key = array_search($params['sortorder'], $directions);
                        $direction = $directions[$key];
                        // TODO: PAGE SYSTEM
                        $dataSet = $dbConnection->query("SELECT * FROM Accounts ORDER BY $order $direction LIMIT 100", $params['sortby']);
                        $dataArray = array();


                        foreach ($dataSet as $data) {
                            $entity = new MembersEntity();
                            if ($private)
                                $entity = new MembersPrivateEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
        });
        $app->group('/login_attempts', function () use ($app, $keyValidate, $dbConnection) {

            $params = $app->request->post();

            $app->post('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['IP_Address'])) {
                            $dataParams = array($params['IP_Address']);
                            $dbConnection->query('INSERT INTO Login_Attempts (IP_Address, Attempt_Date) VALUES (?,Now())', $dataParams);
                            $app->render(201, array(
                                'msg' => 'The record has been created.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->delete();
            $app->delete('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($data['Attempt_ID'])) {
                            $dataParams = array($data['Attempt_ID']);
                            $dataSet = $dbConnection->query("SELECT * FROM Login_Attempts WHERE Attempt_ID = ?", $dataParams);

                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Login_Attempts WHERE Attempt_ID = ?', $dataParams);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        } else
                            $app->render(400, array(
                                'msg' => 'Parameters must be present.',
                            ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->get();
            $app->get('/attempt/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $dataSet = $dbConnection->query("SELECT * FROM Login_Attempts WHERE Attempt_ID = ?", $id);
                        if (!empty($dataSet)) {
                            $entity = new Login_AttemptsEntity();
                            $entity->exchangeArray($dataSet[0]);
                            return $entity;
                        }
                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
            $app->get('/ip/:id', function ($id) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        // TODO: PAGE SYSTEM - NOT NEEDED BUT OPTIONAL
                        $dataSet = $dbConnection->query("SELECT * FROM Login_Attempts WHERE `IP_Address` = ? LIMIT 1", array($id));
                        $dataArray = array();
                        foreach ($dataSet as $data) {
                            $entity = new Login_AttemptsEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $app->get('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (!isset($params['sortby']))
                    $params['sortby'] = "Attempt_ID";
                if (!isset($params['sortorder']))
                    $params['sortorder'] = "asc";
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $orders = array("Attempt_ID", "IP_Address", "Attempt_Date");
                        $key = array_search($params['sortby'], $orders);
                        $order = $orders[$key];

                        $directions = array("desc", "asc");
                        $key = array_search($params['sortorder'], $directions);
                        $direction = $directions[$key];
                        // TODO: PAGE SYSTEM
                        $dataSet = $dbConnection->query("SELECT * FROM Login_Attempts ORDER BY $order $direction LIMIT 100", $params['sortby']);
                        $dataArray = array();


                        foreach ($dataSet as $data) {
                            $entity = new Login_AttemptsEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
        });
        $app->group('/configuration', function () use ($app, $keyValidate, $dbConnection) {

            $params = $app->request->post();

            $app->post('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Name']) && isset($params['Value'])) {
                            $dataParams = array($params['Name'], $params['Value']);
                            $dataSet = $dbConnection->query("SELECT * FROM Configuration WHERE Name = ?", $dataParams[0]);
                            if (empty($dataSet)) {
                                $dbConnection->query('INSERT INTO Configuration (Name, Value) VALUES (?,?)', $dataParams);
                            } else {
                                $dbConnection->query('UPDATE Configuration SET Value = ? WHERE Name = ?', array_reverse($dataParams));
                            }
                            $app->render(201, array(
                                'msg' => 'The record has been modified/created.',
                            ));
                        }
                        $app->render(400, array(
                            'msg' => 'Parameters must be present.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->delete();
            $app->delete('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        if (isset($params['Name'])) {
                            $dataParams = array($params['Name']);
                            $dataSet = $dbConnection->query("SELECT * FROM Configuration WHERE Name = ?", $dataParams);
                            if (!empty($dataSet)) {
                                $dbConnection->query('DELETE FROM Configuration WHERE Name = ?', $dataParams);
                                $app->render(202, array(
                                    'msg' => 'The record has been deleted.',
                                ));
                            }
                            $app->render(409, array(
                                'msg' => 'This record does not exist.',
                            ));
                        }
                        $app->render(400, array(
                            'msg' => 'Parameters must be present.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $params = $app->request->get();
            $app->get('/config/:name', function ($name) use ($app, $keyValidate, $dbConnection, $params) {
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $dataSet = $dbConnection->query("SELECT * FROM Configuration WHERE Name = ?", $name);
                        if (!empty($dataSet)) {
                            $entity = new ConfigurationEntity();
                            $entity->exchangeArray($dataSet[0]);
                            $app->render(200, array("results" => $entity->getArrayCopy()));
                        }
                        $app->render(409, array(
                            'msg' => 'This record does not exist.',
                        ));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });

            $app->get('/', function () use ($app, $keyValidate, $dbConnection, $params) {
                if (!isset($params['sortby']))
                    $params['sortby'] = "ID";
                if (!isset($params['sortorder']))
                    $params['sortorder'] = "asc";
                if (array_key_exists("key", $params) || array_key_exists("secret", $params)) {
                    if ($keyValidate->validateKey($params)) {
                        $orders = array("ID", "Name", "Value");
                        $key = array_search($params['sortby'], $orders);
                        $order = $orders[$key];

                        $directions = array("desc", "asc");
                        $key = array_search($params['sortorder'], $directions);
                        $direction = $directions[$key];
                        // TODO: PAGE SYSTEM
                        $dataSet = $dbConnection->query("SELECT * FROM Configuration ORDER BY $order $direction LIMIT 100", $params['sortby']);
                        $dataArray = array();


                        foreach ($dataSet as $data) {
                            $entity = new Login_AttemptsEntity();
                            $entity->exchangeArray($data);
                            array_push($dataArray, $entity->getArrayCopy());
                        }
                        $app->render(200, array("results" => $dataArray));
                    }
                }
                $app->render(401, array(
                    'msg' => 'Provide a valid key to make API calls.',
                ));
            });
        });
    });
} else {
    $app->notFound(function () use ($app, $failMessage) {
        $app->render(418, array(
            'msg' => $failMessage,
        ));
    });
}
$app->run();
function fetchPing($host, $timeout = 2)
{
    $output = array();
    $com = 'ping -n -w ' . $timeout . ' -c 1 ' . escapeshellarg($host);

    $exitcode = 0;
    exec($com, $output, $exitcode);

    if ($exitcode == 0 || $exitcode == 1) {
        foreach ($output as $cline) {
            if (strpos($cline, ' bytes from ') !== FALSE) {
                $out = (int)ceil(floatval(substr($cline, strpos($cline, 'time=') + 5)));
                return $out;
            }
        }
    }

    return "N/A";
}