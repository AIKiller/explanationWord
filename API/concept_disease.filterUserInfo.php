<?php
    include_once '../logic/header.php';
	//过滤不可能疾病
	session_id(trim($_GET["sid"]));
	session_start();
	$general_awareness = json_decode($_SESSION["general_awareness"],true);		//不可能的疾病（一维数组）

	$finalDisease = json_decode($_SESSION["finalDisease"],true);				//获取疾病（一维数组）
	//echo json_encode($finalDisease);
	if(count($general_awareness) == 0)
	{
		$_SESSION["finalDisease"] = json_encode($finalDisease);
		if(isset($_SESSION["finalDisease"]))										
		{
			echo "1";
		}else{
			echo "0";
		}
		exit();
	}
	$finalDisease = array_diff($finalDisease,$general_awareness);				//取差集       $finalDisease - （所有疾病） - （不可能疾病）
	$finalDisease = array_values($finalDisease);
	$_SESSION["finalDisease"] = json_encode($finalDisease);		
	if(isset($_SESSION["finalDisease"]))										
	{
		echo "1";
	}else{
		echo "0";
	}
?>