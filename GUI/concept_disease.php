<html>
	<head>
		<title><?php echo $langText["SYSTEM_TITLE_NAME"];?></title>
		<?php include("tools/headr.php");?>
		<style>
			.badge{margin-left:5px;}
			#panel_body .btn{margin-top:5px;margin-bottom:5px;width:100%;text-align: left;text-indent:1.2em;}
			#panel_body .btn-info{background-color: #5CB85C;border-color: #5CB85C;color: #FFFCFC} 
			#panel_body .btn-info:hover,.btn-success:active{border-color:#5CB85C;}
			#panel_body .btn-warning {color: #000000;background-color: #5AEC0B;border-color: #5AEC0B;}
			#panel_body .btn-warning:hover{border-color:#5AEC0B;}
			#panel_body .btn-danger {color: #FFFFFF;background-color: #FF0000;border-color: #FF0000;}
			#panel_body .btn-danger:hover{border-color:#FF0000;}
			.panel-body{font-weight: bold;}
			.panel{box-shadow: 0 1px 1px rgba(0, 0, 0, 0.5);}
			.panel>p{padding: 3px 15px;}
			#panel_body .btn-danger>.badge{color: #ffffff;width: 50px;text-indent: 0em;float: right;margin-right:1%;line-height:1.1;margin-top: 6px;}
			
			#panel_body .badge{color: #3C7641;width: 50px;text-indent: 0em;float: right;margin-right:1%;line-height:1.1;margin-top: 6px;}
			#panel_body{width:98%;}
			.sm-screen-left{
					float:left;
				}
				.sm-screen-right{
					float: right;
    				width: 53%
				}
			#mergeGroupRelatedSymptom button{ margin-top:2%;}
			#reachGroupDisease button{ margin-top:2%;}
			#sortFunction button{ margin-top:2%;}
			.rare-disease-left{
			    width: 25px;
			    height: 40px;
			}
			.glyphicon {
				top:20px;
			}
			#panel_body .btn {
				text-indent: 0.1em;
			}
			@media (max-width:550px) {
				.comb{
					    float: inherit;
					    display: block;
					    width: 170px;
					    margin-top: 8px;
				    }
				
				.sm-screen-left{
					float:left;
					line-height: 4;
				}
				.sm-screen-right{
					float: right;
    				width: 38%;
				}
			}
		</style>
		
	</head>
	<body>
		<h1><?php echo $langText["concept_disease"]["PAGE_TITLE"];?></h1>
		<div id="panel_title">
			<div id="useful"><h4 style="display: inline-block;margin-right: 20px;"><?php echo $langText["concept_disease"]["USER_RESULT_LABEL"];?></h4><button class="btn btn-info" id="launch" data-toggle="modal" data-target="#myModal" onclick="parent.toggleModel()" style="display: inline-block;"><?php echo $langText['index']['useful']['MODEL_BUTTON']; ?></button> <button class="btn btn-info" id="controlBtn" data-toggle="modal" data-target="#controlModal" onclick="parent.toggleModelControl()" style="display: inline-block;"><?php echo $langText['index']['control']['MODEL_BUTTON']; ?></button></div>		
			<h4><?php echo $langText["concept_disease"]["RELATED_SYPTOM_NUMBER"];?></h4>
			<div id="mergeGroupRelatedSymptom"></div>
			<h4><?php echo $langText["concept_disease"]["UNRELATED_SYPTOM_NUMBER"];?></h4>
			<div id="mergeGroupUnrelatedSymptom"></div>
			<h4><?php echo $langText["concept_disease"]["DISEASE_NUMBER"];?></h4>
			<div id="reachGroupDisease"></div>
			<div id="sortFunction"></div>
		</div>
		<div id="panel_body">
			<h4 style="display: inline-block;"><?php echo $langText["concept_disease"]["DISEASE_NUMBER"];?></h4>
			<div id="getFinalDisease"></div>
			<!--<div id="debug"></div>-->
		</div>

	</body>
<script>
var _finalDiseases = new Object();
//以下是 主程序 函数
//查找每个分组的症状列表
function mergeGroupSymptom(){
	var sid = $("#sid",parent.document).html();
	//alert(concepts);
	parent.openProgressModal();
	parent.setProgress(10,'<?php echo $langText["concept_disease"]["LOADING_WAIT"];?>');
	$.ajax({
			type:"get",
			url:"../API/concept_disease.mergeGroupSymptom.php?sid="+sid,
			async:true,
			error:function(state){
				alert("error:"+state);
			},
			success:function(value){
				//reachGroupDisease();
				
				reachGroupDisease();
				//alert(value);
				//document.write(value);
				//return ;
				//$("#debug").html(value);
				var show = JSON.parse(value);
				var Messages = '';
				for(var concept in show["related_symptoms_number"] ){
					Messages += '<button class="btn btn-primary" type="button" style="margin-left:8px;margin-top: 8px;">';
					Messages += concept+'<span class="badge">'+show["related_symptoms_number"][concept]+'</span>';
					Messages += '</button>';
				}
				$("#mergeGroupRelatedSymptom").html(Messages);
				Messages = '';
				for(var concept in show["unrelated_symptoms_number"] ){
					Messages += '<button class="btn btn-danger" type="button" style="margin-left:8px;margin-top: 8px;">';
					Messages += concept+'<span class="badge">'+show["unrelated_symptoms_number"][concept]+'</span>';
					Messages += '</button>';
				}
				$("#mergeGroupUnrelatedSymptom").html(Messages);
			}
	})
}
//寻找每个分组的疾病
function reachGroupDisease(){
	var sid = $("#sid",parent.document).html();
	parent.setProgress(20,'<?php echo $langText["concept_disease"]["LOADING_WAIT"];?>');
	$.ajax({
			type:"get",
			url:"../API/concept_disease.reachGroupDisease.php?sid="+sid,
			async:true,
			error:function(state){
				alert("error:"+state);
			},
			success:function(value){
				
				//document.write(value);
				//return;
				getFinalDisease();
				//$("#debug").html(value);
				var show = JSON.parse(value);
				var Messages = '';
				for(var concept in show ){
					Messages += '<button class="btn btn-success" type="button" style="margin-left:8px;margin-top: 8px;">';
					Messages += concept+'<span class="badge">'+show[concept]+'</span>';
					Messages += '</button>';
				}
				$("#reachGroupDisease").html(Messages);
			}
	})
}
//获取最终疾病。
function getFinalDisease(){
	var sid = $("#sid",parent.document).html();
	//var count = getJsonObjLength(diseases);
	
	parent.setProgress(30,'<?php echo $langText["concept_disease"]["LOADING_WAIT"];?>');
	//console.log(count);
	$.ajax({
			type:"get",
			url:"../API/concept_disease.getFinalDisease.php?sid="+sid,
			async:true,
			error:function(state){
				alert("error:"+state);
			},
			success:function(value){
				//alert(value);
				if(value =="1"){
					filterUnrelatedDisease();
				}
			}
	})
}
//过滤无关因子关联的疾病
function filterUnrelatedDisease(){
	var sid = $("#sid",parent.document).html();
	parent.setProgress(40,'<?php echo $langText["concept_disease"]["LOADING_WAIT"];?>');
	$.ajax({
			type:"get",
			url:"../API/concept_disease.filterUnrelatedDisease.php?sid="+sid,
			async:true,
			error:function(state){
				alert("error:"+state);
			},
			success:function(value){
				if(value == "1"){
					filterUserInfo();
				}
			}
	})
}
//过滤用户基本信息关联的疾病(不可能的疾病)
//过滤无关因子关联的疾病
function filterUserInfo(){
	var sid = $("#sid",parent.document).html();
	parent.setProgress(50,'<?php echo $langText["concept_disease"]["LOADING_WAIT"];?>');
	$.ajax({
			type:"get",
			url:"../API/concept_disease.filterUserInfo.php?sid="+sid,
			async:true,
			error:function(state){
				alert("error:"+state);
			},
			success:function(value){
				if(value == "1"){
					showFinalDiseaseToPaper();
				}
			}
	})
}
//显示最终疾病
function showFinalDiseaseToPaper(){
	var sid = $("#sid",parent.document).html();
	parent.setProgress(60,'<?php echo $langText["concept_disease"]["LOADING_WAIT"];?>');
	$.ajax({
			type:"get",
			url:"../API/concept_disease.showfinalDiseaseDetails.php?sid="+sid,
			async:true,
			error:function(state){
				alert("error:"+state);
			},
			success:function(value){
				//alert(value);
				var show = JSON.parse(value);
				_finalDiseases = show["finalDisease"];//赋值给全局变量
				
				if(_finalDiseases.length == 1){
					//alert(_finalDiseases[0]["disease_site_id"]);
					//疾病结果唯一，查找相关联疾病。
					getSimilarDiseaseBySymptomSetTextEditDistance(_finalDiseases[0]["disease_site_id"]);
				
				}
				var system_diseaseDescripted_num;
				//setGroupIncremental(show["groupIncremental"]);
				//alert(value);
				//setSeparateIncremental(show["separateIncremental"]);//建立分组的扩展关键词
				var number = setFinalDiseaseHtml(show["finalDisease"]);//显示最终疾病
				//compare_diseaseDescriptedNum();//如果系统找到的数目小于用户输入，则弹窗警告
				//alert(number);
				$("#total").remove();
				$("#reachGroupDisease").append('<button class="btn btn-danger" id="total" type="button" style="margin-left:8px;float:right;"><?php echo $langText["concept_disease"]["TOTAL"];?>=<span class="badge">'+number+'</span></button>');
				buttonHtml = '<button class="btn btn-default" type="button" style="margin-left:8px;float: left;;" onclick="sortForR()"><?php echo $langText["concept_disease"]["SORT_PARAMETER_R"];?></button>';
				buttonHtml += '<button class="btn btn-default" type="button" style="margin-left:8px;float: left;;" onclick="sortForP()"><?php echo $langText["concept_disease"]["SORT_PARAMETER_P"];?></button>';
                buttonHtml += '<button class="btn btn-default" type="button" style="margin-left:8px;float: left;;" onclick="sortForPVW()"><?php echo $langText["concept_disease"]["SORT_PARAMETER_PVW"];?></button>';
                buttonHtml += '<button class="btn btn-default" type="button" style="margin-left:8px;float: left;;" onclick="sortForNVW()"><?php echo $langText["concept_disease"]["SORT_PARAMETER_NVW"];?></button>';
                buttonHtml += '<div style="clear: both;"></div>';
				$("#sortFunction").html(buttonHtml);
				if(show["multi-morbidity"] == 1){
					parent.setProgress(100,'');
					parent.hideProgressModal();
					parent.showMulti_morbidity();
					return;
				}else if(show["multi-morbidity"] == 3){
					parent.setProgress(100,'');
					parent.hideProgressModal();
					parent.showEmptySet();
					return;
				}else if(show["finalDisease"] == ''){
					getLastStepFinalDisease();
					return;
				}
				getGroupConceptIncremental();
			}
	})
}
function getSimilarDiseaseBySymptomSetTextEditDistance(disease_site_id){
	var sid = $("#sid",parent.document).html();
	$.ajax({
			type:"get",
			url:"../API/concept_disease.getSimilarDiseaseBySymptomSetTextEditDistance.php?site_id="+disease_site_id+"&sid="+sid,
			async:true,
			error:function(state){
				alert("error:"+state);
			},
			success:function(value){
				if(value!=""){
					var show = JSON.parse(value);
					var sameDiseases = '<div class="panel panel-default">';
					sameDiseases +=	'<div class="panel-heading" style="text-align: center;"><?php echo $langText["concept_disease"]["WITH_SAME_CLASS_DISEASE"];?> '+show["className"]+'</div>'
					for(var i in show["similaryDiseases"]){
						sameDiseases += "<p>"+i+" = "+show["similaryDiseases"][i]+"%</p>";
					}
					$("#getFinalDisease").append('<div class="panel-body">'+sameDiseases+' </div></div>');
				}
			}
	})


}

function getLastStepFinalDisease(){
	$.ajax({
			type:"get",
			url:"../API/concept_disease.getLastStepFinalDisease.php?sid="+sid,
			async:true,
			error:function(state){
				alert("error:"+state);
			},
			success:function(value){
				getFinalDisease();
			}
	})

}
//扩展每个分组症状的备选关键词列表
function getGroupConceptIncremental(){
	var sid = $("#sid",parent.document).html();
	parent.setProgress(70,'<?php echo $langText["concept_disease"]["LOADING_WAIT"];?>');
	$.ajax({
			type:"get",
			url:"../API/concept_disease.getGroupConceptIncremental.php?sid="+sid,
			async:true,
			error:function(state){
				alert("error:"+state);
			},
			success:function(value){
				//alert(value);
				//document.write(value);
				//return;
				//filterSymptomWithUnrelatedWord();
				var incremental_concepts = JSON.parse(value);
				
				//console.log(incremental_concepts);
				
				for (var index in incremental_concepts) { 
					var show_str ='';
					//alert(incremental_concepts[index].length);
					for(increment_concept in incremental_concepts[index]){
						show_str +='<li><a onclick=incrementConcept("'+increment_concept+'","'+index+'") style="cursor: default;">'+increment_concept+'\t'+incremental_concepts[index][increment_concept]+'</a></li>';				
					}
					$('#increment_'+index,parent.frames["ifram.word_concept"].document).html(show_str);
				}
				getAdvisedConceptIncremental();
			}
	})
}
//扩展每个分组症状的备选关键词列表
function getAdvisedConceptIncremental(){
	var sid = $("#sid",parent.document).html();
	parent.setProgress(80,'<?php echo $langText["concept_disease"]["LOADING_WAIT"];?>');
	$.ajax({
			type:"get",
			url:"../API/concept_disease.getAdvisedConceptIncremental.php?sid="+sid,
			async:true,
			error:function(state){
				alert("error:"+state);
			},
			success:function(value){
				//alert(value);
				//document.write(value);
				//return;
				//filterSymptomWithUnrelatedWord();
				var incremental_concepts = JSON.parse(value);
				setSeparateIncremental(incremental_concepts);
				parent.setProgress(100,'');
				parent.hideProgressModal();
			}
	})
}
//以下是 工具函数

function setSeparateIncremental(incrementalArr){
	//alert(incrementalArr);
	//console.log(incrementalArr);
	var show_str = '';
	var i=0;
	for(increment_concept in incrementalArr){
		//alert(increment_concept);
		show_str = '<button class="btn btn-info" style="margin-top:10px;margin-right:10px;min-width:170px;">'+increment_concept+'\t'+incrementalArr[increment_concept];
		show_str +='</button><div class="btn-group" style="margin-top:10px"><button class="btn btn-success" onclick=incrementGroupConcept("'+increment_concept+'") ><?php echo $langText["concept_disease"]["INCREMENTAL_HAVE"];?></button>';
		show_str += '<button class="btn btn-danger" onclick=unrelatedConcept("'+increment_concept+'")><?php echo $langText["concept_disease"]["INCREMENTAL_NOT_HAVE"];?></button></div><br>';
		if(i < 15){ 
			$('#advisedConcept',parent.frames["ifram.word_concept"].document).append(show_str);
			//return;
		}
		i++;
		
		
		//show_str +='<li><a onclick=incrementGroupConcept("'+increment_concept+'") style="cursor: default;">'+increment_concept+'\t'+incrementalArr[increment_concept]+'</a></li>';
	}
	//$('#advisedConcept',parent.frames["ifram.word_concept"].document).html(show_str);
}
/*function setGroupIncremental(incrementalArr){
	//首先清空所有的列表
	$('#replace_synonymConjugate_word ul',parent.frames["ifram.word_concept"].document).each(function(){
		$('#'+this.id,parent.frames["ifram.word_concept"].document).empty()
	});	
	for (var index in incrementalArr) { 
		var show_str ='';
		//alert(incremental_concepts[index].length);
		for(increment_concept in incrementalArr[index]){
			show_str +='<li><a onclick=incrementConcept("'+increment_concept+'","'+index+'") style="cursor: default;">'+increment_concept+'\t'+incrementalArr[index][increment_concept]+'</a></li>';				
		}
		
		$('#increment_'+index,parent.frames["ifram.word_concept"].document).html(show_str);
	}
	return;	
}*/


function setFinalDiseaseHtml(show){
	//console.log(show);
	var Messages = '';
	var number = 0;
	for(var index in show){
		
		//console.log(show[index]);
		
		if(show[index]["status"] == "rare"){
			Messages += '<button id="'+show[index]["disease_site_id"]+'" name="'+show[index]["disease_name"]+'" class="btn btn-warning" type="button" onclick="getDiseaseInfo(this.id,this.name)" style="margin-left:8px">';
		}else if(show[index]["status"] == "normal"){
			Messages += '<button id="'+show[index]["disease_site_id"]+'" name="'+show[index]["disease_name"]+'" class="btn btn-info" type="button" onclick="getDiseaseInfo(this.id,this.name)" style="margin-left:8px">';
		}else if(show[index]["status"] == "lethality"){
			Messages += '<button id="'+show[index]["disease_site_id"]+'" name="'+show[index]["disease_name"]+'" class="btn btn-danger" type="button" onclick="getDiseaseInfo(this.id,this.name)" style="margin-left:8px">';
		}
		Messages += '<div class="sm-screen-left"><span>'+show[index]["disease_name"]+'</span></div><div class="sm-screen-right"><span class="badge">'+show[index]["details"]+'</span><span class="badge">R:'+show[index]["percentage"]+'%</span><div class="comb"><span class="badge">P:'+show[index]["prev_rf"]+'</span><span class="badge" style="width:80px">PVW:'+show[index]["PVW"]+'</span><span class="badge" style="width:80px">NVW:'+show[index]["NVW"]+'</span></div></div>';
		
		
		
		/*if(show[index]["status"] == "rare"){
			Messages += '<div class="sm-screen-left rare-disease-left"><span class="glyphicon glyphicon-exclamation-sign" style="color:#ec971f;" title="Warning : " data-content="The disease is a rare disease.It happens at less then 5 out of 10000 patients." onMouseOver="showRareDiseaseWarning(this)" onMouseOut="hideRareDiseaseWarning(this)" aria-hidden="true"></span></div><div class="sm-screen-left"><span>'+show[index]["disease_name"]+'</span></div><div class="sm-screen-right"><span class="badge">'+show[index]["details"]+'</span><span class="badge">R:'+show[index]["percentage"]+'%</span><div class="comb"><span class="badge">P:'+show[index]["prev_rf"]+'</span><span class="badge" style="width:80px">Comb:'+show[index]["comb"]+'</span></div></div>';
		}else{
			Messages += '<div class="sm-screen-left" style="width:40px;height:40px;"></div><div class="sm-screen-left"><span>'+show[index]["disease_name"]+'</span></div><div class="sm-screen-right"><span class="badge">'+show[index]["details"]+'</span><span class="badge">R:'+show[index]["percentage"]+'%</span><div class="comb"><span class="badge">P:'+show[index]["prev_rf"]+'</span><span class="badge" style="width:80px">Comb:'+show[index]["comb"]+'</span></div></div>';
		}*/
		Messages += '</button>';
		Messages +=	'<br>';
		//alert("name:"+show["finalDiesease"][index]["name"]+"num:"+show["finalDiesease"][index]["num"]+"total:"+show["finalDiesease"][index]["total"]);
		//return 1;
		number++;
		system_diseaseDescripted_num = show[index]["diseaseDescripted_num"];//获得目前系统能找的疾病的数目
	}
	//alert(Messages);
	$("#getFinalDisease").html(Messages);
	return number;
}

function showRareDiseaseWarning(element){
	$(element).popover('show');
}
function hideRareDiseaseWarning(element){
	$(element).popover('hide');
}

function compare_diseaseDescriptedNum(){

	var userDiseaseDescriptedNum = $("#mergeGroupRelatedSymptom").children().length;
	var system_diseaseDescripted_num = $("#reachGroupDisease").children().length;
	//console.log($div.length);
	//alert(system_diseaseDescripted_num);
	if(system_diseaseDescripted_num<userDiseaseDescriptedNum){//如果系统找到的数目小于用户输入，则弹窗
		parent.showErr();
	}
}
function getDiseaseInfo(id,name){
	parent.frames["ifram.disease_symptom"].getDiseaseDescription(id,name);
}
//获取json数组的长度
function getJsonObjLength(jsonObj) {
	jsonObj = JSON.parse(jsonObj);
    var Length = 0;
    for (var item in jsonObj) {
        Length++;
    }
    return Length;
}
//按照P参数对疾病进行排序。
function sortForR(){
	_finalDiseases = _finalDiseases.sort(function(a,b){

		var offset = b["percentage"] - a["percentage"];
		if(offset == 0){
			//如果两个疾病的R 参数相同，则参考P参数
			offset = b["prev_rf"] - a["prev_rf"];
			if(offset == 0){
				offset = b["comb"] - a["comb"];
			}
		}
		return offset;
	});
	setFinalDiseaseHtml(_finalDiseases);
}
function sortForP(){
	_finalDiseases = _finalDiseases.sort(function(a,b){
		var offset =b["prev_rf"] - a["prev_rf"];
		if(offset == 0){
			//如果两个疾病的P 参数相同，则参考R参数
			offset = b["percentage"] - a["percentage"];
			if(offset == 0){
				offset = b["comb"] - a["comb"];
			}
		}
		return offset;

	});
		
	setFinalDiseaseHtml(_finalDiseases);
}
//按照PVW排序
function sortForPVW(){
    _finalDiseases = _finalDiseases.sort(function(a,b){
        var offset =b["PVW"] - a["PVW"];
        return offset;
    });
    setFinalDiseaseHtml(_finalDiseases);
}

//按照NVW排序
function sortForNVW(){
    _finalDiseases = _finalDiseases.sort(function(a,b){
        var offset =b["NVW"] - a["NVW"];
        return offset;
    });
    setFinalDiseaseHtml(_finalDiseases);
}

function toggleCombSettings(){
	$("#combSettingsModal").modal('toggle');
}
function sortForComb(){
	var sid = $("#sid",parent.document).html();
	var setting_a = $("#a").val();
	var setting_b = $("#b").val();
	if(!checkRate(setting_a)||!checkRate(setting_b)){
		alert('<?php echo $langText["concept_disease"]["INPUT_WARNING"];?>');
		return;
	}
	
	$.get("../API/concept_disease.showfinalDiseaseDetails.php?sid="+sid+"&a="+setting_a+"&b="+setting_b,function(value){
		var show = JSON.parse(value);
		_finalDiseases = show["finalDisease"]//赋值给全局变量
		//console.log(show);
		setFinalDiseaseHtml(show["finalDisease"]);//显示最终疾病
	});
	toggleCombSettings();
}
function checkRate(nubmer){
     var re = /^[0-9]+.?[0-9]*$/;   //判断字符串是否为数字     //判断正整数 /^[1-9]+[0-9]*]*$/  
    //var nubmer = document.getElementById(input).value;
     if (!re.test(nubmer))
    {
        return false;
     }
     return true;
}
</script>
</html>