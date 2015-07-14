<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-04-25
 * Time: 8:42 PM
 */
?>

<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed " data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <img style="margin-left: auto; margin-right: auto; display: block; width: 200px; height: 50px; background-image:url('http://placehold.it/200x50'); background-repeat: no-repeat;">
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav" >
                <?php
                try {
                    $menuOptions = json_decode($Main->configInfo->fetchConfigByName("Public_Menu")->Value);
                    if ($menuOptions != null) {
                        foreach ($menuOptions as $menuOption) {
                            echo '<li ';
                            if (strpos(strtolower($menuOption->Link), $curRoom) != -1) ;
                            echo 'class="active" ';


                            echo '><a href="' . ($menuOption->Link) . '">' . $menuOption->Text . '</a></li>';
                        }
                    }
                } catch (Exception $e){
                    echo '<li><a>Failed to fetch info.</a></li>';
                }
                ?>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
