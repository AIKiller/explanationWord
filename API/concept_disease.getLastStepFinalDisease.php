<?php
session_id($_GET["sid"]);
session_start();
//获取session数据
$disease_site_ids = json_decode($_SESSION["disease_site_ids"],TRUE);
$concepts = json_decode($_SESSION["concepts"],TRUE);
			//echo "1";
array_pop($disease_site_ids);

if(count($concepts)<2){//如果concept的集合已经开始小于2，则停止循环删除
	$_SESSION["multi-morbidity"] = 3;//已经无法循环减少获取症状，停止程序死循环。
}else{
	$_SESSION["multi-morbidity"] = 2;
	$_SESSION["disease_site_ids"] = json_encode($disease_site_ids);
}


?>