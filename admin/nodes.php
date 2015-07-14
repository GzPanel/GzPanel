<?php
namespace AdminPanel;
use DataHandlers\Main;
use DateTime;

require('../DataHandlers/Main.php');
$Main = new Main();

?>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1>Nodes</h1>
        </div>
        <div class="col-md-1 text-left">
        </div>
        <div class="col-md-10">

        </div>
        <div class="col-md-1 text-right">
            <a><button type="button" data-toggle="modal" data-target="#myModal" class="btn btn-success">Add Node</button></a>
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
                $nodeList = $Main->nodesInfo->fetchNodes(array("page" => 1));



                for ($var = 0; $var < count($nodeList); $var++) {
                    $NodeStatus = $Main->nodesStatusInfo->fetchStatusByNodeID($nodeList[$var]->getID());

                    $tagName = "Unknown";
                    $tagType = "warning";
                    if ($nodeList[$var]->getOnline() == 1) {
                        $tagName = "Online";
                        $tagType = "success";
                    } else if ($nodeList[$var]->getOnline() == 0) {
                        $tagName = "Offline";
                        $tagType = "danger";
                    }
                    $LOAD_AVG = "N/A";
                    $HDDSPACE = "N/A";
                    $RAM = "N/A";
                    $Ping = "N/A";
                    $difference = "N/A";
                    if ($NodeStatus != null) {
                        $LOAD_AVG = ($NodeStatus->Load_AVG >= 4 ? "High" : ($NodeStatus->Load_AVG >= 2 ? "Medium" : ($NodeStatus->Load_AVG < 2 ? "Low" : "")));
                        $HDDSPACE = floor((explode("/", $NodeStatus->HDD_Space)[1] - explode("/", $NodeStatus->HDD_Space)[0]) / 1000) . " GB";
                        $RAM = floor((explode("/", $NodeStatus->RAM)[1] - explode("/", $NodeStatus->RAM)[0])) . " MB";
                        $Ping = $NodeStatus->Ping;
                        $now = new DateTime();
                        $then = new DateTime($NodeStatus->Time_Pinged);
                        $diff = $now->diff($then);
                        $difference = $diff->format('%im %ss');
                        if ($diff->format('%i') == 0)
                            $difference = $diff->format('%ss');
                    }
                    ?>
                    <!-- Content for Popover #1 -->
                    <div class="hidden text-center center" id="data_popover_<?php echo $var;?>">
                        <div class="popover-heading center text-center">Node Status</div>
                        <div class="popover-body text-center center">
                            <b>RAM Free:</b> <?php echo $RAM;?><br>
                            <b>HDD Free:</b> <?php echo $HDDSPACE;?><br>
                            <b>Load:</b> <?php echo $LOAD_AVG;?><br>
                            <br>
                            <b>Ping:</b> <?php echo $Ping;?> ms
                            <br>
                            <b>Queried <?php echo $difference . " ago"; ?></b>
                        </div>
                    </div>
                    <tr>
                        <th scope="row"><?php echo $var + 1; ?></th>
                        <td><?php echo $nodeList[$var]->getName(); ?></td>
                        <td><?php echo $nodeList[$var]->getHost(); ?></td>
                        <td><a tabindex="0" role="button" class="btn label label-<?php echo $tagType;?>" data-toggle="popover" data-trigger="focus" data-popover-content="#data_popover_<?php echo $var;?>" data-placement="bottom" data-html="true"><?php echo $tagName;?></a></td>
                        <td><a class="nolink" href="manage?node=<?php echo $nodeList[$var]->getID();?>"><span
                                    class="fa fa-pencil fa-2x" aria-hidden="true"></span></a> <a class="nolink"
                                                                                                 href="delete?node=<?php echo $nodeList[$var]->getID(); ?>"><span
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
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title text-center" id="myModalLabel">Adding Node</h2>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger text-center" role="alert"><b>Do not</b> give anyone access to the generated scripts.</div>
                <h3>Steps:</h3>
                <ul>
                    <li>Click 'Generate Script' below</li>
                    <li>Upload file and set permissions to <b>777</b></li>
                    <li>Execute the script by running '<b>./node_setup.sh</b>'</li>
                    <li>Follow prompts</li>
                </ul>
                <div class="center text-center">
                    <button type="button" class="btn btn-success" id="getLinuxCommand" data-loading-text="Generating Command..." onclick="generateCommand();">Generate Linux Command Line</button>
                    <br>
                    <input type="text" class="form-control" style="display: none;" placeholder="Linux Command" id="linuxCommand"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="generateButton" data-loading-text="Generating..." onclick="generateScript();">Generate Script</button>
                <script>
                    function generateScript (){
                        $('#generateButton').button('loading');
                        document.getElementById('downloadFile').src = 'generateNodeScript.php';
                    }
                    function generateCommand(){
                        document.getElementById('getLinuxCommand').style.display = "none";
                        document.getElementById('linuxCommand').style.display = "block";

                        if (window.XMLHttpRequest)
                        {// code for IE7+, Firefox, Chrome, Opera, Safari
                            xmlhttp=new XMLHttpRequest();
                        }
                        else
                        {// code for IE6, IE5
                            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                        }
                        xmlhttp.onreadystatechange=function()
                        {
                            if (xmlhttp.readyState==4 && xmlhttp.status==200)
                            {
                                document.getElementById('linuxCommand').value = "wget -O \"node_setup.sh\" "+ window.location.href.replace("/nodes", "") + "/generateNodeScript.php?tempKey=" +xmlhttp.responseText + ";chmod 777 node_setup.sh;./node_setup.sh";

                            }
                        };
                        xmlhttp.open("GET", "generateResult.php?keyType=dlScripts", false);
                        xmlhttp.send();
                    }

                </script>
                <iframe id="downloadFile" style="display: none;">
            </div>
        </div>
    </div>
</div>

