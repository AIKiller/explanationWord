<?php
session_id($_GET["sid"]);
session_start();
$_SESSION["concepts"] = $_GET["concepts"];
$_SESSION["unrelated_concepts"] = json_encode(array());
?>