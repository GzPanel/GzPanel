<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-07-11
 * Time: 10:33 AM
 */
$requirements = array(
    "pdo [pdo extension]" => "pdo_mysql",
    "php [mysql extension]" => "mysql",
    "php version [5.5+ required]" => "version:5.5.0",
    "php [curl extension]" => "curl",
    "php [json extension]" => "json",
    "php [hash extension]" => "hash",
    "is 'tmp' folder writable (777)" => "writable:../../tmp",
    "is 'Configuration' folder writable (777)" => "writable:../../Configuration"
);

function checkPrereq($libName = null)
{
    if ($libName == null)
        return false;
    if (strpos($libName, ":")) {
        switch (strtolower(explode(":", $libName)[0])) {
            case "version":
                if (version_compare(PHP_VERSION, strtolower(explode(":", $libName)[1])) >= 0) {
                    return true;
                }
                return false;
            case "writable":
                if (is_writable(explode(":", $libName)[1])) {
                    return true;
                }
                return false;
            default:
                return false;
        }
    }
    if (extension_loaded($libName))
        return true;
    return false;
}

?>
<h1 class="text-center">
    <small>Below are the requirements to installing GzPanel</small>
</h1>
<div class="row">
    <div class="col-sm-3">
    </div>
    <div class="col-sm-6  text-center">
        <table class="table">
            <?php
            $allowInstall = true;
            foreach ($requirements as $key => $val) {
                $passed = checkPrereq($val);

                if (!$passed)
                    $allowInstall = false;
                ?>
                <tr>
                    <td class="text-center"><?php echo $key ?></td>
                    <td class="text-center"><i
                            class="fa <?php echo $passed ? "green-text fa-check" : "red-text fa-times" ?> fa-2x"></i>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
        if ($allowInstall) {
            ?>
            <a id="mysql_settings" class="localPageChange">
                <button type="submit" id="mysql_settings" class="btn btn-primary">Begin installation</button>
            </a>
            <?php
        } else {
            ?>
            <br/>
            <a id="curState" class="localPageChange">
                <button type="submit" class="btn btn-primary">Refresh</button>
            </a>
            <?php
        }
        ?>
    </div>
    <div class="col-sm-3"></div>
</div>
<script>

    (function ($) {
        $(document).ready(function () {
            // Bind to StateChange Event
            $('.localPageChange').click(function () {
                loadPage(this.id);
                return false;
            })
        });
    })(jQuery);
</script>
