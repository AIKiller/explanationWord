<?php
include("../logic/common.inc.php");
$concepts = json_decode($_SESSION["concepts"],TRUE);
$unrelated_concepts = json_decode($_SESSION["unrelated_concepts"],TRUE);
//unset($_SESSION["unrelated_concepts"]);
$db = getDB();
//有关因子的变量声明
$concept_symptoms = array();
$concept_symptoms_number = array();
//无关因子的变量声明
$unrelated_concept_symptoms = array();
$unrelated_concept_symptoms_number = array();
$info_num = array();
//计算无关联因子的症状分组集合。
mergeGroupSymptom($unrelated_concepts);//匹配每个分组的症状列表
$unrelated_groupSymps = intersectionGroupSymp($unrelated_concepts);//计算每个分组症状的交集集合
$unrelated_total_symps = merge_unrelated_symptoms($unrelated_groupSymps);
$info_num["unrelated_symptoms_number"] = getSympNumberOfGroup($unrelated_concepts,$unrelated_groupSymps);
$_SESSION["unrelated_groupSymps"] = json_encode($unrelated_groupSymps);
$_SESSION["unrelated_concepts"] = json_encode($unrelated_concepts);


//计算关联因子的症状分组集合。
mergeGroupSymptom($concepts);//匹配每个分组的症状列表
$related_groupSymps = intersectionGroupSymp($concepts);//计算每个分组症状的交集集合
//将无关症状筛选出去
foreach($related_groupSymps as $group_id=>$groupSymp){

    $related_groupSymps[$group_id] = array_diff($groupSymp,$unrelated_total_symps);
}
$info_num["related_symptoms_number"] = getSympNumberOfGroup($concepts,$related_groupSymps);
$_SESSION["related_groupSymps"] = json_encode($related_groupSymps);
$_SESSION["concepts"] = json_encode($concepts);



echo $_SESSION["info_num"] = json_encode($info_num);


//获取每个分组的症状列表
function  mergeGroupSymptom(&$concepts ){
	foreach($concepts as $group_index => $group_concept){
		searchSymptom($concepts,$group_index);
	}
}
//计算每个分组下面关键词的症状集合。
function intersectionGroupSymp($concepts){
	$intersectionGroupSymps = array();
	foreach($concepts as $group_index => $group_concept){
		$intersectionGroupSymps[$group_index] = intersection($concepts,$group_index);
	}
	return $intersectionGroupSymps;
}
function mergeGroupSymp($concepts){
	$mergeGroupSymps = array();
	foreach($concepts as $group_index => $group_concept){
		$mergeGroupSymps[$group_index] = merge($concepts,$group_index);
	}
	return $mergeGroupSymps;
}
//获得每个分组下症状的个数
function  getSympNumberOfGroup($concepts,$groupSymps){
	$symptoms_number = array();
	foreach($concepts as $group_index => $group_concepts){
	
		$wordStr = getWordStr($group_concepts);
		$symptoms_number[$wordStr] = count($groupSymps[$group_index]);
	}
	return $symptoms_number;
}

//获取组内word的名称组合
function getWordStr($group_concept){

	
	$word_names = array();
	foreach($group_concept as $word_id => $wordObj){
	
		$word_names[] = $wordObj["word"];
	}
	return implode(',',$word_names);
}


//根据分组的concept 查找symptom的集合
function searchSymptom(&$words,$group_index){
	global $db;
	$group_words  = $words[$group_index];//获取每个分组的对象。
	foreach($group_words as $word_id => $wordObj){
		//根据word的同义词或近义词查询症状
		$word_union_symptoms = array();
		$concept_ids = getConcept_ids($wordObj);
		$sql_str = '("'.implode('","',$concept_ids).'")';
		$sql='SELECT concept_id,site_id FROM symp_concept WHERE concept_id in'.$sql_str;
		$result = $db->query($sql);
		while($row = $result->fetch_array()){
			$word_union_symptoms[]= $row["site_id"];
		}
		$word_union_symptoms = array_unique($word_union_symptoms);
		$words[$group_index][$word_id]["symptom_ids"] = $word_union_symptoms;
	}
}
//获得concept_id 的集合
function getConcept_ids($wordObj){
	$concept_ids = array();
	if($wordObj["type"] == "conjugate"){
		foreach($wordObj["concepts"] as $concept_id =>$concept){
			$concept_ids[] = $concept_id;
		}
		return $concept_ids;
	}
	if($wordObj["type"] == "synonym"){
		foreach($wordObj["concepts"] as $concept_id =>$concept){
			$concept_ids[] = $concept_id;
		}
		return $concept_ids;
	}
}
//二维多数组交叉取交集
function intersection($words,$group_index){
	if(count($words[$group_index])==1){
		return  $words[$group_index][key($words[$group_index])]["symptom_ids"];
	}else{
		
		$intersections = array();
		$intersections = $words[$group_index][key($words[$group_index])]["symptom_ids"];
		//print_r($intersections);
		while(next($words[$group_index])){
			
			$newWord_symptomIds = $words[$group_index][key($words[$group_index])]["symptom_ids"];
			
			//print_r($newWord_symptomIds);
			
			$intersections = array_intersect($intersections,$newWord_symptomIds);
			
		}
		return array_values($intersections);
	}
}
//二维多数组交叉取并集
function merge($words,$group_index){
	if(count($words[$group_index])==1){
		return  $words[$group_index][key($words[$group_index])]["symptom_ids"];
	}else{
		
		$merges = array();
		$merges = $words[$group_index][key($words[$group_index])]["symptom_ids"];
		//print_r($intersections);
		while(next($words[$group_index])){
			
			$newWord_symptomIds = $words[$group_index][key($words[$group_index])]["symptom_ids"];
			
			//print_r($newWord_symptomIds);
			
			$merges = array_merge($merges,$newWord_symptomIds);
			
		}
		return array_values(array_unique($merges));
	}
}

function merge_unrelated_symptoms($related_groupSymps){
    if(count($related_groupSymps)<1){
        return array();
    }else if(count($related_groupSymps)==1){
        return $related_groupSymps[0];
    }else{

        $merges = $related_groupSymps[0];
        while(next($related_groupSymps)){

            $newSymp =$related_groupSymps[key($related_groupSymps)];

            $merges = array_merge($merges,$newSymp);
        }
        return array_values(array_unique($merges));
    }
}
?>