<html>
	<head>
		<?php include("tools/headr.php");?>
		<title><?php echo $langText["SYSTEM_TITLE_NAME"];?></title>
	</head>
	<body>
	<div class="container" style="margin-top: 10%;">
		<div class="row">
			<div class="col-md-7 col-md-offset-3" style="text-align: center;">
				<div class="row">
					<div class="col-md-10" style="border: 1px solid rgba(0, 0, 0, 0.25);padding: 20px;border-radius: 15px;box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.05);">
						<h3><?php echo $langText["login"]["MODEL_TITLE"];?></h3>
						<table style="margin: 0 auto;width: 60%;">
							<tr>
								<td><div class="form-group"><input type="text" id="username" class="form-control" placeholder="<?php echo $langText['login']['USERNAME'];?>"></div></td>
							</tr>
							<tr>
								<td><div class="form-group"><input type="password" id="password" class="form-control" placeholder="<?php echo $langText['login']['PASSWORD'];?>"></div></td>
							</tr>
							<tr>
								<td><button class="btn btn-info" style="width:100%" onclick="userLogin()"><?php echo $langText["login"]["SIGNIN_BUTTON"];?></button></td>
							</tr>
							<tr>
								<td><a onclick="changeLanguage()" style="float:right;cursor: pointer;">change language</a></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script>
$(document).ready(function(){
	document.onkeydown=function(e){
		var ev = e || window.event;//获取event对象  
		var objElement = ev.target || ev.srcElement;//获取事件源
		if (ev.keyCode == 13){
			userLogin();
		}
	}
})
function userLogin(){
	var userName = $('#username').val();
	var password = $('#password').val();
	var lang = getCookie("lang")||"en";//默认语言版本是英语
	$.get('../API/login.php?password='+password+"&username="+userName+"&lang="+lang,function(result){
		//alert(result);
		if(result != 0){
			setCookie('userName',userName);//设置cookie.
			setCookie("session_id",result);//设置session_id;
			location = 'index.php';
		}else{
			alert("<?php echo $langText['login']['CAN_NOT_LOGIN_IN'];?>");
		}
	});
}
function changeLanguage(){
	var lang = getCookie("lang")||"en";//默认语言版本是英语
	if(lang == "en"){
		setCookie('lang',"nl");//设置cookie.
	}else if(lang == "nl"){
		setCookie('lang',"en");//设置cookie.
	}
	location = location;
}
</script>
</html>