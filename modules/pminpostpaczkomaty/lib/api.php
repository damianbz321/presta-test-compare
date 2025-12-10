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

class InpostApi
{
    protected static $instance;
    protected static $client;
    protected static $errors;
    public static $wsdl = 'https://api.paczkomaty.pl/';
    protected static $login;
    protected static $pass;
    protected static $label_format;
    protected static $label_type;

    public function __construct()
    {
        self::$errors = array();
        self::$login = Configuration::get('PMINPOSTPACZKOMATY_LOGIN');
        self::$pass = Configuration::get('PMINPOSTPACZKOMATY_PASSWORD');
        self::$label_format = Configuration::get('PMINPOSTPACZKOMATY_LABEL_FORMAT');
        self::$label_type = Configuration::get('PMINPOSTPACZKOMATY_LABEL_SIZE');
        if (Tools::isSubmit('format') && Tools::isSubmit('size_l')) {
            self::$label_format = Tools::getValue('format');
            self::$label_type = Tools::getValue('size_l');
        }
    }

    public function inpostDigest($string)
    {
        $version = phpversion();
        return ($version[0] < 5 ? base64_encode(pack('H*', md5($string))) : base64_encode(md5($string, true)));
    }

    private function send($method, $data = '')
    {
        $url = self::$wsdl.'?do='.$method;

        $email = self::$login;
        $digest = $this->inpostDigest(self::$pass);

        $packsData = array(
        'email' => $email,
        'digest' => $digest,
        'content' => $data
        );

        $data = http_build_query($packsData);
        return $this->inpostPostRequest($url, $data);
    }

    public function inpostPostRequest($url, $data, $xml = true)
    {
        error_reporting(0);
        $latArg_output = ini_get('arg_separator.output');
        ini_set('arg_separator.output', '&');
        $length = Tools::strlen($data) . "\r\n";
        $params = array(
          'http' => array(
              'method' => 'POST',
              'header' => "Content-type: application/x-www-form-urlencoded\r\n"."Content-Length: ".$length,
              'content' => $data
          )
        );
        $ctx = stream_context_create($params);

        $fp = fopen($url, 'rb', false, $ctx);

        if (!$fp) {
            self::$errors[] = 'Na serwerze należy włączyć: allow_url_fopen - zapytaj swojego dostawcy';
            return false;
        }
        $response = '';
        while (!feof($fp)) {
            $response .= fread($fp, 8192);
        }
        if ($response === false || $response == '') {
            self::$errors[] = 'Błąd komunikacji';
            return false;
        }
        ini_set('arg_separator.output', $latArg_output);
        if ($xml) {
            try {
                $xml = new SimpleXMLElement($response);
             
                if (isset($xml->pack->error)) {
                    self::$errors = (string)$xml->pack->error;
                    return false;
                } elseif (isset($xml->error)) {
                    self::$errors = (string)$xml->error;
                    return false;
                }
            } catch (Exception $exp) {
                self::$errors = $exp->getMessage();
                return false;
            }
            return $xml;
        } else {
            try {
                $xml = new SimpleXMLElement($response);
             
                if (isset($xml->pack->error)) {
                    self::$errors = (string)$xml->pack->error;
                    return false;
                }
                if (isset($xml->error)) {
                    self::$errors = (string)$xml->error;
                    return false;
                }
            } catch (Exception $exp) {
                return -5;
            }
            return false;
        }
    }
    
    public function inpostPostRequest2($url, $data, $xml = true)
    {
        error_reporting(0);
        $latArg_output = ini_get('arg_separator.output');
        ini_set('arg_separator.output', '&');

        $params = array('http' => array(
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n"."Content-Length: ".
            Tools::strlen($data) . "\r\n",
        'content' => $data
        ));

        $ctx = stream_context_create($params);

        $fp = fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            return 0;
        }
        $response = '';

        while (!feof($fp)) {
            $response .= fread($fp, 8192);
        }

        if ($response === false || $response == '') {
            self::$errors[] = 'Błąd komunikacji';
            return false;
        }

        ini_set('arg_separator.output', $latArg_output);
        if ($xml) {
            try {
                $xml = new SimpleXMLElement($response);
                if (isset($xml->pack->error)) {
                    self::$errors = (string)$xml->pack->error;
                    return false;
                } elseif (isset($xml->error)) {
                    self::$errors = (string)$xml->error;
                    return false;
                }
            } catch (Exception $exp) {
                self::$errors = $exp->getMessage();
                return false;
            }
            return $xml;
        } else {
            try {
                $xml = new SimpleXMLElement($response);
                if (isset($xml->pack->error)) {
                    self::$errors = (string)$xml->pack->error;
                    return false;
                }
                if (isset($xml->error)) {
                    self::$errors = (string)$xml->error;
                    return false;
                }
            } catch (Exception $exp) {
                return $response;
            }
        }
    }

    public function getErrors()
    {
        return self::$errors;
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new InpostApi();
        }
        return self::$instance;
    }

    public function getsticker($pack_code, $format = false)
    {
        $method = 'getsticker';
        $url = self::$wsdl.'?do='.$method;

        $email = self::$login;
        $digest = $this->inpostDigest(self::$pass);

        if ($format !== false) {
            self::$label_format = $format;
        }
        if (self::$label_format == 'Epl') {
            self::$label_format = 'Epl2';
        }

        if (self::$label_format == 'Zpl') {
            self::$label_format = 'Pdf';
        }
        $packsData = array(
            'email' => $email,
            'digest' => $digest,
            'packcode' => $pack_code,
            'labelType' => self::$label_type,
        );
        $bane = $pack_code.'.'.$format;
        $data = http_build_query($packsData);
        $file = $this->inpostPostRequest2($url, $data, false);
        if (!$file) {
            return false;
        }
        return $file;
    }

    public function sender()
    {
        $xml = '';
        $sender = array(
        'name' => Configuration::get('PMINPOSTPACZKOMATY_COMPANY_NAME').' '.Configuration::get('PMINPOSTPACZKOMATY_FIRST_NAME'),
        'surName' => Configuration::get('PMINPOSTPACZKOMATY_LAST_NAME'),
        'email' => Configuration::get('PMINPOSTPACZKOMATY_EMAIL'),
        'phoneNum' => preg_replace("/[^0-9]/", "", Configuration::get('PMINPOSTPACZKOMATY_PHONE')),
        'street' =>    Configuration::get('PMINPOSTPACZKOMATY_STREET'),
        'buildingNo' => Configuration::get('PMINPOSTPACZKOMATY_BUILDING_NUMBER'),
        'flatNo' => '',
        'town' =>    Configuration::get('PMINPOSTPACZKOMATY_CITY'),
        'zipCode' =>    Configuration::get('PMINPOSTPACZKOMATY_POST_CODE'),
        'province' => ''
        );

        foreach ($sender as $key => $value) {
            $xml .= $this->xml($key, $value);
        }
        return $xml;
    }

    public function xml($name, $value = '')
    {
        $xml = '<'.$name.'>'.$value.'</'.$name.'>';
        return $xml;
    }

    public function payforpack($pack_code)
    {
        return true;
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
        if ((int)Configuration::get('PMINPOSTPACZKOMATY_INSC') != 0 || $innuranceAmount > 0) {
            $ins = true;
            $innuranceAmount = (float)str_replace(',', '.', Configuration::get('PMINPOSTPACZKOMATY_INSC'));
        }
        
        $xml = '<paczkomaty>';
        $xml .= $this->xml('autoLabels', '');http://mbox.pl/2021/inpost/16/panel/index.php?controller=PmInpostPaczkomatyList&token=501165c11392023f32a44c0615b51daa&id_orders=47&vieworders
        $xml .= $this->xml('selfSend', '');

        $xml .= "<pack>";
        $xml .= $this->xml('endOfWeekCollection', $enf_of_week);
        $xml .= $this->xml('adreseeEmail', $mail);
        $xml .= $this->xml('senderEmail', self::$login);
        $xml .= $this->xml('phoneNum', $phone);
        $xml .= $this->xml('boxMachineName', $machine);
        if ($paczkomat_nadania != '') {
            $xml .= $this->xml('senderBoxMachineName', $paczkomat_nadania);
        }
        $xml .= $this->xml('packType', $pack);
        $xml .= $this->xml('customerDelivering', 'false');
        $xml .= $this->xml('insuranceAmount', $innuranceAmount);
        $xml .= $this->xml('onDeliveryAmount', $cod);
        $xml .= $this->xml('customerRef', $reference);
        $xml .= "<senderAddress>\n";
        $xml .= $this->sender();
        $xml .= "</senderAddress>\n";
        $xml .= "</pack>\n";

        $xml .= "</paczkomaty>\n";
        return $this->send('createdeliverypacks', $xml);
    }

    public function checkConnections()
    {
        $this->createList('test@polaczenia.pl', '123123123', 'ABCDEF');// Sprawdzamy czy mamy połączenie
        if (self::$errors == 'Błąd autoryzacji') {
            return false;
        } else {
            return true;
        }
    }

    public function getPackStatus($packcode)
    {
        $xml = '';
        $result = $this->send('getpackstatus&packcode='.$packcode, $xml);
        return $result;
    }

    public function setLabelType($type)
    {
        self::$label_type = $type;
    }
}
