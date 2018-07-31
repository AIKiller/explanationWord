<?php
session_start();
$session_id = session_id();
session_write_close();
echo $session_id ;
?>