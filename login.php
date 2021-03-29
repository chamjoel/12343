<?php
session_start();
require_once('lib/Users.class.php');
$login = New Users;
$login->esegui_login();
?>
<html>
<head>
<title>Login page</title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script> 
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.js"></script> 
<script type="text/javascript" src="js/validation_login.js"></script> 
</head>
<body>
<div id="content">

<?php if($login->get_message()) : ?>
		<div class="message"><p><?php echo $login->get_message(); ?></p></div>
<?php endif; ?>


<?php if(!$login->is_logged()) : ?>
	<?php echo $login->get_login_form(); ?>
	

<?php else: ?>
	<p>Benvenuto <strong><?php echo $login->get_username(); ?></strong></p>
	<?php echo $login->get_link_logout(); ?>
<?php endif; ?>
</div>
</body>
</html>