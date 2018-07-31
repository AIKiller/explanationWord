<?php
include("../logic/common.inc.php");
$db = getDB();
$sex = 'f';//默认值
if(isset($_POST["sex"])){
	$sex = $_POST["sex"];
}
$healthScore=array();
$sql = "SELECT * FROM `healthcondition` WHERE `sex`=\"$sex\"";
//echo $sql;
$result=$db->query($sql);
$i=0;
while($row=$result->fetch_array()){
	$healthScore[$i]=intval($row["healthCondition"]);
	$i++;
}
$num=array_sum($healthScore);
$len=count($healthScore);
echo intval($num/$len);

?>