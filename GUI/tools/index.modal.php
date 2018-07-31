<!--userPersonInformation Modal strat 用户信息modal-->
<div class="modal fade" id="userinformationModal" tabindex="-1" role="dialog" aria-labelledby="userinformationModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title" id="myModalLabel"><?php echo $langText['index']['userInformation']['MODEL_TITLE'];?></h5>
      </div>
      <div class="modal-body">
        <table class="table" style="float:left;">
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['AGE_TOOLTIP']."',{offsetX:270,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['AGE'];?></p>
				</td>
				<td>
					<input type="text" class="form-control" id="age"  onblur="fillUpTableData(this.id,this.value)" value="<?php if(isset($userInformationSet)) echo $userInformationSet['age']; ?>">
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['WEIGHT_TOOLTIP']."',{offsetX:270,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['WEIGHT'];?></p>
				</td>
				<td>
					<input type="text" class="form-control" id="weight"  onblur="fillUpTableData(this.id,this.value)" value="<?php if(isset($userInformationSet)) echo $userInformationSet['weight']; ?>">
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['HIGH_TOOLTIP']."',{offsetX:270,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['HIGH'];?></p>
				</td>
				<td>
					<input type="text" class="form-control" id="high"  onblur="CanculateBMI()" value="<?php if(isset($userInformationSet)) echo $userInformationSet['high']; ?>">
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['SEX_TOOLTIP']."',{offsetX:210,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['SEX'];?></p>
				</td>
				<td>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="sex" value="m" id="male"  <?php if(isset($userInformationSet)&&$userInformationSet['sex'] =="m") echo 'checked="checked"'; ?> ><?php echo $langText['index']['userInformation']['MALE_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="sex" value="f" id="female"  <?php if(isset($userInformationSet)&&$userInformationSet['sex'] =="f") echo 'checked="checked"'; ?> ><?php echo $langText['index']['userInformation']['FEMALE_OPTION']; ?>
					</label>
				</td>
        	</tr>
        	<tr><td></td><td></td></tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['HEREDITARY_TOOLTIP']."',{offsetX:210,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['HEREDITARY'];?></p>
				</td>
				<td>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="hereditary" id="hereditary1" value="1" <?php if(isset($userInformationSet)&&$userInformationSet['hereditary'] == "1") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['YES_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="hereditary" id="hereditary2" value="0" <?php if(isset($userInformationSet)&&$userInformationSet['hereditary']  == "0") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['NO_OPTION']; ?>
					</label>
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['INJURY_HISTORY_TOOLTIP']."',{offsetX:210,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['INJURY_HISTORY'];?></p>
				</td>
				<td>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="trauma" id="trauma1" value="1" <?php if(isset($userInformationSet)&&$userInformationSet['trauma']  == "1") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['YES_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="trauma" id="trauma2" value="0" <?php if(isset($userInformationSet)&&$userInformationSet['trauma']  == "0") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['NO_OPTION']; ?>
					</label>
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['REPETITIVE_TOOLTIP']."',{offsetX:210,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['REPETITIVE'];?></p>
				</td>
				<td>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="repetitive" id="repetitive1" value="1" <?php if(isset($userInformationSet)&&$userInformationSet['hereditary']  == "1") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['YES_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="repetitive" id="repetitive2" value="0" <?php if(isset($userInformationSet)&&$userInformationSet['repetitive']  == "0") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['NO_OPTION']; ?>
					</label>
				</td>
        	</tr>
        	<tr>
        		<td>
					<strong><?php echo $langText['index']['userInformation']['LIFESTYLE'];?></strong>
				</td>
				<td>
					
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['LACK_OF_EXCERSIZE_TOOLTIP']."',{offsetX:210,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['LACK_OF_EXCERSIZE'];?></p>
				</td>
				<td>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="non_active" id="non_active1" value="1" <?php if(isset($userInformationSet)&&$userInformationSet['non_active']  == "1") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['YES_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="non_active" id="non_active2" value="0" <?php if(isset($userInformationSet)&&$userInformationSet['non_active']  == "0") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['NO_OPTION']; ?>
					</label>
				</td>
        	</tr>
        	<tr>
        		<td>
					<p><?php echo $langText['index']['userInformation']['OVERWEIGHT'];?></p>
				</td>
				<td>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="overweight"  id="overweight1" value="1" <?php if(isset($userInformationSet)&&$userInformationSet['overweight']  == "1") echo "checked='checked'"; ?> disabled><?php echo $langText['index']['userInformation']['YES_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="overweight" id="overweight2" value="0"  <?php if(isset($userInformationSet)&&$userInformationSet['overweight']  == "0") echo "checked='checked'"; ?> disabled><?php echo $langText['index']['userInformation']['NO_OPTION']; ?>
					</label>
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['UNHEALTHY_FOOD_TOOLTIP']."',{offsetX:210,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['UNHEALTHY_FOOD'];?></p>
				</td>
				<td>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="food" id="food1" value="1" <?php if(isset($userInformationSet)&&$userInformationSet['food']  == "1") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['YES_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="food" id="food2" value="0" <?php if(isset($userInformationSet)&&$userInformationSet['food']  == "0") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['NO_OPTION']; ?>
					</label>
				</td>
        	</tr>
        	<tr>
        		<td>
					<strong><?php echo $langText['index']['userInformation']['EXTERNAL_FACTORS'];?></strong>
				</td>
				<td>
					
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['AICOHOL_TOOLTIP']."',{offsetX:210,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['AICOHOL'];?></p>
				</td>
				<td>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="alcohol" id="alcohol1" value="1" <?php if(isset($userInformationSet)&&$userInformationSet['alcohol']  == "1") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['YES_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="alcohol" id="alcohol2" value="0" <?php if(isset($userInformationSet)&&$userInformationSet['alcohol']  == "0") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['NO_OPTION']; ?>
					</label>
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['POLLUTANTS_TOOLTIP']."',{offsetX:210,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['POLLUTANTS'];?></p>
				</td>
				<td>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="pollutants" id="pollutants1" value="1" <?php if(isset($userInformationSet)&&$userInformationSet['pollutants']  == "1") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['YES_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="pollutants" id="pollutants2" value="0" <?php if(isset($userInformationSet)&&$userInformationSet['pollutants']  == "0") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['NO_OPTION']; ?>
					</label>
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['DRUGS_TOOLTIP']."',{offsetX:210,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['DRUGS'];?></p>
				</td>
				<td>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="drugs" id="drugs1" value="1" <?php if(isset($userInformationSet)&&$userInformationSet['drugs']  == "1") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['YES_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="drugs" id="drugs2" value="0" <?php if(isset($userInformationSet)&&$userInformationSet['drugs']  == "0") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['NO_OPTION']; ?>
					</label>
				</td>
        	</tr>
        	<?php echo "<tr onmouseover=\"tooltip.pop(this, '".$langText['index']['userInformation']['SMOKING_TOOLTIP']."',{offsetX:210,offsetY:0,position:1})\");>";?>
        		<td>
					<p><?php echo $langText['index']['userInformation']['SMOKING'];?></p>
				</td>
				<td>
				
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="smoking" id="smoking1" value="1" <?php if(isset($userInformationSet)&&$userInformationSet['smoking']  == "1") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['YES_OPTION']; ?>
					</label>
					<label class="radio-inline">
						<input type="radio" onclick="fillUpTableData(this.name,this.value)" name="smoking" id="smoking2" value="0" <?php if(isset($userInformationSet)&&$userInformationSet['smoking']  == "0") echo "checked='checked'"; ?>><?php echo $langText['index']['userInformation']['NO_OPTION']; ?>
					</label>
				</td>
        	</tr>
        </table>
        <div id="ideal">
        	<canvas id="healthCondition" width="200" height="110" style="border:0px solid #c3c3c3;" type="feedbackInfo">
				<?php echo $langText['index']['userInformation']['NOT_SUPPORT']; ?>
			</canvas>
        	<div id="healthAwareness" type="feedbackInfo"></div>
        	<div id="BMILabel" type="feedbackInfo"></div>
        	<div id="idealWeight" type="feedbackInfo"></div>
        	<div id="BMIFeebBack" type="feedbackInfo"></div>
        	<div id="averageHealthCondition" type="feedbackInfo"></div>
        	<div id="deleted_disease" type="feedbackInfo"></div>
        </div>


        <div style="clear:both;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="updateFilterSet('close')"><?php echo $langText['index']['userInformation']['SAVE_BUTTON']; ?></button>
        <button type="reset" class="btn btn-default"  onclick="clearAllInformation()"><?php echo $langText['index']['userInformation']['CLEAR_BUTTON']; ?></button>
        <table class="table" style="display:none;">
        	<tr>
        		<td>Age</td><td>Weight</td><td>High</td><td>Sex</td><td>Hereditary</td><td>Injury history</td><td>Repetitive</td><td>Lack of excersize</td><td>Overweight</td><td>Unhealthy food</td><td>Alcohol</td><td>Pollutants</td><td>Drugs</td><td>Smoking</td>
        	</tr>
        	<tr id="sumbitdata">
        		<td id="s_age"></td><td id="s_weight"></td><td id="s_high"></td><td id="s_sex"></td><td id="s_hereditary"></td><td id="s_trauma"></td><td id="s_repetitive"></td><td id="s_non_active"></td><td id="s_overweight"></td><td id="s_food"></td><td id="s_alcohol"></td><td id="s_pollutants"></td><td id="s_drugs"></td><td id="s_smoking"></td>
        	</tr>
        </table>
      </div>
    </div>
  </div>
</div>
<!--userPersonInformation Modal end-->


<!-- feedback modal start 反馈用户查询建议-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $langText['index']['useful']['MODEL_TITLE']; ?></h4>
      </div>
      <div class="modal-body">
         <form>
          <div class="form-group">
            <label for="recipient-name" class="control-label"><?php echo $langText['index']['useful']['MODEL_USER_NAME']; ?></label>
            <input type="text" class="form-control" id="feedback_name" value="<?php echo $_COOKIE['userName']?>" style="width:70%;display: inline;margin-right:2%;" disabled>
			<button type="button" class="btn btn-primary" onclick="saveUsefulFeedBack()" ><?php echo $langText['index']['useful']['MODEL_SAVE']; ?></button>
          </div>
    	  <div class="form-group">
            <label for="recipient-name" class="control-label"><?php echo $langText['index']['useful']['MODEL_CONCEPT']; ?></label>
            <div id="feedback_concepts"></div>
          </div>
          <div class="form-group" id="diseaseAndSymptoms">
    		<div class="spinner">
			  <div class="rect1"></div>
			  <div class="rect2"></div>
			  <div class="rect3"></div>
			  <div class="rect4"></div>
			  <div class="rect5"></div>
			  <h5 style="color: #67CF22;"><?php echo $langText['index']['useful']['MODEL_LOADING']; ?></h5>
			</div>
    	  </div>
    	  <!--spinner div end-->
        </form>
      </div>
      
    </div>
  </div>
</div>
	
<!--用来提示用户输入的关键词无交集错误-->
<div class="modal fade" id="multi-morbidity" tabindex="-1" role="dialog" aria-labelledby="err">
	<div class="modal-dialog" role="document">
    	<div class="modal-content">
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<h4 class="modal-title" id="myModalLabel"><?php echo $langText['index']['resultWarning']['MODEL_TIPS']; ?></h4>
	      	</div>
	      	<div class="modal-body">
	        	<h3><?php echo $langText['index']['resultWarning']['MULTI_MODEL_BODY']; ?></h3>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $langText['index']['resultWarning']['MODEL_CLOSE']; ?></button>
	      	</div>
    	</div>
  	</div>
</div>
<!-- Modal 用户选择多义词的modal-->
<div class="modal fade" id="synonymSelectModal" tabindex="-1" role="dialog" aria-labelledby="selectSynonymWordH4">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="selectSynonymWordH4"><?php echo $langText['index']['resultWarning']['SYNONYM_MODEL_TITLE']; ?></h4>
      </div>
      <h3 id="selectTips"></h3>
      <div class="modal-body" id="moreWord">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default"  data-dismiss="modal"><?php echo $langText['index']['resultWarning']['SYNONYM_MODEL_RETUR']; ?></button>
        <button type="button" class="btn btn-primary" id="saveSelectBtn" style="margin:5px;" onclick="saveSelect()"><?php echo $langText['index']['resultWarning']['SYNONYM_MODEL_SAVE']; ?></button>
      </div>
    </div>
  </div>
</div>
<!-- 进度条的 Modal -->
<div class="modal fade" id="progressModal" tabindex="-1" role="dialog" aria-labelledby="progressModal" style="overflow-y:hidden;">
  <div class="modal-dialog" role="document" style="margin-top: 10%;">
    <div class="modal-content">
      <div class="modal-body">
        <div class="progress">
		  <div class="progress-bar progress-bar-striped active" id="searchProgress" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%;min-width:2em;"></div>
		</div>
		<div id="progressMessage" style="text-align: center;"></div>
      </div>
    </div>
  </div>
</div>	  
<!--用来提示用户输入的关键词无疾病结果-->
<div class="modal fade" id="emptySet" tabindex="-1" role="dialog" aria-labelledby="err">
	<div class="modal-dialog" role="document">
    	<div class="modal-content">
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<h4 class="modal-title" id="myModalLabel"><?php echo $langText['index']['resultWarning']['MODEL_TIPS']; ?></h4>
	      	</div>
	      	<div class="modal-body">
	        	<h3><?php echo $langText['index']['resultWarning']['NOT_DISEASE_MODEL_BODY']; ?></h3>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $langText['index']['resultWarning']['MODEL_CLOSE']; ?></button>
	      	</div>
    	</div>
  	</div>
</div>
<!--可视化疾病查询结果-->
<div class="modal fade" id="controlModal" tabindex="-1" role="dialog" aria-labelledby="err">
	<div class="modal-dialog" role="document" style="width: 940px;">
    	<div class="modal-content">
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<h4 class="modal-title" id="myModalLabel"><?php echo $langText['index']['control']['MODEL_TITLE']; ?></h4>
	      	</div>
	      	<div class="modal-body">
				<!-- 为 ECharts 准备一个具备大小（宽高）的 DOM -->
				<div id="controlChart" style="width: 800px;height:400px;"></div>
			<table class="table table-bordered" style="width: 20%">
				<tr>
					<th><?php echo $langText['index']['control']['ICPC_CLASS_NUMBER_TITLE']; ?></th>
					<td>1=A</td>
					<td>2=B</td>
					<td>3=D</td>
					<td>4=F</td>
					<td>5=H</td>
					<td>6=K</td>
					<td>7=L</td>
					<td>8=N</td>
					<td>9=P</td>
					<td>10=R</td>
					<td>11=S</td>
					<td>12=T</td>
					<td>13=U</td>
					<td>14=W</td>
					<td>15=X</td>
					<td>16=Y</td>
					<td>17=Z</td>
				</tr>
				<tr>
					<th><?php echo $langText['index']['control']['ICPC_CLASS_NAME_TITLE']; ?></th>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_A']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_B']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_D']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_F']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_H']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_K']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_L']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_N']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_P']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_R']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_S']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_T']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_U']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_W']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_X']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_Y']; ?></td>
					<td><?php echo $langText['index']['control']['ICPC_CLASS_NAME_Z']; ?></td>
				</tr>
			</table>
			
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $langText['index']['control']['MODEL_CLOSE']; ?></button>
	      	</div>
    	</div>
  	</div>
</div>	  
	
		  
		  		  