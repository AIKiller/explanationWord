<?php
session_id($_GET["sid"]);
session_start();
//echo json_encode($_SESSION);

//print_r($_SESSION);
$_SESSION = array();
?>