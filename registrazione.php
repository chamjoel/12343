<?php
session_start();
require_once('lib/Users.class.php');
$login = New Users;
//$login->esegui_registrazione();
if($login->esegui_registrazione()==TRUE){
	header("location: ".$login->Urls['login_page']."?message=".$login->message_script);
	exit;
	}
?>
<html>
<head>
<title>Register page</title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script> 
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.js"></script> 
<script type="text/javascript" src="js/validation_reg.js"></script> 
</head>
<body>
<div id="content">
<?php if($login->get_message()) : ?>
		<div class="message"><p><?php echo $login->get_message(); ?></p></div>
<?php endif; ?>
<?php echo $login->get_register_form(); ?>
</div>
</body>
</html>