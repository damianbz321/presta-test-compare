<?php
/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */

/**
 * Ruch API helper class
 */
class RuchApi
{
    private static $fn = 'pwr_list.json';
    private static $metoda = 95;
    private static $metoda_mini = 122;
    private static $metoda_apm = 127;
    private static $pktWaznosc = 7200;
    private static $edni = array('Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su', 'x');
    private static $pdni = array('pon', 'wt', 'śr', 'czw', 'pt', 'sob', 'niedz', 'x');
    
    /**
     * Test API connection
     */
    static function test($json, $wcache) {
        if(!RuchLib::_issoap())
            return self::$module_instance->l('Brak modułu PHP-soap - połączenie nie będzie działać');
            
        $pass = $json['pass'] ? $json['pass'] : Configuration::get('RUCH_API_PASS');
        $params = array(
            "token" => array(
                "UserName" => $json['user'],
                "Password" => $pass
            ),
            "request" => array(
                "PackageNo" =>  array(
                    "string" => '0'
                )
            )
        );
        $ret = false;
        try {
            $ret = self::callWs('GetLabel', $params, $json['url'], $wcache);
        } catch (Exception $e) {
            RuchLib::log('GetLabel err ' . $e->getMessage());
            Ruch::$errors[] = $e->getMessage();
        }
        
        if($ret === false) {
            $err = Ruch::$errors;
            Ruch::$errors = null;
            return $err;
        }
        RuchLib::log(print_r($ret, true));
        
        if(!isset($ret->GetLabelResult->responseCode) || ($ret->GetLabelResult->responseCode != '1000')) return 'Błąd API: ' . $ret->GetLabelResult->responseDescription;
        return 'OK';
    }

    static function testCurl($json) {
        if(!RuchLib::iscurl())
            return self::$module_instance->l('Brak modułu PHP-curl - test nie będzie działać');
            
        if($json['url'] == null) $url = trim(Configuration::get('RUCH_API_URL'));
        else $url = $json['url'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
        $output = curl_exec($ch);

        if($output === false) {
            $err = curl_error($ch);
            curl_close($ch);
            return $err;
        }
        curl_close($ch);
        return 'OK';
    }
    
    public static function createLabel($e)
    {
        RuchLib::log('createLabel json=' . print_r($e, true));
        $orderReference = $e['order_reference'];
        $d = $e;
        unset($d["order_reference"]);

        $pobranie = $d['cod'];
        $format = Configuration::get('RUCH_FORMAT');
        
        $point = $d['target_point'];
        $tmp = explode(':', $point);
        $typ = 'P';
        $serv = 'M';
        if (count($tmp) > 1) {
            $typ = $tmp[0];
            $point = $tmp[1];
            if (count($tmp) > 2) {
                $serv = $tmp[2];
            }
        }
        
        $metoda = self::$metoda_mini;
        if ($typ != 'A') {
            if ($serv == 'S') {
                $metoda = self::$metoda;
            }
            //$mmax = Configuration::get ( 'RUCH_WMINI' ) != '' ? 1 * Configuration::get ( 'RUCH_WMINI' ) : 0;
            //foreach ($d['parcels'] as $t) if($t['waga'] > $mmax) $metoda = self::$metoda;
        } else {
            $metoda = self::$metoda_apm;
        }
        
        $params = array(
            "token" => array(
                "UserName" => Configuration::get('RUCH_API_ID'),
                "Password" => Configuration::get('RUCH_API_PASS')
            ),
            "ShipmentRequest" => array(
                "ServiceId" => $metoda,
                "ShipFrom" => array(
                        "Name" => $d['nad_naz'],
                        "Street" => $d['nad_adr1'] . ' ' . $d['nad_adr2'],
                        "HouseNumber" => '.',
                        "Local" => '.',
                        "City" => $d['nad_miasto'],
                        "PostCode" => $d['nad_kod'],
                        "CountryCode" => $d['nad_kraj'],
                        "PersonName" => $d['nad_osoba_imie'],
                        "PersonSurname" => $d['nad_osoba_nazw'],
                        "Contact" => $d['nad_tel'],
                        "Email" => $d['nad_mail'],
                ),
                "ShipTo" => array(
                        "Name" => $d['odb_naz'],
                        "Street" => $d['odb_adr1'] . ' ' . $d['odb_adr2'],
                        "HouseNumber" => '.',
                        "Local" => '.',
                        "City" => $d['odb_miasto'],
                        "PostCode" => $d['odb_kod'],
                        "CountryCode" => 'PL',
                        "PersonName" => $d['odb_osoba_imie'],
                        "PersonSurname" => $d['odb_osoba_nazw'],
                        "Contact" => $d['odb_tel'],
                        "Email" => $d['odb_mail'],
                ),
                "ParcelLocker" => array(
                        "PointId" => $point
                ),
                "Parcels" => array(),
                "InsuranceAmount" => ($pobranie != 0 ? $pobranie : Configuration::get('RUCH_DEFI')),
                "MPK" => "",
                "PackageContentDescr" => "",
                "rabateCoupon" => 0,
                "LabelFormat" => ($format == 'S') || ($format == '') ? 'PDF' : 'PDF',
                "ReferenceNumber" => $orderReference,
                "AdditionalServices" => array()
            )
        );
        if ($pobranie) {
            $params['ShipmentRequest']['COD'] = array(
                "Amount" => $pobranie,
                "RetAccountNo" => 0
            );
        }
        foreach ($d['additional'] as $add) {
            if ($add == 'email') {
                $params['ShipmentRequest']['AdditionalServices']['AdditionalService'][] = array('Code' => 'EMAIL');
            }
            if ($add == 'sms') {
                $params['ShipmentRequest']['AdditionalServices']['AdditionalService'][] = array('Code' => 'SMS');
            }
        }
        foreach ($d['parcels'] as $t) {
            if (!isset($t['tpl'])) {
                $t['tpl'] = 'S';
            }
            $params['ShipmentRequest']['Parcels']['Parcel'][] = ($typ == 'A' ?
            array(
                "Type" => $t['tpl'],
                "Weight" => $t['waga'],
                "IsNST" => $t['nst']
            ) :
            array(
                "Type" => 'Package',
                "Weight" => $t['waga'],
                "D" => $t['dlug'],
                "W" => $t['wys'],
                "S" => $t['szer'],
                "IsNST" => $t['nst']
            ));
        }
        
        try {
            $response = self::callWs("CreateShipment", $params);
        } catch (Exception $e) {
            RuchLib::log('CreateShipment err1 ' . $e->getMessage());
            Ruch::$errors[] = $e->getMessage();
            return false;
        }
        if (!isset($response->CreateShipmentResult->responseDescription) ||
        ($response->CreateShipmentResult->responseDescription != 'Success')) {
            RuchLib::log('CreateShipment err2 ' . print_r($response, true));
            Ruch::$errors[] = '[' . $response->CreateShipmentResult->responseCode . '] ' .
            $response->CreateShipmentResult->responseDescription;
            return false;
        }
        if (is_array($response->CreateShipmentResult->ParcelData->Label)) {
            foreach ($response->CreateShipmentResult->ParcelData->Label as $label) {
                $label->MimeData = "[" . Tools::strlen($label->MimeData) . "]";
            }
            RuchLib::log(print_r($response, true));
        } else {
            $shippingLabelContent = $response->CreateShipmentResult->ParcelData->Label->MimeData;
            $response->CreateShipmentResult->ParcelData->Label->MimeData = '[' .
            Tools::strlen($shippingLabelContent) . ']';
            RuchLib::log('CreateShipment r ' . print_r($response, true));
        }

        $d['api_parcel_id'] = $response->CreateShipmentResult->PackageNo;
        RuchLib::updateLabelRow($d);
        
        $order = new Order($d['id_order']);
        $order_carrier = new OrderCarrier($order->getIdOrderCarrier());
        $order->shipping_number = $d['api_parcel_id'];
        $order->update();
        $order_carrier->tracking_number = $d['api_parcel_id'];
        $order_carrier->update();
                
        return $d;
    }
    
    /**
     * Get label pdf for given parcels
     */
    public static function getLabelPdf($ids, $order = false)
    {
        $a = explode(',', $ids);
        $oids = array();
        if (count($a) > 100) {
            $a = array_slice($a, 0, 100);
        }
        $el = array();
        foreach ($a as $id) {
            $order ? $ar = RuchLib::getLabelRow($id) : $ar = RuchLib::getLabelNum($id);
            if ($ar && isset($ar['api_parcel_id']) && $ar['api_parcel_id']) {
                $el[] = $ar['api_parcel_id'];
                $oids[] = $ar['id_order'];
            }
        }
        if (!$el) {
            return null;
        }

        $params = array(
            "token" => array(
                "UserName" => Configuration::getGlobalValue('RUCH_API_ID'),
                "Password" => Configuration::getGlobalValue('RUCH_API_PASS')
            ),
            "request" => array(
                "PackageNo" =>  array(
                    "string" => array()
                )
            )
        );
        foreach ($el as $e) {
            $params['request']['PackageNo']['string'][] = $e;
        }

        $response = self::callWs("GetLabel", $params);
        if (!isset($response->GetLabelResult->responseDescription) ||
        ($response->GetLabelResult->responseDescription != 'Success')) {
            RuchLib::log(print_r($response, true));
            return false;
        }
        if (is_array($response->GetLabelResult->LabelData->Label)) {
            $pdfs = array();
            foreach ($response->GetLabelResult->LabelData->Label as $label) {
                $pdfs[] = $label->MimeData;
                $label->MimeData = "[" . Tools::strlen($label->MimeData) . "]"; //Aby w logu nie było śmieci
            }
            RuchLib::log(print_r($response, true));
            if (!class_exists('Zend_Pdf')) {
                include_once('Zend/Pdf.php');
            }
            $pdf = new \Zend_Pdf();
            foreach ($pdfs as $p) {
                $pdf1 = \Zend_Pdf::parse($p);
                foreach ($pdf1->pages as $page) {
                    $pdf->pages[] = clone $page;
                }
            }
    
            $shippingLabelContent = $pdf->render();
        } else {
            $shippingLabelContent = $response->GetLabelResult->LabelData->Label->MimeData;
            $response->GetLabelResult->LabelData->Label->MimeData = "[" .
            Tools::strlen($shippingLabelContent) . "]"; //Aby w logu nie było śmieci
            RuchLib::log(print_r($response, true));
        }
        return array('data' => $shippingLabelContent, 'num' => join(',', $oids));
    }
    
    /**
     * Not used
     */
    public static function getPts($cod)
    {
        $fn = dirname(__FILE__) . '/../tmp/' . self::$fn . $cod;

        $cart = Context::getContext()->cart;
        $llop = $cart->getDeliveryOptionList();
        $k = 0;
        
        foreach ($llop as $lop) {
            foreach ($lop as $op) {
                foreach ($op['carrier_list'] as $ca) {
                    $c = $ca['instance'];
                    if ($c->external_module_name == 'ruch') {
                        list($serv, $scod) = RuchLib::getSymById($c->id_reference);
                        if ($serv) {
                        }
                        if ($cod == $scod) {
                            $k = $ca['price_with_tax'];
                            break;
                        }
                    }
                }
            }
        }
        
        if (file_exists($fn) && (time() - filemtime($fn) < self::$pktWaznosc)) {
            $t = unserialize(Tools::file_get_contents($fn));
            for ($i = 0; $i < count($t['pts']); $i++) {
                $t['pts'][$i]['k'] = $k;
            }
            return $t;
        }

        if (file_exists($fn)) {
            unlink($fn);
        }
        $resp = self::getPtsApi($cod, dirname(__FILE__) . '/../tmp/zera.txt');
        if (!$resp) {
            return array('error' => 'Błąd pobierania listy punktów');
        }
        file_put_contents($fn, serialize($resp));
        for ($i = 0; $i < count($resp['pts']); $i++) {
            $resp['pts'][$i]['k'] = $k;
        }
        return $resp;
    }
    
    /**
     * Not used
     */
    public static function getPtsApi($cod, $naz)
    {
        $params = array(
            "token" => array(
                "UserName" => Configuration::getGlobalValue('RUCH_API_ID'),
                "Password" => Configuration::getGlobalValue('RUCH_API_PASS')
            )
        );
        $response = self::callWs("GetPointsList", $params);
        RuchLib::log('ws resp=' . print_r($response, true));
        if (!isset($response->GetPointsListResult->responseDescription) ||
        ($response->GetPointsListResult->responseDescription != 'Success')) {
            RuchLib::log(print_r($response, true));
            return false;
        }
        
        $a = array();
        $zera = array();
        foreach ($response->GetPointsListResult->Points->Point as $x) {
            if (($x->Latitude == 0) || ($x->Longitude == 0)) {
                $zera[] = $x;
            }
            if ($x->IsDeleted) {
                continue;
            }
            if (!isset($x->DestinationCode)) {
                continue;
            }
            if (!$x->Available) {
                continue;
            }
            if (($cod  == 0) || $x->CashOnDelivery == 1) {
                $e = array(
                    'la' => '' . $x->Latitude,
                    'lo' => '' . $x->Longitude,
                    't' => 'R',
                    'n' => '' . $x->PointType != 'Unknown' ? $x->PointType : '',
                    'id' => '' . $x->DestinationCode,
                    'a' => '' . (isset($x->AddressLine) && Tools::substr($x->AddressLine, 0, 2) == '. ' ?
                    ((isset($x->City) ? $x->City . ' ': '') . Tools::substr($x->AddressLine, 2)) :
                    (isset($x->AddressLine) ? $x->AddressLine : '') . (isset($x->City) ?
                    (isset($x->AddressLine) ? ', ' : '') . $x->City : '')),
                    'h' => '' . (isset($x->Location) ? $x->Location : ''),
                    'k' => 0
                );
                $o = array();
                $go = '';
                for ($i = 0; $i < 8; $i++) {
                    $ed = self::$edni[$i];
                    $pd = self::$pdni[$i];
                    $n1 = 'OpenHours' . $ed . 'Start';
                    $n2 = 'OpenHours' . $ed . 'End';
                    if (isset($x->$n1) && isset($x->$n2)) {
                        $g = $x->$n1 . '-' . $x->$n2;
                        if ($go != $g) {
                            if ($go != '') {
                                $d1 = '';
                                $d2 = '';
                                $o[] = $d1 . ($d1 != $d2 ? '-' . $d2 : '') . ' ' . $go;
                            }
                            $d1 = $pd;
                            $go = $g;
                        }
                        $d2 = $pd;
                    } else {
                        if ($go != '') {
                            $o[] = $d1 . ($d1 != $d2 ? '-' . $d2 : '') . ' ' . $go;
                        }
                        $go = '';
                    }
                }
                $ot = join(', ', $o);
                if ($ot == 'pon-niedz 00:00-24:00') {
                    $ot = '24/7';
                }
                $e['o'] = $ot;
                $a[] = $e;
            }
        }
        if ($zera) {
            file_put_contents($naz, print_r($zera, true));
        } elseif (file_exists($naz)) {
            unlink($naz);
        }
        return array('error' => 0, 'pts' => $a);
    }
    
    public static function callWs($func, $req, $url = null, $wcache = true)
    {
        if (!extension_loaded('soap')) {
            return 'SOAP extension not loaded';
        }
        RuchLib::log('ws ' . $func . ' req=' . print_r($req, true));
        if($url == null) $url = trim(Configuration::get('RUCH_API_URL'));
        RuchLib::log('url=' . $url);
        if($wcache) {
            $client = new \SoapClient($url);
        }
        else {
            ini_set('soap.wsdl_cache_enabled', '0');
            ini_set('soap.wsdl_cache_ttl', '0');
            $client = new \SoapClient($url, array('trace' => true, 'exceptions' => true));
        }
        $response = $client->__soapCall($func, array($req));
        return $response;
    }
    
    /**
     * Validate data from admin panel during label creating
     */
    public static function validateData($d)
    {
        if (( $d['cod'] != '' ) && ( $d['ins'] == '' )) {
            Ruch::$errors [] = 'Ubezpieczenie - pole wymagane';
            return false;
        }
        if (($d['ins'] != '') && ! is_numeric($d['ins'])) {
            Ruch::$errors [] = 'Ubezpieczenie - błędny format liczby';
            return false;
        }
        if (($d['ins'] != '') && ($d['ins'] < 0)) {
            Ruch::$errors [] = 'Ubezpieczenie - wartość za niska';
            return false;
        }
        
        if (($d['cod'] != '') && (!is_numeric($d['cod']))) {
            Ruch::$errors [] = 'Pobranie - błędny format liczby';
            return false;
        }
        
        if (($d['odb_adr1'] == '')) {
            Ruch::$errors [] = 'Ulica - pole wymagane';
            return false;
        }

        if (($d['odb_adr2'] == '')) {
            Ruch::$errors [] = 'Budynek i lokal - pole wymagane';
            return false;
        }

        if (($d['odb_miasto'] == '')) {
            Ruch::$errors [] = 'Miasto - pole wymagane';
            return false;
        }

        if (($d['odb_kod'] == '')) {
            Ruch::$errors [] = 'Kod - pole wymagane';
            return false;
        }
        
        if (($d['sending_method'] == 'dispatch_order') && ($d['dispatch_point'] == '')) {
            Ruch::$errors [] = 'Punkt odbioru - pole wymagane';
            return false;
        }
         
        foreach ($d['parcels'] as $p) {
            if ($p['temp'] != '') {
                continue;
            }
            if (!is_numeric($p['waga'])) {
                Ruch::$errors [] = 'Waga - błędny format liczby';
                return false;
            }
            if (!is_numeric($p['wys'])) {
                Ruch::$errors [] = 'Wysokość - błędny format liczby';
                return false;
            }
            if (!is_numeric($p['dlug'])) {
                Ruch::$errors [] = 'Długość - błędny format liczby';
                return false;
            }
            if (!is_numeric($p['szer'])) {
                Ruch::$errors [] = 'Szerokość - błędny format liczby';
                return false;
            }
        }
        return true;
    }
}
