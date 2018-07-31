<html>
	<head>
		<?php include("tools/headr.php");?>
		<title><?php echo $langText["SYSTEM_TITLE_NAME"];?></title>
		<link type="text/css" rel="stylesheet" href="css/index.default.css" />
		<link type="text/css" rel="stylesheet" href="tools/tooltip/tooltip.css" />
		<script src="js/H5Draw.js"></script>
		<script type="text/javascript" src="js/echarts.min.js" ></script>
		<script src="tools/tooltip/tooltip.js"></script>
	</head>
	<body>
		<?php include('tools/index.config.php');?>
		<table class="table" style="max-width:150%;width:120%;height:100%;margin-bottom:0px;">
			<tr>
				<td width="27%" ><iframe id="ifram.word_concept" row="5" name="ifram.word_concept" src="word_concept.php" border="0" height="100%" width="100%" frameborder=0 ></iframe></td>
				<td width="30%" ><iframe id="ifram.concept_disease" name="ifram.concept_disease" src="concept_disease.php" border="0" height="100%" width="100%" frameborder=0 ></iframe></td>
				<td><iframe id="ifram.disease_symptom" name="ifram.disease_symptom" src="disease_symptom.php" border="0" height="100%" width="100%" frameborder=0 ></iframe></td>
			</tr>	
		</table>
		<div id="user_accessTime" style="display: none;"></div>
		<div id="user_name" style="display: none;"></div>
		<div id="sid" style="display: none;"></div>
	
		<?php include('tools/index.modal.php');?>
					
	</body>
	<script>
		function changeLanguage(){
			//alert("sadasdd");
			//return;
			var lang = getCookie("lang")||"en";//默认语言版本是英语
			if(lang == "en"){
				setCookie('lang',"nl");//设置cookie.
			}else if(lang == "nl"){
				setCookie('lang',"en");//设置cookie.
			}
			location = location;
		}
		var downLoadState = false;//用来标记当前是否为下载文件的状态。
		//其他页面通过该函数 显示主页面的modal
		function toggleSynonymSelectModal(){
			$("#synonymSelectModal").modal('toggle');
		}
		//当用户输入的关键词造成结果为空集，则出现错误警告
		function showMulti_morbidity(){
			$("#multi-morbidity").modal('toggle');
		}
		function showEmptySet(){
			$("#emptySet").modal('toggle');
		}
		function setUserPersonInformation(){
				$("#userinformationModal").modal('toggle');
				//updateFilterSet('open');
				//init();
		}
		function openProgressModal(){
			$("#progressModal").modal('show');
		}
		function hideProgressModal(){
			$("#progressModal").modal('hide')
		}
		function setProgress(percent,message){
			$("#searchProgress").css('width',percent+"%");
			$("#progressMessage").html('<h4>'+message+'</h4>');
		}
		//根据用户的点击改变按钮的样式。并添加警告信息
		function toggleSelectWordBtn(element){
			var btnAttr = $(element).attr('class');
			if(btnAttr == 'btn btn-primary'){
				$(element).attr('class','btn btn-default');
			}else{
				$(element).attr('class','btn btn-primary');
			}
			var btnElement = $(element).parent().find('button.btn-primary');//在父类中寻找button
			if(btnElement.length < 2){
				$("#selectTips").html('');
				return false;
			}else{
				//当用户的选择大于等于两个的时候，添加警告信息。
				btns = new Array();
				btnElement.each(function(){
					btns.push(this.outerText);
				})
				var selectTips ='<?php echo $langText["index"]["SELECT_MORE_WORD_WARNING_A"];?>'+btns.join('<?php echo $langText["index"]["SELECT_MORE_WORD_WARNING_B"];?>')+'}.';
				$("#selectTips").html(selectTips);
			}
		}
		//保存用户选择的多义词
		function saveSelect(){
			var selectedWordObj = new Object();
			var is_success = true;
			var selectArea = $("#selectWordTable").find("[type='selectArea']").each(function(){
				var selectedWordBtn = $(this).find(".btn-primary");
				if(selectedWordBtn.length == 0){
					$("#selectTips").html('<?php echo $langText["index"]["SELECT_MORE_WORD"];?>');
					is_success = false;
					return;
				}
			});
			if(!is_success){
				return;
			}
			var selectedWordBtn = $("#selectWordTable").find(".btn-primary");
			selectedWordBtn.each(function(){
				//console.log($(this));
				var selectedConceptObj = new Object();
				var word_id = $(this).attr('word_id');
				var group = $(this).attr('group');
				var index = $(this).attr('index');
				var concept_id = $(this)[0].id;
				var concept = $(this)[0].innerHTML;
				if(selectedWordObj[word_id] == undefined){
					selectedWordObj[word_id] = new Object();
					selectedWordObj[word_id]["concepts"] = new Object();
					selectedWordObj[word_id]["concepts"][concept_id] = concept;
					selectedWordObj[word_id]["group"] = group;
					selectedWordObj[word_id]["index"] = index;
				}else{
					selectedWordObj[word_id]["concepts"][concept_id] = concept;
				}
			});
			var session_id = $("#sid").html();
			$.get('../API/index.saveChangeSelectedWord.php',{sid:session_id,selectedWordObj:JSON.stringify(selectedWordObj)},function(result){
				for(var word_id in selectedWordObj){
					var element = $('#word_item_'+selectedWordObj[word_id]["group"],window.frames['ifram.word_concept'].document).find('p[type="conjugate"]');
					//获取改行的内容。
					var text = '';
					element.each(function(){
						text += this.outerHTML;
					})

					for(var concept_id in selectedWordObj[word_id]["concepts"]){
						text += '<p id="'+concept_id+'" word_id="'+word_id+'" type="synonym">'+selectedWordObj[word_id]["concepts"][concept_id]+"</p>";
					}
					console.log(selectedWordObj);
					$('#word_item_'+selectedWordObj[word_id]["group"],window.frames['ifram.word_concept'].document).html(text);
					
				}
				window.frames["ifram.concept_disease"].mergeGroupSymptom();
				toggleModalById("synonymSelectModal");
			});
		}
		//刷新和关闭都会调用onbeforeunload函数
			window.onbeforeunload = function(e){
				var session_id = $("#sid").html();
				var userName = $("#user_name").html();
				var user_accessTime = $("#user_accessTime").html();
				if(!downLoadState){
					$.get("../API/index.recordUserConduct.php",{sid:session_id,userName:userName,user_accessTime:user_accessTime},function(value){});
					return "Thank you for your use.";
				}
			}
		var feedback = new Object();
		function toggleModel(){
			var sid = $("#sid").html();
			//加载需要显示的数据
			//var concept = $(window.frames["ifram.word_concept"].document).find("#user_input").val();
			var concept = new Array()
			var $concept = $(window.frames["ifram.word_concept"].document).find("#replace_synonymConjugate_word");
			$concept.find('table td[data-type="concept"]').each(function(){
				//console.log();
				concept.push($(this)[0].innerHTML);
			});
			$("#feedback_concepts").html(concept);
			
			$.ajax({
				type:"get",
				url:"../API/index.getDiseaseSymptom.php?sid="+sid,
				async:true,
				error:function(state){
					alert("error:"+state);
				},
				success:function(value){
					//alert(value);
					var show = JSON.parse(value);
					//document.write(value);
					found_symptoms = new Array();
					not_found_symptoms = new Array();
					showStr = '';
					for(var disease_site_id in show){
						showStr += '<div class="form-group">';
						showStr += '<div><label for="recipient-name" class="control-label"><?php echo $langText["index"]["DISEASE"];?></label>';
						showStr += '<div style="display: inline;"> '+show[disease_site_id]["name"]+'</div></div>';
						showStr += '<div><label for="recipient-name" class="control-label">ICD:</label>';
						showStr += '<div style="display: inline;"> '+show[disease_site_id]["icd"]+'</div></div>';
						showStr += '<div><label for="recipient-name" class="control-label">ICPC:</label>';
						showStr += '<div style="display: inline;"> '+show[disease_site_id]["icpc"]+'</div></div>';
						showStr += '<div><label for="recipient-name" class="control-label">PREV:</label>';
						showStr += '<div style="display: inline;"> '+show[disease_site_id]["prev_rf"]+'</div></div>';
						showStr += '<div><label for="recipient-name" class="control-label">Comb:</label>';
						showStr += '<div style="display: inline;"> '+show[disease_site_id]["comb"]+'</div></div>';
						for(var symptom_site_id in show[disease_site_id]["symps"]){
							if(show[disease_site_id]["symps"][symptom_site_id]["flag"]==1){
								found_symptoms.push(show[disease_site_id]["symps"][symptom_site_id]["name"]);
							}else if(show[disease_site_id]["symps"][symptom_site_id]["flag"]==0){
								not_found_symptoms.push(show[disease_site_id]["symps"][symptom_site_id]["name"]);
							}
						}
						showStr += '<label for="recipient-name" class="control-label"><?php echo $langText["index"]["LABEL_FOUND_SYMPTOM"];?></label>';
						showStr += '<div>'+found_symptoms.join(", ")+"</div>";
						showStr += '<label for="recipient-name" class="control-label"><?php echo $langText["index"]["LABEL_NOT_FOUND_SYMPTOM"];?></label>';
						showStr += '<div>'+not_found_symptoms.join(", ")+"</div><br>";
					}
					$(".spinner").css("display","none");
					$('#diseaseAndSymptoms').html(showStr);
				}
			});
			$("#myModal").modal('toggle');
		}
		function toggleModelControl(){
			$("#controlModal").modal('toggle');
			var myChart = echarts.init(document.getElementById('controlChart'));
			var sid = $("#sid").html();
			$.ajax({
				type:"get",
				url:"../API/index.getDiseaseControlInfo.php?sid="+sid,
				async:true,
				error:function(state){
					alert("error:"+state);
				},
				success:function(value){
					//alert(value);
					var show = JSON.parse(value);
					//用来拉大数据之间的差距
					var minprev_rf = show["minprev_rf"]/show["maxprev_rf"]*10;
					var maxprev_rf = 10;
					var data = show["data"];
					//option["series"][0]["data"] = show["allDiseases"];
					//option["series"][1]["data"] = show["foundDiseases"];
					option = {
			    title : {
			        text: '<?php echo $langText["index"]["control"]["MODEL_CHART_TITLE"];?>'
			    },
			    grid: {
			        left: '3%',
			        right: '7%',
			        bottom: '3%',
			        containLabel: true
			    },
			   	color:['#39b3d7','#d2322d'],
			    tooltip : {
			        trigger: 'axis',
			        showDelay : 0,
			        formatter : function (params) {
			            if (params.value[2]=='<?php echo $langText["index"]["control"]["MODEL_CHART_ALL_DISEASES"];?>') {
			                return params.seriesName + ' :<br/>'
			                   +'auto_id:'+ params.value[0] +
			                   '<br/>ICPC class:'+ params.value[1];
			            }else{
			            	return params.seriesName + ' :<br/>'
			                   +'auto_id:'+ params.value[0] +
			                   '<br/>ICPC class:'+ params.value[1]+
			                    '<br/>prev_rf:'+params.value[3];
			            }
			        },
			        axisPointer:{
			            show: true,
			            type : 'cross',
			            lineStyle: {
			                type : 'dashed',
			                width : 1
			            }
			        }
			    },
			    toolbox: {
			        feature: {
			            dataZoom: {},
			            brush: {
			                type: ['rect', 'polygon', 'clear']
			            }
			        }
			    },
			    brush: {
			    },
			    legend: {
			        data: ['<?php echo $langText["index"]["control"]["MODEL_CHART_ALL_DISEASES"];?>','<?php echo $langText["index"]["control"]["MODEL_CHART_FOUND_DISEASES"];?>'],
			        left: 'center'
			    },
			    xAxis : [
			        {
			            type : 'value',
			            scale:true,
			            axisLabel : {
			                formatter: '{value}'
			            },
			            splitLine: {
			                show: false
			            }
			        }
			    ],
			    yAxis : [
			        {
			            type : 'value',
			            scale:true,
			            axisLabel : {
			                formatter: '{value}'
			            },
			            splitLine: {
			                show: false
			            }
			        }
			    ],
			    series : [
			        {
			            name:'<?php echo $langText["index"]["control"]["MODEL_CHART_ALL_DISEASES"];?>',
			            type:'scatter',
			            symbolSize: function (data) {
				            return 6;
				        },
			            data:data[0]
			        },
			        {
			            name:'<?php echo $langText["index"]["control"]["MODEL_CHART_FOUND_DISEASES"];?>',
			            type:'scatter',
			            data: data[1],
			            symbolSize: function (data) {
				            return 8+(data[3]*10/maxprev_rf-minprev_rf/maxprev_rf-minprev_rf)*2;
				        },
			        }
			    ]
			};
					myChart.setOption(option);
				}
			})
		}





		//保存用户的反馈信息，并下载PDF文件到用户本地。
		function saveUsefulFeedBack(){		
				saveFeedBack = new Object();
				saveFeedBack["feedback_name"] = $("#feedback_name").val();
				if(saveFeedBack["feedback_name"]==""){
					alert("Please enter your name!Thank You!");
					return;
				}
				downLoadState = true;
				var concepts = new Array();
				$("#feedback_concepts").find('p').each(function(){
					concepts.push(this.id);
				})
				saveFeedBack["feedback_concepts"] = JSON.stringify(concepts);
				saveFeedBack["sid"] = $("#sid").html();
				console.log(saveFeedBack)
				StandardPost("../API/disease_symptom.saveFeedback.php",saveFeedBack);
			}
			function StandardPost (url,args) {
				var form = $("<form method='get'></form>");
				form.attr({"action":url});
				for (arg in args){
					var input = $("<input type='hidden'>");
					input.attr({"name":arg});
					input.val(args[arg]);
					form.append(input);
				}
				form.submit();
				setTimeout(function(){
					
					downLoadState = false;
					
				},3000)
				
			}
			
			function init(){
				var $tds = $("#sumbitdata").children();
				$.each($tds,function() { 
					var elementId = this.id;
					var filteredId = elementId.substr(2,elementId.length);//获取对应的id
					var element = $("#"+filteredId);
					if(element.length == 0){//说明没有选择到对象，该对象是个单项选择
						var element = $("[name = '"+filteredId+"']:checked");//
						if(element.length != 0){
							//console.log(element);
							var value = element[0].value;
							$("#s_"+filteredId).html(value);
						}else{
							$("#s_"+filteredId).html('');
						}
					}else{
						var value = element[0].value;
						$("#s_"+filteredId).html(value);
					}
					
					//console.log(value);
					//$("#s_"+name).html(elementV);
					//userInformations.push(userInformation);
            		
        		});
        		CanculateBMI();  
				updateFilterSet('open');
			}
			function fillUpTableData(name,value){
				//alert(name+"."+value);
				$("#s_"+name).html(value);
				updateFilterSet("open");
			}
			function CanculateBMI(){
				var weights=$('#weight').val();//获取体重
				var elementName="overweight"
				var radio=document.getElementsByName("overweight");//获取overweight 节点
				//根据用户的体重信息来计算是否超重
				if(weights!=null&&weights!=""){
					var highs=$('#high').val();
					fillUpTableData("high",highs);
					weightsFloat=parseFloat(weights);
					highsFloat=parseFloat(highs);
					if(highsFloat>3) highsFloat=highsFloat/100;
					//alert(highsFloat);
					//BMI=体重(千克)/身高*2(米)-----这个BMI的计算应该有年龄的限制吧！？
					BMI=weightsFloat/(highsFloat*highsFloat);
					//alert(BMI);
					$("#BMILabel").html("<strong><?php echo $langText['index']['userInformation']['HEALTH_AWARENESS_GRADE'];?> "+BMI.toFixed(2)+"<strong>");
					//alert(BMI);
					if(BMI>25.0){
						radio[0].checked=true;
						//之所以放在判断语句中是因为，放在外边不管用。。。
						fillUpTableData(elementName,1);
						fillBMIFeedBack(BMI);
						CalculateIdealWeight(weightsFloat,highsFloat);//用来计算理想体重
					}else{
						radio[1].checked=true;
						fillBMIFeedBack(BMI);
						CalculateIdealWeight(weightsFloat,highsFloat);
						fillUpTableData(elementName,0);
						
					}
				}else{
					//alert("kong ");
					radio[0].checked=true;
				}
			}
			//设置反馈信息
			function fillBMIFeedBack(BMI){
				if(BMI<=18.5){	
					$("#BMIFeebBack").html("<?php echo $langText['index']['userInformation']['HEALTH_AWARENESS_LEVEL_1']; ?>");
				}else if(BMI>18.5 && BMI<=25){	
					$("#BMIFeebBack").html("<strong><?php echo $langText['index']['userInformation']['HEALTH_AWARENESS_LEVEL_2']; ?></strong>");
				}else if(BMI>25 && BMI<=27){	
					$("#BMIFeebBack").html("<strong><?php echo $langText['index']['userInformation']['HEALTH_AWARENESS_LEVEL_3']; ?></strong>");
				}else if(BMI>27 && BMI<=30){	
					$("#BMIFeebBack").html("<strong><?php echo $langText['index']['userInformation']['HEALTH_AWARENESS_LEVEL_4']; ?></strong>");
				}else if(BMI>30 && BMI<=40){	
					$("#BMIFeebBack").html("<strong><?php echo $langText['index']['userInformation']['HEALTH_AWARENESS_LEVEL_5']; ?></strong>");
				}else if(BMI>40){	
					$("#BMIFeebBack").html("<strong><?php echo $langText['index']['userInformation']['HEALTH_AWARENESS_LEVEL_6']; ?></strong>");
				}
			}


			//计算理想的体重
			function CalculateIdealWeight(weight,high){
				//alert(high);
				idealWeight=(high*100-100)*0.9;
				if (idealWeight < weight&&idealWeight>0){
					$("#idealWeight").html("<strong>Your ideal weight is "+idealWeight.toFixed(2)+"kg<strong>");
				}
			}
			var healthScore = 0;
			function updateFilterSet(modalState){
				//modalState 用来规定模态框的状态
				var sid = $("#sid").html();
				var $tds = $("#sumbitdata").children();
				var userInformations = new Object();
				var sex = $("#s_sex").html();
				is_check = false;
				$.each($tds,function() { 
					var elementId = this.id;
					var filteredId = elementId.substr(2,elementId.length);

					userInformations[filteredId] = this.innerHTML;
					//alert(this.innerHTML!="");
					if(this.innerHTML!=""){
						//alert(filteredId);
						is_check = true;
					}
					//userInformations.push(userInformation);
        		});  
        		
        		if(is_check){
        			$(window.frames["ifram.word_concept"].document).find("#userinformation").attr("class","btn btn-danger");
        		}else{
        			$(window.frames["ifram.word_concept"].document).find("#userinformation").attr("class","btn btn-info");
        		}
        		
				// console.log(userInformations);

				var userInformationJson = JSON.stringify(userInformations);

				//console.log(userInformationJson);
				//alert(sid);
				$.ajax({
					type:"get",
					url:"../API/index.filterDisease.php?userInformations="+userInformationJson+"&sid="+sid,
					async:true,
					error:function(state){
						alert("error:"+state);
					},
					success:function(value){
						//alert(value);
						$("#deleted_disease").html("<p><?php echo $langText['index']['userInformation']['HEALTH_AWARENESS_DELETED'];?> "+value+" <?php echo $langText['index']['userInformation']['HEALTH_AWARENESS_DISEASE'];?></p>");
						//计算健康值
					   if(sex=="m"){
							healthScore=value/860;
							getAverageHealthCondition(sex);
						}else if(sex=="f"){
							healthScore=value/776;
							getAverageHealthCondition(sex);
						}
						healthScore=parseInt(healthScore.toFixed(2)*100);
						healthScoreStr=healthScore.toString();

						if(sex !=""){
							Draw(healthScoreStr);
						}

						if(modalState == "close"){
							//此时提交信息，更新 healthcondition
							updateDB(healthScore,sex);
							//setUserPersonInformation();
						}
						$("#healthAwareness").html("<h4><?php echo $langText['index']['userInformation']['HEALTH_AWARENESS_TITLE'];?></h4>");
					}

				});
			}
			//画图的函数
			function Draw(healthScoreStr){
				$("#healthCondition").hide();
				$("#healthCondition").fadeIn("slow");
				//$("#healthConditionText").hide();
				//$("#healthConditionText").fadeIn("slow");
			    var h5dctx = H5D.D2('healthCondition');
			    h5dctx .clear();
			    	//这个画的最外边的变化颜色的圆
			    	healthScore=parseInt(healthScoreStr);
			    	first=parseInt(255-(healthScore/100)*255);
			    	firstStr=first.toString();
			    	second=parseInt(0+(healthScore/100)*255);
			    	secondStr=second.toString();
			    	//alert("firstStr: "+firstStr+"secondStr: "+secondStr);
			    h5dctx .drawCircle(100,55,50).fill(
			    {    color:"RGB("+firstStr+", "+secondStr+", 0)",
			        
			    });
			    	//这个是画的中间那个白边
			   	h5dctx .drawCircle(100,55,40).fill(
			    {    color:"RGB(255, 255, 255)",
			        
			    });
			    	//这个是画的最里面的那个黑色的圆
			    h5dctx .drawCircle(100,55,38).fill(
			    {    color:"RGB(55, 55, 55)",
			        
			    });
			    
			    //这里是画的中间的得分
			    //100:65,70,48
			    //两位数：75,70，48
			    //个位数：85,70,48
			    if(healthScoreStr=="100"){
				    drawTexts(h5dctx,healthScoreStr,65)
			    }else{
			    	drawTexts(h5dctx,healthScoreStr,75)
			    }
			}

			function drawTexts(h5dctx,healthScoreStr,xPos){
				h5dctx .drawText({
				        message:healthScoreStr,
				        xPos:xPos,
				        yPos:70,
				        type:"fill",
				        font:{fontSize:48,},
				        color:"rgba(217,217,217,255)",
				    });
			}
			function clearAllInformation(){
				var sid = $("#sid").html();
				$(window.frames["ifram.word_concept"].document).find("#userinformation").attr("class","btn btn-info");
				$.ajax({
					type:"get",
					url:"../API/index.clearAllInformation.php?sid="+sid,
					async:true,
					error:function(state){
						alert("error:"+state);
					},
					success:function(value){
						//alert(value);
						$(":input[type='text']").val("");
						$(":input[type='radio']").attr("checked",false);
						$("[type='feedbackInfo']").html('');
						var h5dctx = H5D.D2('healthCondition');
			    		h5dctx .clear();
						setUserPersonInformation();
					}
				});
			}
			function getAverageHealthCondition(sex){
				var sid = $("#sid").html();
				$.ajax({
					type : "POST",
					url : "../API/index.getAverageHealthCondition.php?sid="+sid,
					data:"sex=" + sex,
					async: false,
					error : function(state){
						alert ("Error" + state.status);
					},
					success : function(value){
						//alert(value);
						if(sex=="f"){
							$("#averageHealthCondition").html('Female average health <br> awareness is: '+value);
						}else{
							$("#averageHealthCondition").html('Male average health <br> awareness is: '+value);
						}
					}
				});
			}
			function updateDB(healthScore,sex){
				var sid = $("#sid").html();
				//alert(healthCondition);
				$.ajax({
					type : "GET",
					url : "../API/index.updateHealthCondition.php?healthCondition="+healthScore+"&sex="+sex+"&sid="+sid,
					async: false,
					error : function(state){
						alert ("Error" + state.status);
					},
					success : function(value){
						//alert(value);
						if(value == 0){
							alert("Error!Please try agian!");
						}else if(value == 1){
							$("#userinformationModal").modal('toggle');
						}
					}
				});
			}
			$(function(){
				init();
			})
	</script>
</html>