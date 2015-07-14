<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-05-24
 * Time: 1:30 AM
 */
use DataHandlers\Main;

require('../DataHandlers/Main.php');
$Main = new Main();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="text-center">Adding Application Support</h1>

            <h1 class="text-center">
                <small>This feature may contain bugs.</small>
            </h1>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <table class="table">
                <tr>
                    <td colspan="2" class="text-center">Operating System</td>
                </tr>
                <tr>
                    <td class="text-center clickable" onclick="chooseOS(this);" id="Linux"><i
                            class="fa fa-linux fa-5x"></i></td>
                    <td class="text-center clickable" onclick="chooseOS(this);" id="Windows"><i
                            class="fa fa-windows fa-5x"></i></td>
                </tr>
            </table>

            <table class="table">
                <tr>
                    <td class="text-center"><label for="appName">Application Name</label></td>
                    <td><input type="text" class="form-control" id="applicationName"/></td>
                </tr>
                <tr>
                    <td class="text-center"><label for="appDesc">Description</label></td>
                    <td><input type="text" class="form-control" id="applicationDesc"/></td>
                </tr>
            </table>

            <table id="installFields" class="table table-bordered">
                <thead>
                <td class="text-center">
                    <button onclick="removeField(this);" style="float: left;" type="button" class="btn btn-danger"
                            aria-label="Remove field">
                        <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
                    </button>
                    <label for="execution">Installation</label>
                    <button onclick="addField(this);" style="float: right;" type="button" class="btn btn-success"
                            aria-label="Add field">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    </button>
                </td>
                <thead>
            </table>
            <table id="executeFields" class="table table-bordered">
                <thead>
                <td class="text-center">
                    <button onclick="removeField(this);" style="float: left;" type="button" class="btn btn-danger"
                            aria-label="Remove field">
                        <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
                    </button>
                    <label for="execution">Execution</label>
                    <button onclick="addField(this);" style="float: right;" type="button" class="btn btn-success"
                            aria-label="Add field">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    </button>
                </td>
                </thead>
            </table>

            <table class="table">
                <thead>
                <td colspan="2" class="text-center">
                    <label for="execution">
                        <Button type="submit" onclick="addApplicationSupport();" class="btn btn-primary">Add Template
                        </Button>
                    </label>
                </td>
                </thead>
            </table>
        </div>
        <div class="col-md-3">
        </div>
    </div>
    <br/>

</div>

<script>
    var operatingSystem = null;
    function chooseOS(button) {
        if (operatingSystem != null)
            document.getElementById(operatingSystem).classList.remove("outline");

        operatingSystem = button.id;
        button.classList.add("outline");
    }
    function addField(addButton) {
        var table = addButton.parentNode.parentNode.parentNode;// Fetch the table object - It is the 3rd parent of the button.

        var row = table.insertRow(-1);// Insert a new row at the end of the table
        var cell = row.insertCell(0);// Insert a new cell
        cell.className = "text-center";// Set the cell's class to center the text.
        cell.innerHTML = '<input type="text" placeholder="System Command #' + (table.childNodes.length - 2) + '" class="form-control"/>';
        $(row).hide(0, function () {
            $(row).show("fast");
        });
    }

    function removeField(removeButton) {
        var table = removeButton.parentNode.parentNode.parentNode;// Fetch the table object - It is the 3rd parent of the button.
        if (table.rows.length > 1) {
            var lastRow = $(table.rows[table.rows.length - 1]);
            lastRow.hide('fast', function () {
                lastRow.remove();
            });
        }
    }

    function addApplicationSupport() {
        if (operatingSystem != null) {
            var applicationArray = {};

            // Add Name entry into applicationArray.
            applicationArray["Name"] = document.getElementById("applicationName").value;

            // Add Description entry into applicationArray.
            applicationArray["Description"] = document.getElementById("applicationDesc").value;
            /*
             Initiating installCommands array
             */
            var installCommands = [];
            /*
             Initiating executionCommands array
             */
            var executionCommands = [];


            var installTable = document.getElementById("installFields");
            // Loop through all rows except the first one (This is because it has the title)
            for (var insY = 1, insRow; insRow = installTable.rows[insY]; insY++) {
                for (var insX = 0, insCol; insCol = insRow.cells[insX]; insX++) {
                    var inputField = insCol.getElementsByTagName("input")[0];
                    if (inputField != null && inputField.value) {
                        installCommands.push(inputField.value);
                    }
                }
            }

            var executeTable = document.getElementById("executeFields");
            for (var exeY = 1, exeRow; exeRow = executeTable.rows[exeY]; exeY++) {
                for (var exeX = 0, exeCol; exeCol = exeRow.cells[exeX]; exeX++) {
                    var executeField = exeCol.getElementsByTagName("input")[0];
                    // We make sure the value is not null and is not empty.
                    if (executeField != null && executeField.value) {
                        executionCommands.push(executeField.value);
                    }
                }
            }

            applicationArray["Install_Commands"] = installCommands;
            applicationArray["Execute_Commands"] = executionCommands;
            applicationArray["OS"] = operatingSystem;

            console.log(applicationArray);
            submitSettingsChange("add_application_support", JSON.stringify(applicationArray), function (data) {
                window.open("settings", "_self")
            });
        }
        else
            alert("Choose an operating system.");
    }

</script>