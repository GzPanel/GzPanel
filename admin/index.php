<?php
use DataHandlers\Main;

require('../DataHandlers/Main.php');
$Main = new Main();

?>
<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Control Panel - Index</title>
    <meta name="description" content="A panel that meets all your hosting needs!">


    <!--<link rel="apple-touch-icon" href="apple-touch-icon.png"> -->
    <!-- Place favicon.ico in the root directory -->
    <!-- Slidebars CSS -->
    <link rel="stylesheet" href="../css/slidebars.css">
    <!-- Website CSS -->
    <!-- jQuery -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

    <!-- jsDelivr :: Sortable :: Latest (http://www.jsdelivr.com/) -->
    <script src="//cdn.jsdelivr.net/sortable/latest/Sortable.min.js"></script>

    <!-- Custom Panel JS -->
    <script src="../js/panel.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src="../js/history_library/bundled/html4+html5/jquery.history.js"></script>
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/panel_main.css">
    <link rel="stylesheet" href="../css/themes/greenday_theme.css">

    <script src="../js/slidebars.js"></script>
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->



<!-- Navbar -->
<noscript>
    This page needs JavaScript activated to work.
    <style>div { display:none; }</style>
</noscript>

<?php
$loggedIn = false;
try {
    $loggedIn = $Main->clientInfo->isLoggedIn();
} catch (Exception $e){
    echo $e;
}
if ($loggedIn) {
    ?>
    <!-- Fixed navbar -->
    <div class="panel_menu container">
        <div class="row text-center">
            <a id="nodes" class="localPageChange" href="nodes">
                <div class="col-xs-2 col-md-2 text-center">
                    <span class="glyphicon glyphicon-cloud text-center" aria-hidden="true"></span>

                    <p>Nodes</p>
                </div>
            </a>
            <a id="servers" class="localPageChange" href="servers">
                <div class="col-xs-2 col-md-2 text-center">
                    <span class="glyphicon glyphicon-hdd text-center" aria-hidden="true"></span>

                    <p>Servers</p>
                </div>
            </a>
            <a id="dashboard" class="localPageChange" href="dashboard">
                <div class="col-xs-4 col-md-4 text-center">
                    <span class="glyphicon glyphicon-home text-center" aria-hidden="true"></span>

                    <p>Home</p>
                </div>
            </a>
            <a id="tickets" class="localPageChange" href="tickets">
                <div class="col-xs-2 col-md-2 text-center">
                    <span class="glyphicon glyphicon-bullhorn text-center" aria-hidden="true"></span>

                    <p>Tickets</p>
                </div>
            </a>
            <a id="settings" class="localPageChange" href="settings">
                <div class="col-xs-2 col-md-2 text-center">
                    <span class="glyphicon glyphicon-cog text-center" aria-hidden="true"></span>

                    <p>Settings</p>
                </div>
            </a>
        </div>

    </div>
    <?php
}
?>

<div id="sb-site">
    <!-- Fixed navbar -->
    <?php
    if ($Main->clientInfo->isLoggedIn()) {
        ?>
        <img class="panel_sidemenu img-circle sb-toggle-right"/>

        <div id="loadingSpinner">
            <span class="spinner glyphicon glyphicon-refresh"></span>
        </div>
        <div id="contentLoader" class="center">

        </div>

        <?php
    } else {
        ?>
        <div class="admin-login-darkbg" id="adminPanel">
            <div class="admin-login">
                <form method="POST" action="" id="form">
                    <?php
                    if (isset($_POST['adminEmail']) && isset($_POST['adminPassword'])) {
                            ?>
                            <div class="alert alert-warning" role="alert">Invalid password/email provided. Please try
                                again or request to <a href="accountDetails.php?requestPassword">change password</a>.
                            </div>
                        <?php
                    }

                    ?>
                    <div class="form-group">
                        <label for="adminEmail">Email address</label>
                        <input type="email" class="form-control" name="adminEmail" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="adminPassword">Password</label>
                        <input type="password" class="form-control" name="adminPassword" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-inverse">Login</button>
                </form>
            </div>
        </div>
    <?php
    }
    ?>
</div>
<!-- /.navbar-collapse -->
<!-- /.sb-left -->

<!-- Slidebars -->
<div class="sb-slidebar sb-right">
    <nav>
        <ul class="sb-menu">
            <li class="text-center"> <img
                    style="margin-left: auto; margin-right: auto; display: block; width: 150px; height: 150px; background-image:url('http://placehold.it/150x150'); background-repeat: no-repeat;"
                    class="img-circle"/><br>Logged in as <?php echo $Main->clientInfo->getClient()->getUsername(); ?>
                <br><a href="../admin/logout" id="logout" class="nolink localPageChange">
                    <small>Not <?php echo $Main->clientInfo->getClient()->getUsername(); ?>?</small>
                </a></li>
            <li class="sb-close"><a id="profile" class="localPageChange" href="../admin/profile">Edit Profile</a></li>
            <li class="sb-close"><a id="preferences" class="localPageChange" href="../admin/preferences">Preferences</a></li>
            <li class="sb-close"><a id="logout" class="localPageChange" href="../admin/logout">Logout</a></li>
        </ul>
    </nav>
</div>
<!-- .sb-right -->


<!-- We highly recommend you use SASS and write your custom styles in sass/_custom.scss.
     However, there is a blank style.css in the css directory should you prefer -->
<!-- <link rel="stylesheet" href="css/style.css"> -->
<!-- Slidebars -->
<script>

    (function ($) {
        $(document).ready(function () {
            var $currentPage = "";

            var $pageDefault = 'dashboard';
            $pageDefault = '<?php if (isset($_GET['state'])){echo $_GET['state'];} else {echo "dashboard";};?>';
            //History.pushState({state: 1}, $pageDefault, $pageDefault);
            $currentPage = $pageDefault;
            window.document.title = $pageDefault.substring(0, 1).toUpperCase() + $pageDefault.substring(1).replace("_", " ");
            loadPage($currentPage);




            var slideBars = $.slidebars({
                siteClose: true, // true or false
                disableOver: false, // integer or false
                hideControlClasses: true, // true or false
                scrollLock: false // true or false
            });

            // Bind to StateChange Event
            History.Adapter.bind(window, 'statechange', function () { // Note: We are using statechange instead of popstate
                var State = History.getState(); // Note: We are using History.getState() instead of event.state
            });


            $('.localPageChange').click(function () {
                // Change our States
                if (this.id == "logout"){
                  History.pushState({state: 1}, this.id, this.id);
                  $currentPage = this.id;
                  window.document.title = "Login";
                  document.cookie = "accountSession" + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                    var loc = window.location.pathname;
                    var dir = loc.substring(0, loc.lastIndexOf('/'));
                    window.location.href = dir;
                }
                else if ($currentPage != this.id) {
                    History.pushState({state: 1}, this.id, this.id);
                    $currentPage = this.id;
                    window.document.title = this.id.substring(0, 1).toUpperCase() + this.id.substring(1).replace("_", " ");
                    loadPage($currentPage);
                }
                return false;
            })
        });
    })(jQuery);
    function loadPage (newPage){
        toggleLoadingSpinner('show');
        $("#contentLoader").load(newPage + '.php', window.location.search.replace("?", ""), function () {
            toggleLoadingSpinner('hide');
        })
    }
    function toggleLoadingSpinner(status){
        if ("show" == status){
            $("#loadingSpinner").fadeIn("fast");

        }
        else if (status == "hide"){
            $("#loadingSpinner").fadeOut("slow");
            $("#contentLoader").fadeIn("fast");
        }

    }
</script>
<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<script>
    (function (b, o, i, l, e, r) {
        b.GoogleAnalyticsObject = l;
        b[l] || (b[l] =
            function () {
                (b[l].q = b[l].q || []).push(arguments)
            });
        b[l].l = +new Date;
        e = o.createElement(i);
        r = o.getElementsByTagName(i)[0];
        e.src = 'https://www.google-analytics.com/analytics.js';
        r.parentNode.insertBefore(e, r)
    }(window, document, 'script', 'ga'));
    ga('create', 'UA-63794417-1', 'auto');
    ga('send', 'pageview');
</script>
</body>
</html>
