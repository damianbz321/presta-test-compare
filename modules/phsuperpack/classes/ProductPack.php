<?php

class ProductPack extends ObjectModel
{
    public $id_superpack_product;
    public $id_product;
    public $id_product_pack;

    public static $definition = array(
        'table' => 'phsuperpack_product',
        'primary' => 'id_superpack_product',               
        'multishop' => true,
        'fields' => array(                        
            'id_superpack_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            //shop fields
            'id_product' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'),
			'id_product_pack' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId')
        ),
    );

    public static function getPackById($id_superpack_product)
    {
        $id_shop = (int)Context::getContext()->shop->id;

		$q = 'SELECT hs.`id_superpack_product`, hs.`id_product_pack`, hs.`id_product_attribute` FROM '._DB_PREFIX_.'phsuperpack_product hs
			WHERE hs.id_shop = ' . (int)$id_shop . '
			AND hs.id_superpack_product = ' . (int)$id_superpack_product;		
		return DB::getInstance()->getRow($q);
    }

	public static function getPack($id_product)
	{
		$id_shop = (int)Context::getContext()->shop->id;

		$q = 'SELECT hs.`id_superpack_product`, hs.`id_product_pack`, hs.`active`, hs.`id_product_attribute` FROM '._DB_PREFIX_.'phsuperpack_product hs
			WHERE hs.id_shop = ' . (int)$id_shop . '
			AND hs.id_product = ' . (int)$id_product . ' ORDER BY hs.`position` asc ';		
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q);
	}

    public static function getPackProductId($id_product_pack)
    {
        $id_shop = (int)Context::getContext()->shop->id;

		$q = 'SELECT hs.`id_superpack_product`, hs.`id_product_pack`, hs.`id_product_attribute` FROM '._DB_PREFIX_.'phsuperpack_product hs
			WHERE hs.id_shop = ' . (int)$id_shop . '
			AND hs.id_product_pack = ' . (int)$id_product_pack;		
		return DB::getInstance()->getRow($q);
    }

    public function add($auto_date = true, $null_values = false)
    {
        $return = parent::add($auto_date, $null_values);
        
        return $return;
    }

    public function update($null_values = false)
    {
        if (Tools::getValue('groupBox')) {
            $this->updateGroup(Tools::getValue('groupBox'));
        }
        $return = parent::update($null_values);
        return $return;
    }

    public function delete()
    {
        //$this->cleanGroups();
        $id_superpack_product_settings = ProductPackSettings::getIdByProductId(Tools::getValue('id_product'));
        $phsuperpack_product_settings = new ProductPackSettings($id_superpack_product_settings);
        $phsuperpack_product_settings->delete();
        return parent::delete();
    }

    protected function query($sql)
    {
        try {
            return Db::getInstance()->execute($sql);
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }

    protected function execute($sqls)
    {
        foreach ($sqls as $sql) {
            if (!$this->query($sql)) {
                return false;
            }
        }

        return true;
    }
}
