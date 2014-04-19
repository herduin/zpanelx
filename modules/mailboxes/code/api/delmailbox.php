<?php

/**
* Metodo para editar el tamaño de la cuenta...
*/

	if (!isset($this->datos->mailbox) ) {
	    $this->sendJSON(array("Error"=>"Faltan datos para ejecutar la accion.","DataRequest"=>$this->datos),400);
	}

    global $zdbh;
    global $controller;
    runtime_hook::Execute('OnBeforeDeleteMailbox');

    $numrows = $zdbh->prepare("SELECT * FROM x_mailboxes WHERE mb_address_vc=:mailbox");
    $numrows->bindParam(':mailbox', $this->datos->mailbox);
    $numrows->execute();
    $rowmailbox = $numrows->fetch();

    $time = time();
    $sql = "UPDATE x_mailboxes SET mb_deleted_ts=:time WHERE mb_address_vc=:mailbox";
    $sql = $zdbh->prepare($sql);
    $sql->bindParam(':time', $time);
    $sql->bindParam(':mailbox', $this->datos->mailbox);
    $sql->execute();



	$mailserver_db = ctrl_options::GetSystemOption('mailserver_db');
	include('cnf/db.php');
	$z_db_user = $user;
	$z_db_pass = $pass;
	try {
	    $mail_db = new db_driver("mysql:host=" . $host . ";dbname=" . $mailserver_db . "", $z_db_user, $z_db_pass);
	} catch (PDOException $e) {
	    echo $e;
	}
	
    $sql = $mail_db->prepare("DELETE FROM mailbox WHERE username=:mailbox");
    $sql->bindParam(':mailbox', $this->datos->mailbox);
    $sql->execute();
    
    
    $sql = $mail_db->prepare("DELETE FROM alias WHERE address=:mailbox");
    $sql->bindParam(':mailbox', $this->datos->mailbox);
    $sql->execute();
	
    runtime_hook::Execute('OnAfterDeleteMailbox');

$this->sendJSON(true);

