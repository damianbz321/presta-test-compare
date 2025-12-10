<?php

require_once __DIR__.'/../../classes/Description.php';

class phdescriptiongetDescriptionModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        parent::initContent();

        $id_product = (int)$_REQUEST['id_product'];

        $return = array();
        if ($id_product > 0) {
            $name = 'phdescription';
            $list = Description::getProductDescriptionByLang((int)$id_product, (int)$this->context->shop->id);
            $tpl = '';
            if (!empty($list)) {
                $this->context->smarty->assign(array(
                    'list' => $list,
                    'id_product' => $id_product,
                    'languages' => Language::getLanguages(false, $this->context->shop->id),
                    'addFileUrl' => $this->context->link->getModuleLink($name, 'addFile'),
                    'edit' => (int)Configuration::get('PHDESCRIPTION_EDIT'),
                ));
                $tpl = $this->context->smarty->fetch(__DIR__.'/../../views/templates/admin/list.tpl');
            }
            $return['tpl'] = $tpl;
            $tpl2 = $this->context->smarty->fetch(__DIR__.'/../../views/templates/admin/head.tpl');
            $return['head'] = $tpl2;
            $return['counts'] = count($list);
        }

        echo json_encode($return);
        exit();
    }

}
