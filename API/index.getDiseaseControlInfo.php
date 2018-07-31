<?php
include("../logic/common.inc.php");
$db = getDB();
$minprev_rf = 10;
$maxprev_rf = 0;
$finalDiseaseSet  = json_decode($_SESSION["finalDisease"],true);
//$finalDiseaseToPage = json_decode($_SESSION["finalDieseaseToPage"],true);
$icpcClass = array("a"=>1,"b"=>2,"d"=>3,"f"=>4,"h"=>5,"k"=>6,"l"=>7,"n"=>8,"p"=>9,"r"=>10,"s"=>11,"t"=>12,"u"=>13,"w"=>14,"x"=>15,"y"=>16,"z"=>17);

$allDiseases = getDiseaseControlInfo();
$foundDiseases = getFoundDiseaseControlInfo();
$data[] = $allDiseases;
$data[] = $foundDiseases;

$result = array(
			"minprev_rf" =>$minprev_rf,
			"maxprev_rf" =>$maxprev_rf,
			"data"=>$data
			);
echo json_encode($result);
//echo "<pre>";
//print_r($foundDiseases);
//echo "</pre>";
function getDiseaseControlInfo(){
	global $db,$icpcClass;
	$diseaseList = array();
	$sql="SELECT auto_id,ICPC,prev_rf FROM disease WHERE 1";
	$result = $db->query($sql);
	while($row = $result->fetch_array()){
		$temp = array();
		if($row["ICPC"] != "?"||$row["prev_rf"]!=0){//为了过滤没有ICPC的疾病
			$temp[] = $row["auto_id"];
			$temp[] = $icpcClass[strtolower(substr($row["ICPC"],0,1))];
			$temp[] = "all diseases";
			//$temp[] = 2;//设置默认的长度。
			
			$diseaseList[] = $temp;
		}
	}
	return $diseaseList;
}
function getFoundDiseaseControlInfo(){
	global $db,$finalDiseaseSet,$icpcClass,$minprev_rf,$maxprev_rf;
	$queryStr = "'".implode("','",$finalDiseaseSet)."'";
	$sql = "SELECT auto_id,ICPC,prev_rf FROM disease WHERE site_id in (".$queryStr.")";
	$diseaseList = array();
	$result = $db->query($sql);
	while($row = $result->fetch_array()){
		$temp = array();
		if($row["ICPC"] != "?"&&$row["prev_rf"]!=0){//为了过滤没有ICPC的疾病
			$temp[] = $row["auto_id"];
			$temp[] = $icpcClass[strtolower(substr($row["ICPC"],0,1))];
			$temp[] = "found diseases";
			$temp[] = floatval($row["prev_rf"]);
			$diseaseList[] = $temp;
			//获取最大值，最小值；
			if($row["prev_rf"]>$maxprev_rf){
				$maxprev_rf = $row["prev_rf"];
			}
			if($row["prev_rf"]<$minprev_rf){
				$minprev_rf = $row["prev_rf"];
			}
		}
	}
	return $diseaseList;
}

?>