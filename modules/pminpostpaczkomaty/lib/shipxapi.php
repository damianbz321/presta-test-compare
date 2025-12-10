<?php
/**
* 2014-2020 Presta-Mod.pl Rafał Zontek
*
* NOTICE OF LICENSE
*
* Poniższy kod jest kodem płatnym, rozpowszechanie bez pisemnej zgody autora zabronione
* Moduł można zakupić na stronie Presta-Mod.pl. Modyfikacja kodu jest zabroniona,
* wszelkie modyfikacje powodują utratę gwarancji
*
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

class InpostApiShipx extends InpostApi
{
    private $tokenShipx = '';
    private $organizationId = '';
    protected static $instance;
    protected static $errors;
    public static $shipxUrl = 'https://api-shipx-pl.easypack24.net';

    public function __construct()
    {
        self::$errors = array();
        $this->organizationId = Configuration::get('PMINPOSTPACZKOMATY_ID');
        $this->tokenShipx = Configuration::get('PMINPOSTPACZKOMATY_TOKEN');
        self::$label_format = Configuration::get('PMINPOSTPACZKOMATY_LABEL_FORMAT');
        self::$label_type = Configuration::get('PMINPOSTPACZKOMATY_LABEL_SIZE') == 'A6P' ? 'A6' : 'normal';
        if (Tools::isSubmit('format') && Tools::isSubmit('size_l')) {
            self::$label_format = Tools::getValue('format');
            self::$label_type = Tools::getValue('size_l') == 'A6P' ? 'A6' : 'normal';
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new InpostApiShipx();
        }
        return self::$instance;
    }

    public function createList(
        $mail,
        $phone,
        $machine = '',
        $pack = '',
        $innuranceAmount = '',
        $cod = '',
        $paczkomat_nadania = '',
        $reference = '',
        $enf_of_week = 'false'
    ) {
          $comments = Configuration::get('PMINPOSTPACZKOMATY_COMMENTS');
        $mpk = Configuration::get('PMINPOSTPACZKOMATY_MPK');
        if ($cod == 0) {
            $ins = Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_BW');
        } else {
            $ins = Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_COD');
            $innuranceAmount = $cod;
        }
        if ((int)Configuration::get('PMINPOSTPACZKOMATY_INSC') != 0 || $innuranceAmount > 0) {
            $ins = true;
            $innuranceAmount = (float)str_replace(',', '.', Configuration::get('PMINPOSTPACZKOMATY_INSC'));
            if ($cod) {
                $innuranceAmount = $cod;
            }
        }

        
        
        if (strpos($mail, '@allegromail.pl') !== false ||
            strpos($mail, '@user.allegromail.pl') !== false ||
            strpos($mail, '@user.allegrogroup.pl') !== false
        ) {
            $service = 'inpost_locker_allegro_smart';
        } else {
            $service = 'inpost_locker_standard';
        }

        switch ($pack) {
            case 'A':
                $packageSize = 'small';
                break;
            case 'B':
                $packageSize = 'medium';
                break;
            case 'C':
                $packageSize = 'large';
                break;
        }
        $parcels = array();
        $parcels[] = array(
            'id' => 'small',
            'template' => $packageSize,
            'tracking_number' => null,
            'is_non_standard' => false
        );
        $inpostData = array(
            'receiver' => array(
                'email' => $mail,
                'phone' => $phone,
                'name' => ''
            ),
            'sender' => array(
                'name' => 'Nazwa',
                'company_name' => Configuration::get('PMINPOSTPACZKOMATY_COMPANY_NAME'),
                'first_name' => Configuration::get('PMINPOSTPACZKOMATY_FIRST_NAME'),
                'last_name' => Configuration::get('PMINPOSTPACZKOMATY_LAST_NAME'),
                'email' => Configuration::get('PMINPOSTPACZKOMATY_EMAIL'),
                'phone' => preg_replace("/[^0-9]/", "", Configuration::get('PMINPOSTPACZKOMATY_PHONE')),
                'address' => array(
                    'street' => Configuration::get('PMINPOSTPACZKOMATY_STREET'),
                    'building_number' => Configuration::get('PMINPOSTPACZKOMATY_BUILDING_NUMBER'),
                    'city' => Configuration::get('PMINPOSTPACZKOMATY_CITY'),
                    'post_code' => Configuration::get('PMINPOSTPACZKOMATY_POST_CODE'),
                    'country_code' => 'PL'
                )
            ),
            'cod' => array(
                'amount' => $cod,
                'currency' => 'PLN'
            ),
            'insurance' => array(
                'amount' => $innuranceAmount,
                'currency' => 'PLN'
            ),
            'mpk' => $mpk,
            'reference' => $reference,
            'parcels' => $parcels,
            'service' => $service,
            'end_of_week_collection' => $enf_of_week,
            'only_choice_of_offer' => false,
            'custom_attributes' => array(
                'target_point' => $machine,
                'dropoff_point' => $paczkomat_nadania,
            )
        );

        if ($mpk == '') {
            unset($inpostData['mpk']);
        }
        if ($cod == 0) {
            unset($inpostData['cod']);
        }
        if (!$ins) {
            unset($inpostData['insurance']);
        }
        if (!is_null($paczkomat_nadania)) {
            $inpostData['custom_attributes']['sending_method'] = 'parcel_locker';
        }
        if ($service == 'inpost_locker_allegro_smart' && !isset($inpostData['custom_attributes']['sending_method'])) {
            $inpostData['custom_attributes']['sending_method'] = 'dispatch_order';
        }
        if (Configuration::get('PMINPOSTPACZKOMATY_SHIPPING_METHOD') && $paczkomat_nadania == '') {
            $inpostData['custom_attributes']['sending_method'] = 'pop';
        }
        $result = $this->curl(self::$shipxUrl.'/v1/organizations/'.$this->organizationId.'/shipments', $inpostData);

        if ($result['status'] == 400 || $result['status'] == 401) {
            self::$errors[] = $result['message'];
            if (isset($result['details'])) {
                self::$errors[] = $this->parseErrors($result['details']);
            }
            return false;
        } elseif (isset($result['message']) || isset($result['details'])) {
            if (isset($result['message'])) {
                self::$errors[] = $result['message'];
            }
            if (isset($result['details'])) {
                self::$errors[] = $this->parseErrors($result['details']);
            }
            return false;
        }

        return $result;
    }

    public function parseErrors($details)
    {
        $return = '';
        foreach ($details as $key => $item) {
            if (is_array($item)) {
                $sub = '<ul>'.$this->parseErrors($item).'</ul>';
                if (strpos($sub, '<li>') === false) {
                    $sub = str_replace(array('<ul>','</ul>'), array('',''), $sub);
                }
            } else {
                $sub = $item;
            }
            if (!is_numeric($key)) {
                $return.= '<li>'.$key.': '.$sub.'</li>';
            } else {
                $return = $sub;
            }
        }
        return $return;
    }

    public function getErrors()
    {
        return self::$errors;
    }

    public function getShipments()
    {
        $url = self::$shipxUrl.'/v1/organizations/'.$this->organizationId.'/shipments';
        $serverURL = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $serverURL);

        $authorization = 'Authorization: Bearer '.$this->tokenShipx;

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        curl_close($ch);

        if ($result === false) {
            return false;
        }
        $resultData = json_decode($result, true);
        return $resultData;
    }

    public function payByPack($id)
    {
        $this->getShipments();
        $url = self::$shipxUrl.'/v1/shipments/'.(int)$id.'/buy';
        $serverURL = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $serverURL);
        $post = array();
        $post['offer_id'] = (string)$id;
        $post['id'] = (string)$id;
        $authorization = 'Authorization: Bearer '.$this->tokenShipx;

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data_string = json_encode($post);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $result = curl_exec($ch);

        curl_close($ch);

        if ($result === false) {
            return false;
        }
        $resultData = json_decode($result, true);
        return $resultData;
    }

    public function curl($url, $post, $method = 'POST', $return_array = true)
    {
        $serverURL = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $serverURL);

        $authorization = 'Authorization: Bearer '.$this->tokenShipx;

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (is_array($post) && sizeof($post)) {
            $data_string = json_encode($post);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        }

        $result = curl_exec($ch);

        curl_close($ch);

        if ($result === false) {
            return false;
        }
        if (Configuration::get('PMINPOSTPACZKOMATY_LOG')) {
            file_put_contents(dirname(__FILE__).'/../log/log.txt', date('Y-m-d H:i:s').PHP_EOL, FILE_APPEND);
            file_put_contents(dirname(__FILE__).'/../log/log.txt', print_r($post, true), FILE_APPEND);
            file_put_contents(dirname(__FILE__).'/../log/log.txt', print_r(json_decode($result), true), FILE_APPEND);
        }

        if ($return_array) {
            $resultData = json_decode($result, true);
            return $resultData;
        } else {
            return $result;
        }
    }

    public function downloadLabel($id, $format = false)
    {
        if ($format !== false) {
            self::$label_format = $format;
        }
        if (self::$label_format == 'Pdf') {
            $url = self::$shipxUrl.'/v1/shipments/'.$id.'/label?format='.self::$label_format.'&type='.self::$label_type;
        } else {
            $url = self::$shipxUrl.'/v1/shipments/'.$id.'/label?format='.self::$label_format;
        }
        
        $serverURL = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $serverURL);

        $post = array();
        $authorization = 'Authorization: Bearer '.$this->tokenShipx;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data_string = json_encode($post);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false) {
            return false;
        }

        // Decode the result
        return $result;
    }

    public function getTrackingNumber($id)
    {
        for ($i = 0; $i < 10; $i++) {
            sleep(3);
            $url = self::$shipxUrl.'/v1/shipments/'.$id;
            $result = $this->curl($url, false, 'GET');
            if ($result['status'] == 404) {
                if ($i) {
                    self::$errors[] = $result['message'];
                    return false;
                }
            }
            if ($result['status'] == 'offer_selected') {
                if ($i) {
                    self::$errors[] = 'offered';
                    return false;
                }
            }

            if (!isset($result['tracking_number']) || $result['tracking_number'] == '') {
                if ($i) {
                    self::$errors[] = 'trackempty';
                    return false;
                }
            }
            if (isset($result['tracking_number']) && $result['tracking_number'] != '') {
                return $result['tracking_number'];
            } else {
                sleep(1);
            }
        }
        return false;
    }

    public function getSticker($numer_listu, $format = false)
    {
        if ($numer_listu == '') {
            self::$errors[] = 'Nie udało się pobrać listu przewozowego';
            return false;
        }
        $sql = 'SELECT id_pack FROM `'._DB_PREFIX_.'pminpostpaczkomatylist` WHERE nr_listu = "'.$numer_listu.'"';
        $value = Db::getInstance()->getValue($sql);

        if ($value === false) {
            return false;
        } else {
            return $this->downloadLabel($value, $format);
        }
    }

    public function checkConnections()
    {
        $url = self::$shipxUrl.'/v1/organizations';
        $result = $this->curl($url, array(), $method = 'GET', $return_array = true);
        return $result;
    }

    public function getPackStatus($packcode)
    {
        $xml = '';
        $url = self::$shipxUrl.'/v1/tracking/'.$packcode;

        $serverURL = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $serverURL);

        $post = array();
        $authorization = 'Authorization: Bearer '.$this->tokenShipx;

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data_string = json_encode($post);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $result = curl_exec($ch);
        return $result;
    }

    public function createPackage(
        $pack = 'A',
        $cod = '',
        $paczkomat_nadania = null,
        $reference = 'ABC'
    ) {
        $comments = Configuration::get('PMINPOSTPACZKOMATY_COMMENTS');
        $mpk = Configuration::get('PMINPOSTPACZKOMATY_MPK');
        if ($cod == 0) {
            $ins = Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_BW');
        } else {
            $ins = Configuration::get('PMINPOSTPACZKOMATY_ISSURANCE_BW');
            $innuranceAmount = $cod;
            if (Tools::isSubmit('pminpostorder_ubezpieczenie_value')) {
                $innuranceAmount = Tools::getValue('pminpostorder_ubezpieczenie_value');
            }
        }
        switch ($pack) {
            case 'A':
                $packageSize = 'small';
                break;
            case 'B':
                $packageSize = 'medium';
                break;
            case 'C':
                $packageSize = 'large';
                break;
        }
        $parcels = array();
        $parcels[] = array(
            'id' => 'small',
            'template' => $packageSize,
            'tracking_number' => null,
            'is_non_standard' => false
        );
        $paczkomat_nadania = 'BBI07A';
        $inpostData = array(
            'receiver' => array(
                'name' => '',
                'first_name' => 'Jan',
                'last_name' => 'Kowalski',
                'name' => 'Nazwa',
                'email' => 'receiver@example.com',
                'phone' => '888000000',
                'address' => array(
                    'id' => '123',
                    'street' => 'Malborska',
                    'building_number' => '130',
                    'city' => 'Kraków',
                    'post_code' => '30-624',
                    'country_code' => 'PL'
                )
            ),
            'sender' => array(
                'name' => 'Nazwa',
                'company_name' => Configuration::get('PMINPOSTPACZKOMATY_COMPANY_NAME'),
                'first_name' => Configuration::get('PMINPOSTPACZKOMATY_FIRST_NAME'),
                'last_name' => Configuration::get('PMINPOSTPACZKOMATY_LAST_NAME'),
                'email' => Configuration::get('PMINPOSTPACZKOMATY_EMAIL'),
                'phone' => preg_replace("/[^0-9]/", "", Configuration::get('PMINPOSTPACZKOMATY_PHONE')),
                'address' => array(
                    'street' => Configuration::get('PMINPOSTPACZKOMATY_STREET'),
                    'building_number' => Configuration::get('PMINPOSTPACZKOMATY_BUILDING_NUMBER'),
                    'city' => Configuration::get('PMINPOSTPACZKOMATY_CITY'),
                    'post_code' => Configuration::get('PMINPOSTPACZKOMATY_POST_CODE'),
                    'country_code' => 'PL'
                )
            ),
            'cod' => array(
                'amount' => $cod,
                'currency' => 'PLN'
            ),
            'insurance' => array(
                'amount' => $innuranceAmount,
                'currency' => 'PLN'
            ),
            'mpk' => $mpk,
            'reference' => $reference,
            'parcels' => $parcels,
            'service' => 'inpost_letter_allegro',
            'only_choice_of_offer' => false,
            'custom_attributes' => array(
                'dropoff_point' => 'CHO096',
                'target_point' => 'CHO096',
            )
        );

        if ($mpk == '') {
            unset($inpostData['mpk']);
        }
        if ($cod == 0) {
            unset($inpostData['cod']);
        }
        if (!$ins) {
            unset($inpostData['insurance']);
        }
        if (!is_null($paczkomat_nadania)) {
            $inpostData['custom_attributes']['sending_method'] = 'parcel_locker';
        }

        $result = $this->curl(self::$shipxUrl.'/v1/organizations/'.$this->organizationId.'/shipments', $inpostData);
        if ($result['status'] == 400 || $result['status'] == 401) {
            self::$errors[] = $result['message'];
            if (isset($result['details'])) {
                self::$errors[] = $this->parseErrors($result['details']);
            }
            return false;
        }
        return $result;
    }

    public function getZlecenia()
    {
        $shp = array();
        $result = $this->curl(
            self::$shipxUrl.'/v1/organizations/'.$this->organizationId.'/dispatch_orders',
            array(),
            'GET'
        );
        // $result = $this->getStatus();
        return $result;
    }

    public function zlecenie($ids)
    {
        $shp = array();
        $shp['address'] = array(
            'street' => Configuration::get('PMINPOSTPACZKOMATY_STREET'),
            'building_number' => Configuration::get('PMINPOSTPACZKOMATY_BUILDING_NUMBER'),
            'city' => Configuration::get('PMINPOSTPACZKOMATY_CITY'),
            'post_code' => Configuration::get('PMINPOSTPACZKOMATY_POST_CODE'),
            'country_code' => 'PL'
        );
        $shp['shipments'] = $ids;

        $result = $this->curl(
            self::$shipxUrl.'/v1/organizations/'.$this->organizationId.'/dispatch_orders',
            $shp
        );
        if ($result['status'] == 400 || $result['status'] == 401) {
            self::$errors[] = $result['message'];
            if (isset($result['details'])) {
                self::$errors[] = $this->parseErrors($result['details']);
            }
            return false;
        }
        return $result;
    }

    public function printZlecenie($id)
    {
        if (file_exists(dirname(__FILE__).'/../cache/'.$id.'.pdf')) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="'.$id.'.pdf"');
            $result = file_get_contents(dirname(__FILE__).'/../cache/'.$id.'.pdf');
            die($result);
        }
        $shp = array();
        $result = $this->curl(
            self::$shipxUrl.'/v1/organizations/'.$this->organizationId.'/dispatch_orders/printouts?'.
            'dispatch_order_id='.$id.'&format=Pdf',
            array(),
            'GET',
            false
        );
        if (strlen($result) < 300) {
            return 'Nie udało się pobrać zlecenia transportowego.';
        }
        $resultData = @json_decode($result, true);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="'.$id.'.pdf"');
        file_put_contents(dirname(__FILE__).'/../cache/'.$id.'.pdf', $result);
        die($result);
    }

    public function setLabelType($type)
    {
        self::$label_type = $type == 'A6P' ? 'A6' : 'normal';
    }
}
