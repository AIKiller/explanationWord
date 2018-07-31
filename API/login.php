<?php
include("../logic/common.inc.php");
$db=getDB();
//设置为中国的标准时间
date_default_timezone_set('PRC');  
$password = trim($_GET["password"]);
$username = strtolower(trim($_GET["username"]));
$sessionServerUrl = "http://localhost/explanationWord/API/getSession_id.php";

if($password == '123456'){
	$sid = login($username);
	echo $sid;
}else{
	echo  0;
}
function login($username){
	global $db,$sessionServerUrl;
	$sql = "SELECT auto_id FROM userInformation WHERE name='".$username."'";
	$result= $db->query($sql);
	if($result->num_rows){
		//存在当前用户设置cookies
		$sql="SELECT session_id FROM userInformation WHERE name='".$username."'";
		$result=$db->query($sql)->fetch_array();
		$sql="UPDATE userinformation SET last_at='".date('Y-m-d   H:i:s')."' WHERE name='".$username."'";
		$db->query($sql);
		$sid = $result["session_id"];
	}else{
		//存储当前用户
		$sid = file_get_contents($sessionServerUrl);	
		//setcookie("session_id",$sid,time()+3600*24*356);//设置一年的过期时间
		$sql = "INSERT INTO userInformation (name,session_id,create_at,last_at)VALUES('".$username."','".$sid."','".date('Y-m-d   H:i:s')."','".date('Y-m-d   H:i:s')."')";
		$db->query($sql);
		//当前用户是新用户			
		session_id($sid);
		session_start();
		$_SESSION["lang"] = $COOKIE["lang"];
	}
	return $sid;
}
?>