<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-05-16
 * Time: 1:20 AM
 */
namespace install;

require_once(dirname(dirname(__FILE__)) . '/Data_Building/Data_Query/Data_Fetcher.php');
require_once(dirname(dirname(__FILE__)) . '/Data_Building/Exceptions/FailedDatabaseConnection.php');
use Data_Building\Data_Query\Data_Fetcher;
use Data_Building\Exceptions\FailedDatabaseConnection;

$fetcher = new Data_Fetcher();
try {
    $fetcher->fetchData("anyInterface");
    if (file_exists("../Configuration/internal_data.json"))
        header("Location: ..");
} catch (FailedDatabaseConnection $e) {
    // Failed to find a 'good' db system. They should be able to rebuild.
}
?>
<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Installation</title>
    <meta name="description" content="Installation panel for GzPanel.">

    <!-- jQuery -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src="../js/history_library/bundled/html4+html5/jquery.history.js"></script>
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/panel_main.css">
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->


<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 text-center">
            <h1 class="text-center">Installation of GzPanel</h1>

            <div id="contentLoader">

            </div>

        </div>
    </div>
</div>
<script>
    var currentPage = "";

    (function ($) {
        $(document).ready(function () {

            var pageDefault = '<?php if (isset($_GET['state'])){echo $_GET['state'];} else {echo "requirements";};?>';
            loadPage(pageDefault);

            // Bind to StateChange Event
            History.Adapter.bind(window, 'statechange', function () { // Note: We are using statechange instead of popstate
                var State = History.getState(); // Note: We are using History.getState() instead of event.state
            });


            $('.localPageChange').click(function () {
                // Change our States
                loadPage(this.id);
                return false;
            })
        });
    })(jQuery);
    function loadPage(newPage) {
        if (newPage !== "curState" && (currentPage != newPage || currentPage == "requirements")) {
            History.pushState({state: 1}, newPage, newPage);
            currentPage = newPage;
            window.document.title = newPage.substring(0, 1).toUpperCase() + newPage.substring(1).replace("_", " ");
        }
        newPage = currentPage;
        var content = $("#contentLoader");
        content.fadeOut('slow', function () {
            content.load("modules/" + newPage + '.php', function () {
                content.fadeIn('slow');
            });
        });

    }


</script>

</body>
</html>
