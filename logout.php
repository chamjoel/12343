<?php
session_start();
require_once('lib/Users.class.php');
$login = New Users;
$login->logout();
?>