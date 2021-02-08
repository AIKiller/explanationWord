<?php
	set_time_limit(0);
	include("../logic/common.inc.php");	
	$db= getDB();
	//$main_site_id = $_GET["site_id"];
	$N = 500; //The variable is used to control the number of output diseases.
	$similary_diseases = array();
	$functionType = "text";//$_GET["type"];
	$systemDiseaseSiteIdsObj = getSystemDiseasesInformation();//初始化所有疾病信息。
	//echo count($systemDiseaseSiteIdsObj);	
	//print_r($main_siteIdObj);
	if(count($systemDiseaseSiteIdsObj)==0){
		echo "<h2>The disease does not exist in the database!</h2>";
		exit;
	}
	//初始化所有系统疾病的症状信息
	//$system_diseases = getSystemDiseasesInformation();
	$system_diseases = $systemDiseaseSiteIdsObj;
	//echo getDiseaseSimilary("DS00466","DS00466");
	//echo "start_time:".date("Y-m-d H:i:s")."<br>";
	//循环比对主疾病和从疾病的相似度。
	//$diseasesSimilary = array();
	//比较每个系统疾病与所有系统疾病的相似度。
	//foreach($systemDiseaseSiteIdsObj as $main_site_id => $main_disease){
		$diseaseSimilarities = array();
		$main_site_id = "DS00094";
		echo "<pre>";
		print_r($systemDiseaseSiteIdsObj[$main_site_id]);
		echo "</pre>";
		$diseaseSimilarities = getDiseaseSimilarties($main_site_id,$systemDiseaseSiteIdsObj[$main_site_id]);
		arsort($diseaseSimilarities);
		$i = 0;
		if(count($diseaseSimilarities)==0){
			echo "<h2>The disease does not have relationship symptoms!</h2>";
			// continue;
		}
		while($i<$N){
			$disease_site_id = key($diseaseSimilarities);
			echo "<pre>";
			print_r($systemDiseaseSiteIdsObj[$disease_site_id]);
			echo "</pre>";
			echo "(".$system_diseases[$disease_site_id]["icpc"].")".$system_diseases[$disease_site_id]["name_decode"].":".$diseaseSimilarities[$disease_site_id]."%<br>";
			if(!next($diseaseSimilarities)){
				break;
			}
			$i++;
		}
		echo "<br><br><br><br>";
		ob_flush();
		flush();
		//sleep(1);
	//}
	
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
	//根据类名获取疾病site_id
	function getSystemDiseasesInformation(){
		global $db,$main_site_id;
		$disease_site_ids = array();
		$sql = "SELECT site_id FROM disease WHERE 1";
		$result =$db->query($sql);
		while($row = $result->fetch_array()){
			if($row["site_id"] != $main_site_id){
				$disease_site_ids[] = $row["site_id"];
			}
		}
		$diseasesInformation = getDiseaseInfo($disease_site_ids,TRUE);
		return $diseasesInformation;
	}
	//获取主疾病和相似疾病的相似度
	function getDiseaseSimilarties($main_site_id,$main_disease){
		global $system_diseases;
		if(!is_array($main_disease["symptoms"])){
			
			return array();
			
			//echo "<h2>The disease does not have relationship symptoms!</h2>";
			//exit;
		}
		$diseasSim = array();
		foreach($system_diseases as $similar_site_id => $similar_disease){
			if($main_site_id == $similar_site_id){
				continue;
			}
			$simD1D2 = calculateDiseaseSim($main_disease,$similar_disease);
			$simD2D1 = calculateDiseaseSim($similar_disease,$main_disease);
			$diseasSim[$similar_site_id] = round(($simD1D2+$simD2D1)/2,1);
		}
		return $diseasSim;
	}
	function calculateDiseaseSim($D1,$D2){
		
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
	function simBySymptoms($x,$y){
		similar_text($x,$y,$sim);
		return $sim;
	}
?>