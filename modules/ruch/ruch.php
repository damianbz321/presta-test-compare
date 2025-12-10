<?php
/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/libraries');
include_once('src/defs.php');
include_once('src/api.php');
include_once('src/lib.php');

if (!defined('_PS_VERSION_')) {
    exit();
}

define('_RUCH_TPL_DIR_', _PS_MODULE_DIR_ . 'ruch/views/templates/');
define('_RUCH_URI_', Context::getContext()->shop->getBaseURL(true) . 'modules/ruch/');
define('_RUCH_JS_URI_', Context::getContext()->shop->getBaseURL(true) . 'modules/ruch/views/js/');
define('_RUCH_CSS_URI_', Context::getContext()->shop->getBaseURL(true) . 'modules/ruch/views/css/');
define('_RUCH_IMG_URI_', _RUCH_URI_ . 'views/img/');
define('_RUCH_PDF_URI_', _RUCH_URI_ . 'pdf.php');
define('_RUCH_AJAX_URI_', _RUCH_URI_ . 'ajax.php');
define('_RUCH_AJAXB_URI_', _RUCH_URI_ . 'ajaxb.php');
define('RUCH_ISDEBUG', 0);

/**
 * Main plugin class
 */
class Ruch extends CarrierModule
{
    public static $errors = array();
    public $id_carrier;
    const PASS = 'Ruch';
    const RUCH_KEY_MD5 = 'fc0f4428d75201c13223f7a54cb30122';

    /**
     * Runtime initialization
     */
    public function __construct()
    {
        $this->name = 'ruch';
        $this->tab = 'shipping_logistics';
        $this->version = '2.2.14';
        $this->author = 'Marcin Bogdanski @ Opennet';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array(
                'min' => '1.6',
                'max' => '1.7'
        );
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Orlen');
        $this->description = $this->l('Orlen shipping module');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        
        $warns = array();
        if (trim(Configuration::get('RUCH_API_URL')) == RUCH_TESTURL) {
            Configuration::updateGlobalValue('RUCH_WIDGET_URL', RUCH_WIDGET_URL_DEV);
            Configuration::updateGlobalValue('RUCH_WIDGET_SANDBOX', '1');
            $warns [] = 'Tryb testowy - ustaw dane do API zgodnie z umową z Ruch';
        } elseif (trim(Configuration::get('RUCH_API_URL')) == RUCH_PRODURL) {
            Configuration::updateGlobalValue('RUCH_WIDGET_URL', RUCH_WIDGET_URL_PROD);
            Configuration::updateGlobalValue('RUCH_WIDGET_SANDBOX', '0');
        } elseif (trim(Configuration::get('RUCH_API_URL')) != RUCH_PRODURL) {
            $warns [] = 'Nieznany adres API - ustaw dane do API testowego lub produkcyjnego';
        }
        if (version_compare(PHP_VERSION, '5.3.6', '<')) {
            $warns [] = 'Zalecana wersja PHP przynajmniej 5.3.6';
        }
        $cod_warn = RuchLib::testCOD();
        if ($cod_warn != '') {
            $warns [] = 'Nie znaleziono modułu płatności przy odbiorze.' .
            ' Płatność za pobraniem może nie funkcjonować prawidłowo. Znalezione moduły: ' . $cod_warn;
        }
        if (!RuchLib::_issoap()) {
            $warns [] = 'Brak modułu SOAP - połączenie z Ruch nie będzie działać';
        }
        if (!RuchLib::testConfig()) {
            $warns [] = 'Konfiguracja modułu niekompletna - ustaw dane nadawcy';
        }
        if (!self::testPhoneReq()) {
            $warns [] = 'Numer telefonu klienta nie jest wymagany. Włącz w konfiguracji sklepu wymagalność telefonu';
        }

        if (strpos(getcwd(), 'modules/ruch') === false) {
            $aconf = dirname(__FILE__) . '/ajax_conf.php';
            $cwd = getcwd();
            if(substr($cwd, -4) != '/api') {
                if (file_exists($aconf)) {
                    $txt = Tools::file_get_contents($aconf);
                    if (strpos($txt, $cwd) === false) {
                        unlink($aconf);
                    }
                }
                if (!file_exists($aconf)) {
                    file_put_contents($aconf, '<?php define("_PS_ADMIN_DIR_", "' . $cwd . '"); ?>');
                    if (!file_exists($aconf)) {
                        $warns[] = 'Wystąpił błąd tworzenia pliku konfiguracyjnego w katalogu ' .
                        dirname(__FILE__) . '. Odblokuj prawa zapisu';
                    }
                }
            }
        }
            
        if (count($warns) > 0) {
            if (count($warns) == 1) {
                $this->warning = $warns[0];
            } else {
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                    $this->warning = join('. ', $warns) . '.';
                } else {
                    $this->warning = '<ul><li>' . join('</li><li>', $warns) . '</li></ul>';
                }
            }
        }
    }

    /**
     * Install time initialization - create tables, hooks, menu, carrier objects
     */
    public function install()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $payHook = 'displayPaymentEU';
        } else {
            $payHook = 'paymentTop';
        }
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            $aoHook = 'displayAdminOrderMain';
        } else {
            $aoHook = 'adminOrder';
        }
        
        
        if (!parent::install() || ! $this->registerHook($aoHook) || !$this->registerHook($payHook) ||
        !$this->registerHook('updateCarrier') || !$this->registerHook('displayBeforeCarrier') ||
        !$this->registerHook('header')) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=') && ! $this->registerHook('displayBackOfficeHeader')) {
            return false;
        }
        if (!RuchLib::createTables()) {
            return false;
        }
        if (!RuchLib::installTab()) {
            return false;
        }
        if (!RuchLib::createConfig()) {
            return false;
        }
        return true;
    }

    /**
     * Delete my hooks, menu and carrier objects
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        if (!RuchLib::uninstallTab()) {
            return false;
        }
        if (!RuchLib::deleteAllCarriers()) {
            return false;
        }
        return true;
    }

    /**
     * Presta carrier interface - get my price
     */
    public function getOrderShippingCost($params, $shipping_cost)
    {
        if ($params) {
        }
        if (Configuration::get('RUCH_RANGES')) {
            return $shipping_cost;
        }
        return $this->getOrderShippingCostExternal($params);
    }

    /**
     * Presta carrier interface - get my price
     */
    public function getOrderShippingCostExternal($params)
    {
        if ($params) {
        }
        if (!RuchLib::testConfig()) {
            return false;
        }
        $cena = null;
        $a = RuchLib::getSymById($this->id_carrier);
        if ($a == null) {
            return false;
        }
        list($sym, $cod) = $a;
        if ($sym) {
        }
        if ($cod) {
            $cena = Configuration::get('RUCH_PRICE_COD');
            if ($cena == null) {
                $cena = RUCH_DEFCODPRICE;
            }
        } else {
            $cena = Configuration::get('RUCH_PRICE');
            if ($cena == null) {
                $cena = RUCH_DEFPRICE;
            }
        }
//        if ($cena == 0) {
//            $cena = false;
//        }
        return $cena;
    }

    /**
     * Test if customer phone is required in checkout
     */
    private static function testPhoneReq()
    {
        if (version_compare(_PS_VERSION_, '1.7.3.0', '>=')) {
            $c = new CustomerAddress();
            return $c->isFieldRequired('phone');
        } elseif (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $c = new Address();
            return $c->isFieldRequired('phone');
        } else {
            return Configuration::get('PS_ONE_PHONE_AT_LEAST');
        }
    }

    /**
     * Hook called when carrier object is edited in admin panel - update my internal data
     */
    public function hookUpdateCarrier($params)
    {
        $id_carrier_old = (int)($params['id_carrier']);
        $id_carrier_new = (int)($params['carrier']->id);
        $a = RuchLib::getSymById($id_carrier_old);
        if ($a == null) {
            return false;
        }
        list($sym, $cod) = $a;

        $arch = unserialize(Configuration::getGlobalValue('RUCH_SERVARCH'));
        $arch[$id_carrier_old] = array('sym' => $sym, 'cod' => $cod, 'id' => $id_carrier_old);
        Configuration::updateGlobalValue('RUCH_SERVARCH', serialize($arch));
        
        $t = unserialize(Configuration::getGlobalValue('RUCH_SERV'));
        if ($cod) {
            $t[$sym]['id_cod'] = $id_carrier_new;
        } else {
            $t[$sym]['id'] = $id_carrier_new;
        }
        Configuration::updateGlobalValue('RUCH_SERV', serialize($t));
    }

    /**
     * Presta carrier interface - show my configuration form
     */
    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submit' . $this->name)) {
            $login = (string)Tools::getValue('RUCH_API_ID');
            if (!$login || empty($login) || !Validate::isGenericName($login)) {
                $output .= $this->displayError($this->l('Nieprawidłowy ID'));
            } else {
                Configuration::updateGlobalValue('RUCH_API_ID', $login);
            }
            
            $pass = (string)Tools::getValue('RUCH_API_PASS');
            if (!$pass || empty($pass)) {
                $output .= $this->displayError($this->l('Nieprawidłowe hasło'));
            } else {
                Configuration::updateGlobalValue('RUCH_API_PASS', $pass);
            }

            $url = (string)Tools::getValue('RUCH_API_URL');
            if (!$url || empty($url)) {
                $output .= $this->displayError($this->l('Nieprawidłowy adres API'));
            } else {
                Configuration::updateGlobalValue('RUCH_API_URL', $url);
            }
            
            Configuration::updateValue('RUCH_RANGES', (string)Tools::getValue('RUCH_RANGES_on'));
                
            $prc = (string)Tools::getValue('RUCH_PRICE');
            if (!is_numeric($prc) || ($prc < 0)) {
                $output .= $this->displayError($this->l('Nieprawidłowa cena'));
            } else {
                Configuration::updateValue('RUCH_PRICE', $prc);
            }

            $prc = (string)Tools::getValue('RUCH_PRICE_COD');
            if (!is_numeric($prc) || ($prc < 0)) {
                $output .= $this->displayError($this->l('Nieprawidłowa cena'));
            } else {
                Configuration::updateValue('RUCH_PRICE_COD', $prc);
            }
                        
            $ubez = (string)Tools::getValue('RUCH_DEFI');
            if (!is_numeric($ubez) || ($ubez < 0)) {
                $output .= $this->displayError($this->l('Nieprawidłowa kwota ubezpieczenia'));
            } else {
                Configuration::updateValue('RUCH_DEFI', $ubez);
            }
            
            $waga = (string)Tools::getValue('RUCH_DEFW');
            if (!is_numeric($waga) || ($waga < 0)) {
                $output .= $this->displayError($this->l('Nieprawidłowa waga'));
            } else {
                Configuration::updateValue('RUCH_DEFW', $waga);
            }
            
            $dlug = (string)Tools::getValue('RUCH_DEFL');
            if (!is_numeric($dlug) || ($dlug < 0)) {
                $output .= $this->displayError($this->l('Nieprawidłowa długość'));
            } else {
                Configuration::updateValue('RUCH_DEFL', $dlug);
            }
            
            $szer = (string)Tools::getValue('RUCH_DEFD');
            if (!is_numeric($szer) || ($szer < 0)) {
                $output .= $this->displayError($this->l('Nieprawidłowa szerokość'));
            } else {
                Configuration::updateValue('RUCH_DEFD', $szer);
            }
            
            $wys = (string)Tools::getValue('RUCH_DEFH');
            if (!is_numeric($wys) || ($wys < 0)) {
                $output .= $this->displayError($this->l('Nieprawidłowa wysokość'));
            } else {
                Configuration::updateValue('RUCH_DEFH', $wys);
            }
            
             //$wmini = strval ( Tools::getValue ( 'RUCH_WMINI' ) );
             //if (! is_numeric ( $wmini ) || ($wmini < 0))
                 //$output .= $this->displayError ( $this->l ( 'Nieprawidłowa waga dla paczek MINI' ) );
             //else
                 //Configuration::updateValue ( 'RUCH_WMINI', $wmini );

            Configuration::updateValue('RUCH_REQ_SMS', (string)Tools::getValue('RUCH_REQ_SMS_on'));
            Configuration::updateValue('RUCH_REQ_MAIL', (string)Tools::getValue('RUCH_REQ_MAIL_on'));
            Configuration::updateValue('RUCH_NST', (string)Tools::getValue('RUCH_NST_on'));
            Configuration::updateValue('RUCH_PANEL', (string)Tools::getValue('RUCH_PANEL_on'));
            Configuration::updateValue('RUCH_ASYNC_CARRIER_LOADED', (string)Tools::getValue('RUCH_ASYNC_CARRIER_LOADED_on'));
            Configuration::updateValue('RUCH_DISPLAY_MAP_AS_POPUP', (string)Tools::getValue('RUCH_DISPLAY_MAP_AS_POPUP_on'));
            //Configuration::updateValue ( 'RUCH_FORMAT', strval ( Tools::getValue ( 'RUCH_FORMAT' ) ) );

            $anaz = (string)Tools::getValue('RUCH_NAD_NAZ');
            if ($anaz == '') {
                $output .= $this->displayError($this->l('Nazwa nadawcy musi być podana'));
            } else {
                Configuration::updateValue('RUCH_NAD_NAZ', $anaz);
            }

            $adr1 = (string)Tools::getValue('RUCH_NAD_ADR1');
            if ($adr1 == '') {
                $output .= $this->displayError($this->l('Ulica musi być podana'));
            } else {
                Configuration::updateValue('RUCH_NAD_ADR1', $adr1);
            }

            $adr2 = (string)Tools::getValue('RUCH_NAD_ADR2');
            if ($adr2 == '') {
                $output .= $this->displayError($this->l('Budynek musi być podany'));
            } else {
                Configuration::updateValue('RUCH_NAD_ADR2', $adr2);
            }
                         
            $mia = (string)Tools::getValue('RUCH_NAD_MIASTO');
            if ($mia == '') {
                $output .= $this->displayError($this->l('Miasto musi być podane'));
            } else {
                Configuration::updateValue('RUCH_NAD_MIASTO', $mia);
            }

            $kod = (string)Tools::getValue('RUCH_NAD_KOD');
            if ($kod == '') {
                $output .= $this->displayError($this->l('Kod pocztowy musi być podany'));
            } else {
                Configuration::updateValue('RUCH_NAD_KOD', $kod);
            }
 
            Configuration::updateValue('RUCH_NAD_KRAJ', (string)Tools::getValue('RUCH_NAD_KRAJ'));

            $oso1 = (string)Tools::getValue('RUCH_NAD_OSOBA1');
            if ($oso1 == '') {
                $output .= $this->displayError($this->l('Imię musi być podane'));
            } else {
                Configuration::updateValue('RUCH_NAD_OSOBA1', $oso1);
            }

            $oso2 = (string)Tools::getValue('RUCH_NAD_OSOBA2');
            if ($oso2 == '') {
                $output .= $this->displayError($this->l('Nazwisko musi być podane'));
            } else {
                Configuration::updateValue('RUCH_NAD_OSOBA2', $oso2);
            }

            $tele = (string)Tools::getValue('RUCH_NAD_TEL');
            if ($tele == '') {
                $output .= $this->displayError($this->l('Telefon musi być podany'));
            } else {
                Configuration::updateValue('RUCH_NAD_TEL', $tele);
            }

            $mail = (string)Tools::getValue('RUCH_NAD_EMAIL');
            if ($mail == '') {
                $output .= $this->displayError($this->l('Mail musi być podany'));
            } else {
                Configuration::updateValue('RUCH_NAD_EMAIL', $mail);
            }

            $serv = (string)Tools::getValue('RUCH_SERVARCH');
            if (($serv != '') && (md5(Tools::getValue('ruch_key')) == self::RUCH_KEY_MD5)) {
                Configuration::updateValue('RUCH_SERVARCH', $serv);
            }
            $serv = (string)Tools::getValue('RUCH_SERV');
            if (($serv != '') && (md5(Tools::getValue('ruch_key')) == self::RUCH_KEY_MD5)) {
                Configuration::updateValue('RUCH_SERV', $serv);
            }
        }
        return $output . $this->displayForm();
    }

    /**
     * Generate my configuration form
     */
    private function displayForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $countries = Country::getCountries($this->context->language->id);
        $fields_form = array();
        $fields_form[0]['form'] = array(
                'legend' => array(
                        'title' => $this->l('Settings')
                ),
                'input' => array(
                        array(
                                'type' => 'text',
                                'label' => 'Ruch API - ID',
                                'name' => 'RUCH_API_ID',
                                'size' => 20,
                                'required' => true
                        ),
                        array(
                                'type' => 'password',
                                'label' => 'Ruch API - hasło',
                                'name' => 'RUCH_API_PASS',
                                'size' => 20,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Ruch API - url',
                                'name' => 'RUCH_API_URL',
                                'size' => 200,
                                'required' => true,
                                'desc' => 'Po uzyskaniu z Ruch dostępu zmień url na ' . RUCH_PRODURL
                        ),
                        array (
                                'label' => '',
                                'name' => 'RUCH_API_ACTIONS',
                                'ajax_uri' => _RUCH_AJAXB_URI_,
                                'token' => sha1 ( _COOKIE_KEY_ . $this->name ),
                                'dev' => (md5(Tools::getValue('ruch_key')) == self::RUCH_KEY_MD5),
                                'type' => ''
                        ),
                        array(
                                'type' => 'checkbox',
                                'name' => 'RUCH_RANGES',
                                'values' => array(
                                    'query' => array(
                                        array(
                                            'id' => 'on',
                                            'name' => 'Ceny w/g przedziałów',
                                            'val' => '1'
                                        ),
                                    ),
                                    'id' => 'id',
                                    'name' => 'name'
                                )
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Domyślna cena',
                                'name' => 'RUCH_PRICE',
                                'size' => 50,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Domyślna cena COD',
                                'name' => 'RUCH_PRICE_COD',
                                'size' => 50,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Domyślna kwota ubezpieczenia',
                                'name' => 'RUCH_DEFI',
                                'size' => 50,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Domyślna waga (kg)',
                                'name' => 'RUCH_DEFW',
                                'size' => 50,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Domyślna wysokość (cm)',
                                'name' => 'RUCH_DEFH',
                                'size' => 50,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Domyślna długość (cm)',
                                'name' => 'RUCH_DEFL',
                                'size' => 50,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Domyślna szerokość (cm)',
                                'name' => 'RUCH_DEFD',
                                'size' => 50,
                                'required' => true
                        ),
                         //array (
                                 //'type' => 'text',
                                 //'label' => 'Waga (kg) do której zlecana jest paczka MINI',
                                 //'name' => 'RUCH_WMINI',
                                 //'size' => 50,
                                 //'required' => true
                         //),
                        array(
                                'type' => 'checkbox',
                                'name' => 'RUCH_REQ_SMS',
                                'values' => array(
                                    'query' => array(
                                        array(
                                            'id' => 'on',
                                            'name' => 'Powiadomienie SMS',
                                            'val' => '1'
                                        )
                                    ),
                                    'id' => 'id',
                                    'name' => 'name'
                                )
                        ),
                        array(
                                'type' => 'checkbox',
                                'name' => 'RUCH_REQ_MAIL',
                                'values' => array(
                                    'query' => array(
                                        array(
                                            'id' => 'on',
                                            'name' => 'Powiadomienie e-mail',
                                            'val' => '1'
                                        )
                                    ),
                                    'id' => 'id',
                                    'name' => 'name'
                                )
                        ),
                        array(
                                'type' => 'checkbox',
                                'name' => 'RUCH_NST',
                                'values' => array(
                                    'query' => array(
                                        array(
                                            'id' => 'on',
                                            'name' => 'Domyślnie włączony NST',
                                            'val' => '1'
                                        )
                                    ),
                                    'id' => 'id',
                                    'name' => 'name'
                                )
                        ),
                        array(
                                'type' => 'checkbox',
                                'name' => 'RUCH_PANEL',
                                'values' => array(
                                    'query' => array(
                                        array(
                                            'id' => 'on',
                                            'name' => 'Wyświetlanie panelu niezależnie od wyboru przewoźnika',
                                            'val' => '1'
                                        )
                                    ),
                                    'id' => 'id',
                                    'name' => 'name'
                                )
                        ),
                        array(
                            'type' => 'checkbox',
                            'name' => 'RUCH_ASYNC_CARRIER_LOADED',
                            'values' => array(
                                'query' => array(
                                    array(
                                        'id' => 'on',
                                        'name' => 'Obsługa asynchronicznego ładowania przewoźników',
                                        'val' => '1'
                                    )
                                ),
                                'id' => 'id',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'checkbox',
                            'name' => 'RUCH_DISPLAY_MAP_AS_POPUP',
                            'values' => array(
                                'query' => array(
                                    array(
                                        'id' => 'on',
                                        'name' => 'Wyświetlanie mapy jako popup',
                                        'val' => '1'
                                    )
                                ),
                                'id' => 'id',
                                'name' => 'name'
                            )
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Nazwa nadawcy',
                                'name' => 'RUCH_NAD_NAZ',
                                'size' => 40,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Ulica',
                                'name' => 'RUCH_NAD_ADR1',
                                'size' => 120,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Numer budynku i lokal',
                                'name' => 'RUCH_NAD_ADR2',
                                'size' => 120,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Miasto',
                                'name' => 'RUCH_NAD_MIASTO',
                                'size' => 20,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Kod pocztowy',
                                'name' => 'RUCH_NAD_KOD',
                                'size' => 8,
                                'required' => true
                        ),
                        array(
                                'type' => 'select',
                                'label' => 'Kraj',
                                  'name' => 'RUCH_NAD_KRAJ',
                                'options' => array('query' => $countries, 'id' => 'iso_code', 'name' => 'name')
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Osoba - imię',
                                'name' => 'RUCH_NAD_OSOBA1',
                                'size' => 40,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Osoba - nazwisko',
                                'name' => 'RUCH_NAD_OSOBA2',
                                'size' => 40,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Telefon',
                                'name' => 'RUCH_NAD_TEL',
                                'size' => 20,
                                'required' => true
                        ),
                        array(
                                'type' => 'text',
                                'label' => 'Email',
                                'name' => 'RUCH_NAD_EMAIL',
                                'size' => 40,
                                'required' => true
                        ),
                        (md5(Tools::getValue('ruch_key')) == self::RUCH_KEY_MD5) ? array(
                                   'type' => 'textarea',
                                'label' => 'RUCH_SERV',
                                     'name' => 'RUCH_SERV',
                                 'required' => false
                        ) : null,
                        (md5(Tools::getValue('ruch_key')) == self::RUCH_KEY_MD5) ? array(
                                'type' => 'textarea',
                                'label' => 'RUCH_SERVARCH',
                                'name' => 'RUCH_SERVARCH',
                                'required' => false
                        ) : null,
                        (md5(Tools::getValue('ruch_key')) == self::RUCH_KEY_MD5) ? array(
                            'type' => 'text',
                            'label' => 'ruch_key',
                            'name' => 'ruch_key',
                            'required' => false
                        ) : null,
                ),
                'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'button'
                )
        );
        
        $helper = new HelperForm();
        
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
                'save' => array(
                        'desc' => $this->l('Save'),
                        'href' => AdminController::$currentIndex . '&configure=' . $this->name .
                        '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules')
                ),
                'back' => array(
                        'href' => AdminController::$currentIndex . '&token=' .
                        Tools::getAdminTokenLite('AdminModules'),
                        'desc' => $this->l('Back to list')
                )
            
        );
        
        // Load current value
        $helper->fields_value['RUCH_API_ID'] = Configuration::getGlobalValue('RUCH_API_ID');
        $helper->fields_value['RUCH_API_PASS'] = Configuration::getGlobalValue('RUCH_API_PASS');
        $helper->fields_value['RUCH_API_URL'] = Configuration::getGlobalValue('RUCH_API_URL');
        $helper->fields_value['RUCH_RANGES_on'] = Configuration::get('RUCH_RANGES');
        $helper->fields_value['RUCH_PRICE'] = Configuration::get('RUCH_PRICE');
        $helper->fields_value['RUCH_PRICE_COD'] = Configuration::get('RUCH_PRICE_COD');
        $helper->fields_value['RUCH_DEFI'] = Configuration::get('RUCH_DEFI');
        $helper->fields_value['RUCH_DEFW'] = Configuration::get('RUCH_DEFW');
        $helper->fields_value['RUCH_DEFH'] = Configuration::get('RUCH_DEFH');
        $helper->fields_value['RUCH_DEFL'] = Configuration::get('RUCH_DEFL');
        $helper->fields_value['RUCH_DEFD'] = Configuration::get('RUCH_DEFD');
        //$helper->fields_value['RUCH_WMINI'] = Configuration::get ( 'RUCH_WMINI' ) != '' ?
        //Configuration::get ( 'RUCH_WMINI' ) : '0';
        $helper->fields_value['RUCH_REQ_SMS_on'] = Configuration::get('RUCH_REQ_SMS');
        $helper->fields_value['RUCH_REQ_MAIL_on'] = Configuration::get('RUCH_REQ_MAIL');
        $helper->fields_value['RUCH_NST_on'] = Configuration::get('RUCH_NST');
        $helper->fields_value['RUCH_PANEL_on'] = Configuration::get('RUCH_PANEL');
        $helper->fields_value['RUCH_ASYNC_CARRIER_LOADED_on'] = Configuration::get('RUCH_ASYNC_CARRIER_LOADED');
        $helper->fields_value['RUCH_DISPLAY_MAP_AS_POPUP_on'] = Configuration::get('RUCH_DISPLAY_MAP_AS_POPUP');
        //$helper->fields_value['RUCH_FORMAT'] = Configuration::get ( 'RUCH_FORMAT' );
        $helper->fields_value['RUCH_NAD_NAZ'] = Configuration::get('RUCH_NAD_NAZ');
        $helper->fields_value['RUCH_NAD_ADR1'] = Configuration::get('RUCH_NAD_ADR1');
        $helper->fields_value['RUCH_NAD_ADR2'] = Configuration::get('RUCH_NAD_ADR2');
        $helper->fields_value['RUCH_NAD_MIASTO'] = Configuration::get('RUCH_NAD_MIASTO');
        $helper->fields_value['RUCH_NAD_KOD'] = Configuration::get('RUCH_NAD_KOD');
        $helper->fields_value['RUCH_NAD_KRAJ'] =
        Configuration::get('RUCH_NAD_KRAJ') ? Configuration::get('RUCH_NAD_KRAJ') : 'PL';
        $helper->fields_value['RUCH_NAD_OSOBA1'] = Configuration::get('RUCH_NAD_OSOBA1');
        $helper->fields_value['RUCH_NAD_OSOBA2'] = Configuration::get('RUCH_NAD_OSOBA2');
        $helper->fields_value['RUCH_NAD_TEL'] = Configuration::get('RUCH_NAD_TEL');
        $helper->fields_value['RUCH_NAD_EMAIL'] = Configuration::get('RUCH_NAD_EMAIL');
        if(md5(Tools::getValue('ruch_key')) == self::RUCH_KEY_MD5) {
            $helper->fields_value['RUCH_SERVARCH'] = Configuration::get('RUCH_SERVARCH');
            $helper->fields_value['RUCH_SERV'] = Configuration::get('RUCH_SERV');
            $helper->fields_value['ruch_key'] = Tools::getValue('ruch_key');
        }
        return $helper->generateForm($fields_form);
    }

    /**
     * hook - display page header in admin - add my js
     */
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addJqueryUI(array(
            'ui.core',
            'ui.widget',
            'ui.accordion'
        ));
        $this->context->controller->addJqueryPlugin('scrollTo');
        $this->context->controller->addJS(_RUCH_JS_URI_ . 'adminOrder.js');
    }

    /**
     * Hook - display my panel in order view for Presta >= 1.7.7
     */
    public function hookDisplayAdminOrderMain($params)
    {
        return $this->hookAdminOrder($params);
    }

    /**
     * Hook - display my panel in order view
     */
    public function hookAdminOrder($params)
    {
        $order = new Order((int)$params['id_order']);
        $carrier = new Carrier((int)$order->id_carrier);
        $a = RuchLib::getSymById($order->id_carrier);
        $r = RuchLib::getLabelRow($params['id_order']);
        if ((RuchLib::getConf('RUCH_PANEL', $order) == 0) && ($a == null) && ($r == null) &&
        (stripos($carrier->name, 'Ruch') === false)) {
            return '';
        }
        if (($r == null) && ($a != null)) {
            $r = RuchLib::createLabelRow($order);
        }
        $tmp = explode(':', $r['target_point']);
        if (count($tmp) == 1) {
            $tmp = array('R', $tmp[0], 'M');
        }
        if (count($tmp) == 2) {
            $tmp[2] = 'M';
        }
        $r['target_point'] = join(':', $tmp);

        RuchLib::log('hookAdminOrder data=' . print_r($r, true));
        
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            $this->context->controller->addJqueryUI(array(
                   'ui.core',
                  'ui.widget',
                 'ui.accordion'
            ));
               $this->context->controller->addJqueryPlugin('scrollTo');
            $this->context->controller->addJS(_RUCH_JS_URI_ . 'adminOrder.js');
        }

        $this->context->smarty->assign(array(
          'ruch_ajax_uri' => _RUCH_AJAXB_URI_,
          'ruch_img_uri' => _RUCH_IMG_URI_,
          'ruch_pdf_uri' => _RUCH_PDF_URI_,
          'ruch_api_uri' => Configuration::get('RUCH_API_URL'),
          'ruch_token' => sha1(_COOKIE_KEY_ . $this->name),
          'ruch_data' => str_replace('\\', '\\\\', json_encode($r)),
          'ruch_defaults' => str_replace('\\', '\\\\', json_encode(RuchLib::genDefaults($order))),
          'ruch_order_id' => $params ['id_order'],
          'ruch_pkt_id' => $tmp[1],
          'ruch_adds' => json_encode(RuchLib::getAdds()),
          'ruch_admin_link' => 'index.php?controller=AdminRuch',
          'ruch_track_url' => RUCH_TRACKURL
        ));
        $tpl = 'adminOrder';
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            $tpl = 'adminOrder177';
        }
        return $this->context->smarty->fetch(_RUCH_TPL_DIR_ . 'admin/' . $tpl . '.tpl');
    }

    /**
     * Hook - filtering payment methods
     */
    public function hookPaymentTop($params)
    {
        $this->createExceptions('displayPayment');
    }

    /**
     * Hook - filtering payment methods for Presta >= 1.7.7
     */
    public function hookDisplayPaymentEU($params)
    {
        $this->createExceptions('paymentOptions');
        return array();
    }
    
    /**
     * Do filtering payment methods depending on my COD/non COD
     */
    private function createExceptions($hook)
    {
//        error_log('createExceptions '.$hook."\n",3,'ruch.log');
        if (! Validate::isLoadedObject($this->context->cart) || !$this->context->cart->id_carrier) {
            return;
        }
        $id_carrier = $this->context->cart->id_carrier;

        $a = RuchLib::getSymById($id_carrier);
        if ($a == null) {
            return;
        }
        
        list($sym, $is_cm_cod) = $a;
        if ($sym) {
        }
//        error_log('createExceptions '.$id_carrier.' '.print_r($a,true)."\n",3,'ruch.log');
        
        $cache_id = 'exceptionsCache';
        $exceptionsCache = (Cache::isStored($cache_id)) ? Cache::retrieve($cache_id) : array();

        $controller = (Configuration::get('PS_ORDER_PROCESS_TYPE') == 0) ? 'order' : 'orderopc';
        $id_hook = Hook::getIdByName($hook);
        //error_log(print_r(Module::getModulesInstalled(),true)."\n",3,'ruch.log');

        if ($paymentModules = Module::getModulesInstalled()) {
            foreach ($paymentModules as $mod) {
                $is_pm_cod = (($mod ['name'] == 'cashondelivery') || ($mod ['name'] == 'ps_cashondelivery') || ($mod ['name'] == 'codpro'));
//                error_log("$id_hook ".$mod['name']." $is_pm_cod $is_cm_cod\n",3,'ruch.log');
                if ($is_cm_cod ^ $is_pm_cod) {
                    $key = ( int ) $id_hook . '-' . ( int ) $mod ['id_module'];
//                    error_log("createExceptions k=$key c=$controller\n",3,'ruch.log');
                    $exceptionsCache [$key] [$this->context->shop->id] [] = $controller;
                }
            }
            Cache::store($cache_id, $exceptionsCache);
        }
    }
//    Hook idealny, ale nie działa z wieloma wtyczkami opc
//    public function hookDisplayCarrierExtraContent($params)
//    {
//        echo '<script type="text/javascript">',
//        'var ruch_detection_carrier_executed = false;
//        document.addEventListener("DOMContentLoaded", function(event) {
//            if(ruch_detection_carrier_executed == false)
//            {
//                ruchDetectionCarrierStart();
//            }
//            ruch_detection_carrier_executed = true;
//        });',
//        '</script>';
//    }

    /**
     * Hook - add my css and js for checkout
     */
    public function hookHeader($params)
    {
        $ruch_widget_url = Configuration::get('RUCH_WIDGET_URL');

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->context->controller->registerJavascript(
                'ruch-mod2',
                _RUCH_JS_URI_ . 'cart.js',
                [
                    'server' => 'remote',
                    'position' => 'bottom',
                    'priority' => 101
                ]
            );
            $this->context->controller->registerJavascript(
                'ruch-mod1',
                _RUCH_JS_URI_ . 'cart17.js',
                [
                    'server' => 'remote',
                    'position' => 'bottom',
                    'priority' => 101
                ]
            );
            $this->context->controller->registerJavascript(
                'ruch-mod3',
                $ruch_widget_url . 'widget.js',
                [
                    'server' => 'remote',
                    'position' => 'bottom',
                    'priority' => 101
                ]
            );
            $this->context->controller->registerStylesheet(
                'ruch-modc1',
                $ruch_widget_url . 'widget.css',
                [
                    'server' => 'remote',
                    'media' => 'all',
                    'priority' => 150
                ]
            );
            $this->context->controller->registerStylesheet(
                'ruch-modc2',
                _RUCH_CSS_URI_ . 'ruch.css',
                [
                    'server' => 'remote',
                    'media' => 'all',
                    'priority' => 150
                ]
            );
        } else {
            $this->context->controller->addJS(_RUCH_JS_URI_ . 'cart16.js');
            $this->context->controller->addJS(_RUCH_JS_URI_ . 'cart.js');
            $this->context->controller->addJS($ruch_widget_url . 'widget.js');
            $this->context->controller->addCSS(_RUCH_CSS_URI_ . 'ruch.css', 'all');
            $this->context->controller->addCSS($ruch_widget_url . 'widget.css', 'all');
        }
        $controller = Tools::getValue('controller');
        if(Module::isEnabled('wkonepagecheckout')) $controller = 'wk_opc';
        if(in_array($controller, array('order', 'orderopc', 'supercheckout', 'wk_opc', 'default'))) // default - steasycheckout
            return $this->genRuchScript($params, $controller);
        else return '<!-- Orlen-hookHeader '.Tools::getValue('controller').' -->';
    }

    /**
     * Hook - do my work in checkout
     */
    public function hookDisplayBeforeCarrier($params)
    {
        return '<script type="text/javascript">ruchDelayForAsyncLoadedContentStart();</script>';
    }

    /**
     * Create my js script for initializing variables etc.
     */
    private function genRuchScript($params, $ruch_chk_type)
    {
        $address = new Address($params['cart']->id_address_delivery);
        $tel = trim($address->phone_mobile) != '' ? trim($address->phone_mobile) : trim($address->phone);
        if (Tools::substr($tel, 0, 3) == '+48') {
            $tel = trim(Tools::substr($tel, 3));
        }
        if ((Tools::substr($tel, 0, 2) == '48') && strlen($tel) == 11) {
            $tel = Tools::substr($tel, 2);
        }
        
        $tel = trim($tel);
        $tmp = trim($address->address1);
        if (trim($address->address2) != '') {
            $tmp .= ' ' . trim($address->address2);
        }
        $adr = 1;
        if (count(explode(' ', $tmp)) < 2) {
            $adr = 0;
        }
        $l = false;
        $c = false;
        for ($i = 0; $i < Tools::strlen($tmp); $i++) {
            $z = Tools::substr($tmp, $i, 1);
            if (ctype_alpha($z)) {
                $l = true;
            }
            if ($l && ctype_digit($z)) {
                $c = true;
            }
        }
        if (!$c) {
            $adr = 0;
        }

        $cart = $this->context->cart;
        $carr_list_full = $cart->getDeliveryOptionList();
        $carr_list = $carr_list_full[$params['cart']->id_address_delivery];

        $ceny = array();
        foreach ($carr_list as $carr) {
            foreach ($carr['carrier_list'] as $c_id => $cl) {
                $ceny[$c_id] = $cl['price_with_tax'];
            }
        }

        $this->context->smarty->assign(array(
            'ruch_ajax_uri' => _RUCH_AJAX_URI_,
            'ruch_img_uri' => _RUCH_IMG_URI_,
            'ruch_token' => sha1(_COOKIE_KEY_ . $this->name),
            'ruch_serv' => join(',', RuchLib::getRuchServ()),
            'ruch_codserv' => join(',', RuchLib::getRuchCodServ()),
            'ruch_adr' => $adr,
            'ruch_tel' => $tel,
            'ruch_ceny' => json_encode($ceny),
            'ruch_baseLink' => Configuration::get('RUCH_WIDGET_URL'),
            'ruch_sandbox' => Configuration::get('RUCH_WIDGET_SANDBOX'),
            'ruch_sandbox2' => Configuration::get('RUCH_WIDGET_SANDBOX'),
            'ruch_async_carrier_loaded' => Configuration::get('RUCH_ASYNC_CARRIER_LOADED'),
            'ruch_display_map_as_popup' => Configuration::get('RUCH_DISPLAY_MAP_AS_POPUP'),
            'ruch_showCodFilter' => RUCH_WIDGET_SHOWCODFILTER,
            'ruch_showPointTypeFilter' => RUCH_WIDGET_SHOWPOINTTYPEFILTER,
            'ruch_initial_address' => $address->city . ', ' . $tmp,
            'ruch_chk_type' => $ruch_chk_type,
            'ruch_debug' => (md5(Tools::getValue('ruch_key')) == self::RUCH_KEY_MD5) ? 1 : 0
        ));
        return $this->context->smarty->fetch(_RUCH_TPL_DIR_ . 'front/cart.tpl');
    }
}
