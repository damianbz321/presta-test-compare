<?php

require_once __DIR__.'/../../classes/Description.php';

class phdescriptionaddDescriptionModuleFrontController extends ModuleFrontController
{

    private $name = 'phdescription';

    public function initContent()
    {
        parent::initContent();

        $id_product = (int)$_REQUEST['id_product'];
        $type = (int)$_REQUEST['type'];
        $id_shop = (int)$_REQUEST['id_shop'];

        $return['id'] = 0;
        $return['error'] = '';
        $return['tpl'] = '';
        if ($id_product > 0) {
            $id_description = (int)Description::addDescription((int)$id_product, (int)$type, (int)$id_shop);
            if ($id_description > 0) {
                $return['id'] = $id_description;
                $list = count(Description::getProductDescriptionByLang((int)$id_product, (int)$id_shop));
                $this->context->smarty->assign(array(
                    'id_product' => $id_product,
                    'id_description' => $id_description,
                    'addFileUrl' => $this->context->link->getModuleLink($this->name, 'addFile'),
                    'languages' => Language::getLanguages(false, $this->context->shop->id),
                    'ki' => $list-1,
                ));
                $return['tpl'] = $this->context->smarty->fetch(__DIR__.'/../../views/templates/admin/_partial/type-'.$type.'.tpl');
                $return['ki'] = $list-1;
            } else {
                $return['error'] = 'Brak id description';
            }
        } else {
            $return['error'] = 'Brak id produktu!';
        }

        echo json_encode($return);
        exit();
    }

}
