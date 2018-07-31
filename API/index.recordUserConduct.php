<?php
include("../logic/common.inc.php");
ini_set('date.timezone','Asia/Shanghai');//设置上海时区
//链接数据库
$db = getDB();

unset($_SESSION["word"]);
if(isset($_SESSION["synonyms"])){
	unset($_SESSION["synonyms"]);
}
if(isset($_SESSION["unrelated_concepts"])){
	unset($_SESSION["unrelated_concepts"]);
}
$_SESSION["multi-morbidity"] = -1;
$user_startTime = $_GET["user_accessTime"];
$user_name = $_GET["userName"];
$user_endTime =  date('Y-m-d H:i:s',time());
$user_duration = calculateUserDuration();
$user_ip = getClientIP();
$user_sessionId = $_GET["sid"];
$sql = 'INSERT INTO user_conduct(session_id,name,start_time,end_time,duration,ip)VALUES("'.$user_sessionId.'","'.$user_name.'","'.$user_startTime.'","'.$user_endTime.'","'.$user_duration.'","'.$user_ip.'")';
$db->query($sql);
//根据开始和结束的时间计算用户停留的时间
function calculateUserDuration(){
 	global $user_startTime,$user_endTime;
	$user_duration = strtotime($user_endTime)-strtotime($user_startTime);
	return $user_duration;//返回两个时间相差的秒数。
}
//获取客户端的ip的地址
function getClientIP(){
	$ip = "";
	if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])
	{
	    $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
	}
	elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])
	{
	    $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
	}
	elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"])
	{
	    $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
	}
	elseif (getenv("HTTP_X_FORWARDED_FOR"))
	{
	    $ip = getenv("HTTP_X_FORWARDED_FOR");
	}
	elseif (getenv("HTTP_CLIENT_IP"))
	{
	    $ip = getenv("HTTP_CLIENT_IP");
	}
	elseif (getenv("REMOTE_ADDR"))
	{
	    $ip = getenv("REMOTE_ADDR");
	}
	else
	{
	    $ip = "Unknown";
	}
	return $ip ;
}


?>