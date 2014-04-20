<?php

/**
 * @package zpanelx
 * @subpackage modules
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class webservice extends ws_xmws {

    /**
     * Get the full list of currently active domains on the server.
     */
    public function GetAllDomains() {
        $alldomains = module_controller::ListDomains();
		$this->sendJSON($alldomains);
    }

    /**
     * Gets a list of all the domains that a user has configured on their hosting account (the user id needs to be sent in the <content> tag).
     * @global type $zdbh
     * @return type 
     */
    public function GetDomainsForUser() {
        $alldomains = module_controller::ListDomains($this->datos->user);
		$this->sendJSON($alldomains);
    }

    /**
     * Enables an authenticated user to create a domain on their hosting account.
     * @return type 
     */
    public function CreateDomain() {
        if (module_controller::ExecuteAddDomain($this->datos->uid,$this->datos->domain,$this->datos->destination,$this->datos->autohome)) {
            $data = true;
        } else {
            $data = false;
        }
        $this->sendJSON($data);
    }

    /**
     * Delete a specified domain using the content <domainid> tag to pass the domain DB ID through.
     * @return type 
     */
    public function DeleteDomain() {
        if (module_controller::ExecuteDeleteDomainbyDomainName($this->datos->domain)) {
            $data = true;
        } else {
            $data = false;
        }
        $this->sendJSON($data);
    }

}

?>
