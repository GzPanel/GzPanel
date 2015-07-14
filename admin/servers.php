<?php

use DataHandlers\Main;

require('../DataHandlers/Main.php');
$Main = new Main();

?>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1>Servers</h1>
        </div>
        <div class="col-md-1 text-left">
        </div>
        <div class="col-md-10">

        </div>
        <div class="col-md-1 text-right">
            <a>
                <button type="button" data-toggle="modal" data-target="#myModal" class="btn btn-success">Add Server
                </button>
            </a>
        </div>
        <script>
            $(function () {
                $("[data-toggle=popover]").popover({
                    html: true,
                    trigger: 'hover click',
                    content: function () {
                        var content = $(this).attr("data-popover-content");
                        return $(content).children(".popover-body").html();
                    },
                    title: function () {
                        var title = $(this).attr("data-popover-content");
                        return $(title).children(".popover-heading").html();
                    }
                });
            });
        </script>
        <div class="col-md-12">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Host</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serversList = $Main->serversInfo->fetchServers(array("page" => 1));

                for ($var = 0; $var < count($serversList); $var++) {
                    $serverData = $Main->serversStatusInfo->fetchStatusByServerID($serversList[$var]->getID());
                    $tagName = "Unknown";
                    $tagType = "warning";
                    // TODO: GET SERVER STATUS
                    if ($serverData->Data != null) {
                        $tagName = "Online";
                        $tagType = "success";
                    } else {
                        $tagName = "Offline";
                        $tagType = "danger";
                    }
                    ?>
                    <!-- Content for Popover #1 -->
                    <div class="hidden text-center center" id="data_popover_<?php echo $var; ?>">
                        <div class="popover-heading center text-center">Server Status</div>
                        <div class="popover-body text-center center">
                            <b>Ping:</b> N/A ms
                        </div>
                    </div>
                    <tr>
                        <th scope="row"><?php echo $var + 1; ?></th>
                        <td><?php echo $serversList[$var]->getName(); ?></td>
                        <td><?php echo $serversList[$var]->getHost(); ?></td>
                        <td><a tabindex="0" role="button" class="btn label label-<?php echo $tagType; ?>"
                               data-toggle="popover" data-trigger="focus"
                               data-popover-content="#data_popover_<?php echo $var; ?>" data-placement="bottom"
                               data-html="true"><?php echo $tagName; ?></a></td>
                        <td><a class="nolink" href="manage?server=<?php echo $serversList[$var]->getID() ?>"><span
                                    class="fa fa-pencil fa-2x" aria-hidden="true"></span></a> <a class="nolink"
                                                                                                 href="delete?server=<?php echo $serversList[$var]->getID() ?>"><span
                                    class="fa fa-times fa-2x red-text" aria-hidden="true"></span></a></td>
                    </tr>
                <?php
                }
                ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h2 class="modal-title text-center" id="myModalLabel">Adding Server</h2>
            </div>
            <div class="modal-body">

                <div id="step1">
                    <p>Fill the following fields...</p>
                    <?php
                    ?>
                    <div class="center text-center">
                        <input type="text" class="form-control" placeholder="Server Name" id="serverName"/>
                        <select id="serverNode" class="form-control">
                            <option value="" disabled selected>Choose a location to deploy at</option>
                            <?php
                            foreach ($Main->nodesInfo->fetchNodes() as $node) {
                                echo "<option value='" . $node->getID() . "'>" . substr(strtoupper($node->getOS()), 0, 1) . " - " . $node->getName() . " [" . $node->getHost() . "]" . "</option>";
                            }
                            ?>
                        </select>

                        <select id="serverSelection" class="form-control">
                            <option disabled selected>Choose a server</option>
                            <?php
                            foreach ($Main->applicationSupportInfo->getSupportedServers() as $server) {
                                echo "<option class='" . str_replace(",", "", $server->getOS()) . "' value='" . $server->getID() . "'>" . $server->getName() . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" onclick="deployServer();">Deploy
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function deployServer() {
        var name = document.getElementById('serverName').value;
        var node = document.getElementById('serverNode').value;
        var selection = document.getElementById('serverSelection').value;
        var serverDetails = {};
        serverDetails['Name'] = name;
        serverDetails['Node'] = node;
        serverDetails['Application'] = selection;
        submitSettingsChange("deploy_server", JSON.stringify(serverDetails), function (data) {
            loadPage("servers");
        });
    }
    $(function () {
        var serverSelection = $("#serverSelection");
        serverSelection.hide(0);
        $("#steamLogin").hide(0);
        $('#serverNode').change(function () {
            var chosenNode = $("#serverNode option:selected").text();
            var os = chosenNode.substring(0, 1);

            if (os == "L") {
                $(".W").hide();
                $(".WL").show();
                $(".L").show();
            }
            else if (os == "W") {
                $(".W").show();
                $(".WL").show();
                $(".L").hide();
            }
            serverSelection.show('slow');
            serverSelection.prop('selectedIndex', 0).change();
        });

        serverSelection.change(function () {
            var attr = $('#serverSelection option:selected').attr('data-requirelogin');

            if (attr === "yes") {
                $("#steamLogin").show('slow');
            }
            else
                $("#steamLogin").hide('slow');
        });
    });
</script>