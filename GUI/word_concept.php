<html>
	<head>
		<?php include("tools/headr.php");?>
		<title>Word Processing</title>
		<link type="text/css" rel="stylesheet" href="css/word_concept.default.css" />
	</head>
	<body>
		<a onclick="parent.changeLanguage()" style="display: table-cell;cursor: pointer;">change language</a>
		<h1><?php echo $langText["word_concept"]["MAIN_TITLE"];?></h1>
		<?php 
			if(isset($_SESSION["userInformationSet"])){
				$userInformationSet = json_decode($_SESSION["userInformationSet"],true);
				if(is_check($userInformationSet)){//说明所有的数据都已经填写过了
				
					echo '<button class="btn btn-danger" id="userinformation" onclick="parent.setUserPersonInformation()">'.$langText['index']["userInformation"]["MODEL_BUTTON"].'</button>';
			
				}else{
					
					echo '<button class="btn btn-info" id="userinformation" onclick="parent.setUserPersonInformation()">'.$langText['index']["userInformation"]["MODEL_BUTTON"].'</button>';
					
				}
			}else{
			
				echo '<button class="btn btn-info" id="userinformation" onclick="parent.setUserPersonInformation()">'.$langText['index']["userInformation"]["MODEL_BUTTON"].'</button>';
			
			}
			
			function is_check($userInformationSet){
				$is_ckeck = false;
				foreach($userInformationSet as $field => $value){
					if($value!=""){
						$is_ckeck = true;
					}
				}
				return $is_ckeck;
			}
			
		?>
		
		<table class="table">
			<tr>
				
				<td colspan="2"><h5><?php echo $langText["word_concept"]["WORD_TITLE"];?></h5><textarea type="text" id="user_input" rows="5" class="form-control" placeholder="<?php echo $langText['word_concept']['WORD_TITLE_PLACEHOLDER'];?>"></textarea></td>
				
			</tr>
			<tr>
				
				<td colspan="2">
					<h5><?php echo $langText["word_concept"]["NOT_WORD_TITLE"];?></h5><textarea type="text" id="unrelated_concept" rows="2" class="form-control" placeholder="<?php echo $langText['word_concept']['NOT_WORD_TITLE_PLACEHOLDER'];?>"></textarea>
					<input type="button" id="submit" value="<?php echo $langText['concept_disease']['SUBMIT'];?>" class="btn btn-info" onclick="stopWord_clear()" style="float: right;margin-top:2%;" />
				</td>
				
			</tr>
			<tr>
				<td colspan="2">
					<div class="panel panel-info">
						<div class="panel-heading">
						    <?php echo $langText['word_concept']['SYNONYM_CONJUGATE'];?>
						</div>
						<div id="replace_synonymConjugate_word" class="panel-body"></div>
					</div>
				</td>
			</tr>
		</table>
	</body>
	<script>
	//检测页面回车,调用函数
$(document).ready(function(){
	var sid = $("#sid",parent.document).html();
	//alert(sid);
	//alert(sid);
	document.onkeydown=function(e){
		var ev = e || window.event;//获取event对象  
		var objElement = ev.target || ev.srcElement;//获取事件源
	//console.log(objElement);
		if (ev.keyCode == 13){
			//parent.openProgressModal();
			stopWord_clear();
		}
	}
});
//打散字符串；去除stop word ；返回去除回车的用户输入
function stopWord_clear(){
	var sid = $("#sid",parent.document).html();
	$("#submit").attr("disabled","disabled");//禁用输入按钮			
	var user_input = $("#user_input").val();//获取用户的输入
	$.ajax({
		type:"get",
		url:"../API/word_concept.stopWord_clear.php?user_input="+user_input+"&sid="+sid+"&flag=related",
		async:true,
		error:function(state){
			alert("error:"+state);
			$("#submit").removeAttr("disabled");
		},
		success:function(value){
			$("#user_input").val(value);
			$("#user_input").html(value);
			//replace_synonymConjugate_word();
			unrelated_stopWordClear();
			//unrelatedConcept_stopWordClear_and_replaceSynonymConjugate();
		}
	});
}
function unrelated_stopWordClear(){
	var sid = $("#sid",parent.document).html();
	var user_unrelatedConcept = $("#unrelated_concept").val();
	$.ajax({
		type:"get",
		url:"../API/word_concept.stopWord_clear.php?user_input="+user_unrelatedConcept+"&sid="+sid+"&flag=unrelated",
		async:true,
		error:function(state){
			$("#submit").removeAttr("disabled");
			alert("error:"+state);
		},
		success:function(value){
			$("#unrelated_concept").val(value);
			$("#unrelated_concept").html(value);
			replace_synonymConjugate_word();
		}
	});
}
function replace_synonymConjugate_word(){
	var sid = $("#sid",parent.document).html();
	$.ajax({
		type:"get",
		url:"../API/word_concept.replace_synonymConjugate_word.php?sid="+sid,
		async:true,
		error:function(state){
			$("#submit").removeAttr("disabled");
			alert("error:"+state);
		},
		success:function(value){
			//alert(value);
			//return;
			var number =1;
			var synonym_word = new Array();
			var replaced_word = JSON.parse(value);
			var show_str ='<table class="table">';
			for(index in replaced_word){
				show_str += '<tr><td>('+number+++')</td><td data-type="concept" id="word_item_'+index+'" bg="word_item">';
				for(word_id in replaced_word[index]){
					if(replaced_word[index][word_id]["type"] == 'synonym'){//此word为多义词，且用户第一次进行选择。
						if(replaced_word[index][word_id]["init"] == 0){
							
							//如果该单词为第一次输入的多义词，则记录并显示选择框
							synonym_word.push(replaced_word[index][word_id]);
					
						}else{
							for(var synonym_id in replaced_word[index][word_id]["concepts"]){
								show_str += '<p id="'+synonym_id+'"  word_id = "'+word_id+'"  type="synonym">'+replaced_word[index][word_id]["concepts"][synonym_id]+'</p>'
							
							}
						}
					}else if(replaced_word[index][word_id]["type"] == 'conjugate'){
						//该单词为conjugate，直接显示
						for(var synonym_id in replaced_word[index][word_id]["concepts"]){
							show_str += '<p id="'+synonym_id+'"  word_id = "'+word_id+'"  type="conjugate">'+replaced_word[index][word_id]["concepts"][synonym_id]+'</p>'
						}
				
					}else{
						alert('ERR');
					}
				}
				show_str += '</td><td>';
				show_str += '<div class="btn-group"><button type="button" style="margin-right: 25px;margin-bottom: 10px;" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo $langText["word_concept"]["INCREMENT_CONCEPT"]; ?> <span class="caret"></span></button><ul id="increment_'+index+'" class="dropdown-menu" role="menu" style="overflow-x: auto;height:114px;"></ul></div>';
				//show_str +='<div class="btn-group"><button type="button"  class="btn btn-danger" name="word_item_'+index+'" onclick=removeLineWord(this.name,"'+index+'")>edit</button></div>';
				show_str +='</td></tr>';
			}
			show_str +='<tr id="advisedConceptTr"><td colspan="3"><div id="advisedConcept"><strong><?php echo $langText["word_concept"]["ADVISED_CONCEPT"]; ?></strong><br></div></td></tr>'
			$("#replace_synonymConjugate_word").html(show_str+"</table>");
			selectMoreWord(synonym_word);
			sending = 1;
		}
	});
}
function selectMoreWord(words){
	var openModel = false;
	//console.log(words);
	var tableHtml = '<table class="table" style="width: 100%;" id="selectWordTable"><tr><th><?php echo $langText["word_concept"]["WORD"]; ?></th><th colspan="2"><?php echo $langText["word_concept"]["SYNONYM_CONJUGATE_TITLE"]; ?></th></tr>';
	for(var index in words){
		openModel = true;
		tableHtml +='<tr>';
		tableHtml +='<td style="vertical-align: middle;">'+words[index]["word"]+'</td>';
		/*if(typeof(words[index]["conjugate"]) == "object"){
			//无单词
			//alert(typeof(words[word_id]["conjugate"]));
			tableHtml += '<td style="text-align: left;">';
			for(var concept_id in words[index]["conjugate"]){
				tableHtml += '<button class="btn btn-default" index="'+words[index]["index"]+'" word_id="'+words[index]["word_id"]+'" onclick="toggleSelectWordBtn(this)" id="'+concept_id+'">'+words[index]["conjugate"][concept_id]+'</button>';
			
			}
			tableHtml += '</td>';
		}*/
		if(words[index]["type"] == "synonym"){
			//alert('synonym');
			tableHtml += '<td style="text-align: left;" type="selectArea">';
			for(var concept_id in words[index]["concepts"]){
				tableHtml += '<button class="btn btn-default" group="'+words[index]["group"]+'" index="'+words[index]["index"]+'" word_id="'+words[index]["word_id"]+'" onclick="toggleSelectWordBtn(this)" id="'+concept_id+'">'+words[index]["concepts"][concept_id]+'</button>';
			}
			tableHtml += '</td>';
		}
		tableHtml +='</tr>';
	}
	tableHtml +='</table>';
	$('#moreWord',parent.document).html(tableHtml);
	if(openModel){
		$('#selectSynonymWordH4',parent.document).html('<?php echo $langText["word_concept"]["SELECT_SYNONYM_WORD"]?>');
		$('#saveSelectBtn',parent.document).html('<?php echo $langText["word_concept"]["SAVE_SELECTION"]?>');
		$('#saveSelectBtn',parent.document).attr('onclick','saveSelect()');
		$("#selectTips",parent.document).html(' ');
		parent.toggleSynonymSelectModal();
	}else{
		parent.frames["ifram.concept_disease"].mergeGroupSymptom();
	}
	$("#submit").removeAttr("disabled");
}

function incrementGroupConcept(increment_concept){
	$("#user_input").html('');
	$("#user_input").val(increment_concept);
	stopWord_clear();
}
	function incrementGroupConcept(increment_concept){
	var user_input = $("#user_input").val();
	user_input += ","+increment_concept;
	$("#user_input").val(user_input);
	stopWord_clear();
}
function incrementConcept(increment_concept,group){
	var user_input = $("#user_input").val();
	var group_word = user_input.split(",");
	for(index in group_word){
		if(index == group){
			group_word[index] = increment_concept+" "+group_word[index];
		}else{
			group_word[index] = group_word[index];
		}
	}
	$("#user_input").val(group_word.join(","));
	stopWord_clear();
}
function addNonConcpet(concept){
	nowInput = $("#user_input").val();
	$("#user_input").attr("value",nowInput+","+concept);
	stopWord_clear();
}
function unrelatedConcept(unrelated_concept){
	var unrelated_concept_text= $("#unrelated_concept").val();
	if(unrelated_concept_text ==""){
		unrelated_concept_text = unrelated_concept;
	}else{
		unrelated_concept_text += ","+unrelated_concept;
	}
	$("#unrelated_concept").val(unrelated_concept_text);
	stopWord_clear();
}		
	</script>
</html>