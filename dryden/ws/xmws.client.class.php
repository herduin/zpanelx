<?php


/**
 * Acceso via API para controlar el zpanel
 */
class zPanel {
	
	public $dominio;
	public $resultado;
	public $cuota_x_defecto = 100;
    public $method = null;
    public $username = null;
    public $password = null;
    public $apiKey = null;
    public $host = null;
	
	/**
	* Inicializar el zPanel remoto
	*/
	function zPanel($host=false,$user="",$pass="",$apiKey=""){
		$this->host = $host;
		$this->apiKey = $apiKey;
		$this->username = $user; 
		$this->password = $pass;
	}
	
	
	/**
	* Enviar una peticion json al zPanel
	*/
	function send($module,$metodo,$data=false){
        $xml_data  = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml_data .= "<xmws> 
			<apikey>{$this->apiKey}</apikey> 
			<request>{$metodo}</request> 
			<authuser>{$this->username}</authuser> 
			<authpass>{$this->password}</authpass>";
		 $xml_data .= (($data===false)?"":"<content><![CDATA[".json_encode($data)."]]></content>");
		 $xml_data .= "<json>true</json>
		</xmws>";
        
        $url = $this->host."api/".$module."/";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml; charset=utf-8'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$json = @json_decode($output);
		if($json==false){
			$this->resultado = $output;
	     	return $output;
		}else{
			$this->resultado = $json;
			return $json;
		}
	}
	

	/**
	* Obtener la lista de dominios
	*/
	function getDominios($user=false){
		if($user==false){
			return $this->send("domains","GetAllDomains");
		}else{
			return $this->send("domains","GetDomainsForUser",array("user"=>$user));
		}
	}

	
}