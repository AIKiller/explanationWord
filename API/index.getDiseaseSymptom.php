<?php
include("../logic/common.inc.php");
$db = getDB();
$finalDiseaseDetailSet  = json_decode($_SESSION["finalDieseaseDetails"],true);
$finalDiseaseToPage = json_decode($_SESSION["finalDieseaseToPage"],true);
$feedback_info = array();

foreach($finalDiseaseToPage as $index => $diseaseDetails){
	$feedback_info[$index] = getICDandICPC($diseaseDetails["disease_site_id"]);
	$feedback_info[$index]["name"] = $diseaseDetails["disease_name"].' '.$diseaseDetails["percentage"].'%';
	$feedback_info[$index]["prev_rf"] = $diseaseDetails["prev_rf"];
	# $feedback_info[$index]["comb"] = $diseaseDetails["comb"];
	$feedback_info[$index]["symps"] = getSympInfo($finalDiseaseDetailSet[$diseaseDetails["disease_site_id"]]);
}
echo $_SESSION["feedback_info"] = json_encode($feedback_info);

//获取ICPC和ICD
function getICDandICPC($disease_site_id){
	global $db;
	$sql="SELECT icd,icpc FROM disease WHERE site_id ='".$disease_site_id."'";
	$result = $db->query($sql);
	$row = $result->fetch_array();
	return array(
		"icd"=>$row["icd"],
		"icpc"=>$row["icpc"]
	);
}
//获取疾病关联症状的信息
function getSympInfo($sympInfo){
	global $db;
	//$sympInfoToPage = $sympInfo["all_symp"];
	$sql ="SELECT site_id,name_decode FROM symptom WHERE site_id in ('".implode($sympInfo["all_symp"],"','")."')";
	$result = $db->query($sql);
	while($row = $result->fetch_array()){
		if(in_array($row["site_id"],$sympInfo["marked"])){
			$sympInfoToPage[$row["site_id"]]["flag"] = 1;
		}else{
			$sympInfoToPage[$row["site_id"]]["flag"] = 0;
		}
		$sympInfoToPage[$row["site_id"]]["name"] =  $row["name_decode"];
	}
	return diySort($sympInfoToPage);
}
//自定义多维数组排序
function diySort($symptom_information){
	$descript_array = array();
	$undescript_array = array();
	$description = array();
	foreach($symptom_information as $symptom_site_id => $group){
		if($symptom_information[$symptom_site_id]["flag"]==1){
			$descript_array[$symptom_site_id] = $group;
		}else{
			$undescript_array[$symptom_site_id] = $group;
		}
	}
	return array_merge($descript_array,$undescript_array,$description);
}
?>