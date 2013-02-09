<?php

function connect_database ($database) {
    $link = mysql_connect("sdev.com.ar", "loucimj", "p2ssw0rd")
        or die("Could not connect");
    mysql_select_db($database) or die("Could not select database");
    return $link;
}


?>
