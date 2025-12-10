<?php
/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */

/**
 * Library for interaction with internal Presta data
 */
class RuchLib
{
    public static $db_arr = array('parcels', 'additional');
    public static $db_num = array('id_order', 'id_user', 'api_id', 'ins', 'cod');

    /**
     * Create my tables during install
     */
    public static function createTables()
    {
        $sql = '
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ruch_label' . '` (
            `id_label` int(11) NOT NULL AUTO_INCREMENT,
            `id_order` int(11) NOT NULL,
            `date_ins` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            `id_user` int(11) NOT NULL,
            `api_parcel_id` varchar(80),
            `api_id` int(11),
            `nad_naz` varchar(120),
            `nad_adr1` varchar(50),
            `nad_adr2` varchar(50),
            `nad_miasto` varchar(120),
            `nad_kod` varchar(8),
            `nad_kraj` varchar(2),
            `nad_osoba_imie` varchar(50),
            `nad_osoba_nazw` varchar(50),
            `nad_tel` varchar(15),
            `nad_mail` varchar(80),
            `odb_naz` varchar(120),
            `odb_adr1` varchar(50),
            `odb_adr2` varchar(50),
            `odb_miasto` varchar(120),
            `odb_kod` varchar(8),
            `odb_kraj` varchar(2),
            `odb_osoba_imie` varchar(50),
            `odb_osoba_nazw` varchar(50),
            `odb_tel` varchar(15),
            `odb_mail` varchar(80),
            `parcels` varchar(250),
            `additional` varchar(250),
            `ins` decimal(20,6),
            `cod` decimal(20,6),
            `descr` varchar(250),
            `target_point` varchar(20),
            `last_error` text,
            PRIMARY KEY (`id_label`),
            UNIQUE KEY `id_order` (`id_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        if (!Db::getInstance()->execute($sql)) {
            self::log('Blad tworzenia tabeli: ' . Db::getInstance()->getMsgError());
            return false;
        }
        $sql = '
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ruch_cart_point' . '` (
            `id_cl` int(11) NOT NULL AUTO_INCREMENT,
            `id_cart` int(11) NOT NULL,
            `date_ins` datetime NOT NULL,
            `point` varchar(20),
            PRIMARY KEY (`id_cl`),
            UNIQUE KEY `id_cart` (`id_cart`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        if (!Db::getInstance()->execute($sql)) {
            self::log('Blad tworzenia tabeli: ' . Db::getInstance()->getMsgError());
            return false;
        }
        return true;
    }

    /**
     * Get my config data for given service
     */
    public static function getCarrierConf($sym)
    {
        $a = unserialize(Configuration::getGlobalValue('RUCH_SERV'));
        if (isset($a[$sym])) {
            return $a[$sym];
        }
        return array();
    }

    /**
     * Set my config data for given service
     */
    public static function setCarrierConf($sym, $cc)
    {
        self::log('setCarrierConf sym=' . $sym . ' id=' . $cc['id'] .
        (isset($cc['id_cod']) ? ' id_cod=' . $cc['id_cod'] : ''));
        $a = unserialize(Configuration::getGlobalValue('RUCH_SERV'));
        $a[$sym] = $cc;
        return Configuration::updateGlobalValue('RUCH_SERV', serialize($a));
    }

    /**
     * Delete my config data for given service
     */
    public static function delCarrierConf($sym)
    {
        self::log('delCarrierConf sym=' . $sym);
        $a = unserialize(Configuration::getGlobalValue('RUCH_SERV'));
        unset($a[$sym]);
        return Configuration::updateGlobalValue('RUCH_SERV', serialize($a));
    }

    /**
     * Create my config data and Presta carrier object for given service
     */
    public static function createCarrier($sym, $name, $desc, $cod)
    {
        self::log('createCarrier sym=' . $sym . ' n=' . $name . ' cod=' . ($cod?1:0));
        $cc = self::getCarrierConf($sym);
        $id = null;
        if (!empty($cc)) {
            $id = (int) ($cod ? (isset($cc['id_cod']) ? $cc['id_cod'] : null) : (isset($cc['id']) ? $cc['id'] : null));
        }
        $id = self::createCarrierPresta($id, $desc, $cod);
        if ($id === false) {
            return false;
        }

        $plik = _PS_SHIP_IMG_DIR_.'/' . $id . '.jpg';
        if (!file_exists($plik)) {
            copy(_PS_MODULE_DIR_ . '/ruch/logo.png', $plik);
        }

        if ($cod) {
            $cc['id_cod'] = $id;
        } else {
            $cc['id'] = $id;
        }
        $cc['sym'] = $sym;
        $cc['name'] = $name;
        $cc['desc'] = $desc;
        return self::setCarrierConf($sym, $cc);
    }

    /**
     * Create Presta carrier object for given service
     */
    public static function createCarrierPresta($id, $desc, $cod)
    {
        //$carrier = Carrier::getCarrierByReference( (int)$id );
        $carrier = new Carrier((int)$id);
        if ($id && Validate::isLoadedObject($carrier)) {
            if (!$carrier->deleted) {
                return $id;
            } else {
                $carrier->deleted = 0;
                if ($carrier->save()) {
                    return $id;
                } else {
                    return false;
                }
            }
        }
        $name = $cod ? Tools::substr($desc, 0, 53) . ' - pobranie' : Tools::substr($desc, 0, 64);
        $carrier = new Carrier();
        $carrier->name = $name;
        $carrier->active = 1;
        $carrier->is_free = 0;
        $carrier->shipping_handling = 1;
        $carrier->shipping_external = 1;
        $carrier->shipping_method = 1;
        $carrier->max_width = RUCH_MAXL;
        $carrier->max_height = RUCH_MAXH;
        $carrier->max_depth = RUCH_MAXD;
        $carrier->max_weight = RUCH_MAXW;
        $carrier->grade = 0;
        $carrier->is_module = 1;
        $carrier->need_range = 1;
        $carrier->range_behavior = 1;
        $carrier->external_module_name = 'ruch';
        $carrier->url = RUCH_TRACKURL;

        $delay = array();
        foreach (Language::getLanguages(false) as $language) {
            $delay [$language ['id_lang']] = $name;
        }
        $carrier->delay = $delay;

        if (! $carrier->save()) {
            return false;
        }

        $range_obj = $carrier->getRangeObject();
        $range_obj->id_carrier = ( int ) $carrier->id;
        $range_obj->delimiter1 = 0;
        $range_obj->delimiter2 = RUCH_MAXW;
        if (!$range_obj->save()) {
            return false;
        }

        if (!self::assignGroups($carrier)) {
            return false;
        }

        if (!self::createZone($carrier->id)) {
            return false;
        }

        if (!self::createDelivery($carrier->id, $range_obj->id)) {
            return false;
        }

        self::log('createCarrierPresta id=' . $carrier->id);
        return $carrier->id;
    }

    /**
     * Add carrier to Presta groups
     */
    public static function assignGroups($carrier)
    {
        $groups = array();
        foreach (Group::getGroups((int)Context::getContext()->language->id) as $group) {
            $groups [] = $group ['id_group'];
        }

        if (version_compare(_PS_VERSION_, '1.5.5', '<')) {
            if (!self::setGroupsOld((int)$carrier->id, $groups)) {
                return false;
            }
        } else {
            if (!$carrier->setGroups($groups)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Add carrier to Presta group for old Presta
     */
    public static function setGroupsOld($id_carrier, $groups)
    {
        foreach ($groups as $id_group) {
            if (!Db::getInstance()->execute('
                INSERT INTO `'. _DB_PREFIX_. 'carrier_group` (`id_carrier`, `id_group`)
                VALUES ("'. (int) $id_carrier. '", "'. (int) $id_group. '")
                 ')) {
                    return false;
            }
        }
        return true;
    }

    /**
     * Add carrier to Presta first zone
     */
    public static function createZone($id_carrier)
    {
        return DB::getInstance()->Execute('
            INSERT INTO `' . _DB_PREFIX_ . 'carrier_zone`
            (`id_carrier`, `id_zone`)
            VALUES
            ("' . (int) $id_carrier . '", "1")');
    }

    /**
     * Create default range object
     */
    public static function createDelivery($id_carrier, $id_range)
    {
        return DB::getInstance()->Execute('
            INSERT INTO `' . _DB_PREFIX_ . 'delivery`
            (`id_carrier`, `id_range_weight`, `id_zone`, `price`)
            VALUES
            ("' . (int)$id_carrier . '", "' . (int)$id_range . '", "1", "10")');
    }

    /**
     * Save point selected during checkout to my data
     */
    public static function setPoint($pkt, $typ)
    {
        if (!$pkt || strpos($pkt, '-') === false) {
            return '';
        }
        $tmp = explode('-', $pkt);
        $point = $tmp[1];
        if (!isset(Context::getContext()->cart)) {
            return '';
        }
        $cart = Context::getContext()->cart->id;
        $c = Db::getInstance()->getValue('
            SELECT count(*)
            FROM `' . _DB_PREFIX_ . 'ruch_cart_point`
            WHERE `id_cart`=' . $cart);
        if ($c == 0) {
            return DB::getInstance()->Execute('
               INSERT INTO `' . _DB_PREFIX_ . 'ruch_cart_point`
                (`id_cart`, `date_ins`, `point`)
                VALUES
                ("' . $cart . '", now(), "' . $typ . ':' . $point . '")');
        } else {
            return DB::getInstance()->Execute('
               UPDATE `' . _DB_PREFIX_ . 'ruch_cart_point`
                SET `point`="' . $typ . ':' . $point . '"
                WHERE `id_cart`=' . $cart);
        }
    }
    
    /**
     * Read point selected during checkout from my data
     */
    public static function getPoint($cart)
    {
        $c = Db::getInstance()->getValue('
            SELECT point
            FROM `' . _DB_PREFIX_ . 'ruch_cart_point`
            WHERE `id_cart`=' . $cart);
        if (!$c) {
            $c = '';
        }
        return $c;
    }
    
    /**
     * Add my menu in admin panel
     */
    public static function installTab()
    {
        $id_parent = (int)Tab::getIdFromClassName('AdminParentShipping');
        if (!$id_parent) {
            return false;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminRuch';
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name [$lang ['id_lang']] = 'Przesyłki Orlen Paczka';
        }

        $tab->id_parent = $id_parent;
        $tab->module = 'Ruch';
        return $tab->add();
    }

    /*
     * Not used
     */
    public static function getExt($t)
    {
        if ($t == 'application/zip') {
            return 'zip';
        }
        if ($t == 'application/pdf') {
            return 'pdf';
        }
        return 'bin';
    }

    /**
     * Create default config data during install
     */
    public static function createConfig()
    {
        if (!Configuration::updateGlobalValue('RUCH_SERV', serialize(array()))) {
            return false;
        }
        if (!self::createCarrier('pwr', 'ORLEN Paczka', 'ORLEN Paczka', false)) {
            return false;
        }
        if (!self::createCarrier('pwr', 'ORLEN Paczka', 'ORLEN Paczka', true)) {
            return false;
        }

        if (Configuration::getGlobalValue('RUCH_HASCONFIG')) {
            return true;
        }
        if (!Configuration::updateGlobalValue('RUCH_PRICE', 10)) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_PRICE_COD', 12)) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_SERVARCH', serialize(array()))) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_API_ID', 'demo')) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_API_PASS', 'demo')) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_API_URL', RUCH_TESTURL)) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_DEFI', 0)) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_DEFW', RUCH_DEFW)) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_DEFL', RUCH_DEFL)) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_DEFD', RUCH_DEFD)) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_DEFH', RUCH_DEFH)) {
            return false;
        }
        if (!Configuration::updateGlobalValue('RUCH_HASCONFIG', 1)) {
            return false;
        }
        
        return true;
    }

    /**
     * Find serice symboll by Presta carrier id
     */
    public static function getSymById($id)
    {
        $a = unserialize(Configuration::getGlobalValue('RUCH_SERV'));
        foreach ($a as $k => $w) {
            if (isset($w['id']) && ($w['id'] == $id)) {
                return array($k, false);
            }
            if (isset($w['id_cod']) && ($w['id_cod'] == $id)) {
                return array($k, true);
            }
        }
        $a = unserialize(Configuration::getGlobalValue('RUCH_SERVARCH'));
        if (isset($a[$id]['sym'])) {
            return array($a[$id]['sym'], $a[$id]['cod']);
        }
        return null;
    }

    /**
     * Get all Presta carrier ids of my services
     */
    public static function getRuchServ()
    {
        $a = unserialize(Configuration::getGlobalValue('RUCH_SERV'));
        $ret = array();
        foreach ($a as $w) {
            $ret[] = $w['id'];
            $ret[] = $w['id_cod'];
        }
        return $ret;
    }
    
    /**
     * Get all Presta carrier ids of my COD services
     */
    public static function getRuchCodServ()
    {
        $a = unserialize(Configuration::getGlobalValue('RUCH_SERV'));
        $ret = array();
        foreach ($a as $w) {
            $ret[] = $w['id_cod'];
        }
        return $ret;
    }
    
    /**
     * Get all Presta carrier ids of my non-COD services
     */
    public static function getRuchNotodServ()
    {
        $a = unserialize(Configuration::getGlobalValue('RUCH_SERV'));
        $ret = array();
        foreach ($a as $w) {
            $ret[] = $w['id'];
        }
        return $ret;
    }
    
    /**
     * Delete my config and Presta object of all carriers
     */
    public static function deleteAllCarriers()
    {
        $a = unserialize(Configuration::getGlobalValue('RUCH_SERV'));
        foreach ($a as $cc) {
            if (!self::deleteCarrier($cc)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Delete my config and Presta object of given carrier
     */    
    public static function deleteCarrier($cc)
    {
        self::log('deleteCarrier sym=' . $cc['sym']);
        if (!self::deleteCarrierPresta($cc['id'])) {
            return false;
        }
        if (isset($cc['id_cod']) && !self::deleteCarrierPresta($cc['id_cod'])) {
            return false;
        }
        
        return self::delCarrierConf($cc['sym']);
    }

    /**
     * Delete Presta object of given carrier
     */
    public static function deleteCarrierPresta($id)
    {
        if (!$id) {
            return true;
        }
        $carrier = new Carrier((int)$id);
        if (!Validate::isLoadedObject($carrier)) {
            return true;
        }
        if ($carrier->deleted) {
            return true;
        }

        self::log('deleteCarrierPresta id=' . $id);
        $carrier->deleted = 1;
        return (bool)$carrier->save();
    }

    /**
     * Remove zone for given carrier
     */
    public static function removeZone($id_carrier)
    {
        return DB::getInstance()->Execute('
            DELETE FROM `' . _DB_PREFIX_ . 'carrier_zone`
            WHERE `id_carrier` = "' . (int)$id_carrier . '"
            ');
    }

    /**
     * Delete my menu in admin panel
     */
    public static function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminRuch');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        // else return false;
        return true;
    }

    /**
     * Compute COD amount for order
     */
    public static function getCOD($order)
    {
        $pobranie = $order->total_products_wt + $order->total_shipping_tax_incl +
        $order->total_wrapping_tax_incl - $order->total_discounts;
        return $pobranie;
    }

    /**
     * Test if is COD module installed
     */
    public static function testCOD()
    {
        $cod = false;
        $naz = array();
        if ($pm = Module::getPaymentModules()) {
            foreach ($pm as $mod) {
                $naz [] = $mod ['name'];
            }
        }
        if ($pm = Module::getModulesInstalled()) {
            foreach ($pm as $mod) {
                if ($mod ['name'] == 'cashondelivery') {
                    $cod = true;
                }
                if ($mod ['name'] == 'ps_cashondelivery') {
                    $cod = true;
                }
                if ($mod ['name'] == 'codpro') {
                    $cod = true;
                }
            }
        }
        if ($cod) {
            return '';
        } else {
            return join(', ', $naz);
        }
    }

    /**
     * Generate default remarks for order
     */
    public static function genUwagi($order)
    {
        $ret = self::getConf('RUCH_REQ_REF', $order) ? 'Zamówienie ' . $order->reference : '';
    
        if (RuchLib::getConf('RUCH_CONT', $order)) {
            $oprod = array();
            $aprod = $order->getProducts();
            foreach ($aprod as $prod) {
                self::log(print_r($prod, true));
                $oprod [] = $prod['product_name'] . ($prod['product_quantity'] != 1 ? ' (' .
                $prod['product_quantity'] . ' szt)' : '');
            }
            $ret .= ($ret ? ' / ' : '') . implode(', ', $oprod);
        }
    
        return $ret;
    }
    
    /**
     * Get label data from my table for given order
     */
    public static function getLabelRow($id_order)
    {
        $r = Db::getInstance()->getRow('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'ruch_label`
            WHERE `id_order`=' . (int)$id_order);
        if (!$r) {
            return null;
        }
        $r['parcels'] = unserialize($r['parcels']);
        $r['additional'] = unserialize($r['additional']);
        $r['ins'] = 1 * $r['ins'];
        $r['cod'] = 1 * $r['cod'];
    
        return $r;
    }
    
    /**
     * Get parcel id for given order
     */
    public static function getLabelNum($id_label)
    {
        $r = Db::getInstance()->getRow('
            SELECT api_parcel_id, id_order
            FROM `' . _DB_PREFIX_ . 'ruch_label`
            WHERE `id_label`=' . (int)$id_label);
        if (!$r) {
            return null;
        }
    
        return array('api_parcel_id' => $r['api_parcel_id'], 'id_order' => $r['id_order']);
    }
    
    /**
     * Create my default data for new label
     */
    public static function createLabelRow($order)
    {
        if (! DB::getInstance()->Execute('
            INSERT INTO `' . _DB_PREFIX_ . 'ruch_label`
            (`id_order`, `date_ins`, `date_upd`, `id_user`)
            VALUES
            ("' . (int)$order->id . '",
               now(),
               now(),
            0
            )')) {
            Ruch::$errors[] = "Order #" . $order->id . ": " . 'Database error!';
            self::log(Db::getInstance()->getMsgError());
            return false;
        }
        $r = self::getLabelRow($order->id);
        $r = self::setLabelDefaults($r, $order);
        if (!self::updateLabelRow($r)) {
            return false;
        }
        return $r;
    }

    /**
     * Update my label data
     */
    public static function updateLabelRow($r)
    {
        $p = array();
        foreach ($r as $k => $w) {
            if (($k != 'id_label') && ($k != 'date_upd')) {
                $p[] = $k . "=" . (!in_array($k, self::$db_arr) ? (in_array($k, self::$db_num) ? ($w ? $w : 0) : "'" .
                self::escapeString($w) . "'") : "'" . self::escapeString(serialize($w)) . "'");
            }
        }

        $p[] = 'date_upd=now()';
        $sql = '
            UPDATE `' . _DB_PREFIX_ . 'ruch_label` SET
            ' . join(', ', $p). '
            WHERE id_label=' . $r['id_label'];
        self::log('update sql=' . $sql);
        if (!DB::getInstance()->Execute($sql)) {
            Ruch::$errors[] = 'Database error!';
            self::log(Db::getInstance()->getMsgError());
            return false;
        }
        return true;
    }
    
    /**
     * Generate defalt values for new label
     */
    public static function genDefaults($order)
    {
        $waga = self::getConf('RUCH_DEFW', $order);
        $wys = self::getConf('RUCH_DEFH', $order);
        $szer = self::getConf('RUCH_DEFD', $order);
        $dlug = self::getConf('RUCH_DEFL', $order);
        if ($waga <= 0) {
            $waga = RUCH_DEFW;
        }
        if ($wys <= 0) {
            $wys = RUCH_DEFH;
        }
        if ($szer <= 0) {
            $szer = RUCH_DEFD;
        }
        if ($dlug <= 0) {
            $dlug = RUCH_DEFL;
        }
        $nst = self::getConf('RUCH_NST', $order);
        if ($nst == null) {
            $nst = "0";
        }
        return array('waga' => $waga, 'wys' => $wys, 'szer' => $szer, 'dlug' => $dlug, 'nst' => $nst, 'tpl' => 'A');
    }

    /**
     * Create label using API
     */
    public static function createLabelDef($id_order)
    {
        $id = ( int ) $id_order;
        if ($id == 0) {
            Ruch::$errors[] = 'Brak numeru zamówienia';
            return false;
        }
        $order = new Order($id);
        if ($order == null) {
            Ruch::$errors[] = 'Brak zamówienia';
            return false;
        }
        $r = RuchLib::getLabelRow($id_order);
        if ($r == null) {
            $r = RuchLib::createLabelRow($order);
        }
        $r = self::setLabelNad($r, $order);
        self::updateLabelRow($r);
        $r = array_merge($r, ['order_reference' => $order->reference]);
        return RuchApi::createLabel($r);
    }

    /**
     * Create label using API - called from ajax
     */
    public static function createLabelAjax($r)
    {
        $order = new Order($r['id_order']);
        $r = self::setLabelNad($r, $order);
        self::updateLabelRow($r);
        $r = array_merge($r, ['order_reference' => $order->reference]);
        return RuchApi::createLabel($r);
    }

    /**
     * Set origin data for label
     */
    public static function setLabelNad($r, $order)
    {
        $r['nad_naz'] = self::getConf('RUCH_NAD_NAZ', $order);
        $r['nad_adr1'] = self::getConf('RUCH_NAD_ADR1', $order);
        $r['nad_adr2'] = self::getConf('RUCH_NAD_ADR2', $order);
        $r['nad_miasto'] = self::getConf('RUCH_NAD_MIASTO', $order);
        $r['nad_kod'] = self::getConf('RUCH_NAD_KOD', $order);
        $r['nad_kraj'] = self::getConf('RUCH_NAD_KRAJ', $order);
        $r['nad_osoba_imie'] = self::getConf('RUCH_NAD_OSOBA1', $order);
        $r['nad_osoba_nazw'] = self::getConf('RUCH_NAD_OSOBA2', $order);
        $r['nad_tel'] = self::getConf('RUCH_NAD_TEL', $order);
        $r['nad_mail'] = self::getConf('RUCH_NAD_EMAIL', $order);
        return $r;
    }

    /**
     * Set defaults for new label
     */
    public static function setLabelDefaults($r, $order)
    {
        list($serv, $cod) = self::getSymById($order->id_carrier);
        $address_r = new Address($order->id_address_delivery);
        $order_carrier = new OrderCarrier(self::getIdOrderCarrier($order));
        $pobranie = 0;
        if ($serv) {
        }
        if ($cod) {
            $pobranie = self::getCOD($order);
        }
        $aprod = $order->getProducts();
        $customer = new Customer((int)$order->id_customer);
        
        $waga = $order_carrier->weight;
        $wys = 0;
        $szer = 0;
        $dlug = 0;
        if (count($aprod) == 1) {
            $prod = array_pop($aprod);
            $wys = $prod['height'];
            $szer = $prod['depth'];
            $dlug = $prod['width'];
        }
        $d = self::genDefaults($order);
        if ($waga <= 0) {
            $waga = $d['waga'];
        }
        if ($wys <= 0) {
            $wys = $d['wys'];
        }
        if ($szer <= 0) {
            $szer = $d['szer'];
        }
        if ($dlug <= 0) {
            $dlug = $d['dlug'];
        }
        $ubez = self::getConf('RUCH_DEFI', $order);
        if ($ubez < $pobranie) {
            $ubez = $pobranie;
        }
        $nst = $d['nst'];
        $mail = self::getConf('RUCH_REQ_MAIL', $order);
        $sms = self::getConf('RUCH_REQ_SMS', $order);
        $co = new Country($address_r->id_country);
        
        $oa1 = $address_r->address1;
        $oa2 = $address_r->address2;
        if (trim($oa2) == '') {
            $tmp = explode(' ', $oa1);
            $oa2 = array_pop($tmp);
            $oa1 = join(' ', $tmp);
        }
         
        $tel = trim($address_r->phone_mobile) != '' ? trim($address_r->phone_mobile) : trim($address_r->phone);
        if (Tools::substr($tel, 0, 3) == '+48') {
            $tel = trim(Tools::substr($tel, 3));
        }
        if ((Tools::substr($tel, 0, 2) == '48') && strlen($tel) == 11) {
            $tel = Tools::substr($tel, 2);
        }
        
        $r = self::setLabelNad($r, $order);
        $r['odb_naz'] = $address_r->company;
        $r['odb_adr1'] = $oa1;
        $r['odb_adr2'] = $oa2;
        $r['odb_miasto'] = $address_r->city;
        $r['odb_kod'] = trim($address_r->postcode);
        $r['odb_kraj'] = $co->iso_code;
        $r['odb_osoba_imie'] = $address_r->firstname;
        $r['odb_osoba_nazw'] = $address_r->lastname;
        $r['odb_tel'] = $tel;
        $r['odb_mail'] = $customer->email;
        $r['api_id'] = 0;
        $r['parcels'] = array();
        $r['additional'] = array();
        $r['ins'] = $ubez;
        $r['cod'] = $pobranie;
        //$r['descr'] = self::genUwagi ( $order );
        $r['target_point'] = self::getPoint($order->id_cart);
        //$temp = '';
        //$s = unserialize(Configuration::getGlobalValue('RUCH_SERVTEMP'));
        $r['parcels'][] = array(
           'waga' => $waga,
           'dlug' => $dlug,
           'wys' => $wys,
           'szer' => $szer,
           'nst' => $nst,
           'tpl' => 'S'
        );

        if ($sms) {
            $r['additional'][] = 'sms';
        }
        if ($mail) {
            $r['additional'][] = 'email';
        }

        return $r;
    }

    /**
     * Get carrier id from order
     */
    public static function getIdOrderCarrier($order)
    {
        if (version_compare(_PS_VERSION_, '1.5.5', '<')) {
            return (int)Db::getInstance()->getValue('
                SELECT `id_order_carrier`
                FROM `' . _DB_PREFIX_ . 'order_carrier`
                WHERE `id_order` = ' . (int)$order->id);
        } else {
            return (int)$order->getIdOrderCarrier();
        }
    }

    /**
     * Get service symbols and names
     */
    public static function getServ()
    {
        $tserv = unserialize(Configuration::getGlobalValue('RUCH_SERV'));
        if (!is_array($tserv)) {
            return '';
        }
        $a = array();
        foreach ($tserv as $s) {
            $a[$s['sym']] = $s['desc'];
        }
        return $a;
    }
        
    /**
     * Get available additional services
     */
    public static function getAdds()
    {
        $a = unserialize(Configuration::getGlobalValue('RUCH_ADDS'));
        $a['nst'] = true;
        return $a;
    }

    /**
     * Helper - get config for shop of given order
     */
    public static function getConf($n, $order)
    {
        return Configuration::get($n, null, null, $order->id_shop);
    }

    /**
     * Helper - escape backslashes
     */
    public static function escapeString($s)
    {
        $s = str_replace("'", "\\'", $s);
        $s = str_replace('\\', '\\\\', $s);
        return $s;
    }

    /**
     * Log to file
     */
    public static function log($txt)
    {
        if (RUCH_ISDEBUG) {
            error_log('presta-ruch ' . $txt);
        }
    }

    /**
     * Is soap php extension installed
     */
    static function _issoap() {
        return extension_loaded('soap');
    }

    public static function iscurl()
    {
        return function_exists('curl_version');
    }
    
    /**
     * Test if configuration is complete
     */
    public static function testConfig()
    {
        if (Configuration::get('RUCH_NAD_NAZ') == '') {
            return false;
        }
        if (Configuration::get('RUCH_NAD_ADR1') == '') {
            return false;
        }
        if (Configuration::get('RUCH_NAD_MIASTO') == '') {
            return false;
        }
        if (Configuration::get('RUCH_NAD_KOD') == '') {
            return false;
        }
        if (Configuration::get('RUCH_NAD_KRAJ') == '') {
            return false;
        }
        if (Configuration::get('RUCH_NAD_OSOBA1') == '') {
            return false;
        }
        if (Configuration::get('RUCH_NAD_OSOBA2') == '') {
            return false;
        }
        if (Configuration::get('RUCH_NAD_TEL') == '') {
            return false;
        }
        if (Configuration::get('RUCH_NAD_EMAIL') == '') {
            return false;
        }
        return true;
    }
}
