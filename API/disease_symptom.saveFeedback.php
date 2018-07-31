<?php
include("../logic/common.inc.php");
date_default_timezone_set('Europe/Amsterdam');//设置荷兰时区
$feedback_concepts = json_decode($_GET["feedback_concepts"],true);
$feedback_name = $_GET["feedback_name"];
$concepts = json_decode($_SESSION["concepts"],TRUE);
$unrelatedConcept = json_decode($_SESSION["unrelated_concepts"],TRUE);
$feedback_info = json_decode($_SESSION["feedback_info"],TRUE);


$db = getDB();//链接数据库


$feedback_concepts = array();

$feedback_unrelatedConcepts = array();


$feedback_concepts = getConceptFormArray($concepts);


$feedback_unrelatedConcepts = getConceptFormArray($unrelatedConcept);



$sql = "INSERT INTO `useful_feedback`(`feedback_name`, `feedback_concepts`,`feedback_unrelatedConcept`,`feedback_info`) VALUES ('".$feedback_name."','".json_encode($feedback_concepts)."','".json_encode($feedback_unrelatedConcepts)."','".addslashes(json_encode($feedback_info))."')";
$result = $db->query($sql);
//获取concept的信息。
function getConceptFormArray($words){
	$feedback_concepts = array();
	foreach($words as $group_index => $group_words){
		foreach ($group_words as $word_id => $word) {
			# 循环取值
			foreach ($word["concepts"] as $concept_id => $concept) {
				$feedback_concepts[$group_index][$concept_id] = $concept;
			}
		}

	}
	return $feedback_concepts;
}

//单词数组组合为字符串。
function wordArrToString($wordArr){
	$strings = array();
	foreach($wordArr as $words){
		$strings[] = implode(' ',$words);
	}
	return implode(',',$strings);
}


$outHtml = "<h5 style='display: inline-block;margin: 6px 2px;'>".$langText["index"]["useful"]["MODEL_USER_NAME"]."</h5> ".$feedback_name."<br>";
$outHtml .= "<h5 style='display: inline-block;margin: 6px 2px;'>".$langText["index"]["useful"]["MODEL_CONCEPT"]."</h5> ".wordArrToString($feedback_concepts)."<br>";
$outHtml .= "<h5 style='display: inline-block;margin: 6px 2px;'>".$langText["index"]["useful"]["MODEL_UNRELATED_CONCEPT"]."</h5> ".wordArrToString($feedback_unrelatedConcepts).'<br>';
$outHtml .= "<h4 style='margin: 12px 0px;'>".$langText["index"]["useful"]["SYSTEM_RESULT"]."</h4><br>";
//print_r($feedback_info);
foreach($feedback_info as $index => $disease_info){
	$found_symptoms = array();
	$not_found_symptoms = array();
	$outHtml .="<h5 style='display: inline-block;margin: 3px 4px;'>".$langText["index"]["useful"]["DISEASES"]."</h5>".$disease_info["name"]."</br>";
	$outHtml .="<h5 style='display: inline-block;margin: 3px 4px;'>ICD:</h5>".$disease_info["icd"]."</br>";
	$outHtml .="<h5 style='display: inline-block;margin: 3px 4px;'>ICPC:</h5>".$disease_info["icpc"]."</br>";
	$outHtml .="<h5 style='display: inline-block;margin: 3px 4px;'>PREV:</h5>".$disease_info["prev_rf"]."</br>";
	$outHtml .="<h5 style='display: inline-block;margin: 3px 4px;'>Comb:</h5>".$disease_info["comb"]."</br>";
	foreach ($disease_info["symps"] as $site_id => $symp) {
		# code...
		if($symp["flag"] == 1){
			$found_symptoms[] = $symp["name"];
		}else if($symp["flag"] == 0){
			$not_found_symptoms[] = $symp["name"];
		}
	}
	$outHtml .="<h5 style='display: inline-block;margin: 3px 2em;'>".$langText["index"]["LABEL_FOUND_SYMPTOM"]."</h5>".implode($found_symptoms, ';')."<br>";
	$outHtml .="<h5 style='display: inline-block;margin: 3px 2em;'>".$langText["index"]["LABEL_NOT_FOUND_SYMPTOM"]."</h5>".implode($not_found_symptoms, ';')."<br>";
}
	//echo $outHtml;
/*   Output pdf   */
	require_once('pdf/tcpdf.php');
	//实例化 
	$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false); 
	// 设置文档信息 
	$pdf->SetCreator('Useful'); 
	$pdf->SetAuthor(''); 
	$pdf->SetTitle(''); 
	$pdf->SetSubject('TCPDF Tutorial'); 
	$pdf->SetKeywords('TCPDF, PDF, PHP'); 
	 
	// 设置默认等宽字体 
	$pdf->SetDefaultMonospacedFont('courier'); 
	 
	// 设置间距 
	$pdf->SetMargins(15, 27, 15); 
	$pdf->SetHeaderMargin(5); 
	$pdf->SetFooterMargin(10); 
	 
	// 设置分页 
	$pdf->SetAutoPageBreak(TRUE, 25); 
	 
	// set image scale factor 
	$pdf->setImageScale(1.25); 
	 
	// set default font subsetting mode 
	$pdf->setFontSubsetting(true); 
	 
	//SetFont
	$pdf->SetFont('courier', '', 12); 
	 
	$pdf->AddPage(); 

	$pdf->writeHTML($outHtml);
	 
	$dataNow = date('m.d.Y_his');
	//输出PDF 
	$pdf->Output($feedback_name.'-'. $dataNow .'.pdf', 'D'); 

?>