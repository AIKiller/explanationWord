<?php
include("../logic/common.inc.php");
if(isset($_SESSION["userInformationSet"])){
    echo $_SESSION["userInformationSet"];
}else{
    echo 0;
}

?>