<?php
include("../logic/common.inc.php");
$db = getDB();
$finalDisease = json_decode($_SESSION["finalDisease"],TRUE);//最终疾病数组
$globalConceptName = array();
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
# 去一下重复的症状id
$symptom_site_id_Array = array_keys(array_flip($symptom_site_id_Array));

$returnConceptArray = getGroupConceptIncremental_a($symptom_site_id_Array);
$groupConceptIncremental = getGroupConceptIncremental_b($returnConceptArray);
$groupConceptIncremental = getConceptsName($groupConceptIncremental);

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

function getGroupConceptIncremental($finalDiseaseRelatedSymp){
	
	$symptom_site_id_Array = array();
	foreach($finalDiseaseRelatedSymp as $disease_site_id => $symps){								//以pain为例，找到$finalDiseaseRelatedSymp一共549个，循环549次
	
		foreach($symps as $symptom_site_id){	
			$symptom_site_id_Array[] = $symptom_site_id;
		}
	
	}
	return $symptom_site_id_Array;
}
function getGroupConceptIncremental_a($symptom_site_ids){
    $returnArray = array();
    $sympRelatedConcepts = getSympRelatedConcept($symptom_site_ids);
    // print_r($sympRelatedConcepts);
    foreach ($symptom_site_ids as $symptom_site_id){
        if(isset($sympRelatedConcepts[$symptom_site_id])){
            $returnArray[] = compareConcept($sympRelatedConcepts[$symptom_site_id]);
        }
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
					if(isset($groupConceptIncrement[$group_index][$concept_id])){
						$groupConceptIncrement[$group_index][$concept_id] += 1;
					}else{
						$groupConceptIncrement[$group_index][$concept_id] = 1;
					}
				}
				if(isset($groupConceptIncrement[$group_index])){
					arsort($groupConceptIncrement[$group_index]);
				}
			}
		}
	}
	return 	$groupConceptIncrement;
}
function getConceptsName($groupConceptIncremental){
	global $db,$globalConceptName;
	$resultArray = array();
	foreach ($groupConceptIncremental as $conceptIncremental){
        // 获取到key的数组，一次性查询 当前分组下的所有concept信息
        $concept_ids = array_keys($conceptIncremental);
        $query_concept_ids = '("'.implode($concept_ids,'","').'")';//拼接数组
        $sql="SELECT concept_id,keyword FROM concept WHERE concept_id in ".$query_concept_ids." AND is_del >0";
        $result = $db->query($sql);
        if($result->num_rows > 0){
            while ($row = $result->fetch_array()){
                $globalConceptName[$row["concept_id"]] = $row["keyword"];
            }
        }
        // 重新覆盖
        $tempConceptIncremental = array();
        foreach ($conceptIncremental as $concept_id => $term){
            if(isset($globalConceptName[$concept_id])){
                $tempConceptIncremental[$globalConceptName[$concept_id]] = $term;
            }
        }
        $resultArray[] = $tempConceptIncremental;
    }
	return $resultArray;
}
/*	应该是取出来所有症状对应的疾病	*/
function getSympRelatedConcept($symptom_site_ids){
	global $db;
    $query_symptom_site_ids = '("'.implode($symptom_site_ids,'","').'")';//拼接数组
	$sympRelatedConcept = array();
	$sql ="SELECT site_id,concept_id FROM symp_concept WHERE site_id in ".$query_symptom_site_ids;
	$result = $db->query($sql);
	while($row = $result->fetch_array()){
		$sympRelatedConcept[$row["site_id"]][] = $row["concept_id"];
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