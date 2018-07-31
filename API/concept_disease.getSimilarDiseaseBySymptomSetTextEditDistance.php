<?php
	include("../logic/common.inc.php");	
	$db= getDB();
	$main_site_id = trim($_GET["site_id"]);
	$M = 1;//The variable is used to control the number of output diseases in same parent class.
	$N = 4;//The variable is used to control the number of output diseases in same sub-class.
	$similary_diseases = array();
	$resultInfo = array();
	//ICPC类名
	$CLASSNAME = array(
		"A"=>$langText['index']['control']['ICPC_CLASS_NAME_A'],
		"B"=>$langText['index']['control']['ICPC_CLASS_NAME_B'],
		"D"=>$langText['index']['control']['ICPC_CLASS_NAME_D'],
		"F"=>$langText['index']['control']['ICPC_CLASS_NAME_F'],
		"H"=>$langText['index']['control']['ICPC_CLASS_NAME_H'],
		"K"=>$langText['index']['control']['ICPC_CLASS_NAME_K'],
		"L"=>$langText['index']['control']['ICPC_CLASS_NAME_L'],
		"N"=>$langText['index']['control']['ICPC_CLASS_NAME_N'],
		"P"=>$langText['index']['control']['ICPC_CLASS_NAME_P'],
		"R"=>$langText['index']['control']['ICPC_CLASS_NAME_R'],
		"S"=>$langText['index']['control']['ICPC_CLASS_NAME_S'],
		"T"=>$langText['index']['control']['ICPC_CLASS_NAME_T'],
		"U"=>$langText['index']['control']['ICPC_CLASS_NAME_U'],
		"W"=>$langText['index']['control']['ICPC_CLASS_NAME_W'],
		"X"=>$langText['index']['control']['ICPC_CLASS_NAME_X'],
		"Y"=>$langText['index']['control']['ICPC_CLASS_NAME_Y'],
		"Z"=>$langText['index']['control']['ICPC_CLASS_NAME_Z']
	);
	$functionType = "text";//$_GET["type"];
	$main_siteIdObj = getDiseaseInfo($main_site_id);//初始化疾病信息。
	#print_r($main_siteIdObj);
	/*if(count($main_siteIdObj)==0){
		echo "<h2>The disease does not exist in the database!</h2>";
		exit;
	}*/
	//根据主疾病的className查找同类疾病。
	$className = $main_siteIdObj[$main_site_id]["className"];
	
	$disease_site_ids = getDiseaseSiteIdsByClassName($className);
	
	//print_r($disease_site_ids);
	
	//初始化查询到的可能相似疾病的信息
	
	$similar_siteIdObjs = getDiseaseInfo($disease_site_ids,$is_array=TRUE);//初始化疾病信息。
	
	//echo getDiseaseSimilary("DS00466","DS00466");
	
	
	//echo "start_time:".date("Y-m-d H:i:s")."<br>";
	//循环比对主疾病和从疾病的相似度。
	$diseasesSimilary = array();
	//echo "<pre>";
	//print_r($disease_site_ids);
	foreach($disease_site_ids as $similar_site_id){
		#echo "*********".$similar_site_id."******";
		$simD1D2 = getDiseaseSimilary($main_siteIdObj[$main_site_id],$similar_siteIdObjs[$similar_site_id]); 
		$simD2D1 = getDiseaseSimilary($similar_siteIdObjs[$similar_site_id],$main_siteIdObj[$main_site_id]); 
		
		$diseasesSimilary[$similar_site_id] = round(($simD1D2+$simD2D1)/2,1);
	
	}
	arsort($diseasesSimilary);
	//echo "<pre>";
	//print_R($diseasesSimilary);
	$i = 0;
	while($i<$M){
		$disease_name = "(".$similar_siteIdObjs[key($diseasesSimilary)]["icpc"].")".$similar_siteIdObjs[key($diseasesSimilary)]["name_decode"];
		$resultInfo[$disease_name] =$diseasesSimilary[key($diseasesSimilary)];
		next($diseasesSimilary);
		$i++;
	}
	

	//根据icpc再去查找同类疾病列表
	$icpc = $main_siteIdObj[$main_site_id]["icpc"];
	$disease_site_ids = getDiseaseSiteIdsByClassName($icpc);
	//初始化查询到的可能相似疾病的信息	
	$similar_siteIdObjs = getDiseaseInfo($disease_site_ids,$is_array=TRUE);//初始化疾病信息。

	//循环比对主疾病和从疾病的相似度。
	$diseasesSimilary = array();
	foreach($disease_site_ids as $similar_site_id){
		
		$simD1D2 = getDiseaseSimilary($main_siteIdObj[$main_site_id],$similar_siteIdObjs[$similar_site_id]); 
		$simD2D1 = getDiseaseSimilary($similar_siteIdObjs[$similar_site_id],$main_siteIdObj[$main_site_id]); 
		
		$diseasesSimilary[$similar_site_id] = round(($simD1D2+$simD2D1)/2,1);
	}
	arsort($diseasesSimilary);
//	echo "<pre>";
///	print_R($diseasesSimilary);
	$i = 0;
	if(count($disease_site_ids)==0){
		//echo "NULL";
		exit;
	}else{
		while($i<$N){
			$disease_name = "(".$similar_siteIdObjs[key($diseasesSimilary)]["icpc"].")".$similar_siteIdObjs[key($diseasesSimilary)]["name_decode"];
			$resultInfo[$disease_name] = $diseasesSimilary[key($diseasesSimilary)];
			//echo "(".$similar_siteIdObjs[key($diseasesSimilary)]["icpc"].") ".$similar_siteIdObjs[key($diseasesSimilary)]["name_decode"].":".$diseasesSimilary[key($diseasesSimilary)]."%<br>";
			if(!next($diseasesSimilary)){
					break;
			};
			$i++;
		}
	}
$pageInfo["className"] = $CLASSNAME[$className];
$pageInfo["similaryDiseases"] = $resultInfo;
echo json_encode($pageInfo);


	
	//初始化疾病的信息，包括site_id,className,症状列表和每个症状的concepts
	function getDiseaseInfo($site_id,$is_array = FALSE){
		global $db;
		$diseasesInfo = array();
		if($is_array){
			$stringSite_ids = "'".implode($site_id,"','")."'";
			$sql = "SELECT disease.site_id,icpc,disease.name_decode AS disease_name,symptom_site_id,symptom.name_decode AS symptom_name FROM `disease` LEFT JOIN dis_symp ON disease.site_id = disease_site_id LEFT JOIN symptom ON symptom_site_id = symptom.site_id WHERE disease.site_id in ({$stringSite_ids})";
		}else{
			$sql = "SELECT disease.site_id,icpc,disease.name_decode AS disease_name,symptom_site_id,symptom.name_decode AS symptom_name FROM `disease` LEFT JOIN dis_symp ON disease.site_id = disease_site_id LEFT JOIN symptom ON symptom_site_id = symptom.site_id WHERE disease.site_id  = '{$site_id}'";
		}
		$result =$db->query($sql);
		while($row = $result->fetch_array()){
			$row["site_id"] = trim($row["site_id"]);
			$diseasesInfo[$row["site_id"]]["className"] = getClassNameByICPC($row["icpc"]);
			$diseasesInfo[$row["site_id"]]["icpc"] = $row["icpc"];
			$diseasesInfo[$row["site_id"]]["name_decode"] = $row["disease_name"];
			if($row["symptom_site_id"] == null){
				$diseasesInfo[$row["site_id"]]["symptoms"] = 0;//没有关联症状
			}else{
				$diseasesInfo[$row["site_id"]]["symptoms"][$row["symptom_site_id"]] = $row["symptom_name"];
			}
		}
		return $diseasesInfo;
	}
	//根据疾病的icpc的值获取class name
	function getClassNameByICPC($icpc){
	
		return substr($icpc,0,1);
	
	}
	//根据类名获取疾病site_id
	function getDiseaseSiteIdsByClassName($className){
		global $db,$main_site_id;
		$disease_site_ids = array();
		$sql = "SELECT site_id FROM disease WHERE icpc LIKE '%{$className}%'";
		$result =$db->query($sql);
		while($row = $result->fetch_array()){
			if(trim($row["site_id"]) != trim($main_site_id)){
				$disease_site_ids[] = trim($row["site_id"]);
			}
		}
		return $disease_site_ids;
	}
	//获取主疾病和相似疾病的相似度
	function getDiseaseSimilary($D1,$D2){
		//echo count($main_siteIdObj[$main_site_id]["symptom_site_ids"]);
	
		if(!is_array($D1["symptoms"])||!is_array($D2["symptoms"])){
			return 0;
		}
		foreach($D1["symptoms"] as $D1_symptom_id => $D1_symptom_name){
			
			$temp_similary = array();
			
			foreach($D2["symptoms"] as $D2_symptom_id => $D2_symptom_name){
				
				$temp_similary[] = simBySymptoms($D1_symptom_name,$D2_symptom_name); //方法1 计算症状文本距离
				//$temp_similary[] = simByConcepts($main_concepts,$similar_concepts); //方法2 计算concepts集合算法
			}
			$max_similary[] = max($temp_similary);
			
		}
		return array_sum($max_similary)/count($D1["symptoms"]);
	}
	//获取两个concept集合的相似度
	function simByConcepts($x,$y){
		$similar = count(array_unique(array_intersect($x,$y)))/count(array_unique(array_merge($x,$y)))*100;
		return round($sim,1);
	}
	function simBySymptoms($x,$y){
		similar_text($x,$y,$sim);
		return $sim;
	}
	

	
?>