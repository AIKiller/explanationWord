<?php
include_once '../logic/header.php';
session_start();
$session_id = session_id();
if($session_id == null){
    session_regenerate_id();
}
session_write_close();
echo $session_id ;
?>