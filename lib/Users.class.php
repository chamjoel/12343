<?php
// error_reporting(E_ALL | E_DEPRECATED | E_STRICT);

Class Users{
	/********************************
	SETTING
	*********************************/
	// le credenziali di accesso al database
	private $host_db = 'localhost';
	private $user_db = 'root';
	private $pass_db = '';
	private $name_db = 'test';
	// gli url che gestinranno le operazioni di login
	public $Urls = array(
						'login_page' 	=> 'http://localhost/test/guida_semple/login.php',
						'register_page'	=> 'http://localhost/test/guida_semple/registrazione.php',
						'logout_page'	=> 'http://localhost/test/guida_semple/logout.php'
						);
	
	/*******************************************
	se non sai ciò che fai non toccare più nulla
	*******************************************/
	/*risorse di connessione*/
	protected $conn;
	protected $selezione_db;
	
	/*variabili di registrazione*/
	protected $reg_username;
	protected $reg_email;
	protected $reg_pass;
	protected $reg_confirm_pass;
	protected $reg_crypt_pass;
	
	/*variabili di login*/
	protected $login_username;
	protected $login_password;
	protected $login_cryptpass;
	protected $login_iduser;
	
	/*variabili per gestire gli errori*/
	public $messages = array(
					1 => 'Il campo username è obbligatorio.',
					2 => 'Il campo email è obbligatorio.',
					3 => 'Il campo password è obbligatorio.',
					4 => 'Le due password non coincidono.',
					5 => 'Il campo username contiene caratteri non validi. Sono consentiti solo lettere, numeri il i seguenti simboli . _ -.',
					6 => 'Inserisci una email con sitassi corretta.',
					7 => 'La password scelta è eccessivamente breve. Scegli una password di almeno 8 caratteri.',
					8 => 'Esiste già un utente registrato con questo username.',
					9 => 'Esiste già un utente registrato con questa email.',
					10 => 'Registrazione effettuata con successo.',
					11 => 'Login errato',
					12 => 'Login eseguito con successo.',
					13 => 'Logout eseguito con successo.',
					14 => 'Per accedere a questa pagina occorre essere loggati.'
					);
	
	public $message_script;
	
	// il costruttore attiva la connessione a mysql
	public function __construct(){
		$this->connessione();
		}
	/******************
	CONNESSIONE A MYSQL
	******************/
	protected function connessione(){
		$this->conn = mysql_connect($this->host_db, $this->user_db, $this->pass_db) or die(mysql_error());
		$this->selezione_db = mysql_select_db($this->name_db, $this->conn) or die(mysql_error());
		return TRUE;
		}
		
	/*************************************
	ALCUNI METODI PER ESEGUIRE VALIDAZIONI
	*************************************/
	
	// verifica campo generico non vuoto (TRUE se non vuoto)
	public function empty_string($string){
		$string = trim($string);
		if($string==''){
			return TRUE;
			}
		else{
			return FALSE;
			}
		}
	
	// verifica sintassi username
	public function is_username($username){
		$regex = '/^[a-z0-9\.\-_]{3,30}$/i';
		return preg_match($regex, $username);
		}
	
	// verifica sintassi email (TRUE se ok)
	public function is_email($email){
		$regex = '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/';
		return preg_match($regex, $email);
		}
		
	// verifica sintassi password (per semplicità solo lunghezza) (TRUE se ok)
	public function is_secure_password($password){
		if(strlen($password)>=8){
			return TRUE;
			}
		else{
			return FALSE;
			}
		}
			
	/*****************************************************
	METODI PER VERIFICARE ESISTENZA DI USERNAME E PASSWORD
	******************************************************/
	
	// verifica esistenza username (TRUE se esiste)
	public function isset_username($username){
		$query = "SELECT COUNT(username) AS count 
					FROM users 
					WHERE username='".mysql_real_escape_string($username)."' 
					LIMIT 1";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		if($row['count']==1){
			return TRUE;
			}
		else{
			return FALSE;
			}
		}
	
	// verifica esistenza email (TRUE  se esiste)
	public function isset_email($email){
		$query = "SELECT COUNT(email) AS count 
					FROM users 
					WHERE email='".mysql_real_escape_string($email)."' 
					LIMIT 1";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		if($row['count']==1){
			return TRUE;
			}
		else{
			return FALSE;
			}
		}
	
	/******************************
	I FORM DI LOGIN E REGISTRAZIONE
	******************************/
	public function get_login_form(){
		$html = '
			<form action="' .$this->Urls['login_page']. '" method="post" id="form_login">
			<fieldset>
			<legend>Login<legend>
			<label for="login_user">Username</label>
				<input type="text" name="username" id="login_user" />
			<label for="login_pass">Password</label>
				<input type="password" name="pass" id="login_pass" />
			<input type="submit" name="login" value="login" id="login_submit"/>
			</fieldset>
			</form>';
		return $html;
		}
	
	public function get_register_form(){
		$html = '
			<form action="' .$this->Urls['register_page']. '" method="post" id="form_register">
			<fieldset>
			<legend>Registrazione<legend>
			<label for="reg_user">Username*</label>
				<input type="text" name="username" id="reg_user" />
			<label for="reg_email">Email*</label>
				<input type="text" name="email" id="reg_email" />
			<label for="reg_pass1">Password*</label>
				<input type="password" name="pass1" id="reg_pass1" />
			<label for="reg_pass2">Confirm Password*</label>
				<input type="password" name="pass2" id="reg_pass2" />
			<input type="submit" name="register" value="registra" id="reg_submit" />
			<input type="reset" name="reset" value="cancella" id="reg_reset" />
			</fieldset>
			</form>';
		return $html;
		}
	
	/*****************************
	LINK LOGOUT
	*****************************/
	public function get_link_logout(){
		if($this->is_logged()){
			return '<a href="'.$this->Urls['logout_page'].'" class="logout">Logout</a>';
			}
		return '';
		}
		
	/*******************************
	METODO PER CRIPTARE LE PASSWORD
	*******************************/
	public function crypt_pass($pass){
		return sha1($pass);
		}
	
	/*****************************
	ESECUZIONE DELLA REGISTRAZIONE
	******************************/
	public function esegui_registrazione(){
		// se il form e i suoi input sono stati inviati
		if(isset($_POST['register']) AND 
			isset($_POST['username']) AND 
			isset($_POST['email']) AND 
			isset($_POST['pass1']) AND 
			isset($_POST['pass2'])){ 
			//valorizziamo alcune variabili
			$this->reg_username = trim($_POST['username']);
			$this->reg_email = trim($_POST['email']);
			$this->reg_pass = trim($_POST['pass1']);
			$this->reg_confirm_pass = trim($_POST['pass2']);
			// criptiamo la password
			$this->reg_crypt_pass = $this->crypt_pass($this->reg_pass);
			// eseguiamo la validazione degli input
			$valid_input = $this->check_input_registrazione();
			// se sono validi
			if($valid_input===TRUE){
				// inseriemo all'interno del database i dati
				$this->query_insert_registrazione();
				// settiamo il messaggio di successo della registrazione
				$this->message_script = 10;
				return TRUE;
				}
			}
		return FALSE;
		}
	
	// verifica che gli input siano corretti
	protected function check_input_registrazione(){
		if($this->empty_string($this->reg_username)){
			$this->message_script = 1;
			return FALSE;
			}
		else if($this->empty_string($this->reg_email)){
			$this->message_script = 2;
			return FALSE;
			}
		else if($this->empty_string($this->reg_pass)){
			$this->message_script = 3;
			return FALSE;
			}
		else if($this->reg_pass != $this->reg_confirm_pass){
			$this->message_script = 4;
			return FALSE;
			}
		else if(!$this->is_username($this->reg_username)){
			$this->message_script = 5;
			return FALSE;
			}
		else if(!$this->is_email($this->reg_email)){
			$this->message_script = 6;
			return FALSE;
			}
		else if(!$this->is_secure_password($this->reg_pass)){
			$this->message_script = 7;
			return FALSE;
			}
		else if($this->isset_username($this->reg_username)==TRUE){
			$this->message_script = 8;
			return FALSE;
			}
		else if($this->isset_email($this->reg_email)==TRUE){
			$this->message_script = 9;
			return FALSE;
			}
		return TRUE;
		}
	
	// esecuzione della query insert di registrazione
	protected function query_insert_registrazione(){
		$query = "
					INSERT INTO users 
					SET 
						username='".mysql_real_escape_string($this->reg_username)."', 
						pass='".mysql_real_escape_string($this->reg_crypt_pass)."', 
						email='".mysql_real_escape_string($this->reg_email)."', 
						data_reg= NOW()";
		$result = mysql_query($query) or die(mysql_error());
		return mysql_insert_id();
		}
		
	/*******************
	ESECUZIONE DEL LOGIN
	********************/
	public function esegui_login(){
		// se il form di login e i sui tutti input sono stati inviati 
		if(isset($_POST['login']) AND isset($_POST['username']) AND isset($_POST['pass'])){
			// valorizziamo delle variabili
			$this->login_username = trim($_POST['username']);
			$this->login_password = trim($_POST['pass']);
			// criptiamo la password
			$this->login_cryptpass = $this->crypt_pass($this->login_password);
			// validiamo i dati (non devono essere vuoti)
			$not_empty_input = $this->check_input_login();
			// se la validazione è andata a buon fine
			if($not_empty_input===TRUE){
				// eseguiamo la query e verifichiamo se individua le credenziali
				if($this->query_select_login()==TRUE){
					// settiamo lo status di utente loggato
					$this->set_logged($this->login_iduser);
					// settiamo l'username
					$this->set_username($this->login_username);
					// settiamo il messaggio di successo del login
					$this->message_script = 12;
					return TRUE;
					}
				// se la query non ha trovat utenti con quelle credenziali
				else{
					//  settiamo un messaggio di insuccesso dell'operazone
					$this->message_script = 11;
					}
				}
			}
		return FALSE;
		}
	
	// verifica che gli input del login non siano vuoti
	protected function check_input_login(){
		if($this->empty_string($this->login_username)){
			$this->message_script = 1;
			return FALSE;
			}
		else if($this->empty_string($this->login_password)){
			$this->message_script = 3;
			return FALSE;
			}
		return TRUE;
		}
	
	// esecuzione della qeury per verificare il login
	protected function query_select_login(){
		$query = "
					SELECT id FROM users 
					WHERE 
						username='".mysql_real_escape_string($this->login_username)."' AND 
						pass='".mysql_real_escape_string($this->login_cryptpass)."'";
		$result = mysql_query($query) or die(mysql_error());
		// se individua l'utente
		if(mysql_num_rows($result)==1){
			$row = mysql_fetch_array($result);
			$this->login_iduser = $row['id'];
			return TRUE;
			}
		return FALSE;
		}
		
	/***********************************
	VERIFICA DELLO STATO DI LOGIN UTENTE
	***********************************/
	
	// verifica login
	public function is_logged(){
		return isset($_SESSION['auth']);
		}
	
	// set login
	protected function set_logged($id_user){
		$_SESSION['auth'] = $id_user;
		return;
		}
	
	// access denied
	public function access_denied(){
		if(!$this->is_logged()){
			header("location: ".$this->Urls['login_page']."?message=14");
			exit;
			}
		return;
		}

	protected function set_username($username){
		$_SESSION['username_logged'] = $username;
		return;
		}
	
	public function get_username(){
		return isset($_SESSION['username_logged']) ? $_SESSION['username_logged'] : '';
		}	
		
	// logout
	public function logout(){
		session_unset();
		session_destroy();
		setcookie(session_name(), '', time()-42000, '/');
		header("location: ".$this->Urls['login_page']."?message=13");
		return;
		}

	/*****************************
	METODO PER OTTENERE I MESSAGGI
	******************************/
	public function get_message(){
		if(isset($_GET['message'])){
			$this->message_script = $_GET['message'];
			}
		$key = intval($this->message_script);
		if(array_key_exists($key, $this->messages)){
			return $this->messages[$key];
			}
		return FALSE;
		}
	}
?>