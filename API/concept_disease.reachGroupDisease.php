<?php

include("../logic/common.inc.php");
//echo date('H:i:s',time())."<br>";
$db = getDB();
$disease_site_ids = array();
$resultToPage = array();
$diseaseDetails = array();
$resultToPage = array();
$related_groupSymps = json_decode($_SESSION["related_groupSymps"],true);
$concepts = json_decode($_SESSION["concepts"],true);
//echo date('H:i:s',time())."<br>";
$diseaseList = getDiseasesByGroupSymp($related_groupSymps);
//echo date('H:i:s',time())."<br>";
$diseaseRelatedSymp = getDiseaseRelatedSymp($diseaseList);
//echo date('H:i:s',time())."<br>";
$diseaseDetails = getDiseaseMarked($diseaseRelatedSymp);
//echo date('H:i:s',time())."<br>";
$disease_site_ids = getDiseaseSiteIds($diseaseDetails);
//echo date('H:i:s',time())."<br>";
$_SESSION["diseaseDetails"] = json_encode($diseaseDetails);
$_SESSION["disease_site_ids"] = json_encode($disease_site_ids);
echo json_encode($resultToPage);
/**
* getDiseasesByGroupSymp*
* 根据分组内的症状获取分组内的疾病列表
* @global $db 链接数据库的实例
* @param array $related_groupSymps
* @return array 
*/
function getDiseasesByGroupSymp($related_groupSymps){
	global $db;
	$group_diseases = array();
	foreach($related_groupSymps as $group_index =>$group_symps){
		if(count($group_symps)==0){
			$group_diseases[$group_index] = array();
			//$resultToPage[getWordStr($concepts[$group_index])] = 0;
			continue;
		}
		$sql = "SELECT DISTINCT(disease_site_id) FROM dis_symp WHERE symptom_site_id in ('".implode($group_symps,"','")."')";
		$result = $db->query($sql);
		while($row = $result->fetch_array()){
			$group_diseases[$group_index][] = $row["disease_site_id"];
		}
	}
	return $group_diseases;
}
/**
* getDiseaseRelatedSymp*
* 根据分组内的疾病列表，获取疾病关联症状。
* @global $db 链接数据库的实例
* @param array $related_groupSymps
* @return array 
*/
function getDiseaseRelatedSymp($diseaseList){
	global $db;
	$diseaseRelatedSymp = array();
	foreach($diseaseList as $group_index => $group_disease){
		$sql = "SELECT disease_site_id,symptom_site_id FROM dis_symp WHERE disease_site_id in ('".implode($group_disease,"','")."') order by disease_site_id";
		$result = $db->query($sql);
		while($row = $result->fetch_array()){
			$diseaseRelatedSymp[$group_index][$row["disease_site_id"]]["all_symp"][] = $row["symptom_site_id"];
		}
	}
	return $diseaseRelatedSymp;
}
/**
* getDiseaseMarked*
* 获取疾病在该分组内被描述的症状列表
* @global $concepts 存储concepts信息的数组
* @global $resultToPage 前台用来显示分组内疾病个数
* @global $related_groupSymps
* @param array $diseaseRelatedSymp
* @return array 
*/
function getDiseaseMarked($diseaseRelatedSymp){
	global $concepts,$resultToPage,$related_groupSymps;
	$diseaseDetails = array();
	foreach($diseaseRelatedSymp as $group_index =>$group_disease){
		foreach($group_disease as $disease_site_id => $diseaseInfo){
			$diseaseDetails[$group_index][$disease_site_id]["marked"] = array_values(array_intersect($diseaseInfo["all_symp"],$related_groupSymps[$group_index]));
			$diseaseDetails[$group_index][$disease_site_id]["all_symp"] = array_values(array_unique($diseaseInfo["all_symp"]));
		}
		$resultToPage[getWordStr($concepts[$group_index])] = count($diseaseDetails[$group_index]); //统计每个分组找到的疾病个数，组装返回前台页面的数据。
	}
	return $diseaseDetails;
}
/**
* getDiseaseMarked*
* 获取疾病分组的疾病ids
*
* @param array $diseaseDetails
* @return array 
*/
function getDiseaseSiteIds($diseaseDetails){
	$disease_site_ids = array();
	foreach($diseaseDetails as $group_index => $diseases){
		foreach($diseases as $disease_site_id => $diseaseInfo){
			$disease_site_ids[$group_index][] = $disease_site_id;
		}
	}
	return $disease_site_ids;
}
/**
* getWordStr*
* 获取组内word的名称组合
*
* @param array $group_concept
* @return array 
*/
//
function getWordStr($group_concept){
	$word_names = array();
	foreach($group_concept as $word_id => $wordObj){
		$word_names[] = $wordObj["word"];
	}
	return implode(',',$word_names);
}
?>