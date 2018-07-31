<?php
	include("../logic/common.inc.php");	
	$a = isset($_GET["a"])?$_GET["a"]:$_SESSION['setting_a'];
	$b = isset($_GET["b"])?$_GET["b"]:$_SESSION['setting_a'];
	if(isset($_SESSION["userInformationSet"])){
		$userInformationSet = json_decode($_SESSION["userInformationSet"],true);
	}
	$_SESSION['setting_a'] = $a;
	$_SESSION['setting_b'] = $b;
	
	$db = getDB();
	
	$finalDiseaseSet = json_decode($_SESSION["finalDisease"],true);				//获取疾病（一维数组）
	//print_r($finalDiseaseSet);
	
	$diseaseDetails = json_decode($_SESSION["diseaseDetails"],true);			//疾病详情
	if(count($finalDiseaseSet)==0){
		$_SESSION["finalDieseaseDetails"] = array();
		$finalDiseasePage["finalDisease"] = array();
		$finalDiseasePage["multi-morbidity"] = $_SESSION["multi-morbidity"];
		echo json_encode($finalDiseasePage);
		exit;
	}else{
		if($_SESSION["multi-morbidity"] != 1){
			$_SESSION["multi-morbidity"] = 0;
		}
	
	}
	foreach($finalDiseaseSet as $disease_site_id){
		reset($diseaseDetails);//将数组指针设置为数组开始位置
		$diseaseDetailsFirst = current($diseaseDetails);//获取该数组当前位置（数组第一项）的值。

		//print_r($diseaseDetailsFirst);
		if(!isset($diseaseDetailsFirst[$disease_site_id])){
			continue;//只要最终疾病没有出现在第一组数组中，则不再做任何处理，进行下一个疾病的比较
		}
		$finalDiseaseDetailSet[$disease_site_id]["marked"] = array();
		//如果存在，获取该疾病在每个分组中被描述的症状列表
		foreach($diseaseDetails as $group_index => $group_details){
			if(isset($group_details[$disease_site_id])){
				$finalDiseaseDetailSet[$disease_site_id]["all_symp"] = array_values(array_unique($group_details[$disease_site_id]["all_symp"]));
				$finalDiseaseDetailSet[$disease_site_id]["marked"] = array_merge($finalDiseaseDetailSet[$disease_site_id]["marked"],$group_details[$disease_site_id]["marked"]);
			}
		}
		$finalDiseaseDetailSet[$disease_site_id]["marked"] = array_unique($finalDiseaseDetailSet[$disease_site_id]["marked"]);
	}



	$number = 0;
	foreach($finalDiseaseDetailSet as $disease_site_id =>$disease_details){
		$diseaseInfo = getDiseaseName_PrevFromDB($disease_site_id);//查询数据库 获取疾病的名称和prev_rf。
		$finalDiseaseToPage[$number]["disease_site_id"] = $disease_site_id;
		$finalDiseaseToPage[$number]["disease_name"] = $diseaseInfo["disease_name"];
		$finalDiseaseToPage[$number]["prev_rf"] = round($diseaseInfo["prev_rf"],2);
		$finalDiseaseToPage[$number]["status"] = $diseaseInfo["status"];
		$finalDiseaseToPage[$number]["diseaseDescripted_num"] = count($disease_details["marked"]);
		$finalDiseaseToPage[$number]["details"] = count($disease_details["marked"])."/".count($disease_details["all_symp"]);
		$percentage = count($disease_details["marked"])/count($disease_details["all_symp"]);
		/*if($percentage<0.5){
			$finalDiseaseToPage[$number]["color"] = "danger";//css标签名字-》红色
		}else{
			$finalDiseaseToPage[$number]["color"] = "info";//css标签名字-》绿色
		}*/
		$finalDiseaseToPage[$number]["percentage"] = (int)($percentage*100);
		$number ++;
	}
	
	$prevMax = getMaxFromArray($finalDiseaseToPage,"prev_rf");
	$percentageMax = getMaxFromArray($finalDiseaseToPage,"percentage");

	foreach($finalDiseaseToPage as $index => $diseaseInfo){

		$finalDiseaseToPage[$index]["comb"] = calculateComb($diseaseInfo);

	}
	
	$combMax = getMaxFromArray($finalDiseaseToPage,"comb");

	foreach($finalDiseaseToPage as $index => $diseaseInfo){	
		
		if($combMax == 0){
			$finalDiseaseToPage[$index]["comb"] = 0;
		}else{
			$finalDiseaseToPage[$index]["comb"] = round($finalDiseaseToPage[$index]["comb"]/$combMax,2);
		}
	}

	sortArrByField($finalDiseaseToPage,"comb",true);//二维数组排序。
		
	$_SESSION["finalDieseaseDetails"] = json_encode($finalDiseaseDetailSet);
	$_SESSION["finalDieseaseToPage"] = json_encode($finalDiseaseToPage);
	$finalDiseasePage["finalDisease"] = $finalDiseaseToPage;
	$finalDiseasePage["multi-morbidity"] = $_SESSION["multi-morbidity"];
	echo json_encode($finalDiseasePage);

	function getDiseaseName_PrevFromDB($disease_site_id){
		global $db,$userInformationSet;
		$age = !isset($userInformationSet["age"])?0:$userInformationSet["age"];//储存用户的年龄信息
		//echo $age."<br>";
		$num = 0;//记录查询到的结果数目
		if(is_numeric($age)){
			//如果用户保存了自己的年龄信息，查询数据库获取信息
			$sql = "SELECT name_decode,disease_agelevel.prev,lethality,rf FROM disease_agelevel INNER JOIN disease ON disease_agelevel.site_id=disease.icpc  WHERE disease.site_id ='".$disease_site_id."'and max_age>'".$age."' and min_age<'".$age."'";
			$result = $db->query($sql);
			$num = $result->num_rows;
			if($num > 0){
				$row = $result->fetch_array();
				if($row["rf"]>0){
					$return_diseaseArr = array("disease_name"=>$row["name_decode"],
							"prev_rf" => round($row["prev"]/$row["rf"],5),
							"lethality"=>$row["lethality"]
					);
				}else{
					$return_diseaseArr = array("disease_name"=>$row["name_decode"],
							"prev_rf" => $row["prev"],
							"lethality"=>$row["lethality"]
					);
				}
				
			}
		}
		if($age == ""||$num == 0){
			//如果用户未保存自己的年龄信息，则读取默认值
			$sql = "SELECT name_decode,prev_rf,lethality FROM disease WHERE site_id ='".$disease_site_id."'";
			$result = $db->query($sql);
			$row = $result->fetch_array();
			$return_diseaseArr = array("disease_name"=>$row["name_decode"],
							"prev_rf" => $row["prev_rf"],
							"lethality"=>$row["lethality"]
					);
		}
		/*if($return_diseaseArr["prev_rf"] == 0){
			$sql = "SELECT name_decode,disease_agelevel.prev_rf,min_age FROM disease_agelevel INNER JOIN disease ON disease_agelevel.site_id=disease.icpc  WHERE disease.site_id ='".$disease_site_id."' AND disease_agelevel.prev_rf >0 ORDER BY min_age DESC limit 0,1";
			$result = $db->query($sql);
			$num = $result->num_rows;
			if($num > 0){
				$row = $result->fetch_array();
				$return_diseaseArr = array("disease_name"=>$row["name_decode"],
							"prev_rf" => $row["prev_rf"]
					);
			}
		}*/
		if($return_diseaseArr["prev_rf"]< 0.5 && $return_diseaseArr["prev_rf"] >0){//如果查询到的疾病的prev_rf小于0.5，则认为该疾病为罕见疾病，则进行特殊显示（红色） 默认为绿色
			$return_diseaseArr["status"] ="rare";//css标签名字-》翠绿色
		}else{
			$return_diseaseArr["status"] ="normal";//css标签名字-》绿色
		}
		if($return_diseaseArr["lethality"]){
			$return_diseaseArr["status"] = "lethality";//css标签名字-红色
		
		}
		return $return_diseaseArr;
	}

	//获取分组疾病的id集合。
	function intersectionDiseaseSet($disease_site_ids){
		$finalDiseaseSet = array();
		reset($disease_site_ids);//设置数组指向数组首部位置
		if(count($disease_site_ids) == 0){
			return array();
		}
		if(count($disease_site_ids) == 1) return current($disease_site_ids);//如果疾病id集合等于1，则直接返回。
		//否则，循环取疾病id交集。
		reset($disease_site_ids);//将数组指针设置为数组开始位置
		$finalDiseaseSet = current($disease_site_ids);//获取该数组当前位置（数组第一项）的值。
		foreach($disease_site_ids as $group_index => $group_diseaseSet){
			
			$finalDiseaseSet = array_intersect($finalDiseaseSet,$group_diseaseSet);//A交A === A
		
		}
		return array_values($finalDiseaseSet);
	}

	//获得增量单词（并集）的列表
	function getSeparateIncremental($finalDisease){
		global $db,$concept_ids;
		$incremental = array();
		$sql_str = '("'.implode($finalDisease,'","').'")';//拼接数组
		$sql = 'SELECT count(concept_id) AS num ,concept_id FROM symp_concept WHERE site_id in(SELECT DISTINCT(symptom_site_id) FROM dis_symp WHERE disease_site_id in'.$sql_str.')GROUP BY concept_id';//在剩余的症状中查找concept的数目
		$result = $db->query($sql);
		if($result){
			while ($row = $result->fetch_array()) {
				if(!isset($concept_ids[$row["concept_id"]])){
					$incremental[getConceptName($row["concept_id"])] = $row["num"];//载入数组
				}
			}
			arsort($incremental);//对数组进行排序。
			return $incremental;
		}
	}
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
	//php 多维数组指定字段排序
	function sortArrByField(&$array, $field, $desc = false){
		$fieldArr = array();
		foreach ($array as $k => $v) {
			$fieldArr[$k] = $v[$field];
		}
		$sort = $desc == false ? SORT_ASC : SORT_DESC;
		array_multisort($fieldArr, $sort, $array);
	}

	//根据用户输入获得的最终疾病和userInfo取差集
	function filterDiseaseSet($finalDiseaseSet){
		global $general_awareness;
		if(count($general_awareness) == 0) return $finalDiseaseSet;
		return array_diff($finalDiseaseSet,$general_awareness);
	}
	function getMaxFromArray($disease_array,$fields){
		$maxValue = 0;
		foreach($disease_array as $index => $diseaseInfo){
			$maxValue = max($maxValue,$diseaseInfo[$fields]);
		}
		return $maxValue;
	}
	//正规化P和R值。计算两个维度的关联值
	function calculateComb($diseaseInfo){
		global $prevMax,$percentageMax,$a,$b;
		if($prevMax == 0){
			$p = 0;
			$r = $diseaseInfo["percentage"]/$percentageMax;
		}else if($percentageMax == 0){
			$p = $diseaseInfo["prev_rf"]/$prevMax;
			$r = 0;
		}else{
			$p = $diseaseInfo["prev_rf"]/$prevMax;
			$r = $diseaseInfo["percentage"]/$percentageMax;
		}
		//计算每个因子与该因子最大值的商

		return $a*$p+$b*$r;
		//正规化
		//return $comb = sqrt($a*($p*$p)+$b*($r*$r));
	}
?>