<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-05-17
 * Time: 1:14 PM
 */
use DataHandlers\Main;

require('../DataHandlers/Main.php');
$Main = new Main();

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2">
            <ul class="nav nav-tabs nav-stacked nav-sidebar" role="tablist" id="settingsTab">
                <li role="presentation"><a href="#public" aria-controls="profile" role="tab" data-toggle="tab">Public
                        Panel</a></li>
            </ul>
        </div>
        <div class="col-sm-9">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade" id="public">
                    <h1 class="sub-header">Public</h1>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <h2 class="sub-header text-center">Main menu</h2>
                                <ul id="specialPages" class="list-group">
                                    <li class="list-group-item" id="form">
                                        <div class="row">
                                            <div class="container-fluid">
                                                <div class="col-xs-12">
                                                    <h3 class="text-center">Add Menu Item</h3>

                                                    <div class="form-groupcenter text-center">
                                                        <input type="text" class="form-control" id="pageName"
                                                               placeholder="Name">
                                                        <input type="text" class="form-control" id="pageLink"
                                                               placeholder="Link">
                                                            <span onclick="addLink();"
                                                                  class="btn btn-primary center">Add Link</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <?php

                                    $menuOptions = json_decode($Main->configInfo->fetchConfigByName("Public_Menu")->Value);
                                    for ($counter = 0; $counter < count($menuOptions); $counter++) {
                                        $linkName = $menuOptions[$counter]->Text;
                                        $linkUrl = $menuOptions[$counter]->Link;
                                        ?>
                                        <li class="list-group-item">
                                            <div class="container-fluid">
                                                <div class="row-fluid vcenter">
                                                    <div id="moveHandle" class="col-lg-1 border-right-dotted">
                                                        <span style="font-size: 20px;"
                                                              class="glyphicon glyphicon-move"></span>
                                                    </div>
                                                    <div class="col-lg-10">
                                                        <p id="Text" class="linkName_selection">
                                                            <?php echo $linkName; ?>
                                                        </p>
                                                        <p id="Link" class="linkUrl_selection">
                                                            <?php echo $linkUrl; ?>
                                                        </p>
                                                    </div>
                                                    <div class="col-lg-1 center"><span onclick="deleteItem(this)"
                                                                                       style="font-size: 20px; color: #FF0000; float: right"
                                                                                       class="glyphicon glyphicon-remove"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <h2 class="sub-header text-center">Servers Supported</h2>
                                <ul id="supportedServers" class="list-group">
                                    <li class="list-group-item" id="form">
                                        <div class="row">
                                            <div class="container-fluid">
                                                <div class="col-xs-12 text-center">
                                                    <a id="add_template" href="add_template">
                                                        <button class="btn btn-primary btn-lg">Add Template</button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <?php
                                    $supportedServers = $Main->applicationSupportInfo->getSupportedServers();
                                    foreach ($supportedServers as $supportedServer) {
                                        $serverName = $supportedServer->getName();
                                        $serverApp_ID = $supportedServer->getID();
                                        $serverOS = $supportedServer->getOS();
                                        ?>
                                        <li class="list-group-item">
                                            <div class="container-fluid">
                                                <div class="row-fluid vcenter">
                                                    <div class="col-lg-12">
                                                        <p id="Game_Name" class="linkName_selection">
                                                            <?php echo $serverName; ?>
                                                        </p>
                                                        <input id="App_Name" type="hidden"
                                                               value="<?php echo $serverName; ?>">
                                                        <input id="App_ID" type="hidden"
                                                               value="<?php echo $serverApp_ID; ?>">
                                                        <input id="OS" type="hidden" value="<?php echo $serverOS; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php
                                    }
                                    ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple list
        function getLinks(children) {
            var links = [];
            var order = 0;
            for (var i = 0; i < children.length; i++) {
                var li = children[i].children;
                if (children[i].id != "form") {
                    for (var elementInfo = 0; elementInfo < li.length; elementInfo++) {
                        var div1 = li[elementInfo].children;
                        for (var elementInfo1 = 0; elementInfo1 < div1.length; elementInfo1++) {
                            var div2 = div1[elementInfo1].children;
                            var link = {};
                            for (var elementInfo2 = 0; elementInfo2 < div2.length; elementInfo2++) {
                                // Filter out the buttons - Useless to loop through.
                                if (div2[elementInfo2].className == "col-lg-10") {
                                    var div3 = div2[elementInfo2].children;
                                    for (var elementInfo3 = 0; elementInfo3 < div3.length; elementInfo3++) {
                                        var div4 = div3[elementInfo3];
                                        if (div4.tagName == "P") {
                                            link[div4.id] = div4.innerText.trim();
                                        }
                                    }
                                }
                            }
                            order++;
                            link['Position'] = order;
                            links.push(link);
                        }
                    }
                }
            }
            return JSON.stringify(links);
        }
        function sendLocalMessage(divID, divMessage) {

        }

        $(document).ready(function () {
            $('#settingsTab a:eq(0)').tab('show');
            var menuLinks = document.getElementById('specialPages');
            new Sortable(menuLinks, {
                group: "Menu-list",  // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
                sort: true,  // sorting inside list
                animation: 300,  // ms, animation speed moving items when sorting, `0` — without animation
                scroll: true, // or HTMLElement
                scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
                scrollSpeed: 10, // px
                handle: ".row-fluid",  // Drag handle selector within list items

                // Changed sorting within list
                onUpdate: function (/**Event*/evt) {
                    submitSettingsChange('public_menu', getLinks(menuLinks.children));
                }
            });
        });


        function addLink() {
            var pageName = document.getElementById('pageName');
            var pageLink = document.getElementById('pageLink');

            if (pageName.value != "" && pageName.value != "") {
                var specialPages = document.getElementById('specialPages');
                var newEntry = document.createElement("li");
                newEntry.classList.add("list-group-item");
                newEntry.innerHTML = ' <div class="container-fluid"> <div class="row-fluid vcenter"> <div id="moveHandle" class="col-lg-1 border-right-dotted"> <span style="font-size: 20px;" class="glyphicon glyphicon-move"/> </div> <div class="col-lg-10"> <p id="Text" class="linkName_selection">' + pageName.value + '</p> <p id="Link" class="linkUrl_selection">' + pageLink.value + '</p></div> <div class="col-lg-2 center"><span onclick="deleteItem(this)" style="font-size: 20px; color: #FF0000;" class="glyphicon glyphicon-remove"/> </div> </div> </div>';
                specialPages.appendChild(newEntry);
                submitSettingsChange('Public_Menu', getLinks(specialPages.children));
                pageName.value = "";
                pageLink.value = "";
            }
            return false;
        }
        function deleteItem(obj) {
            obj.parentNode.parentNode.parentNode.parentNode.parentNode.removeChild(obj.parentNode.parentNode.parentNode.parentNode);
            submitSettingsChange('Public_Menu', getLinks(document.getElementById('specialPages').children));
        }

    </script>