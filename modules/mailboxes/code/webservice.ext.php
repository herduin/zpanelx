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

    /**
     * Get the full list of currently active domains on the server.
     */
    public function ListMailboxes() {
        $data = module_controller::ListMailboxes($this->datos->user);
		$this->sendJSON( $data );
    }

}

?>
