<?php
	$lang = isset($_COOKIE["lang"])?$_COOKIE["lang"]:"en";
	if($lang=="en"){
		$langText = include("../lang/en-us.php");//英语版本
	}else if($lang=="nl"){
		$langText = include("../lang/nl-NL.php");//荷兰语版本
	}else{
		echo "语言版本加载失败";
	}
//	if(isset($_COOKIE['session_id'])){
//		session_id($_COOKIE['session_id']);
//		session_start();
// 	}
	//$sid = $_SESSION["seesion_id"];
	if(isset($_COOKIE['lang'])){
		$_SESSION["lang"] = $_COOKIE['lang'];
	}else{
		$_SESSION["lang"] = 'en';
	}

?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--引用script文件-->
<script src="js/jquery.min.js"></script>
<script src="js/function.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js" ></script>
<!--引用css件-->
<link href="css/bootstrap.css" rel="stylesheet">
