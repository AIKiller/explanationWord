<?php
session_id($_GET["sid"]);
session_start();
$_SESSION['lang'] = $_GET["lang"];
echo 1;
?>