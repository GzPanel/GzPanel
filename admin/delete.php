<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-07-12
 * Time: 11:41 AM
 */
namespace AdminPanel;

use DataHandlers\Main;
use Net_SSH2;


set_include_path(get_include_path() . PATH_SEPARATOR . '../libs/phpseclib');
include('../libs/phpseclib/Net/SSH2.php');
require('../DataHandlers/Main.php');
$Main = new Main();
if (isset($_GET['node']) && isset($_GET['username']) && isset($_GET['password'])) {
    // Trying to delete a NODE
    $nodeRequested = $Main->nodesInfo->fetchNodeByID($_GET['node']);

    if ($nodeRequested->getID() != null) {
        // Valid node...
        if (strtolower($nodeRequested->getOS()) == "linux") {
            define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);

            $ssh = new Net_SSH2($nodeRequested->getHost());
            if (!$ssh->login($_GET['username'], $_GET['password'])) {
                exit('Login Failed');
            }
            /*
             * We must create all folders incase they were not created in Templates and Servers - then copy templates folder into servers
             */
            $tempKey = $Main->tokensInfo->fetchNewToken("DL_SCRIPT");
            echo $ssh->exec("wget -O \"node_setup.sh\" " . 'http://' . $_SERVER['SERVER_NAME'] . explode("/delete.php", $_SERVER['REQUEST_URI'])[0] . "/generateNodeScript.php?tempKey=" . $tempKey->Generated . ";chmod 777 node_setup.sh;./node_setup.sh uninstall;");

        }
    }
} else if (isset($_GET['node'])) {
    //Need some info... User/pass?
    ?>
    <h2 class="text-center">
        <small>In order to complete this operation, please enter the following information.</small>
    </h2>
    <div class="container">
        <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-8">
                <table class="table table-responsive">
                    <tr>
                        <td class="text-center">Privileged Username</td>
                        <td class="text-center"><input placeholder="root" id="username" type="text"
                                                       class="form-control"></td>
                    </tr>
                    <tr>
                        <td class="text-center">Privileged Password</td>
                        <td class="text-center"><input id="password" type="password" class="form-control"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center">
                            <button onclick="submitRequest()" class="btn btn-primary text-center" type="submit">Send
                                Request
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-xs-2"></div>
        </div>
    </div>
    <script>
        function submitRequest() {
            var nodeUser = document.getElementById("username").value;
            var nodePass = document.getElementById("password").value;
            deleteNode(nodeUser, nodePass);
        }
        function deleteNode(Username, Password) {

            $.ajax({
                type: "GET",
                url: 'delete.php',
                contentType: 'application/x-www-form-urlencoded',
                data: {username: Username, password: Password, node:<?php echo $_GET['node'];?>},
                success: function (data) {
                    window.open("nodes", "_self")
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("Failed to delete node. Please check online resources for help.");
                }
            });
        }
    </script>
    <?php
} else if (isset($_GET['server'])) {
    //Need some info... User/pass?
    $serverRequested = $Main->serversInfo->fetchServerByID($_GET['server']);
    $nodeRequested = $Main->nodesInfo->fetchNodeByID($serverRequested->Node);
    ?>
    <h1 class="text-center">
        <small>This function has not been developed yet.</small>
    </h1>
    <?php
    if ($serverRequested->ID != null) {
        // Valid node...
        if (strtolower($nodeRequested->getOS()) == "linux") {
//            define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
//
//            $ssh = new Net_SSH2($nodeRequested->Host);
//            if (!$ssh->login($_GET['username'], $_GET['password'])) {
//                exit('Login Failed');
//            }
//            /*
//             * We must create all folders incase they were not created in Templates and Servers - then copy templates folder into servers
//             */
//            $tempKey = $Main->tokensInfo->fetchNewToken("DL_SCRIPT");
//            echo $ssh->exec("wget -O \"node_setup.sh\" " . 'http://' . $_SERVER['SERVER_NAME'] . explode("/delete.php", $_SERVER['REQUEST_URI'])[0] . "/generateNodeScript.php?tempKey=" . $tempKey->Generated . ";chmod 777 node_setup.sh;./node_setup.sh uninstall;");

        }
    }
}