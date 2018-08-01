<?php
//echo $_GET["sid"];
include("../logic/common.inc.php");
if(!isset($_SESSION['setting_a'])&&!isset($_SESSION['setting_b'])){
	$_SESSION['setting_a'] = "1";//设置一个默认值
	$_SESSION['setting_b'] = "1";//设置一个默认值
}
$user_inputer = trim(addslashes(strtolower($_GET["user_input"])));//转化大写字母为小写+给逗号等加反斜线
$flag=$_GET["flag"];
$db = getDB();
$words = array();
$group_word = array();
$cleared_word = array();
$words = breakUpStr($user_inputer);//分割字符串
//循环取单词 判断是否为 stop word
foreach($words as $group_index => $group){
	$group_word = array();
	foreach($group as $word_index => $word){
		$sql = 'SELECT is_del FROM concept WHERE keyword = "'.trim($word).'"';
		$result = $db->query($sql)->fetch_array();
		//echo $word."is_del = ".$result["is_del"]."\n";
		if($result["is_del"]==-1){
			unset($words[$group_index][$word_index]);//删除
		}else{
			$group_word[]=$word;
		}
	}
	$cleared_word[] = $group_word;
	//print_r($group);
}
if($flag == "related"){//如果参数为related 则word数据存入session
	$_SESSION["word"]= json_encode($cleared_word);//将word信息添加到session数组里面
}else{
	$_SESSION["unrelated_word"]= json_encode($cleared_word);//将unrelated_word信息添加到session数组里面
}
echo  $user_inputer;
//print_r($_SESSION);
//echo "<pre>";
//print_r( breakUpStr($user_inputer));
//echo "</pre>";
//根据逗号和空格逐级打算数组，逗号为分组标志。第一步
function breakUpStr($str){
	$group_strs = array();
	$breakUpStrs = array();
	$words = array();
	//以逗号为标志 分组字符串。
	$group_strs = explode(",", $str);
	foreach($group_strs as $group_string){
		$temp_array =explode(" ",$group_string);
		$trimblanked = trimblank($temp_array);
		if(!in_array($trimblanked,$words)){
			$words[] = $trimblanked;
			$breakUpStrs[] =$trimblanked; 
		}
		unset($temp_array);
	}
	return $breakUpStrs;
}

//去除打散的字符串数组中空白的项
function trimblank($strings){
	$trimed_array = array();
	foreach($strings as $value){
		if($value==""||$value==null){
			
		}else{
			$trimed_array[] =$value; 
		}
	}
	return $trimed_array;
}
?>