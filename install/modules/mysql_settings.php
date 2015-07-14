<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-07-11
 * Time: 10:32 AM
 */

?>
<h1 class="text-center">
    <small>Setting up MySQL settings...</small>
</h1>
<div class="row">
    <div class="col-sm-3">
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <table class="table">
                    <tr>
                        <td class="text-center">Mysql Username</td>
                        <td class="text-center"><input id="username" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td class="text-center">Mysql Password</td>
                        <td class="text-center"><input id="password" type="password" class="form-control"></td>
                    </tr>
                    <tr>
                        <td class="text-center">Database name</td>
                        <td class="text-center"><input id="name" placeholder="GZPanel" type="text" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">Mysql Host</td>
                        <td class="text-center"><input id="host" placeholder="localhost" type="text"
                                                       class="form-control"></td>
                    </tr>
                    <tr>
                        <td class="text-center">Mysql Port</td>
                        <td class="text-center"><input id="port" placeholder="3306" type="text" class="form-control">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-3"></div>
</div>

<button type="submit" id="checkDbSettings" data-loading-text="Testing..." class="btn btn-primary">Test connection
</button>
<script>

    (function ($) {
        $(document).ready(function () {
            // Bind to StateChange Event
            $('.localPageChange').click(function () {
                loadPage(this.id);
                return false;
            });
            $('#checkDbSettings').click(function () {
                var dbSettings = {};
                dbSettings['Username'] = document.getElementById("username").value;
                dbSettings['Password'] = document.getElementById("password").value;
                dbSettings['Host'] = document.getElementById("host").value ? document.getElementById("host").value : "localhost";
                dbSettings['Port'] = document.getElementById("port").value ? document.getElementById("port").value : 3306;
                dbSettings['Database_Name'] = document.getElementById("name").value ? document.getElementById("name").value : "GZPanel";
                var btn = $('#checkDbSettings').button('loading');
                checkDbConnection("check_db", JSON.stringify(dbSettings));
            });
        });
    })(jQuery);

    function submitDbInfo(dataName, dataValue) {
        $.ajax({
            type: "POST",
            url: 'modules/updateSetting.php',
            contentType: 'application/x-www-form-urlencoded',
            data: {name: dataName, value: dataValue},
            success: function (data) {
                loadPage('done');

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#checkDbSettings').button('reset');
            }
        });
    }


    function checkDbConnection(dataName, dataValue) {
        $.ajax({
            type: "POST",
            url: 'modules/updateSetting.php',
            contentType: 'application/x-www-form-urlencoded',
            data: {name: dataName, value: dataValue},
            success: function (data) {
                if (data == 1) {
                    // SAVE DATA AND BUILD DB
                    submitDbInfo("set_dbinfo", dataValue);
                }
                else {
                    alert('Failed to connect to database.' + data);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert('Failed to locate file... Error:' + textStatus);
                $('#checkDbSettings').button('reset');
            }
        });
    }
</script>
