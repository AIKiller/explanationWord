<?php
include("../logic/common.inc.php");
$db = getDB();
$finalDisease = json_decode($_SESSION["finalDisease"],TRUE);//最终疾病数组

if(count($finalDisease) == 0){//如果最最终疾病的数组为空，则退出脚本
	echo json_encode(array());
	exit;
}
$concepts = json_decode($_SESSION["concepts"],TRUE);

$concept_site_ids = getConcept_site_ids($concepts);


$diseaseDetails = json_decode($_SESSION["diseaseDetails"],TRUE);//疾病的详情（关联症状。已被描述的症状）
	
$finalDiseaseRelatedSymp = getDiseaseRelatedSymp($finalDisease);
	
//$groupConceptIncremental = getGroupConceptIncremental($finalDiseaseRelatedSymp);

$symptom_site_id_Array = getGroupConceptIncremental($finalDiseaseRelatedSymp);
$returnConceptArray = getGroupConceptIncremental_a($symptom_site_id_Array);

$groupConceptIncremental = getGroupConceptIncremental_b($returnConceptArray);

echo json_encode($groupConceptIncremental);			//输出到前台的，不是测试的，别删
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
/*
function getGroupConceptIncremental($finalDiseaseRelatedSymp){
	global $concept_site_ids;
	$groupConceptIncrement = array();
	
	foreach($finalDiseaseRelatedSymp as $disease_site_id => $symps){								//以pain为例，找到$finalDiseaseRelatedSymp一共549个，循环549次
	
		foreach($symps as $symptom_site_id){														//循环每个$finalDiseaseRelatedSymp的值,循环次数1到70不等，我靠！
			$sympRelatedConcept = getSympRelatedConcept($symptom_site_id);							//取出来所有症状对应的疾病，又是每个数组（ps：一个症状对应多个疾病）
			$returnArray = compareConcept($sympRelatedConcept);										//去掉不包括C_1的，并且把包含C_1 的里面的C_1
			//echo json_encode($returnArray);

			if(count($returnArray)>0){
				foreach($returnArray as $group_index => $conceptIncrement){
					foreach($conceptIncrement as $concept_id){
						if(isset($concept_site_ids[$group_index][$concept_id])) continue;
						$conceptName = getConceptName($concept_id);
						
						if(isset($groupConceptIncrement[$group_index][$conceptName ])){
						
							$groupConceptIncrement[$group_index][$conceptName] += 1;
						
						}else{
						
							$groupConceptIncrement[$group_index][$conceptName] = 1;
						
						}
					
					}
					arsort($groupConceptIncrement[$group_index]);
				}
			
			}
		}
	
	}
	return $groupConceptIncrement;
}  
*/
function getGroupConceptIncremental($finalDiseaseRelatedSymp){
	
	$symptom_site_id_Array = array();
	foreach($finalDiseaseRelatedSymp as $disease_site_id => $symps){								//以pain为例，找到$finalDiseaseRelatedSymp一共549个，循环549次
	
		foreach($symps as $symptom_site_id){	
			$symptom_site_id_Array[] = $symptom_site_id;
		}
	
	}
	return $symptom_site_id_Array;
}
function getGroupConceptIncremental_a($symptom_site_id)
{
	global $concept_site_ids;
	
	for($i=0;$i < count($symptom_site_id);$i++){														//循环每个$finalDiseaseRelatedSymp的值,循环次数1到70不等，我靠！
		$sympRelatedConcept = getSympRelatedConcept($symptom_site_id[$i]);							//取出来所有症状对应的疾病，又是每个数组（ps：一个症状对应多个疾病）
		$returnArray[] = compareConcept($sympRelatedConcept);										//去掉不包括C_1的，并且把包含C_1 的里面的C_1
		}
	return $returnArray;
}
function getGroupConceptIncremental_b($returnConceptArray)
{
	$groupConceptIncrement = array();
	for($i=0;$i < count($returnConceptArray);$i++){
	if(count($returnConceptArray[$i])>0){
			foreach($returnConceptArray[$i] as $group_index => $conceptIncrement){
				foreach($conceptIncrement as $concept_id){					
					if(isset($concept_site_ids[$group_index][$concept_id])) continue;
					$conceptName = getConceptName($concept_id);
					//echo $conceptName."->".$concept_id;
					if($conceptName === false) continue;
					
					if(isset($groupConceptIncrement[$group_index][$conceptName])){
					
						$groupConceptIncrement[$group_index][$conceptName] += 1;
					
					}else{
					
						$groupConceptIncrement[$group_index][$conceptName] = 1;
					
					}
					//echo "<pre>";
					//print_r($groupConceptIncrement);
					//echo "</pre>";
				}
				if(isset($groupConceptIncrement[$group_index])){
					arsort($groupConceptIncrement[$group_index]);
				}
			}
		}
	}
	return 	$groupConceptIncrement;
}
function getConceptName($concept_id){
	global $db;
	$sql='SELECT keyword FROM concept WHERE concept_id="'.$concept_id.'" AND is_del >0';
	$result = $db->query($sql);
	if($result->num_rows){
		$row = $result->fetch_array();
		return $row["keyword"];
	}else{
		return false;
	}
}
/*	应该是取出来所有症状对应的疾病	*/
function getSympRelatedConcept($symptom_site_id){
	global $db;
	$sympRelatedConcept = array();
	echo $sql ="SELECT concept_id FROM symp_concept WHERE site_id = '".$symptom_site_id."'";
	$result = $db->query($sql);
	while($row = $result->fetch_array()){
		$sympRelatedConcept[] = $row["concept_id"];
	}
	return $sympRelatedConcept;
}

/*		这个又是什么鬼	真没看出来		*/
function compareConcept($sympRelatedConcept){
	global $concept_site_ids;					//C_1
	$sympRelatedConceptInremental = array();
	foreach($concept_site_ids as $group_index => $group_concepts){
		$intersectArray =array_intersect($group_concepts,$sympRelatedConcept);		//取交集
		if(count($intersectArray) == count($group_concepts)){
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
				$concept_site_ids[$group_index][] = $concept_id;
			}
		}
	}
	return $concept_site_ids;
}
?>