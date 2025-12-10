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

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class LaberCompareComparatorModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
    }

    public function postProcess()
    {
		header('Content-Type: application/json');
		
		$template = $this->module->name.'/views/templates/front/display-modal.tpl';
		
        $this->setProductsComparison();
		
		if (Tools::file_exists_cache(_PS_THEME_DIR_ .'modules/' . $template)){
			$template = _PS_THEME_DIR_ .'modules/' . $template;
		}
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_ . $template)){
			$template = _PS_MODULE_DIR_ . $template;
		}
		else
			$template = $this->module->l('No template found','comparator');

        $this->ajaxDie(json_encode(array(
            'success' => true,
            'data' => [
                'message' => $this->context->smarty->fetch($template),
                'type' => 'showModal'
            ]
        )));
			
    }
	
    public function setProductsComparison()
    {
        $list_products = array();
        $compareProducts = array();
        $ordered_features = array();
        $product_features = array();
		$list_ids_product = array();
		
        $idLang = (int)$this->context->language->id;
        $idShop = (int)$this->context->shop->id;
        $productsIds = $this->context->cookie->laberCompare;
		
        if ($productsIds) {
            $productsIds = json_decode($productsIds, true);
            foreach ($productsIds as $idProduct) {
				$list_ids_product[] = $idProduct;
                $product =  new Product($idProduct, false, $idLang, $idShop, $this->context);

                if (Validate::isLoadedObject($product)) {
                    $product->id_product = $product->id;
                    $compareProducts[] = (array) $product;
                }
            }
			
            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();

            $assembler = new ProductAssembler($this->context);
            $presenter = new ProductListingPresenter(
                new ImageRetriever(
                    $this->context->link
                ),
                $this->context->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                $this->getTranslator()
            );

            foreach ($compareProducts as $item) {
                $presentedProduct = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($item),
                    $this->context->language
                );

                $list_products[] = $presentedProduct;


                foreach ($presentedProduct['features'] as $feature) {
                    $product_features[$presentedProduct['id_product']][$feature['id_feature']] = $feature['value'];
                }
            }

            $ordered_features = $this->getFeaturesForComparison($productsIds, $idLang);
        }


        $this->context->smarty->assign(array(
            'list_products' => $list_products,
            'ordered_features' => $ordered_features,
            'product_features' => $product_features,
			'list_ids_product' => $list_ids_product
        ));
    }

    public function getFeaturesForComparison($idsArray, $idLang)
    {
        if (!Feature::isFeatureActive()) {
            return false;
        }

        $ids = '';
        foreach ($idsArray as $id) {
            $ids .= (int)$id.',';
        }

        $ids = rtrim($ids, ',');

        if (empty($ids)) {
            return false;
        }

        return Db::getInstance()->executeS('
			SELECT f.*, fl.*
			FROM `'._DB_PREFIX_.'feature` f
			LEFT JOIN `'._DB_PREFIX_.'feature_product` fp
				ON f.`id_feature` = fp.`id_feature`
			LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl
				ON f.`id_feature` = fl.`id_feature`
			WHERE fp.`id_product` IN ('.$ids.')
			AND `id_lang` = '.(int)$idLang.'
			GROUP BY f.`id_feature`
			ORDER BY f.`position` ASC
		');
    }
}
