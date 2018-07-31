<?php
//根据用户输入的无关因子单词，清洗每个分组下的症状列表
$unrelated_groupSymps = json_decode($_SESSION["unrelated_groupSymps"],true);
if(count($unrelated_groupSymps) == 0){
	//如果用户未输入无关因子，则退出脚本程序。不做任何处理
	exit;
}
$related_groupSymps   = json_decode($_SESSION["related_groupSymps"],true);
//否则进行症状过滤

//第一步获取无关因子的症状集合（并集）。
$unrelatedSympSet = array();

foreach($unrelated_groupSymps as $symptom_ids){

	$unrelatedSympSet = array_merge($unrelatedSympSet,$symptom_ids);

}
$unrelatedSympSet = array_unique($unrelatedSympSet);//去除重复的项

traversalGroupSymptom($related_groupSymps,$unrelatedSympSet);//过滤每个分组的症状

//第二步去除空项关联症状集合

$related_groupSymps = checkTraversalResult($related_groupSymps);

$_SESSION["related_groupSymps"] = json_encode($related_groupSymps);


//在每个分组症状中去除无关联因子对应的症状（进行差集运算）
function traversalGroupSymptom(&$related_groupSymps,$unrelatedSympSet){
	foreach($related_groupSymps as $group_index =>$group_symp){
		//array_diff(A,B) === A-B 在A中减去B
		//array_values 重新索引 数组
		$related_groupSymps[$group_index] = array_values(array_diff($group_symp,$unrelatedSympSet));
	
	}
}

function checkTraversalResult($related_groupSymps){
	global $synonyms,$new_synonyms;
	//$groupIndexChange = array();
	$new_related_groupSymps = array();
	//$increment_groupIndex = 0;
	foreach($related_groupSymps as $group_index => $group_symps){
		if(count($group_symps) != 0){//长度不为0的计入新数组内。
			$new_related_groupSymps[$group_index] = $group_symps;
			//长度为零的不计入新数组，且调整synonym的位置
			//首先判断synonym里面是否存在该单词
			/*if(isset($synonyms[$group_index])){
				//如果存在调整位置
				$new_synonyms[$increment_groupIndex] = $synonyms[$group_index];
			}*/
			//$increment_groupIndex ++;
		}
	}
	return $new_related_groupSymps;
}
?>