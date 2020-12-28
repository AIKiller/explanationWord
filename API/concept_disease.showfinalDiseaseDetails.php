<?php
	include("../logic/common.inc.php");	
    //获取用户基本信息
	if(isset($_SESSION["userInformationSet"])){
		$userInformationSet = json_decode($_SESSION["userInformationSet"],true);
	}

	$db = getDB();
	$max_number_diseases = getMaxNumberDiseases();  //the total number of diseases in databases;
	$finalDiseaseSet = json_decode($_SESSION["finalDisease"],true);				//获取疾病（一维数组）
	//print_r($finalDiseaseSet);
	$diseaseDetails = json_decode($_SESSION["diseaseDetails"],true);			//疾病详情 {"A01":{"marked":["symp8049","symp8050","symp8051","symp8052","symp8053"],"all_symp":["symp8049","symp8050","symp8051","symp8052","symp8053"]}}
	$number_eliminated_diseases = $max_number_diseases-count($finalDiseaseSet);    //the total eliminated number of diseases in process;
    $spec = $number_eliminated_diseases/$max_number_diseases; //Spec is short name of specificity.

    if(count($finalDiseaseSet)==0){//最终疾病结果为空时
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
	    //echo "disease_id:".$disease_site_id;
		$diseaseInfo = getDiseaseName_PrevFromDB($disease_site_id);//查询数据库 获取疾病的名称和prev_rf。
		$finalDiseaseToPage[$number]["disease_site_id"] = $disease_site_id;
		$finalDiseaseToPage[$number]["disease_name"] = $diseaseInfo["disease_name"];
		$finalDiseaseToPage[$number]["prev_rf"] = round($diseaseInfo["prev_rf"],2);
        $p = round($diseaseInfo["prev_rf"],2)/1000;
		$finalDiseaseToPage[$number]["status"] = $diseaseInfo["status"];
		$finalDiseaseToPage[$number]["diseaseDescripted_num"] = count($disease_details["marked"]);
		$finalDiseaseToPage[$number]["details"] = count($disease_details["marked"])."/".count($disease_details["all_symp"]);
		$sens = count($disease_details["marked"])/count($disease_details["all_symp"]); //Sens is short name for sensitivity and is equal to R
		$finalDiseaseToPage[$number]["percentage"] = (int)($sens*100);
        $finalDiseaseToPage[$number]["PVW"]=round(getPVW($sens, $spec, $p),3);
        $finalDiseaseToPage[$number]["NVW"]=round(getNVW ($sens, $spec, $p),3);
		$number ++;
	}
    //获取两个参数的最大值
	$prevMax = getMaxFromArray($finalDiseaseToPage,"prev_rf");
	$percentageMax = getMaxFromArray($finalDiseaseToPage,"percentage");


    //准备要在页面显示的数组
	$_SESSION["finalDieseaseDetails"] = json_encode($finalDiseaseDetailSet);
	$_SESSION["finalDieseaseToPage"] = json_encode($finalDiseaseToPage);
	$finalDiseasePage["finalDisease"] = $finalDiseaseToPage;
	$finalDiseasePage["multi-morbidity"] = $_SESSION["multi-morbidity"];
	echo json_encode($finalDiseasePage);


	//以下为公用函数
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
	//从数组中获取指定字段的最大值
	function getMaxFromArray($disease_array,$fields){
		$maxValue = 0;
		foreach($disease_array as $index => $diseaseInfo){
			$maxValue = max($maxValue,$diseaseInfo[$fields]);
		}
		return $maxValue;
	}
	//获取疾病的最大数目
    function getMaxNumberDiseases(){
	    global $db;
	    $maxNumber = 0;
	    $sql = "SELECT COUNT(1) as number FROM disease";
	    $result = $db->query($sql);
	    while ($row=$result->fetch_array()){
            $maxNumber = $row['number'];
        }
	    return $maxNumber;
    }


    function getPVW($sens, $spec, $p){
        $alfa = getAlfa($sens,$spec);
        return (100*$alfa*$p)/($alfa*$p +1-$p);
    }
    //工具函数
    function getAlfa($sens,$spec){
        return $sens/(1-$spec);
    }

    function getNVW ($sens, $spec, $p){
        return 100*(1-$p) * $spec/((1-$p)*$spec + (1-$sens)*$p);
    }

?>