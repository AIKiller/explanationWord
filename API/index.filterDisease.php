<?php
include("../logic/common.inc.php");
$_SESSION["userInformationSet"] = $_GET["userInformations"];
$userInformations = json_decode($_GET["userInformations"],true);//用户信息
$db = getDB();
$disease_site_id = array();
// $ages = array();
// $sexs = array();
$index = 0;
foreach ($userInformations as $field => $value) {
	if($field =="age"&&$value !=""){
		$sql="SELECT site_ID FROM general_awareness WHERE min_age>".$value." or max_age<".$value;//下限大于用户年龄，上限小于用户年龄
		//得到要排除的疾病
		$result = $db->query($sql);
		while($row = $result->fetch_array()){
			$disease_site_id[] = $row["site_ID"];
		}
	}else if($field == "sex"&&$value !=""){
		$sql="SELECT  site_ID FROM  general_awareness WHERE  sex != '".$value."' and sex != ''";//用户为男人，排除掉所有女人的疾病可能
		//获取不满足性别条件的疾病列表
		$result = $db->query($sql);
		while($row = $result->fetch_array()){
			$disease_site_id[] = $row["site_ID"];
		}
	}else if($field=="weight"||$field=="high"){
		continue;
	}else{
		if($value =="")continue;
		if($value == "0"){
			//如果你吸烟，不能排除任何疾病，如果不吸烟，才能排除掉所有吸烟的疾病
			$sql="SELECT site_ID FROM general_awareness WHERE ".$field." !=".$value;
			$result = $db->query($sql);
			while($row = $result->fetch_array()){
				$disease_site_id[] = $row["site_ID"];
			}
		}
	}
	
}

$disease_site_id = array_unique($disease_site_id);//数组去重
$disease_site_id = array_values($disease_site_id);//数组键值重新索引
//但是年龄和性别对于结果有影响

//print_r($disease_site_id);

$_SESSION["general_awareness"] = json_encode($disease_site_id);

echo count($disease_site_id);
?>