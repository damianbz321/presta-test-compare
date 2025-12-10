<?php

class Cart extends CartCore
{

    public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true, bool $keepOrderPrices = false)
    {
        if (!$this->_products || $refresh) {
            $this->_products = parent::getProducts($refresh, false, $id_country, true, $keepOrderPrices);
            Hook::exec('actionCartGetProductsAfter', [
                'products' => &$this->_products,
            ]);
        }

        if (is_int($id_product)) {
            foreach ($this->_products as $product) {
                if ($product['id_product'] == $id_product) {
                    return [$product];
                }
            }
            return [];
        }

        return $this->_products;
    }

}
