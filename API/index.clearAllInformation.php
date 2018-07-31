<?php
//include("../logic/common.inc.php");
//echo $_GET["userInformations"];
//print_r($_SESSION);
session_id($_GET["sid"]);
session_start();
if(isset($_SESSION["general_awareness"])){
	//echo "清除";
	$_SESSION["general_awareness"] = json_encode(array());
}
if(isset($_SESSION["userInformationSet"])){
	
	unset($_SESSION["userInformationSet"]);// = json_encode(array());
}
?>