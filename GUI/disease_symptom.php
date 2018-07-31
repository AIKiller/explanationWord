<html>
	<head>
		<?php include("tools/headr.php");?>
		<script type="text/javascript" src="js/highcharts.js" ></script>
		<title><?php echo $langText["SYSTEM_TITLE_NAME"];?></title>
		<style>
			#symptom_list .btn-primary{width:98%;text-align: center;color: #38489B;;background-color: #38489B;border-color: #38489B}
			#symptom_list .btn-success{width:98%;text-align: center;color: #000000;;background-color: #CCCCCC;border-color: rgba(53, 126, 189, 0);}
		</style>
	</head>
	<body>
		<h1><?php echo $langText["disease_symptom"]["PAGE_TITLE"];?></h1>
		<div id="panel_title" style="width:50%;float:right;">
			<div id="disease_name"></div>
			<div id="icd"></div>
			<div id="icpc"></div> 
			<div id="symptom_descript"></div>
		</div>
		 <div id="ageInformations" style="display:none;width:300px;height:300px;float:left;"></div>
		<div id="panel_body" style="width:100%;float:left;">
			<h4><?php echo $langText["disease_symptom"]["SYMPTOM_LIST"];?></h4>
			<div id="symptom_list"></div>
		</div>
		<script>
			var feedback;
			var found_symptom_site_ids = new Array();
			var not_found_symptom_site_ids = new Array();
			var _name;

			function getDiseaseDescription(id,name){
				var sid = $("#sid",parent.document).html();//session_id
				_name = name;
				//alert(id+name);
				$("#disease_name").html('<h4><?php echo $langText["disease_symptom"]["DISEASE_NAME"];?><button id="disease_nameBtn" class="btn btn-primary active" type="button" style="margin-left:8px">'+name+'</button></h4>');
				$("#disease_site_id").html(id);
				$.ajax({
					type:"get",
					url:"../API/disease_symptom.getDiseaseDescription.php?site_id="+id+'&sid='+sid,
					async:true,
					error:function(state){
						alert("error:"+state);
					},
					success:function(value){
						feedback = value;
						//alert(value);
						//$("#debug").html(value);
						var show = JSON.parse(value);
						//var symptoms_total = 0;
						//var symptom_descripted = 0;
						getDiseaseAgeInformations(show["ageInformation"]);
						$("#icd").html('<h4>ICD: '+show["icd"]+'</h5>');
						$("#icpc").html('<h4>ICPC: '+show["icpc"]+'</h5>');
						var Messages = '';
						for(var symptom_site_id in show["all_symp"]){
							if(show["all_symp"][symptom_site_id]["flag"]==1){
								//symptom_descripted++;
								Messages += '<button id="'+symptom_site_id+'" name="'+show["all_symp"][symptom_site_id]["name"]+'" onclick="fadeToggle(this.id)" is_description=1 class="btn btn-primary" type="button" style="margin-left:8px;margin-top: 10px;color:#ffffff">';
								Messages += show["all_symp"][symptom_site_id]["name"];
								Messages += '</button>';
							}else{
								Messages += '<button id="'+symptom_site_id+'" name="'+show["all_symp"][symptom_site_id]["name"]+'" onclick=fadeToggle(this.id) is_description=0 class="btn btn-success" type="button" style="margin-left:8px;margin-top: 10px;width:81%;color:#000000">';
								Messages += show["all_symp"][symptom_site_id]["name"];
								Messages += '</button>';
								Messages +='<button class="btn btn-success" type="button" style="margin-left:8px;margin-top: 10px;color:#000000;width:15%;" onclick=asInput("'+symptom_site_id+'")>as input</button>'
							}
							Messages +=	'<br>';
							//症状下concept的div
							Messages +='<div id="'+symptom_site_id+'conceptList" style="display:none;" >';
							for(var concept_id in show["all_symp"][symptom_site_id]["concepts"]){
								Messages += '<button id="'+concept_id+'" class="btn btn-default" type="button" symptom="'+symptom_site_id+'" onclick=getConceptOfDiseaseDescription(this) style="width:89%;margin-left:4%;margin-top: 10px;">';
	  							Messages += show["all_symp"][symptom_site_id]["concepts"][concept_id];
								Messages += '</button>';
								//删除按钮
								//Messages += '<button id="'+show[symptom_site_id]["info"][index]["concept_id"]+'"  class="btn btn-danger" type="button" style="width:40%;margin-left:2%;margin-top: 10px;">';
								//Messages += 'DELETE</button>';
								Messages +=	'<br>';
							}
							Messages += '</div>';
						}
						$("#symptom_list").html(Messages);
						$("#useful").css('display','block')
						//console.log(show);
						//var percent = Math.round((symptom_descripted/symptoms_total)*100);
						//parent.frames["ifram.concept_disease"].changeDescriptedPercent(id,percent);
					}
				})
			}
			function getConceptOfDiseaseDescription(element){
				//首先获取用户已经输入的concept
				var user_input = $('#user_input',parent.frames["ifram.word_concept"].document).html();
				
				//获取点击的concept从属的症状id
				var symptom_site_id = $(element).attr("symptom");
				
				var is_description = $("#"+symptom_site_id).attr("is_description");//is_description =1 当前症状已被描述 is_description=0 未被描述
				
				
				if(is_description == 1){//is_description=1 症状已被描述
					var systemOfConcepts = new Array();
					$(element).parent().find("button").each(function(){
							systemOfConcepts.push(this.innerHTML);
						})
					var user_concept_array = user_input.split(",");
					
					var similarityNums = new Array();
					for(var group_index in user_concept_array){
					
						//console.log(user_concept_array[group_index]);
						similarityNums[group_index] = similarityNum(user_concept_array[group_index].split(" "),systemOfConcepts.join(","));
					
					
					}
					max = Math.max.apply(null,similarityNums);
					
					for(var group_index in similarityNums){
					
						if(similarityNums[group_index] == max){
							var oncept = $(element).html();
							if(user_concept_array[group_index].indexOf(oncept) == -1){
							
								user_concept_array[group_index] += " "+oncept;
							}
						
						}
					
					}
					
					user_input = user_concept_array.join(",");

				}else{

					user_input += ","+$(element).html();			
				}
				
				$('#user_input',parent.frames["ifram.word_concept"].document).html(user_input);
				$('#user_input',parent.frames["ifram.word_concept"].document).val(user_input);
				parent.frames["ifram.word_concept"].stopWord_clear();
			}
			
			function similarityNum(array1,string){
				
		
				//console.log(string);
				var similarityNum =0;
				
				
				for(var index in array1){
				
					console.log(string);
					console.log(array1[index]);
					if(string.indexOf(array1[index]) != -1){
						
						similarityNum++;
					
					}
				
				}
				console.log(similarityNum);

				return similarityNum;
			}
			
			function getDiseaseAgeInformations(ageInformations){
				$("#ageInformations").show();
				$("#panel_title").css("float","right");
				if(ageInformations == -1){
					$("#ageInformations").hide();
					$("#panel_title").css("float","left");
					return;
				}
				//ageInformations = JSON.stringify(ageInformations);
				var option =
				{
					title: {
			            text: ''
			        },
					 chart: {
			            type: 'column'
			        },
					xAxis: {
			            categories: ['<5','10','20','35','55','70','>75']
			        },
			       	yAxis: {
			       		title:{
			            	text: 'Prev values (0/00)'
			            }
			        },
			        tooltip: {
			            headerFormat: '<span style="font-size:10px">Age:{point.key}</span><table>',
			            pointFormat: '<tr><td style="color:{series.color};padding:0">P=</td>' +
			                "<td style='padding:0'><b>{point.y:.4f}</b></td></tr>",
			            footerFormat: '</table>',
			            shared: true,
			            useHTML: true
			        },
			        series: [{
			            name: '<?php echo $langText["disease_symptom"]["AGE"];?>',
			            max:2,
			            data: ageInformations
			        }]
			    }
			    //option["series"][0]["data"] = ageInformations;
				console.log(option);
				$('#ageInformations').highcharts(option);
			}
			function fadeToggle(id){
				var diease_site_id = $("#disease_site_id").html();
				var disease_name = $("#disease_nameBtn").html();
				//alert(disease_name);
				$("#"+id+"conceptList").fadeToggle("slow");
			}
			function asInput(id){
				var name = $("#"+id).attr("name");
				parent.frames["ifram.word_concept"].addNonConcpet(name);
				var diease_site_id = $("#disease_site_id").html();
				var disease_name = $("#disease_nameBtn").html();
				setTimeout(function(){
					//alert(_id);
					getDiseaseDescription(_id,_name);
				},1000);
			}
			//
			function setFeedback(){
				var disease_name = $("#disease_nameBtn").html();
				var found_symptoms = new Array();
				var not_found_symptoms = new Array();
				found_symptom_site_ids.length=0;
				not_found_symptom_site_ids.length=0;
				$('#feedback_disease').html(disease_name);
				symptoms = JSON.parse(feedback);
				for(x in symptoms){
					if(symptoms[x]["flag"]==1){
						found_symptoms.push(symptoms[x]["name"]);
						found_symptom_site_ids.push(x);
					}else if(symptoms[x]["flag"]==0){
						not_found_symptoms.push(symptoms[x]["name"]);
						not_found_symptom_site_ids.push(x);
					}
				}
				$("#found_symptom").html(found_symptoms.join(", "));
				$("#not_symptom_symptom").html(not_found_symptoms.join(", "));
				//$("#debug").html("found:"+found_symptom_site_ids+"<br>not:"+not_found_symptom_site_ids);
			}
			/*
			function saveUsefulFeedBack(){
				var sid = $("#sid",parent.document).html();//session_id
				saveFeedBack = new Object();
				infomations = new Object();
				infomations["found_symptoms"] = found_symptom_site_ids;
				infomations["not_found_symptoms"] = not_found_symptom_site_ids;
				saveFeedBack["feedback_name"] = $("#feedback-name").val();
				saveFeedBack["feedback_disease"] = $("#feedback_disease").html();
				saveFeedBack["feedback_info"] = infomations;
				//alert(JSON.stringify(saveFeedBack));
				$.ajax({
					type:"get",
					url:"../API/disease_symptom.saveFeedback.php?feedbacks="+JSON.stringify(saveFeedBack)+"&sid="+sid,
					async:true,
					error:function(state){
						alert("Err"+state.status);
					},
					success:function(string){
						//alert(string);
						if(string == "OK"){
							alert("Save success!");
							$(".modal").modal('hide');
						}else if(string == "ERR"){
							alert("Error!Please try agian!");
						}
					}
				});
				
			}*/
			
		</script>
		
		
		
	</body>
</html>