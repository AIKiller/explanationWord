<?php
include("../logic/common.inc.php");
$db = getDB();
$finalDisease = json_decode($_SESSION["finalDisease"],TRUE);//最终疾病数组
if(count($finalDisease) == 0){//如果最最终疾病的数组为空，则退出脚本
	exit;
}
$concepts = json_decode($_SESSION["concepts"],TRUE);
	
$concept_site_ids = getConcept_site_ids($concepts);

	
$diseaseDetails = json_decode($_SESSION["diseaseDetails"],TRUE);//疾病的详情（关联症状。已被描述的症状）
	
$finalDiseaseRelatedSymp = getDiseaseRelatedSymp($finalDisease);

$groupConceptIncremental = getGroupConceptIncremental($finalDiseaseRelatedSymp);



/**
* getDiseaseDetails*
* 获取最终疾病的详情（包括all_symp 疾病的关联症状个数 每个症状关联的concept）
* @global $diseaseDetails 每个分组下的疾病列表和疾病信息（all_symp和marked）
* @param array String $finalDisease
* @return array 
*/
function getDiseaseRelatedSymp($finalDisease){
	global $diseaseDetails;
	$firstDiseaseDetail = current($diseaseDetails);
	$diseaseRelatedSymp = array();
	foreach($finalDisease as $disease_site_id){
		$diseaseRelatedSymp[$disease_site_id] = $firstDiseaseDetail[$disease_site_id]["all_symp"];
	}
	return $diseaseRelatedSymp;
}
/**
* getGroupConceptIncremental*
* 获取分组下每个concept组合的扩展单词
* @global 
* @param array String $finalDiseaseRelatedSymp
* @return array 
*/
function getGroupConceptIncremental($finalDiseaseRelatedSymp){
	$groupConceptIncrement = array();
	foreach($finalDiseaseRelatedSymp as $disease_site_id => $symps){
		foreach($symps as $symptom_site_id){
			$sympRelatedConcept = getSympRelatedConcept($symptom_site_id);
			compareConcept($sympRelatedConcept);
		}
	
	}

}

function getSympRelatedConcept($symptom_site_id){
	global $db;
	$sympRelatedConcept = array();
	$sql ="SELECT concept_id FROM symp_concept WHERE site_id = '".$symptom_site_id."'";
	$result = $db->query($sql);
	while($row = $result->fetch_array()){
		$sympRelatedConcept[] = $row["concept_id"];
	}
	return $sympRelatedConcept;
}


function compareConcept($sympRelatedConcept){
	global $concept_site_ids;
	foreach($concept_site_ids as $group_index => $group_concepts){
		$offestArray =array_intersect($group_concepts,$sympRelatedConcept);		
		if(count($offestArray) == count($group_concepts)){
			return $offestArray;
		}
	}
}
function getConcept_site_ids($concepts){
	$concept_site_ids = array();
	foreach($concepts as $group_index => $group_concepts){
		foreach($group_concepts as $concept_site_id => $conceptInfo){
			$concept_site_ids[$group_index][] = $concept_site_id;
		}
	}
	return $concept_site_ids;
}

exit;

$incremental_concepts = array();
$related_groupSymps = json_decode($_SESSION["related_groupSymps"],TRUE);
//unset($_SESSION["concept_symptoms"]);
//print_r($concept_symptoms);
$concepts = json_decode($_SESSION["concepts"],TRUE);
//print_r($concepts);
//print_r(json_decode($_SESSION["symptom_diseases"]));
foreach ($related_groupSymps as $index => $symptoms) {
	$concept_ids = array();
	if(count($symptoms)==0){
		$incremental_concepts[] = array();
		continue;
	}
	$sql_str = '("'.implode($symptoms,'","').'")';//拼接数组
	echo $sql = 'SELECT count(concept_id) AS num ,concept_id FROM symp_concept WHERE site_id in'.$sql_str.' GROUP BY concept_id';//在剩余的症状中查找concept的数目
	$result = $db->query($sql);
	if($result){
		while ($row = $result->fetch_array()) {
			//print_r($concepts[$index]);
			//echo $concepts[$index][$row["concept_id"]];
			if(!is_existWord($index,$row["concept_id"])){
				$concept_ids[getConceptName($row["concept_id"])] = $row["num"];//载入数组
			}
		}
		arsort($concept_ids);//对数组进行排序。
		//print_r($concept_ids);
		$incremental_concepts[] = $concept_ids;
	}
}
echo json_encode($incremental_concepts);
/**
* 根据concept_id获取concept的名称
* @param string $concep_id
* @return string keyword
*/
function getConceptName($concept_id){
	global $db;
	$sql='SELECT keyword FROM concept WHERE concept_id="'.$concept_id.'"';
	$result = $db->query($sql);
	if($result){
		$row = $result->fetch_array();
		return $row["keyword"];
	}
}
function is_existWord($group_index,$concept_id){
	global $concepts;
	foreach($concepts[$group_index] as $word_id => $wordInfo){
		if(trim($word_id) == trim($concept_id)){
			return true;
		}else if(isset($wordInfo["concepts"][$concept_id])){
			return true;
		}
	}
	return false;
}
?>