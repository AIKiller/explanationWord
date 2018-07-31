<?php
$sid ='jr6dg3nt8oh91cv9k68pm51sc5';//测试用session_id	
include("../logic/common.inc.php");
//header("Content-Type: text/html; charset=UTF-8");
set_time_limit(0);
ini_set('date.timezone','Asia/Shanghai');
$db = getDB();
$alpha = 1;
$beta =  1;
//autoTestWriteLog("Program start at:".date('Y-m-d H:i:s',time()));
$start_time = date('Y-m-d H:i:s',time());

echo 'Program start at: '.$start_time."<br>";
echo "alpha : ".$alpha."beta : ".$beta."<br>";



$userInputs = array();

$ACC = 0;
$N = 0;
getAllDiseases();
//echo json_encode($userInputs);
echo $ACC/$N;

echo '<br>Finished at: '.date('Y-m-d H:i:s',time())."<br>";


function getAllDiseases(){
	global $ACC;
	global $N;
	global $db;
	global $userInputs;
	$diseases = array();
	$sql='SELECT site_id,icd,name_decode FROM disease WHERE 1';//limit 850,30';
	$result = $db->query($sql);
	if($result){
		while($row = $result->fetch_array()){
			$userInputs[$row["site_id"]]["disease_name"] = $row["name_decode"];
			$userInputs[$row["site_id"]]["symptoms"] = array();
			getSymptomsFormDisease($row["site_id"]);
			//print_r($userInputs);
			//如果不是有效的疾病（没有症状，或没有concept，不予计算）
			if($userInputs[$row["site_id"]]["accuracy"]!=-2){
				$ACC = $ACC + $userInputs[$row["site_id"]]["accuracy"];
				$N++;
				echo $N.".";
				echo $row["site_id"]." accuracy:".$userInputs[$row["site_id"]]["accuracy"]."\t";
				if($N%20==0) echo "<br>\n";
				ob_flush();
				flush();
				sleep(1);
			}
		}
	}
	//return $incremental_concepts;
}




//对于每一个疾病，选择alpha症状
function getSymptomsFormDisease($disease_id){
	global $db;
	global $userInputs;
	global $alpha;
	
	$symptomArray = array();
	$sql='select site_id, name_decode from (SELECT symptom_site_id FROM dis_symp WHERE disease_site_id="'.$disease_id.'") as S inner join symptom where S.symptom_site_id=symptom.site_id';
	$result = $db->query($sql);
	if($result){
		while($row = $result->fetch_array()){
			$symptomArray[] = $row;
		}
	}
	
	if (count($symptomArray)==0){ 
		$userInputs[$disease_id]["result"] = array();
		$userInputs[$disease_id]["accuracy"] = -2;//symptom为空
		return;
	}
	
	//向上进位，获得一个[1,n]的值，取这么多个symptom
	$all_number = count($symptomArray);
	$number = ceil($all_number*$alpha);
	//echo "number=".$number."<br>";
	$selected = 0;
	$a=0;
	while($selected < $number){
		$index = rand(0,$all_number-1);
		$symptom = $symptomArray[$index];//取一个数据
		//echo $symptom["site_id"]." ";
		if(!isset($userInputs[$disease_id]["symptoms"][$symptom["site_id"]])){
			$name = str_replace('"','\"',$symptom['name_decode']);
			$userInputs[$disease_id]["symptoms"][$symptom["site_id"]]["symptom_name"] = $name;
			$userInputs[$disease_id]["symptoms"][$symptom["site_id"]]["concepts"] = array();
			getConceptsFromSymptom($disease_id,$symptom["site_id"]);
			$selected++;
		//亲测 这个地方是有问题的。 如果把limit的限制改为10，这个程序基本上很快就跑完。如果改为20。直接没反应了。
		//但是如果把我下面注释的sele 语句放开，改为100都没事。
		//怀疑这地方产生死循环了。因为如果把我写的函数 getConceptsFromDiseaseJson($disease_id); 注释掉 81 cup直接100%；
		//昨天弄了很久。没找到是哪个疾病造成的。
		}
		if($a++>100){
			//echo $disease_id;
			break;
		}
	}
}
//对于每一个症状，选择beta概念
function getConceptsFromSymptom($disease_id,$symptom_id){
	global $db;
	global $userInputs;
	global $beta;
	global $text_concept,$page_result;//用来错误记录信息。
	
	$conceptArray = array();
	$sql='select S.concept_id, keyword from (SELECT concept_id FROM symp_concept WHERE site_id="'.$symptom_id.'") as S inner join concept on S.concept_id=concept.concept_id where is_del=1';
	$result = $db->query($sql);
	if($result){
		while($row = $result->fetch_array()){
			$conceptArray[] = $row;
		}
	}
	
	if (count($conceptArray)==0){ 
		$userInputs[$disease_id]["result"] = array();
		$userInputs[$disease_id]["accuracy"] = -2;//symptom为空
		return;
	};

	//向上进位，获得一个[1,n]的值，取这么多个concept
	$all_number = count($conceptArray);
	$number = ceil($all_number*$beta);
	$selected = 0;
	$a =0;
	while($selected < $number){
		$index = rand(0,$all_number-1);
		$concept = $conceptArray[$index];//取一个数据
		if(!isset($userInputs[$disease_id]["symptoms"][$symptom_id]["concepts"][$concept["concept_id"]])){
			$userInputs[$disease_id]["symptoms"][$symptom_id]["concepts"][$concept["concept_id"]] = $concept["keyword"];
			$selected++;
		}
		
		if($a++>100){
			//echo $symptom_id;
			break;
		}
	}
	$is_success = true;
	$number = 0;
	do{
		if($number>0){
			sleep(1);//防止CPU压力过大，第二次循环，休息1S
		}
		if($number>3){//防止出现死循环
			$userInputs[$disease_id]["accuracy"] = 0;
			$flag = autoTestWriteLog($disease_id,json_encode($userInputs[$disease_id]),$text_concept,$page_result);
			if(!$flag){
				die("log insert error!");
			}
			break;
		}
		$is_success = getConceptsFromDiseaseJson($disease_id);
		$number++;
	}while(!$is_success);//循环直到getConceptsFromDiseaseJson($disease_id)返回true;
	//print_r($userInputs[$disease_id]);
}
$text_concept = "";
$page_result = array();
function getConceptsFromDiseaseJson($disease_id){
	global $userInputs,$sid,$text_concept,$page_result;
	$concepts = array();
	foreach($userInputs[$disease_id]["symptoms"] as $symptom_id => $symptom){
		if(!count($symptom["concepts"])==0){
			$concepts[] = $symptom["concepts"];
		}
	}
	$text_concept = transformArryToText($concepts);
	if(count($concepts)==0){
		$userInputs[$disease_id]["result"] = array();
		$userInputs[$disease_id]["accuracy"] = -2;//concep为空
		return;
	}
	//print_r($concepts);
	//将concepts集合添加到session数组中 array('0'=>array('0'=>'abdominal','1'=>'pain'),'1'=>arry('0'=>'fever'))
	//file_get_contents('http://'.$hostname.'/explanationWord/API/setSessionInfoAboutTest.php?sid='.$sid.'&concepts='.json_encode($concepts));
	
	//echo 'http://'.$hostname.'/explanationWord/API/word_concept.stopWord_clear.php?sid='.$sid.'&user_input='.$text_concept;
	$hostname = "localhost";
	/*
		版本更新后，修改测试程序测试流程。
		1、去除stopword 						-> word_concept.stopWord_clear.php
		2、替换同义词   						-> word_concept.replace_synonymConjugate_word.php
		3、查找每个分组单词的关联症状 			-> concept_disease.mergeGroupSymptom.php
		4、查找每个分组的关联症状对应的关联疾病 -> concept_disease.reachGroupDisease.php
		5、显示最终疾病的json 					-> concept_disease.showfinalDiseaseDetails.php
	*/
	//第一步
	file_get_contents('http://'.$hostname.'/explanationWord/API/word_concept.stopWord_clear.php?sid='.$sid.'&user_input='.urlencode($text_concept)."&flag=related");
	file_get_contents('http://'.$hostname.'/explanationWord/API/word_concept.stopWord_clear.php?sid='.$sid.'&user_input=""&flag=unrelated');
	
	//第二步
	file_get_contents('http://'.$hostname.'/explanationWord/API/word_concept.replace_synonymConjugate_word.php?sid='.$sid);
	
	//第三步
	//根据concepts 集合获取每个分组的 症状集合，结果存储在session数组中   array('0'=>array(根据abdominal和pain找到的症状集合),'1'=>arry(根据fever找到的症状集合))
	file_get_contents('http://'.$hostname.'/explanationWord/API/concept_disease.mergeGroupSymptom.php?sid='.$sid);
	
	//第四步
	//根据分组的症状集合查找疾病集合，存储到session数组中 array('0'=>array(根据abdominal和pain找到的疾病集合),'1'=>arry(根据fever找到的疾病集合))
	file_get_contents('http://'.$hostname.'/explanationWord/API/concept_disease.reachGroupDisease.php?sid='.$sid);
	
	//获取最终疾病集合的过滤层
		file_get_contents('http://'.$hostname.'/explanationWord/API/concept_disease.getFinalDisease.php?sid='.$sid);
		//file_get_contents('http://'.$hostname.'/explanationWord/API/concept_disease.getFinalDisease.php?sid='.$sid);
		file_get_contents('http://'.$hostname.'/explanationWord/API/concept_disease.filterUnrelatedDisease.php?sid='.$sid);
		file_get_contents('http://'.$hostname.'/explanationWord/API/concept_disease.filterUserInfo.php?sid='.$sid);
	
	//第五步
	//根据过滤之后的疾病集合，获取最终疾病的json
	$page_result = file_get_contents('http://'.$hostname.'/explanationWord/API/concept_disease.showfinalDiseaseDetails.php?sid='.$sid);
	
	//echo $result;
	
	$symptom_diseases = json_decode($page_result,true);
	//print_r($symptom_diseases);
	//exit;
	$result = array();
	
	foreach($symptom_diseases as $disease){
		//similar_text($userInputs[$disease_id]["disease_name"],$disease["disease_name"],$similarity);
		//echo $disease."=>".$userInputs[$disease_id]["disease_name"]."####".(round($similarity)/100)."<br>";
		$result[$disease["disease_site_id"]] = ($disease["percentage"]/100);
	}
	$userInputs[$disease_id]["result"] = $result;
	
	if(isset($result[$disease_id])){//disease found!
		if(count($result)==1){
			$userInputs[$disease_id]["accuracy"] = 	1;
		}else{
			$userInputs[$disease_id]["accuracy"] = 1/count($result);
		}
		return true;
	}else{
		//$userInputs[$disease_id]["accuracy"] = 	0;//未在打开的页面中查找到该疾病，则应进行记录。
		//getSymptomsFormDisease($disease_id);
		return false;
	}
	
	/*if(count($result)<=5&&count($result)>0){//小于等于5时，result的疾病相似度累乘
		$accuracy = 1;
		foreach($result as $result_disease_id => $similarity){
			$accuracy = $accuracy*$similarity;
		}
		$userInputs[$disease_id]["accuracy"] = 	$accuracy;
	}else{
		$userInputs[$disease_id]["accuracy"] = 0;
	}*/
}

function transformArryToText($concepts){
	$group_concept = array();
	foreach($concepts as $group => $concept){
		$group_concept[] = implode(' ',$concept);
	}
	return implode(',',$group_concept);
}
function autoTestWriteLog($disease_id,$disease_json,$text_concept,$page_result){
	global $start_time,$db;
	$sql ="SELECT count(auto_id) AS num FROM autotesterror WHERE disease_site_id='".$disease_id."' AND update_at = '".$start_time."'";
	$result = $db->query($sql)->fetch_array();
	//echo 
	if($result["num"] == 0){
		$sql = "INSERT INTO autotesterror (disease_site_id,disease_json,text_concept,page_result,update_at) VALUES ('".$disease_id."','".$disease_json."','".$text_concept."','".$page_result."','".$start_time."')";
		$result = $db->query($sql);
		if($result){
			return true;
		}
		return false;
	}
	return true;
	
}
?>