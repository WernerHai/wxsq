<?php
$version = "7.7";
if (!empty($_GET["version"])) {
    echo $version . '<br>';
    if (file_exists("wall/cj_plug/cj_html.php")) {
        echo 'cj_plug<br>';
    }
    if (file_exists("myadmin/shady.php")) {
        echo 'cjg_plug<br>';
    }
    if (file_exists("wall/qdq_plug/qdq_html.php")) {
        echo 'qdq_plug<br>';
    }
    if (file_exists("wall/ddp_plug/ddp_html.php")) {
        echo 'ddp_plug<br>';
    }
    if (file_exists("wall/vote_plug/ddp_html.php")) {
        echo 'vote_plug<br>';
    }
    if (file_exists("shake/index.php")) {
        echo 'shake_plug<br>';
    }
    if (file_exists("qyweixin/index.php")) {
        echo 'qyweixin_plug<br>';
    }
}