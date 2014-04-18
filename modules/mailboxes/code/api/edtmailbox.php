<?php

/**
* Metodo para editar el tamaño de la cuenta...
*/

	if (!isset($this->datos->mailbox) ) {
	    $this->sendJSON(array("Error"=>"Faltan datos para ejecutar la accion.","DataRequest"=>$this->datos),400);
	}

	$mailserver_db = ctrl_options::GetSystemOption('mailserver_db');
	include('cnf/db.php');
	$z_db_user = $user;
	$z_db_pass = $pass;
	try {
	    $mail_db = new db_driver("mysql:host=" . $host . ";dbname=" . $mailserver_db . "", $z_db_user, $z_db_pass);
	} catch (PDOException $e) {
	    echo $e;
	}
	
	if(isset($this->datos->quota)) {
		if(strlen($this->datos->quota)<2 || $this->datos->quota < 10){
		    $this->sendJSON(array("Error"=>"La cuota no puede ser menor a 10MB","DataRequest"=>$this->datos),400);
		}
		$sql = $mail_db->prepare("UPDATE mailbox SET quota=:quota, modified=NOW() WHERE username=:mailbox");
		$sql->bindParam(':quota', $this->datos->quota);
		$sql->bindParam(':mailbox', $this->datos->mailbox);
		$sql->execute();		
	}
	
	if(isset($this->datos->password)) {
		if(strlen($this->datos->password)<6){
		    $this->sendJSON(array("Error"=>"La clave debe contener como minimo 6 caracteres.","DataRequest"=>$this->datos),400);
		}
        $sql = $mail_db->prepare("UPDATE mailbox SET password=:password, modified=NOW() WHERE username=:mailbox");
        $password = '{PLAIN-MD5}' . md5($this->datos->password);
        $sql->bindParam(':password', $password);
	    $sql->bindParam(':mailbox', $this->datos->mailbox);
        $sql->execute();
	}
	
	if(isset($this->datos->enable)) {
	    $sql = $mail_db->prepare("UPDATE mailbox SET active=:enabled, modified=NOW() WHERE username=:mailbox");
	    $status = ($this->datos->enable==1)?1:0;
	    $sql->bindParam(':enabled', $status);
	    $sql->bindParam(':mailbox', $this->datos->mailbox);
	    $sql->execute();
	}

$this->sendJSON(true);

