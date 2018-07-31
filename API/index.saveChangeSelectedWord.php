<?php
include("../logic/common.inc.php");
$db = getDB();
//echo "<pre>";
$selectedWord = json_decode($_GET["selectedWordObj"],true);
//print_r($selectedWord);
$symptom_information = array();
$group_keyword = json_decode($_SESSION["concepts"],TRUE);
//print_r($group_keyword);
if(isset($_SESSION["synonyms"]))
	$synonyms = json_decode($_SESSION["synonyms"],true);
else 
	$synonyms = array();
//print_r($selectedWord);
foreach($selectedWord as $word_id => $concepts){

	$group_keyword[$concepts["group"]][$word_id]["type"] = 'synonym';
	//$group_keyword[$concepts["group"]][$word_id]["synonym"] = array();
	$group_keyword[$concepts["group"]][$word_id]["concepts"] = $concepts["concepts"];
	$synonyms[$concepts["group"]][$word_id] = $concepts["concepts"];

}
//print_r($group_keyword);
$_SESSION["concepts"] = json_encode($group_keyword);
$_SESSION["synonyms"] = json_encode($synonyms);
?>