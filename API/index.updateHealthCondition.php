<?php
include("../logic/common.inc.php");
$db = getDB();
//设置为中国的标准时间
date_default_timezone_set('PRC');  
$healthCondition="45";
$sex = 'f';
if(isset($_GET["healthCondition"])&&$_GET["healthCondition"]!=0){
	$healthCondition=$_GET["healthCondition"];
}
if(isset($_GET["sex"])&&$_GET["sex"]!=""){
	$sex = $_GET["sex"];
}
$sql = "INSERT INTO `healthcondition` (`sex`, `healthCondition`, `date`) VALUES ('$sex', '$healthCondition', '".date('Y-m-d   H:i:s')."');";
$result = $db->query($sql);
if($result){
	echo  "1";
	exit;
}
echo  "0";
?>