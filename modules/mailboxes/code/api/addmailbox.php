<?php
       
	global $zdbh;
	$uid = $this->datos->userID;
	$address = $this->datos->username;
	$domain = $this->datos->domain;
	$password = $this->datos->pass;
	$maxMail = ctrl_options::GetSystemOption('max_mail_size');
	$tamano = (isset($this->datos->tamano))?$this->datos->tamano:$maxMail;
	$address = strtolower(str_replace(' ', '', $address));
	$fulladdress = strtolower(str_replace(' ', '', $address . "@" . $domain));
	
	if ($uid=="" || $address=="" || $domain=="" || $password=="") {
	    $this->sendJSON(array("Error"=>"Faltan datos para la cuenta...",$this->datos),400);
	}
	
	
	$currentuser = ctrl_users::GetUserDetail($uid);
	if ($currentuser['userid']=="") {
	    $this->sendJSON(array("Error"=>"Falta el id del propietario de la cuenta.",$this->datos),400);
	}


	runtime_hook::Execute('OnBeforeCreateMailbox');

	$mailserver_db = ctrl_options::GetSystemOption('mailserver_db');
	include('cnf/db.php');
	$z_db_user = $user;
	$z_db_pass = $pass;
	try {
	    $mail_db = new db_driver("mysql:host=" . $host . ";dbname=" . $mailserver_db . "", $z_db_user, $z_db_pass);
	} catch (PDOException $e) {
	    echo $e;
	}
	
    $numrows = $mail_db->prepare("SELECT username FROM mailbox WHERE username=:fulladdress");
    $numrows->bindParam(':fulladdress', $fulladdress);
    $numrows->execute();
    $result = $numrows->fetch();
    if ($result!=false) {
	    $this->sendJSON(array("Error"=>"La cuenta ya existe...",$fulladdress),400);
    }
	

    //$result = $mail_db->query("SELECT domain FROM domain WHERE domain='" . $domain . "'")->Fetch();
    $numrows = $mail_db->prepare("SELECT domain FROM domain WHERE domain=:domain");
    $numrows->bindParam(':domain', $domain);
    $numrows->execute();
    $result = $numrows->fetch();
    if (!$result) {
        $sql = $mail_db->prepare("INSERT INTO domain (  domain,
                                                        description,
                                                        aliases,
                                                        mailboxes,
                                                        maxquota,
                                                        quota,
                                                        transport,
                                                        backupmx,
                                                        created,
                                                        modified,
                                                        active) VALUES (
                                                        :domain,
                                                        '',
                                                        0,
                                                        0,
                                                        0,
                                                        0,
                                                        '',
                                                        0,
                                                        NOW(),
                                                        NOW(),
                                                        '1')");
        $sql->bindParam(':domain', $domain);
        $sql->execute();
    }
    //$result = $mail_db->query("SELECT username FROM mailbox WHERE username='" . $fulladdress . "'")->Fetch();
    $numrows = $mail_db->prepare("SELECT username FROM mailbox WHERE username=:fulladdress");
    $numrows->bindParam(':fulladdress', $fulladdress);
    $numrows->execute();
    $result = $numrows->fetch();
    if (!$result) {
        $sql = $mail_db->prepare("INSERT INTO mailbox (username,
								 							password,
														 	name,
															maildir,
														 	local_part,
														 	quota,
														 	domain,
														 	created,
														 	modified,
														 	active) VALUES (
														 	:fulladdress,
														 	:password,
														 	:address,
														 	:location,
														 	:address2,
														 	:maxMail,
														 	:domain,
														 	NOW(),
														 	NOW(),
														 	'1')");
        $password = '{PLAIN-MD5}' . md5($password);
        $location = $domain . "/" . $address . "/";
        
        
        

        $sql->bindParam(':password', $password);
        $sql->bindParam(':address', $address);
        $sql->bindParam(':fulladdress', $fulladdress);
        $sql->bindParam(':location', $location);
        $sql->bindParam(':address2', $address);
        $sql->bindParam(':maxMail', $tamano); //$maxMail
        $sql->bindParam(':domain', $domain);
        $sql->execute();
        
        
        $sql = $mail_db->prepare("INSERT INTO alias  (address,
														 	goto,
														 	domain,
															created,
														 	modified,
														 	active) VALUES (
														 	:fulladdress,
														 	:fulladdress2,
														 	:domain,
														 	NOW(),
														 	NOW(),
														 	'1')");
        $sql->bindParam(':domain', $domain);
        $sql->bindParam(':fulladdress', $fulladdress);
        $sql->bindParam(':fulladdress2', $fulladdress);
        $sql->execute();
    }

	$sql = "INSERT INTO x_mailboxes (mb_acc_fk,
									 mb_address_vc,
									 mb_created_ts) VALUES (
									 :userid,
									 :fulladdress,
									 :time)";
	$time = time();
	$sql = $zdbh->prepare($sql);
	$sql->bindParam(':time', $time);
	$sql->bindParam(':userid', $currentuser['userid']);
	$sql->bindParam(':fulladdress', $fulladdress);
	$sql->execute();
	runtime_hook::Execute('OnAfterCreateMailbox');
	
	$this->sendJSON( true );





