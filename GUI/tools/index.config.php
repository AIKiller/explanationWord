<?php 
	//根据时候含有用户名参数来判断用户时候登入。如果未登录则跳转登陆页面
	if(!isset($_COOKIE["userName"])){
		echo '未登录的用户';
		$url  = "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/explanationWord/GUI/login.php";
		#header("Location: $url"); 
		echo "<script type='text/javascript'>window.location.replace('".$url."');</script>";
		exit;
	}
	ini_set('date.timezone','Asia/Shanghai');//设置上海时区
	
	if(isset($_SESSION["userInformationSet"])){
		$userInformationSet = json_decode($_SESSION["userInformationSet"],true);
		//print_R($userInformationSet); 
	}
	echo '<script>$(document).ready(function(){$("#sid").html("'.$_COOKIE['session_id'].'");updateFilterSet("open");})</script>';
		
		
	//$sessionServerUrl = "http://".$_SERVER["SERVER_NAME"]."/explanationWord/API/getSession_id.php";
	/*if(isset($_COOKIE['session_id'])){
		session_id($_COOKIE['session_id']);
		session_start();
		if(isset($_SESSION["userInformationSet"])){
			$userInformationSet = json_decode($_SESSION["userInformationSet"],true);
			//print_R($userInformationSet); 
		}
		echo '<script>$(document).ready(function(){$("#sid").html("'.$_COOKIE['session_id'].'");updateFilterSet("open");})</script>';
	}else{
		session_start();
		$sid = session_id();
		session_write_close();
		@setcookie("session_id",$sid,time()+3600*24*356);//设置一年的过期时间
		echo '<script>$(document).ready(function(){$("#sid").html("'.$sid.'");})</script>';

	}*/
	echo '<script>$(document).ready(function(){$("#user_name").html("'.$_COOKIE["userName"].'");})</script>';
	$user_accessTime = date('Y-m-d H:i:s',time());
	echo '<script>$(document).ready(function(){$("#user_accessTime").html("'.$user_accessTime.'");})</script>';	
?>