<?php
session_start();
require_once('lib/Users.class.php');
$login = New Users;
$login->access_denied();
?>
<html>
<head>
<title>Pagina protetta</title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
<body>
<h1>Questa è una pagina protetta</h1>
<p>Potrai accedere a questa pagina solo hai eseguito il login.</p>
</body>
</html>
