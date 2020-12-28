<?php
	//	//过滤无关因子
	include("../logic/common.inc.php");	
	$db = getDB();
	$unrelated_groupSymps = json_decode($_SESSION["unrelated_groupSymps"],true);		// 获取不相关的症状集合，可能是多维数组
	$related_finalDisease = json_decode($_SESSION["related_finalDisease"],true);		// 获取可能的疾病数组，一维数组
	
	//没有取到不相关症状的集合，返回可能疾病列表，退出
	if(count($unrelated_groupSymps) == 0)
	{
		$_SESSION["finalDisease"] = json_encode($related_finalDisease);
		if(isset($_SESSION["finalDisease"]))
		{
			echo "1";
		}else{
			echo "0";
		}
		exit();
	}

	$unrelated_groupDisease = getDiseaseOfDb($unrelated_groupSymps);					//获得这些症状对应的疾病
	//print_r($unrelated_groupDisease);
	$temp_finalDisease = array_diff($related_finalDisease,$unrelated_groupDisease);		//取差集
	$temp_finalDisease = array_values($temp_finalDisease);
	$_SESSION["finalDisease"] = json_encode($temp_finalDisease); 
	//echo json_encode($temp_finalDisease);
	if(isset($_SESSION["finalDisease"]))
	{
		echo "1";
	}else{
		echo "0";
	}
	

	/**
     * getDiseaseOfDb()*
     * 获取数据库中症状对应的疾病
     *
     * @param Array $unrelated_groupSymps      
     * @return Array          
     */
	function getDiseaseOfDb($unrelated_groupSymps)
	{
		global $db;	
		$tempKey = 0;
		$unrelated_groupDisease = array();
		$temp_unrelated_groupDisease = array();
		foreach($unrelated_groupSymps as $key=>$value)
		{
			
			$sympsString=implode("','",$value);	//数组变成字符串，
			$sql = "SELECT DISTINCT(disease_site_id) FROM `dis_symp` WHERE `symptom_site_id` in ( '".$sympsString."')";
			$result = $db->query($sql);
			if($result->num_rows > 0){
                while($row = $result->fetch_array())
                {
                    $temp_unrelated_groupDisease[$key][] = $row["disease_site_id"];
                }
            }else{
                $temp_unrelated_groupDisease[$key] = array();
            }
			//取交集
			if($tempKey == 0)
			{
				$unrelated_groupDisease = $temp_unrelated_groupDisease[$key];													//
			}else{
				$unrelated_groupDisease = array_merge($temp_unrelated_groupDisease[$key],$unrelated_groupDisease);	//并集
			}
			$tempKey++;
		
		}
		$unrelated_groupDisease =array_values($unrelated_groupDisease);
		return $unrelated_groupDisease;
	}

?>