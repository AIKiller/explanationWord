<?php
    include_once 'header.php';

	$php_self = php_self();
	if($php_self != "login.php"){
		//先暂时这个样。			
		if(!isset($_GET["sid"])){
			echo "参数个数错误";
			exit;
		}
		session_id($_GET["sid"]);
		session_start();
		//获取用户的语言版本
		$lang = isset($_SESSION["lang"])?$_SESSION["lang"]:"en";//其他的页面通过session获取语言版本信息

	}else{
		$lang = isset($_GET["lang"])?$_GET["lang"]:"en";//登录页面通过cookie获取语言版本信息
	}
	//加载语言版本配置文件和数据表名称	
	if($lang == "en"){
		$langText = include("../lang/en-us.php");//英语版本
	}else if($lang == "nl"){
		$langText = include("../lang/nl-NL.php");//荷兰语版本
	}else{
		echo "语言版本加载错误";
		exit;
	}
	//echo DBNAME;
	//设置头信息
	header("Content-type: text/html; charset=utf-8");//设置发送头为utf-8的编码
	ini_set('date.timezone','Asia/Shanghai');//设置上海时区
	$url = $_SERVER["REQUEST_URI"];
	function getDB(){
		global $dbName;
		$host = "222.173.149.146";
		$usr = "root";
		$psw = "AILab@3DBySeasonTest";
		$port = 12306;
		//$psw = "";
		 //= "Symptom_Cleaners";
		$mysqli = new mysqli($host,$usr,$psw,$dbName,$port) or die(mysqli_connect_error());
		$mysqli->query("SET NAMES utf8");//与数据库建立utf8的连接。
		return $mysqli;
	}
	function getDBbyDbName($dbName){
		$host = "222.173.149.146";
		$usr = "root";
		$psw = "AILab@3DBySeasonTest";
		$port = 12306;
		$mysqli = new mysqli($host,$usr,$psw,$dbName,$port) or die(mysqli_connect_error());
		$mysqli->query("SET NAMES utf8");
		return $mysqli;
	}
	function php_self(){
		$php_self = substr($_SERVER["PHP_SELF"],strrpos($_SERVER["PHP_SELF"],'/')+1);
		return $php_self;
	}
?>