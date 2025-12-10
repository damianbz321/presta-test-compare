<?php
/**
* 2014-2020 Presta-Mod.pl Rafał Zontek
*
* NOTICE OF LICENSE
*
* Obiekt automatycznie wygenerowany za pomocą narzędzia ObjectGenerator
* http://presta-mod.pl
*
* DISCLAIMER
*
*
*  @author    Presta-Mod.pl Rafał Zontek <biuro@presta-mod.pl>
*  @copyright 2014-2020 Presta-Mod.pl
*  @license   Licecnja na jedną domenę
*  Presta-Mod.pl Rafał Zontek
*/

class PaczkomatyList extends ObjectModel
{
    /* Classic fields */
    public $id;
    /** @var string id_cart */
    public $id_cart;
    /** @var string machine */
    public $machine;
    /** @var string nr_listu */
    public $nr_listu;
    /** @var string status */
    public $status;
    /** @var string post_info */
    public $post_info;
    /** @var string id_pack */
    public $id_pack;
    /** @var int pack_type */
    public $pack_type;
    /** @var string pack_status */
    public $pack_status;

    public $id_order = '';

    public static $definition = array(
        'table' => 'pminpostpaczkomatylist',
        'primary' => 'id',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            /* Classic fields */
            'id_cart' => array(
                'type' => self::TYPE_STRING,
                'size' => 100,
            ),
            'machine' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
            ),
            'nr_listu' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
            ),
            'status' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
            ),
            'post_info' => array(
                'type' => self::TYPE_STRING,
            ),
            'id_pack' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
            ),
            'pack_type' => array(
                'type' => self::TYPE_INT,
                'size' => 11,
            ),
            'pack_status' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
            ),


        ),
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'machines',
        'fields' => array(
            'id_cart' => array('xlink_resource' => 'carts'),
            'id_order' => array('xlink_resource' => 'orders'),
        ),
    );

    public static function installSql()
    {
        $sql = array();
        $sql[] = '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pminpostpaczkomatylist`
            (
              `id` INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
              `id_cart` CHAR(100) ,
              `machine` CHAR(255) ,
              `nr_listu` CHAR(255) ,
              `status` CHAR(255) ,
              `post_info` LONGTEXT ,
              `id_pack` CHAR(255) ,
              `zlecenie` CHAR(255) ,
              `pack_type` INT(11) ,
              `pack_status` CHAR(255)
            ) CHARACTER SET utf8 COLLATE utf8_general_ci;';

        foreach ($sql as $item) {
            if (!Db::getInstance()->execute($item)) {
                return false;
            }
        }
        return true;
    }

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        if ($this->id) {
            $this->id_order = $this->getIdOrder($this->id_cart);
            $this->post_info = Tools::stripslashes($this->post_info);
        }
    }

    public static function uninstallSql()
    {
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'pminpostpaczkomatylist`';

        foreach ($sql as $item) {
            if (!Db::getInstance()->execute($item)) {
                return false;
            }
        }
        return true;
    }

    public static function update268()
    {
        try {
            $sql = '
            ALTER TABLE `'._DB_PREFIX_.'pminpostpaczkomatylist` 
            ADD COLUMN zlecenie char(255)';
            Db::getInstance()->execute($sql);
        } catch (Exception $exp) {
        }
    }

    public static function update262()
    {
        $sql = '
            ALTER TABLE                 `'._DB_PREFIX_.'pminpostpaczkomatylist` 
            CONVERT TO CHARACTER SET    utf8 
            COLLATE                     utf8_general_ci';
        Db::getInstance()->execute($sql);
    }

    public static function update256()
    {
        try {
            $sql = '
            ALTER TABLE `'._DB_PREFIX_.'pminpostpaczkomatylist`
            ADD COLUMN pack_type int(11) DEFAULT 0,
            ADD COLUMN pack_status CHAR(255)';
            Db::getInstance()->execute($sql);
        } catch (Exception $exp) {
        }
    }

    public static function update259()
    {
        try {
            $sql = '
                ALTER TABLE `'._DB_PREFIX_.'pminpostpaczkomatylist`
                ADD COLUMN pack_status CHAR(255)';
            Db::getInstance()->execute($sql);
        } catch (Exception $exp) {
        }
        try {
            $sql = '
                ALTER TABLE `'._DB_PREFIX_.'pminpostpaczkomatylist`
                ADD COLUMN id_pack CHAR(255)';
            Db::getInstance()->execute($sql);
        } catch (Exception $exp) {
        }

        try {
            $sql = '
                ALTER TABLE `'._DB_PREFIX_.'pminpostpaczkomatylist`
                ADD COLUMN pack_type int(11) DEFAULT 0';
            Db::getInstance()->execute($sql);
        } catch (Exception $exp) {
        }
    }

    public static function getSelectedMachine($id_cart)
    {
        $sql = '
            SELECT      machine 
            FROM    `'._DB_PREFIX_.'pminpostpaczkomatylist`
            WHERE   id_cart = '.(int)$id_cart.' 
            ORDER   BY id DESC';
        $machine = Db::getInstance()->getValue($sql);
        if ($machine === false) {
            return '';
        } else {
            return $machine;
        }
    }

    public static function getPostInfoByIdCart($id_cart)
    {
        $sql = '
            SELECT      post_info 
            FROM    `'._DB_PREFIX_.'pminpostpaczkomatylist`
            WHERE   id_cart = '.(int)$id_cart.' 
            ORDER   BY id DESC';
        return Tools::stripslashes(Db::getInstance()->getValue($sql));
    }

    public static function getByIdCart($id_cart)
    {
        if (Tools::isSubmit('id')) {
            $sql = '
                SELECT      id 
                FROM    `'._DB_PREFIX_.'pminpostpaczkomatylist`
                WHERE   id_cart = '.(int)$id_cart.'
                AND     id = '.(int)Tools::getValue('id').' 
                ORDER   BY id DESC';
            $id = Db::getInstance()->getValue($sql);
        } else {
            $sql = '
                SELECT      id 
                FROM    `'._DB_PREFIX_.'pminpostpaczkomatylist`
                WHERE   id_cart = '.(int)$id_cart.' 
                ORDER   BY id DESC';
            $id = Db::getInstance()->getValue($sql);
        }
        if ($id) {
            $paczkomatyList = new PaczkomatyList($id);
        } else {
            $paczkomatyList = new PaczkomatyList();
            $paczkomatyList->id_cart = $id_cart;
        }
        return $paczkomatyList;
    }

    public static function createEmpty($id_cart)
    {
        $sql = '
            SELECT      id 
            FROM    `'._DB_PREFIX_.'pminpostpaczkomatylist`
            WHERE   id_cart = '.(int)$id_cart.' 
            ORDER   BY id DESC';
        $id = Db::getInstance()->getValue($sql);
        if ($id) {
            $paczkomatyList = new PaczkomatyList($id);
            $paczkomatyList->id = false;
            $paczkomatyList->post_info = '';
            $paczkomatyList->nr_listu = '';
            $paczkomatyList->pack_status = '';
            $paczkomatyList->status = '';
            $paczkomatyList->save();
        }
        return $paczkomatyList;
    }

    public static function getNrListuByIdCart($id_cart)
    {
        if (Tools::isSubmit('id')) {
            $sql = '
                SELECT      nr_listu 
                FROM    `'._DB_PREFIX_.'pminpostpaczkomatylist`
                WHERE   id_cart = '.(int)$id_cart.' 
                AND     id = '.(int)Tools::getValue('id').'
                ORDER   BY id DESC';
            return Db::getInstance()->getValue($sql);
        } else {
            $sql = '
                SELECT      nr_listu 
                FROM    `'._DB_PREFIX_.'pminpostpaczkomatylist`
                WHERE   id_cart = '.(int)$id_cart.' 
                ORDER   BY id DESC';
            return Db::getInstance()->getValue($sql);
        }
    }

    public static function getObjectByShippingNumber($shipping_number)
    {
        $sql = '
            SELECT      id 
            FROM    `'._DB_PREFIX_.'pminpostpaczkomatylist`
            WHERE   nr_listu = "'.(string)pSql($shipping_number).'"';
        $id = Db::getInstance()->getValue($sql);
        if ($id) {
            return new PaczkomatyList($id);
        } else {
            return false;
        }
    }

    public static function getPackages($id_cart)
    {
        $sql = '
            SELECT      id,nr_listu 
            FROM        `'._DB_PREFIX_.'pminpostpaczkomatylist` 
            WHERE       id_cart = '.(int)$id_cart.'
            ORDER BY    id ASC';
        return Db::getInstance()->executeS($sql);
    }

    public static function getSizeFormat()
    {
        $module = Module::getInstanceByName('pminpostpaczkomaty');

        $fomat = Configuration::get('PMINPOSTPACZKOMATY_LABEL_FORMAT');
        $size = Configuration::get('PMINPOSTPACZKOMATY_LABEL_SIZE');
        if (Tools::isSubmit('format')) {
            $format = Tools::getValue('format');
            $size = Tools::getValue('size_l');
        }
        if (strtolower($format) != 'pdf') {
            $size = '';
        }
        
        if ($size != '') {
            return '-'.$size.'.pdf';
        } else {
            return '.'.strtolower($format);
        }
    }

    public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
    {
        $result = parent::getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit);
        foreach ($result as &$item) {
            $object = new PaczkomatyList($item['id']);
            $item['id_order'] = $this->getIdOrder($object->id_cart);
        }
        return $result;
    }

    public function getIdOrder($id_cart)
    {
        if ((int)$id_cart) {
            $sql = '
                SELECT      id_order
                FROM        `'._DB_PREFIX_.'orders`
                WHERE       id_cart = '.(int)$id_cart;
            return Db::getInstance()->getValue($sql);
        }
    }
}
