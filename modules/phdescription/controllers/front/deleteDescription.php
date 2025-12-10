<?php

require_once __DIR__.'/../../classes/Description.php';

class phdescriptiondeleteDescriptionModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        parent::initContent();

        $id_description = (int)$_REQUEST['id_description'];

        $return['status'] = false;
        $return['error'] = '';
        if ($id_description > 0) {
            if ($res = Description::deleteDescription((int)$id_description)) {
                $return['status'] = true;
            } else {
                $return['error'] = $res;
            }
        } else {
            $return['error'] = 'Brak id opisu!';
        }
        echo json_encode($return);
        exit();
    }

}
