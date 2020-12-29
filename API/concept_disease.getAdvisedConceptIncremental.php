<?php
include("../logic/common.inc.php");
$db = getDB();
$finalDisease = json_decode($_SESSION["finalDisease"],TRUE);//最终疾病数组
if(count($finalDisease) == 0){//如果最最终疾病的数组为空，则退出脚本
	echo json_encode(array());
	exit;
}
$concepts = json_decode($_SESSION["concepts"],TRUE);
// 获取concept集合
$concept_site_ids = getConcept_site_ids($concepts);

$diseaseDetails = json_decode($_SESSION["diseaseDetails"],TRUE);//疾病的详情（关联症状。已被描述的症状）
// 根据最终疾病获取症状合集
$SymptomSet = getSymptomSet($finalDisease);
// 获取建议的concept列表
$advisedConceptIncremental = getAdvisedConceptIncremental($SymptomSet);
//echo "<pre>";
//print_r($advisedConceptIncremental);
//echo "</pre>";
echo json_encode($advisedConceptIncremental);

/**
* getDiseaseDetails*
* 获取最终疾病的详情（包括all_symp 疾病的关联症状个数 每个症状关联的concept）
* @global $diseaseDetails 每个分组下的疾病列表和疾病信息（all_symp和marked）
* @param array String $finalDisease
* @return array 
*/
function getSymptomSet($finalDisease){
	global $diseaseDetails;
	$firstDiseaseDetail = current($diseaseDetails);
	$symptomSet = array();
	foreach($finalDisease as $disease_site_id){
		$symptomSet = array_merge($symptomSet,$firstDiseaseDetail[$disease_site_id]["all_symp"]);
	}
	return array_values(array_unique($symptomSet));
}
/**
* getGroupConceptIncremental*
* 获取分组下每个concept组合的扩展单词
* @global 
* @param array String $finalDiseaseRelatedSymp
* @return array 
*/
function getAdvisedConceptIncremental($SymptomSet){
	global $concept_site_ids;
	$advisedConceptIncremental = array();
	$sympsRelatedConcepts = getSympsRelatedConcepts($SymptomSet);
    $conceptsName = getConceptsName($sympsRelatedConcepts);
	// 循环取值
	foreach($sympsRelatedConcepts as $concept_site_id => $num){
		if(!in_array($concept_site_id,$concept_site_ids)){
			$advisedConceptIncremental[$conceptsName[$concept_site_id]] = $num;
		}
	}
	
	arsort($advisedConceptIncremental);	

	return $advisedConceptIncremental;
}
function getConceptsName($sympsRelatedConcepts){
	global $db;
    // 组装查询条件
    $conceptsName = array();
    $advisedConceptIds = array_keys($sympsRelatedConcepts);
    $query_advisedConceptIds = "('".implode($advisedConceptIds,"','")."')";
	$sql='SELECT concept_id,keyword FROM concept WHERE concept_id in '.$query_advisedConceptIds.' AND is_del > 0';
	$result = $db->query($sql);
	if($result && $result->num_rows > 0){
		while ($row = $result->fetch_array()){
            $conceptsName[$row['concept_id']] = $row['keyword'];

        }
	}
    return $conceptsName;
}
function getSympsRelatedConcepts($symptom_site_ids){
	global $db;
	$sympRelatedConcept = array();
	$sql ="SELECT COUNT(concept_id)AS num,concept_id FROM symp_concept WHERE site_id in ('".implode($symptom_site_ids,"','")."') group by concept_id";
	$result = $db->query($sql);
	while($row = $result->fetch_array()){
		$sympRelatedConcept[$row["concept_id"]] = $row["num"];
	}
	return $sympRelatedConcept;
}


function compareConcept($sympRelatedConcept){
	global $concept_site_ids;
	$sympRelatedConceptInremental = array();
	foreach($concept_site_ids as $group_index => $group_concepts){
		$intersectArray =array_intersect($group_concepts,$sympRelatedConcept);		
		if(count($intersectArray) != count($group_concepts)){
			$offestArray = array_diff($sympRelatedConcept,$group_concepts);
			if(count($offestArray)>0){
			
				$sympRelatedConceptInremental[$group_index] = $offestArray;
			
			}
		}
	}
	return $sympRelatedConceptInremental;
}
function getConcept_site_ids($concepts){
	$concept_site_ids = array();
	foreach($concepts as $group_index => $group_concepts){
		foreach($group_concepts as $concept_site_id => $conceptInfo){
			foreach($conceptInfo["concepts"] as $concept_id => $concept){
				$concept_site_ids[] = $concept_id;
			}
		}
	}
	return $concept_site_ids;
}
?>