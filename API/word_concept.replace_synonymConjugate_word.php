<?php
include("../logic/common.inc.php");
$db = getDB();
$words = array();
$concepts = array();
$replace_words = array();
if(isset($_SESSION["synonyms"]))
	$synonyms = json_decode($_SESSION["synonyms"],true);
else
	$synonyms = array();

$words = json_decode($_SESSION["word"],TRUE);
//print_r($words);
searchConjugateOrSynonym($words);//替换关联因子的数据
$concepts = array_values($concepts);
$_SESSION["synonyms"] = json_encode($new_synonyms);
echo $_SESSION["concepts"]= json_encode($concepts);//将word信息添加到session数组里面
//计算无关因子的信息
$concepts = array();
$unrelated_word = json_decode($_SESSION["unrelated_word"],TRUE);
searchConjugateOrSynonym($unrelated_word);//替换无关因子的数据
$_SESSION["unrelated_concepts"]= json_encode($concepts);//将word信息添加到session数组里面

//unset($_SESSION["word"]);//读取出来后 删除掉此信息，释放资源空间
//循环取word并在同义词/变种词变中匹配
$new_synonyms = array();
//print_R($synonyms);
//print_R($words);
function searchConjugateOrSynonym($words){
	global $concepts,$new_synonyms,$synonyms;
	foreach($words as $group_index => $words){
		$word_index_num = 0;
		foreach($words as $word_index => $word){
			$wordArray = array();
			$word_id = getWord_id($word);
			//echo $word_id."<br>";
			//判断该单词是否已经存在于同义词session数组
			$flag = ifExistSynonyms($group_index,$word_id);
			//var_dump($flag);
			if($flag){
					//存在，直接获取信息。
					$wordArray["type"] = 'synonym';
					$wordArray["init"] = 1;//该参数用来描述当前为多义词的word是否已经被用户选择过
					if(!isset($synonyms[$group_index][$word_id])){
						throw new Exception('多义词 定位错误 行号：'.__LINE__); 
					}
					$wordArray["concepts"] = $synonyms[$group_index][$word_id];
					$new_synonyms[$group_index][$word_id] = $synonyms[$group_index][$word_id];//重新替换一下同义词表
			}else{
				//echo "asdasdd";
				$wordArray["type"] = 'conjugate';
			
				$wordArray["concepts"] =searchInConjugate($word);
					//print_r($wordArray);
				if(count($wordArray["concepts"])==0){
					$wordArray["type"] = 'synonym';
					$wordArray["concepts"] = searchInSynonym($word);
					if(count($wordArray["concepts"])==0){
						continue;
					}
					$wordArray["init"] = 0;//该参数用来描述当前为多义词的word是否已经被用户选择过
				}
				
			}
			$wordArray["word"] = $word;
			$wordArray["word_id"] = $word_id ;
			$wordArray["group"] = $group_index;
			$wordArray["index"] = $word_index_num++;
			$concepts[$group_index][$word_id] = $wordArray;
		
		}
	}
}
//print_r($concepts);

//echo json_encode($concepts);
//echo "<pre>";
//print_r($concepts);
//echo "</pre>";


function searchInConjugate($word){
	global $db,$group_index;
	$conjugateWords = array();
	$sql = 'SELECT DISTINCT(concept.concept_id),keyword FROM concept INNER JOIN concept_conjugate ON concept.concept_id = concept_conjugate.concept_id AND concept_conjugate.conjugate ="'.$word.'" AND is_del = 1';
	$result = $db->query($sql);
	if($result){
		while($row =$result->fetch_array()){
			$conjugateWords[$row["concept_id"]] = $row["keyword"];
		}
	}		
	return	$conjugateWords;
}
function searchInSynonym($word){
	global $db;
	$synonymWords = array();
	$sql = 'SELECT concept_id,keyword FROM concept WHERE keyword in (SELECT synonym FROM concept INNER JOIN concept_synonym ON concept.concept_id = concept_synonym.concept_id WHERE keyword = "'.$word.'") AND is_del = 1';
	$result = $db->query($sql);
	if($result){
		while($row =$result->fetch_array()){
			$synonymWords[$row["concept_id"]] = $row["keyword"];
		}
	}
	return	$synonymWords;
}
function getWord_id($word){
	global $db;
	$word_id = '';
	$sql = 'SELECT concept_id FROM concept WHERE keyword = "'.$word.'"';
	$result = $db->query($sql);
	if($result){
		while($row = $result->fetch_array()){
			$word_id = $row["concept_id"];
		}
	}
	return $word_id;
}
function ifExistSynonyms($group_index,$word_id){
	global $synonyms;
	if(count($synonyms)==0){
		return false;
	}
	if(isset($synonyms[$group_index][$word_id])){
		return true;
	}
	return false;
}
?>