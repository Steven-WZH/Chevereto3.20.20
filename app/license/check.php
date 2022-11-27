<?php

/* --------------------------------------------------------------------

  Chevereto
  https://chevereto.com/

  @author	Rodolfo Berrios A. <http://rodolfoberrios.com/>
            <inbox@rodolfoberrios.com>

  Copyright (C) Rodolfo Berrios A. All rights reserved.

  BY USING THIS SOFTWARE YOU DECLARE TO ACCEPT THE CHEVERETO EULA
  https://chevereto.com/license

  --------------------------------------------------------------------- */

if (!defined('access') or !access) {
    die('This file cannot be directly accessed.');
}
  
if (isset($_REQUEST['chv-license-info'])) {
    function get_license_info()
    {
        require_once(G_APP_PATH . 'license/key.php');
        list($public_id, $secret) = explode(':', $license);
        $json = ['code' => 400];
        if ($_REQUEST['get'] == 'id') {
            $return = $public_id;
        }
        if ($_REQUEST['get'] == 'hash' and !empty($_REQUEST['complement'])) {
            $return = hash('sha512', $secret.$_REQUEST['complement']);
        }
        if (isset($return)) {
            $json = ['code' => 200, 'return' => $return];
        }
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-type: application/json; charset=UTF-8');
        die(json_encode($json));
    }
    get_license_info();
}
