<?php
use DataHandlers\Main;

require('../DataHandlers/Main.php');
$Main = new Main();

function getRingItems()
{
    global $Main;
    $dashboardRings = json_decode($Main->configInfo->fetchConfigByName("dashboard_rings")->Value, true);
    return $dashboardRings;
}

function cleanCapitalize($stringToClean)
{
    $stringToClean = str_replace("_", " ", $stringToClean);
    $splitString = explode(" ", $stringToClean);
    foreach ($splitString as $stringPart) {
        $stringToClean = str_replace($stringPart, strtoupper(substr($stringPart, 0, 1)) . strtolower(substr($stringPart, 1)), $stringToClean);
    }

    return $stringToClean;
}

$dashboardRingItems = getRingItems();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <h1 class="page-header text-center">Dashboard</h1>
        </div>
        <div class="col-sm-12 col-md-12">
            <div class="container-fluid" style="padding: 25px;">

                <div class="row list-group" id="dashboardStatus">

                </div>
                <!-- Content for adding new status ring -->
                <div class="hidden" id="ringCreate">
                    <div class="popover-heading">Adding ring status</div>

                    <div class="popover-body">
                        <b>Border Type</b><br>
                        <select id="Ring_Type" class="form-control">
                            <option value="solid_border">Solid</option>
                            <option value="double_border">Double</option>
                            <option value="dotted_border">Dotted</option>
                            <option value="groove_border">Groove</option>
                        </select>
                        <b>Border Colour</b><br>
                        <select id="Ring_Colour" class="form-control">
                            <option value="red_border">Red</option>
                            <option value="green_border">Green</option>
                            <option value="blue_border">Blue</option>
                            <option value="black_border">Black</option>
                            <option value="white_border">White</option>
                        </select>
                        <b>Info</b><br>
                        <select id="Ring_Value" class="form-control">
                            <?php
                            $dataSet = json_decode($Main->configInfo->fetchConfigByName("ring_data")->Value, true);
                            for ($count = 0; $count < count($dataSet); $count++) {
                                ?>
                                <option value="<?php echo $dataSet[$count]['data_name']; ?>"><?php echo cleanCapitalize($dataSet[$count]['data_name']); ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <a class="form-control btn-primary btn" onclick="addRing_popover(this);">Add</a>
                        <a class="form-control btn-danger btn" onclick="dismissPopovers();">Cancel</a>
                    </div>
                </div>
                <row>
                    <div class="col-lg-12 text-center">

                        <a tabindex="0" class="btn btn-primary" role="button" data-toggle="popover" id="popoverAdd"
                           data-popover-content="#ringCreate" data-placement="bottom" data-html="true">Add Ring</a>
                    </div>
                </row>
            </div>
            <!-- Tab panes -->
            <div>
            </div>
        </div>
    </div>
</div>
<script>
    var dashboardStatus = document.getElementById('dashboardStatus');
    $(document).ready(function () {
        $("[data-toggle=popover]").popover({
            html: true,
            content: function () {
                var content = $(this).attr("data-popover-content");
                return $(content).children(".popover-body").html();
            },
            title: function () {
                var title = $(this).attr("data-popover-content");
                return $(title).children(".popover-heading").html();
            }
        });
        <?php
        for ($count = 0; $count < count($dashboardRingItems); $count++){
            echo "addRing('".cleanCapitalize($dashboardRingItems[$count]['Value'])."','".$dashboardRingItems[$count]['Value']."','".$dashboardRingItems[$count]['Type']."','".$dashboardRingItems[$count]['Colour']."', 0);";
        }
        ?>

        new Sortable(dashboardStatus, {
            group: "Dashboard Status",  // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
            sort: true,  // sorting inside list
            animation: 300,  // ms, animation speed moving items when sorting, `0` — without animation
            scroll: false, // or HTMLElement
            scrollSensitivity: 1, // px, how near the mouse must be to an edge to start scrolling.
            scrollSpeed: 10, // px

            handle: '.img-circle',
            // Changed sorting within list
            onUpdate: function (/**Event*/evt) {
                updateDashboardRings();
            }
        });


        (function () {
            // do some stuff
            getUpdatedValues();
            setTimeout(arguments.callee, 2000);

        })();

    });

    function updateDashboardRings() {
        submitSettingsChange('dashboard_rings', getLinks(dashboardStatus.children));
    }

    function addRing_popover(popover_button) {
        var ringText = popover_button.parentNode.childNodes.item(14).options[popover_button.parentNode.childNodes.item(14).selectedIndex].text;
        var ringBorderType = popover_button.parentNode.childNodes.item(4).value;
        var ringBorderColour = popover_button.parentNode.childNodes.item(9).value;
        var ringData = popover_button.parentNode.childNodes.item(14).value;
        addRing(ringText, ringData, ringBorderType, ringBorderColour, 1);
        $('#popoverAdd').popover('hide');
        return true;
    }

    function addRing(ringText, ringData, ringBorderType, ringBorderColour, saveChildren) {
        var children = document.getElementById('dashboardStatus').children;
        var currentNumber = Math.floor(12 / (children.length));
        var newNumber = Math.floor(12 / (children.length + 1));

        for (var count = 0; count < children.length; count++) {
            children[count].classList.remove('col-lg-' + currentNumber);
            children[count].classList.add('col-lg-' + newNumber);
        }

        var newEntry = document.createElement("div");
        newEntry.id = "statusRing";
        newEntry.className = 'col-lg-' + newNumber + ' center';
        newEntry.innerHTML = '<span style="font-size: 20px; color: #FF0000;" id="deleteRing" onclick="deleteRing(this)" class="glyphicon glyphicon-remove"/><a id="' + ringData + '" class="center nolink localPageChange img-circle ' + ringBorderColour + ' ' + ringBorderType + '" href="' + ringData + '"><div class="center"><h1 class="center"><span class="' + ringData + '">' + "Loading..." + '</span><small><br>' + ringText + '</small></h1></div></a>';
        document.getElementById('dashboardStatus').appendChild(newEntry);
        if (saveChildren)
            updateDashboardRings();
        children = document.getElementById('dashboardStatus').children;

        if (children.length >= 4) {
            $('#popoverAdd').addClass('disabled');
        }
        getUpdatedValues();
    }
    function getUpdatedValues() {
        $.get("generateResult.php?valueSet=dashboardData", function (data, status) {
            var dataSet = JSON.parse(data);
            for (var x = 0; x < dataSet.length; x++) {
                var elements = document.getElementsByClassName(dataSet[x]['Name']);
                for (var count = 0; count < elements.length; count++) {
                    elements[count].innerText = (dataSet[x]['Value'] == null ? "N/A" : dataSet[x]['Value']);
                }
            }
        });

    }
    function dismissPopovers() {
        $('#popoverAdd').popover('hide');
        return false;
    }

    function deleteRing(ringDeleteButton) {
        var ring = ringDeleteButton.parentNode;
        var currentNumber = Math.floor(12 / (ring.parentNode.children.length));
        var newNumber = Math.floor(12 / (ring.parentNode.children.length - 1));
        ring.style.display = "none";
        ring.parentNode.removeChild(ring);
        updateDashboardRings();

        var children = $('#dashboardStatus')[0].children;

        for (var count = 0; count < children.length; count++) {
            children[count].classList.remove('col-lg-' + currentNumber);
            children[count].classList.add('col-lg-' + newNumber);
        }

        if (children.length < 4) {
            $('#popoverAdd').removeClass('disabled');
        }

    }
    function getLinks(children) {
        var order = 0;
        var allDashboardRingInfo = [];
        for (var i = 0; i < children.length; i++) {
            var dashboardRingInfo = {};
            var divRingParent = children[i].children;
            for (var elementInfo = 1; elementInfo < divRingParent.length; elementInfo++) {
                var divRingChild = divRingParent[elementInfo].children;
                dashboardRingInfo["Value"] = divRingParent[elementInfo].id;
                dashboardRingInfo["Colour"] = divRingParent[elementInfo].classList[4];
                dashboardRingInfo["Type"] = divRingParent[elementInfo].classList[5];
                order++;
                dashboardRingInfo["Position"] = order;
            }
            allDashboardRingInfo.push(dashboardRingInfo);
        }
        return JSON.stringify(allDashboardRingInfo);
    }


</script>