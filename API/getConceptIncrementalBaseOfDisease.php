<?php
	
/**
* 根据finalDisease分组获取用户选择的concept的增量集合
* @param array $finalDisease
* @return array $groupIncremental
*/
function getGroupIncremental($finalDisease){
	global $db;
	$diseases_symptoms = array();
	$sql_str = '("'.implode($finalDisease,'","').'")';//拼接数组
	$sql='SELECT disease_site_id,symptom_site_id FROM dis_symp WHERE disease_site_id in'.$sql_str;
	$result = $db->query($sql);
	if($result){
		while ($row = $result->fetch_array()) {
			$diseases_symptoms[$row["disease_site_id"]][] = getSympRelationConcepts($row["symptom_site_id"]);//载入数组
		}
	}
	//print_R($diseases_symptoms);
}
/**
* 根据symptom_site_id获得每个症状的关联concept的名字
* @param array $symptom_site_id
* @return void
*/
function getSympRelationConcepts($symptom_site_id){
	global $db;
	$relationConcepts = array();
	$sql = 'SELECT concept.concept_id AS concept_id,keyword FROM concept INNER JOIN symp_concept ON concept.concept_id = symp_concept.concept_id AND site_id="'.$symptom_site_id.'"';
	$result = $db->query($sql);
	if($result){
		while ($row = $result->fetch_array()) {
			$relationConcepts[$row["concept_id"]] = $row["keyword"];
		}
		//echo "<pre>";
		//print_r($relationConcepts);
		//echo "</pre>";
		setGroupIncremental($relationConcepts);
	}
	//$res[$symptom_site_id] = $relationConcepts;
	//return $res;
}
/**
* 根据每个症状的关联concept去更新用户输入的concept的增量列表
* @param array $symptom_site_id
* @return void
*/
function setGroupIncremental($relationConcepts){
	global $groupIncremental,$concepts;
	$diff = array();
	//首先循环取用户输入的分组concept
	foreach($concepts as $index => $group_concept){
		//获得分组的长度
		$_groupCount = count($group_concept); 
		if($_groupCount == concept_array_intersect($relationConcepts,$group_concept)){
			$diff = concept_array_diff($relationConcepts,$group_concept);
			foreach($diff as $concept){
				if(isset($groupIncremental[$index][$concept])&&searchWord($concept)){
					$groupIncremental[$index][$concept]++;
				}else{
					$groupIncremental[$index][$concept]=1;
				}
			}
		}
	}
}
function searchWord($incrementalWord){
	global $concepts;
	foreach($concepts as $group => $group_word){
		foreach($group_word as $word_id => $wordArr){
			if($wordArr["word"] == $incrementalWord){
				return false;
			}
		}
	}
	return true;
}

//一个单词可能会有多个同义词，所以同义词之间的关系为或。 自定义取交集
function concept_array_intersect($relationConcepts,$group_concept){
	$intersect = array();
	$num = 0;
	foreach($group_concept as $word_id => $word){
		if(isset($word["synonym"])&&count($word["synonym"])>0){//该单词存在近义词且数量大于1 
			$intersect = array_intersect($relationConcepts,$word["synonym"]);
			if(count($intersect)>0){
				$num++;
			}
		}else{
			$intersect = array_intersect($relationConcepts,$word["conjugate"]);
			if(count($intersect)>0){
				$num++;
			}
		}
	
	}
	return $num;
}
//自定义取差集
function concept_array_diff($diff,$group_concept){
	$diffArr = array();
	//取每个分组与relationConcepts的差集
	$concept = array();
	foreach($group_concept as $word_id => $word){
		if(isset($word["synonym"])&&count($word["synonym"])>0){//该单词存在近义词且数量大于1 
			$diff = array_diff($diff,$word["synonym"]);
			if(count($diff)>0){
				$diffArr = $diff;
			}
		}else{
			$diff = array_diff($diff,$word["conjugate"]);
			if(count($diff)>0){
				$diffArr = $diff;
			}
		}
	}
	return $diffArr;
}
function DiyArsort($groupIncremental){
	foreach($groupIncremental as $index=>$group){
		arsort($group);
		$groupIncremental[$index] = $group;
	}
	return $groupIncremental;

}
?>