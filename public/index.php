<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home Page</title>
    <meta name="description" content="">

    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <!-- Place favicon.ico in the root directory -->

    <!-- Slidebars CSS -->
    <link rel="stylesheet" href="css/slidebars.css">
    <!-- Website CSS -->

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">

    <!-- jQuery -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>


    <!-- Latest compiled and minified JavaScript -->
    <script src="js/bootstrap.min.js"></script>


    <link rel="stylesheet" href="css/panel_main.css">
    <link rel="stylesheet" href="css/themes/greenday_theme.css">
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Add your site or application content here -->
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$curRoom = "index.php";
use DataHandlers\Main;

require('../DataHandlers/Main.php');
$Main = new Main();

?>

<!-- Navbar -->

<div id="sb-site">
    <?php
    include 'header.php';
    ?>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</div>
<!-- /.navbar-collapse -->
<!-- Brand and toggle get grouped for better mobile display -->

<!-- Slidebars -->
<div class="sb-slidebar sb-left">
    <nav>
        <ul class="sb-menu">
        </ul>
    </nav>
</div>
<!-- /.sb-left -->

<!-- Slidebars -->
<div class="sb-slidebar sb-right">
    <nav>
        <ul class="sb-menu">
        </ul>
    </nav>
</div>
<!-- /.sb-right -->


<!-- We highly recommend you use SASS and write your custom styles in sass/_custom.scss.
     However, there is a blank style.css in the css directory should you prefer -->
<!-- <link rel="stylesheet" href="css/style.css"> -->

<!-- Slidebars -->
<script src="js/slidebars.js"></script>
<script>
    (function ($) {
        $(document).ready(function () {
            $.slidebars({
                siteClose: true, // true or false
                disableOver: false, // integer or false
                hideControlClasses: false, // true or false
                scrollLock: false // true or false
            });
        });
    })(jQuery);
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
