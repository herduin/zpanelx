<?php

/**
 * @package zpanelx
 * @subpackage modules
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
error_reporting(E_ALL);
ini_set('display_errors','On');

class webservice extends ws_xmws {

	var $create;
	var $ok;
    
    
        
    /**
     * Get the full list of currently active domains on the server.
     */
    public function getMailboxes() {
		try{
			$data = module_controller::ListMailboxes($this->datos->user);
		}catch(Exception $e){
			$data = false;
		}
		$this->sendJSON( $data );	    	
    }
	
	
    /**
     * Get the full list of currently active domains on the server.
     */
    public function addMailbox() {
    	require("api/addmailbox.php");
	}
	
	
    /**
     * Get the full list of currently active domains on the server.
     */
    public function edtMailbox() {
    	require("api/edtmailbox.php");
	}
	
	
    /**
     * Obtener la lista de dominios del usuario enviado en $this->datos->user por json.
     */
	public function getDomains(){
        global $zdbh;
        $sql = "SELECT * FROM x_vhosts WHERE vh_acc_fk=:userid AND vh_enabled_in=1 AND vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $this->datos->user);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':userid', $this->datos->user);
            $res = array();
            $sql->execute();
            while ($rowdomains = $sql->fetch()) {
                $res[] = array('domain' => $rowdomains['vh_name_vc']);
            }
            $data = $res;
        } else {
            $data = false;
        }

	   $this->sendJSON( $data );
	}
	
	
    /**
     * Obtener el id del usuario basado en el nombre de usuario.
     */
    public function getUserID() {
        global $zdbh;
        $rows = $zdbh->prepare("SELECT ac_id_pk FROM x_accounts WHERE ac_user_vc= :username");
        $rows->bindParam(':username', $this->datos->username);
        $rows->execute();
        $dbvals = $rows->fetch();
		$this->sendJSON(array("userID"=>$dbvals['ac_id_pk']));
    }
	
}

