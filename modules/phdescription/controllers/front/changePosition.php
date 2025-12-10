<?php

require_once __DIR__.'/../../classes/Description.php';

class phdescriptionchangePositionModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        parent::initContent();

        $id_product = (int)$_REQUEST['id_product'];
        $id_description = (int)$_REQUEST['id_description'];
        $end_position = (int)$_REQUEST['end'];
        $return['status'] = false;
        if ($id_product > 0) {
            if ($id_description > 0) {
                $current = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'phdescription` WHERE `id_description` = '.(int)$id_description);
                if ($end_position != $current['position']) {
                    $to = 'up';
                    if ($end_position > $current['position']) {
                        $to = 'down';
                    }
                    if ($to == 'up') {
                        $descriptions = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'phdescription` WHERE `id_product` = ' . (int)$id_product . ' AND `id_description` != ' . $id_description . ' AND `position` >= ' . $end_position . ' AND `position` < ' . $current['position']);
                    } else {
                        $descriptions = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'phdescription` WHERE `id_product` = ' . (int)$id_product . ' AND `id_description` != ' . $id_description . ' AND `position` <= ' . $end_position . ' AND `position` > ' . $current['position']);
                    }
                    if (!empty($descriptions)) {
                        foreach ($descriptions as $description) {
                            if ($to == 'up') {
                                $position = $description['position'] + 1;
                            } else {
                                $position = $description['position'] - 1;
                            }
                            if ($position < 0) {
                                $position = 0;
                            }
                            Db::getInstance()->update('phdescription', array(
                                'position' => (int)$position,
                            ), 'id_description = ' . (int)$description['id_description']);
                        }
                    }
                    Db::getInstance()->update('phdescription', array(
                        'position' => (int)$end_position,
                    ), 'id_description = ' . (int)$id_description);
                    $return['status'] = true;
                }
            }
        }
        echo json_encode($return);
        exit();
    }

}
