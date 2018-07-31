<?php
/*
目的：建立concept_suggestion 数据库 字段：auto_id matched_concept_id(升序排列) incremental_concept
1.取的concept 集合
2.根据single concept 查找 症状集合，根据症状集合查询每个症状中concept的出现次数。建立初步的double concept
3.根据double concept 再次查找症状集合，根据症状集合查询每个症状中concept的出现次数。建立初步的triple concept
*/
set_time_limit(0);
include("../logic/common.inc.php");
$db = getDB();
$concepts = array();
//获得当前系统中concept的集合
$sql = "SELECT concept_id FROM concept WHERE is_del = 1";
$result = $db->query($sql);
if($result){
	while($row = $result->fetch_array()){
		$concepts[] = $row["concept_id"];//获得concept的数组集合
	}
}
setSingleConcept($concepts);
//setSingleConcept($concepts);
//插入单个single concept的数据。
//例如 1 C_1 abdominal
//     2 C_1 chest
function setSingleConcept($concepts){
	$concept_num = array();
	foreach($concepts as $concept_id){
		getIncrementArray($concept_id);//循环取值
	}
	setDoubleConcept();
}
//首先获得目前concept 与他的备选单词
//根据concept 与 备选单词 获取症状集合。根据症状集合 获取分组的concept的出现次数
function setDoubleConcept(){
	global $db;
	$sql="SELECT matched_concept_id,incremental_concept FROM concept_suggestion WHERE 1";
	$result = $db->query($sql);
	if($result){
		while($row = $result->fetch_array()){
			$concepts = array();
			$concepts[$row["matched_concept_id"]] = getConceptid($row["incremental_concept"]);//获得concept的数组集合
			getIncrementArray($concepts);
			//print_R($concepts);
		}
	}
	setTripleConcept();	
}
function setTripleConcept(){
	global $db;
	$sql="SELECT matched_concept_id,incremental_concept FROM concept_suggestion WHERE 1";
	$result = $db->query($sql);
	if($result){
		while($row = $result->fetch_array()){
			$concepts = array();
			$matched_concept_ids = array();
			$exploded_concept_ids = explode(" ",$row["matched_concept_id"]);
			//echo count($exploded_concept_ids);
			if(count($exploded_concept_ids) == 2){
				$concepts[$row["matched_concept_id"]] = getConceptid($row["incremental_concept"]);//获得concept的数组集合
				getIncrementArray($concepts);
			}
			//print_R($concepts);
		}
	}	
	echo "successful";
}
function getIncrementArray($concept_ids){
	global $db;
	$incrementArray = array();
	//如果传入的值为数组，则组合进行查询。
	//echo is_array($concept_ids);
	//return ;
	if(is_array($concept_ids)){
		foreach($concept_ids as $matched_concept_id => $increament_concept_id){
			//判断是double concept  还是triple concept
			$concept_symptoms = array();
			$exploded_concept_ids = array();
			$matched_concept_ids = array();
			$exploded_concept_ids = explode(" ",$matched_concept_id);
			//echo count($exploded_concept_ids);
			if(count($exploded_concept_ids) == 1){
				//建立double concept 
				//echo $exploded_concept_ids[0]."_".$exploded_concept_ids[1];
				$matched_concept_ids[] =$matched_concept_id;
				$matched_concept_ids[] = $increament_concept_id;
				$sql_str = '("'.implode('","',$matched_concept_ids).'")';
				$sql='SELECT concept_id,site_id FROM symp_concept WHERE concept_id in'.$sql_str;
				$result = $db->query($sql);
				if($result){
					while($row = $result->fetch_array()){
						$concept_symptoms[$row["concept_id"]][]= $row["site_id"];
					}
				}
				$intersectioned = intersection($concept_symptoms,$matched_concept_ids);
				$sql_str = '("'.implode('","',$intersectioned).'")';
				$sql = 'SELECT count(concept_id) AS num ,concept_id FROM symp_concept WHERE site_id in'.$sql_str.'AND concept_id !="'.$matched_concept_ids[0].'" AND concept_id !="'.$matched_concept_ids[1].'" GROUP BY concept_id';
			}else{
				$matched_concept_ids[] =$exploded_concept_ids[0];
				$matched_concept_ids[] =$exploded_concept_ids[1];
				$matched_concept_ids[] = $increament_concept_id;
				$sql_str = '("'.implode('","',$matched_concept_ids).'")';
				$sql='SELECT concept_id,site_id FROM symp_concept WHERE concept_id in'.$sql_str;
				$result = $db->query($sql);
				if($result){
					while($row = $result->fetch_array()){
						$concept_symptoms[$row["concept_id"]][]= $row["site_id"];
					}
				}
				$intersectioned = intersection($concept_symptoms,$matched_concept_ids);
				$sql_str = '("'.implode('","',$intersectioned).'")';
				$sql = 'SELECT count(concept_id) AS num ,concept_id FROM symp_concept WHERE site_id in'.$sql_str.'AND concept_id !="'.$matched_concept_ids[0].'"AND concept_id !="'.$matched_concept_ids[1].'" AND concept_id !="'.$matched_concept_ids[2].'" GROUP BY concept_id';
			}
		
		}
	}else{
	//否则生成单一的sql语句
		$sql = 'SELECT count(concept_id) AS num ,concept_id FROM symp_concept WHERE site_id in(SELECT DISTINCT(site_id) FROM symp_concept WHERE concept_id ="'.$concept_ids.'")AND concept_id !="'.$concept_ids.'" GROUP BY concept_id';
	}
	$result = $db->query($sql);
	if($result){
		while($row = $result->fetch_array()){
			$incrementArray[getConceptName($row["concept_id"])] = $row["num"];//获得concept名指向数目的数组
			//$incrementArray[getConceptName($row["concept_id"])] = $row["concept_id"];//获得concept名指向concept_id的数组
		}
		arsort($incrementArray);//对数组进行排序。
	}
	//echo "<pre>";
	//print_R($incrementArray);
	//echo "</pre>";
	//return;
	insertDatabaseInfo($concept_ids,$incrementArray);
}
function insertDatabaseInfo($concept_ids,$incrementArray){
	global $db;
	if(is_array($concept_ids)){
			foreach($concept_ids as $matched_concept_id => $increament_concept_id){
				//判断是double concept  还是triple concept
				$exploded_concept_ids = array();
				$matched_concept_ids = array();
				$exploded_concept_ids = explode(" ",$matched_concept_id);
				if(count($exploded_concept_ids) == 1){
					//建立double concept 
					$matched_concept_ids[] =$matched_concept_id;
					$matched_concept_ids[] = $increament_concept_id;
					usort($matched_concept_ids, "strnatcmp");
					$match_concept_id = implode(" ",$matched_concept_ids);
				}else{
					$matched_concept_ids[] =$exploded_concept_ids[0];
					$matched_concept_ids[] =$exploded_concept_ids[1];
					$matched_concept_ids[] = $increament_concept_id;
					usort($matched_concept_ids, "strnatcmp");
					$match_concept_id = implode(" ",$matched_concept_ids);
				
				}
			}
			foreach($incrementArray as $incrementConcept => $num){
				//首先检测要插入的信息是否已经存在
				$sql = 'SELECT auto_id FROM concept_suggestion WHERE matched_concept_id="'.$match_concept_id.'" AND incremental_concept = "'.$incrementConcept.'"';
				$result = $db->query($sql);
				if($result->num_rows == 0 ){
					echo $sql='INSERT INTO concept_suggestion(matched_concept_id,incremental_concept,term)VALUES("'.$match_concept_id.'","'.$incrementConcept.'","'.$num.'")';
					echo "<br>";
					$result = $db->query($sql);
				}
			}
	}else{
	//非数组时插入单个信息
		foreach($incrementArray as $incrementConcept => $num){
			echo $sql='INSERT INTO concept_suggestion(matched_concept_id,incremental_concept,term)VALUES("'.$concept_ids.'","'.$incrementConcept.'","'.$num.'")';
			echo "<br>";
			$result = $db->query($sql);
		}
	}
}
function getConceptName($concept_id){
	global $db;
	$sql='SELECT keyword FROM concept WHERE concept_id="'.$concept_id.'"';
	$result = $db->query($sql);
	if($result){
		$row = $result->fetch_array();
		return $row["keyword"];
	}
}
function getConceptid($concept){
	global $db;
	$sql='SELECT concept_id FROM concept WHERE keyword="'.$concept.'"';
	$result = $db->query($sql);
	if($result){
		$row = $result->fetch_array();
		return $row["concept_id"];
	}
}
//二维多数组交叉取交集
function intersection($concept_symptoms,$concept_ids){
	$intersections = array();
	//print_r($concept_symptoms);
	$intersections = $concept_symptoms[$concept_ids[0]];
	for($index=0;$index<count($concept_ids)-1;$index++){
		//print_r($intersections);
		$intersections = array_intersect($intersections,$concept_symptoms[$concept_ids[$index+1]]);
	}
	return array_values($intersections);
}
?>