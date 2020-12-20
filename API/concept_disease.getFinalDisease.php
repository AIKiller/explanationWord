<?php
include("../logic/common.inc.php");
//include("getConceptIncrementalBaseOfDisease.php");
//链接数据库
$db = getDB();
$finalDiseaseSet = array();
$finalDiseaseDetailSet = array();
$finalDiseaseToPage = array();
//获取session数据
$diseaseDetails = json_decode($_SESSION["diseaseDetails"],TRUE);
$concepts = json_decode($_SESSION["concepts"],TRUE);
$disease_site_ids = json_decode($_SESSION["disease_site_ids"],TRUE);
//不可能的疾病（一维数组）
if(isset($_SESSION["general_awareness"])){
    $general_awareness = json_decode($_SESSION["general_awareness"],TRUE);
}else{
    $general_awareness = [];
}

if(isset($_SESSION["multi-morbidity"])&&$_SESSION["multi-morbidity"] == 2){
	$_SESSION["multi-morbidity"] = 1;//用来标记疾病结果是否为零 需要循环
}else if(isset($_SESSION["multi-morbidity"])&&$_SESSION["multi-morbidity"] == 3){
	$_SESSION["multi-morbidity"] = 3;//用来标记疾病结果是否为零 需要循环
}else{
	$_SESSION["multi-morbidity"] = 0;//用来标记疾病结果是否为零
}

$concept_ids = getWordConcept_ids($concepts);
//unset($_SESSION["disease_site_ids"]);
//print_r($disease_site_ids);

$finalDiseaseSet = intersectionDiseaseSet($disease_site_ids);//获取最终疾病的集合

$_SESSION["related_finalDisease"] = json_encode($finalDiseaseSet);

echo "1";

//获取分组疾病的id集合。
function intersectionDiseaseSet($disease_site_ids){
	//print_r($disease_site_ids);
	$finalDiseaseSet = array();
	reset($disease_site_ids);//设置数组指向数组首部位置
	if(count($disease_site_ids) == 0){
		return array();
	}
	if(count($disease_site_ids) == 1) return current($disease_site_ids);//如果疾病id集合等于1，则直接返回。
	//否则，循环取疾病id交集。
	
	
	do{
		reset($disease_site_ids);//将数组指针设置为数组开始位置	
		$finalDiseaseSet = current($disease_site_ids);//获取该数组当前位置（数组第一项）的值。
		foreach($disease_site_ids as $group_index => $group_diseaseSet){
			$finalDiseaseSet = array_intersect($finalDiseaseSet,$group_diseaseSet);//A交A === A
		}
		if(count($finalDiseaseSet) == 0){
			//echo "1";
			array_pop($disease_site_ids);
			$_SESSION["multi-morbidity"] = 1;
		}
	}while(count($finalDiseaseSet)==0);
	//print_r($finalDiseaseSet);
	return array_values($finalDiseaseSet);
}
//获取用户输入的关键词中concept id
function getWordConcept_ids($concepts){
	$concept_ids = array();
	foreach($concepts as $group_index =>$words){
		foreach($words as $concept_id => $concept){
			if($concept["type"]=="conjugate"){
				$concept_ids = array_merge($concept_ids,$concept["concepts"]);
			}else{
				$concept_ids[] = $concept_id;
			}
		}
	}
	return $concept_ids;
}
?>