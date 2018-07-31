<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) { 

	header('WWW-Authenticate: Basic realm="xLoli.Net Auth"'); 

	header('HTTP/1.0 401 Unauthorized'); 

	echo "<h2>Access Denied!</h2>"; 

	exit; 
}else{
	//echo $_SERVER['PHP_AUTH_USER'];
	if ($_SERVER['PHP_AUTH_USER'] != "killer" || $_SERVER['PHP_AUTH_PW'] != "missintime") {        
		//unset($_SERVER['PHP_AUTH_USER']);
		//unset($_SERVER['PHP_AUTH_PW']);             
		exit("<h2>Authentication Failed!</h2>");

	}
}	

?>