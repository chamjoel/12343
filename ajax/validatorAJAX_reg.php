<?php
session_start();
require_once('../lib/Users.class.php');
$login = New Users;

/*
PROCESSIAMO LA RICHIESTA AJAX
*/
if(isset($_POST['username'])){
	if(!$login->isset_username($_POST['username'])){
		echo 'true';
		}
	else{
		echo 'false';
		}
	}
else if(isset($_POST['email'])){
	if(!$login->isset_email($_POST['email'])){
		echo 'true';
		}
	else{
		echo 'false';
		}
	}
?>