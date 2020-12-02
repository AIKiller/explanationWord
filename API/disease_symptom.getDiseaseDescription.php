<?php
include("../logic/common.inc.php");
$db = getDB();
$disease_site_id = trim($_GET["site_id"]);
$symptom_information = array();

$finalDiesease = json_decode($_SESSION["finalDieseaseDetails"],TRUE);


$diseaseInfo = getDiseaseInfo($disease_site_id);

echo json_encode($diseaseInfo);

function getDiseaseInfo($disease_site_id){
	global $finalDiesease,$option;
	$diseaseInfo = array();
	$diseaseInfo = getICDandICPC($disease_site_id);
	$diseaseInfo["ageInformation"] = getAgeInformation($disease_site_id);
	$diseaseInfo["all_symp"] = getSympInfo($finalDiesease[$disease_site_id]);
	//print_r($finalDiesease[$disease_site_id]);
	return $diseaseInfo;
}


function getICDandICPC($disease_site_id){
	global $db;
	$sql="SELECT icd,icpc,name_decode FROM disease WHERE site_id ='".$disease_site_id."'";
	$result = $db->query($sql);
	$row = $result->fetch_array();
	return array(
		"icd"=>$row["icd"],
		"icpc"=>$row["icpc"],
        "diseaseName"=>$row["name_decode"]
	);
}
function getSympInfo($sympInfo){
	global $db;
	//$sympInfoToPage = $sympInfo["all_symp"];
	$sql ="SELECT symp_concept.site_id,concept.concept_id,keyword,name_decode FROM concept,symp_concept,symptom WHERE symp_concept.site_id in ('".implode($sympInfo["all_symp"],"','")."') AND symp_concept.site_id = symptom.site_id AND  symp_concept.concept_id = concept.concept_id AND concept.is_del >0 order by site_id";
	$result = $db->query($sql);
	while($row = $result->fetch_array()){
		if(in_array($row["site_id"],$sympInfo["marked"])){
			$sympInfoToPage[$row["site_id"]]["flag"] = 1;
		}else{
			$sympInfoToPage[$row["site_id"]]["flag"] = 0;
		}
		$sympInfoToPage[$row["site_id"]]["concepts"][$row["concept_id"]] =  $row["keyword"];
		$sympInfoToPage[$row["site_id"]]["name"] =  $row["name_decode"];
	}
	//如果查询到的数组小于预定数组，则单独查询一遍，防止遗漏症状
	//echo count($sympInfoToPage);
	if(count($sympInfoToPage)< count($sympInfo["all_symp"])){
		$sql ="SELECT site_id,name_decode FROM symptom WHERE site_id in ('".implode($sympInfo["all_symp"],"','")."')";
		$result = $db->query($sql);
		while($row = $result->fetch_array()){
			if(!isset($sympInfoToPage[$row["site_id"]])){
				$sympInfoToPage[$row["site_id"]]["flag"] = 0;
				$sympInfoToPage[$row["site_id"]]["concepts"] = array();
				$sympInfoToPage[$row["site_id"]]["name"] =  $row["name_decode"];
			}
		}
	}
	
	return diySort($sympInfoToPage);
}

function getICPCByDisease_site_id($disease_site_id){
	global $db;
	$diseaseInformations = array();
	$sql='SELECT ICPC,rf FROM disease WHERE site_id = "'.$disease_site_id.'"';
	$result = $db->query($sql)->fetch_array();
	$diseaseInformations["ICPC"] = $result["ICPC"];
	$diseaseInformations["rf"] = $result["rf"];
	return $diseaseInformations;

}
//根据disease_site_id查找该疾病的年龄信息
function getAgeInformation($disease_site_id){
	global $db;
	$diseaseInformations = getICPCByDisease_site_id($disease_site_id);
	$ageInformations = array();
	$sql="SELECT * FROM disease_agelevel WHERE site_id='".$diseaseInformations["ICPC"]."'order by auto_id";
	$result = $db->query($sql);
	if($result->num_rows){
		while($row = $result->fetch_array()){
			if($diseaseInformations["rf"]>1){
				$ageInformations[] = round($row["prev"]/$diseaseInformations["rf"],5);
			}else{
				$ageInformations[] = round($row["prev"],5);
			}
		}
	}else{
		return -1;
	}
	//$maxAge = max($ageInformations);
	//print_R($ageInformations);
	//foreach($ageInformations as $index=>$ageInfo){
	//	$ageInformations[$index] = round($ageInfo/$maxAge,2);
	//}
	//print_R($ageInformations);
	return $ageInformations;
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