<?php
/*
* 2017 AxonVIP
*
* NOTICE OF LICENSE
*
*  @author AxonVIP <axonvip@gmail.com>
*  @copyright  2017 axonvip.com
*   
*/

class LaberCompareActionsModuleFrontController extends ModuleFrontController
{
    /**
     * @var int
     */
    public $id_product;

    public function init()
    {
        parent::init();
		require_once(dirname(__FILE__).'/../../labercompare.php');
        $this->id_product = (int)Tools::getValue('id_product');
    }

    public function postProcess()
    {
        if (Tools::getValue('process') == 'remove') {
            $this->processRemove();
        } elseif (Tools::getValue('process') == 'add') {
            $this->processAdd();
        } elseif (Tools::getValue('process') == 'removeAll') {
            $this->processRemoveAll();
        }
    }

    /**
     * Remove a compare product.
     */
    public function processRemove()
    {
        header('Content-Type: application/json');
        $idProduct = (int)Tools::getValue('idProduct');
        $productsIds = $this->context->cookie->laberCompare;
        $productsIds = json_decode($productsIds, true);
        unset($productsIds[$idProduct]);
        $productsIds = json_encode($productsIds, true);
        $this->context->cookie->__set('laberCompare', $productsIds);

        $this->ajaxDie(json_encode(array(
            'success' => true,
            'data' => [
                'message' => $this->module->l('Product successfully removed from the product comparison!','actions'),
                'type' => 'removed',
				'view' => $this->module->l('View compare','actions')
            ]
        )));
    }

    /**
     * Remove all compare products.
     */
    public function processRemoveAll()
    {
        header('Content-Type: application/json');

        $productsIds = array();
        $productsIds = json_encode($productsIds, true);
        $this->context->cookie->__set('laberCompare', $productsIds);
		
        $this->ajaxDie(json_encode(array(
            'success' => true,
            'data' => [
                'message' => $this->module->l('All products removed','actions'),
                'type' => 'removedAll',
				'view' => $this->module->l('View compare','actions')
            ]
        )));
    }

    /**
     * Add a compare product.
     */
    public function processAdd()
    {
        header('Content-Type: application/json');

        $idProduct = (int)Tools::getValue('idProduct');

        $productsIds = $this->context->cookie->laberCompare;
        $productsIds = json_decode($productsIds, true);
		$compare = new LaberCompare();
        if (!isset($productsIds[$idProduct])) {
			if(count($productsIds) >= Configuration::get($compare->prefix . 'COMPARATOR_MAX_ITEM')){
				$this->ajaxDie(json_encode(array(
					'success' => false,
					'data' => [
						'message' => sprintf($this->module->l('You cannot add more than %s product(s) to the product Comparison','actions'), Configuration::get($compare->prefix . 'COMPARATOR_MAX_ITEM')),
						'type' => 'full',
						'view' => $this->module->l('View compare','actions')
					]
				)));
			}
            $productsIds[$idProduct] = $idProduct;
            $productsIds = json_encode($productsIds, true);

            $this->context->cookie->__set('laberCompare', $productsIds);

            $this->ajaxDie(json_encode(array(
                'success' => true,
                'data' => [
                    'message' => $this->module->l('Has been added to compare.','actions'),
                    'type' => 'added',
					'view' => $this->module->l('View compare','actions')
                ]
            )));
        }
    }
}
