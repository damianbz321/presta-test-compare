<?php
namespace DhlAssistant\Wrappers;

use DhlAssistant\Classes\Forms\Translations;
use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Wrappers;

/**
 *
 */
class SourceWrapper
{
    protected static $S_PsStaticConfigFilename = 'PsStaticConfig.php';
    protected static $S_PsStaticConfigValues = null;

    /**
     *
     */
    public static function __AutoloadInit()
    {
        $values = self::LoadPsStaticConfig();

        if ($values) {
            self::$S_PsStaticConfigValues = $values;

            if (!defined('_PS_ADMIN_DIR_')) {
                define('_PS_ADMIN_DIR_', $values['PsPanelDir']);

                if (!defined('PS_ADMIN_DIR')) {
                    define('PS_ADMIN_DIR', _PS_ADMIN_DIR_);
                }

                require_once($values['PsConfigDir'] . '/config.inc.php');
            }
        } else {
            require_once(realpath(dirname(__FILE__) . '/../../../config') . '/config.inc.php');
        }
    }

    public function GetTranslation()
    {
        return new Translations();
    }

    /**
     * @return array|null
     */
    public static function LoadConfig()
    {
        $result = [
            'PsUri' => __PS_BASE_URI__,
            'ModulePath' => 'modules/dhlassistant/',
            'DbPrefix' => _DB_PREFIX_ . 'dhla_',
            'JQueryVersion' => _PS_JQUERY_VERSION_,
            'PrestaDefaultShippedOrderState' => \Configuration::get('PS_OS_SHIPPING'),
        ];

        if (self::$S_PsStaticConfigValues && is_array(self::$S_PsStaticConfigValues)) {
            $result = array_merge($result, self::$S_PsStaticConfigValues);
            $employee = \Context::getContext()->employee;

            if (isset(self::$S_PsStaticConfigValues['PsPanelThemesPath']) && $employee) {
                $result['PsPanelThemeCssPath'] = self::$S_PsStaticConfigValues['PsPanelThemesPath'] . $employee->bo_theme . '/css/' . $employee->bo_css;
                $result['PsPanelDefaultThemePath'] = self::$S_PsStaticConfigValues['PsPanelThemesPath'] . 'default/';
            }
        }

        return $result;
    }

    /**
     * @return array|mixed
     */
    public static function LoadPsStaticConfig()
    {
        $values = [];

        if (is_file(\DhlAssistant\Core::$BASEDIR . self::$S_PsStaticConfigFilename)) {
            $str_value = '';

            require(\DhlAssistant\Core::$BASEDIR . self::$S_PsStaticConfigFilename);

            $values = unserialize(base64_decode($str_value));
        }

        return $values;
    }

    /**
     * @return bool
     */
    public static function SavePsStaticConfig()
    {
        if (!defined('_PS_ROOT_DIR_')
            || !defined('_PS_ADMIN_DIR_')
            || !defined('_PS_ROOT_DIR_')
            || !defined('_PS_CONFIG_DIR_')
        ) {
            return false;
        }

        $values = [];
        $values['PsPanelDir'] = _PS_ADMIN_DIR_ . '/';

        $values['PsPanelPath'] = substr(
            str_replace('\\',
                '/',
                str_replace(_PS_ROOT_DIR_, '', $values['PsPanelDir'])
            ),
            1
        );

        $values['PsPanelThemesPath'] = substr(
            str_replace(
                '\\',
                '/',
                str_replace(_PS_ROOT_DIR_, '', _PS_BO_ALL_THEMES_DIR_)
            ), 1
        );

        $values['PsConfigDir'] = _PS_CONFIG_DIR_;
        $str_value = '<?php $str_value=\'' . base64_encode(serialize($values)) . '\'; ?>';

        file_put_contents(\DhlAssistant\Core::$BASEDIR . self::$S_PsStaticConfigFilename, $str_value);
        ConfigWrapper::AddVariables($values);

        return true;
    }

    /**
     * @return bool
     */
    public static function DeletePsStaticConfig()
    {
        if (is_file(\DhlAssistant\Core::$BASEDIR . self::$S_PsStaticConfigFilename)) {
            unlink(\DhlAssistant\Core::$BASEDIR . self::$S_PsStaticConfigFilename);
        }

        return true;
    }

    /**
     * @param $iSourceId
     * @return mixed
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public static function ImportShipmentBySourceId($iSourceId)
    {
        ConfigWrapper::CheckIsModuleConfigured();

        if (self::SourceExists($iSourceId)) {
            if (self::HasSourceShipment($iSourceId)) {
                return self::GetShipmentForSource($iSourceId);
            } else {
                $order = new \Order((int)$iSourceId);

                \Context::getContext()->currency = new \Currency($order->id_currency);

                $shipment = self::_ImportShipment($order);

                return DbWrapper::Save($shipment);
            }
        } else {
            throw new Exceptions\SourceLoggedException("Zamówienie #{$iSourceId} nie istnieje!");
        }
    }

    /**
     * @param DataModels\DhlUser $oDhlUser
     * @param $sCountryCode
     * @param DataModels\SourceOrderAdditionalParams $oParams
     * @return DataModels\DhlCountryService
     * @throws Exceptions\SourceLoggedException
     */
    public static function DetermineDhlCountryService(
        DataModels\DhlUser $oDhlUser,
        $sCountryCode,
        DataModels\SourceOrderAdditionalParams $oParams
    ) {

        while (true) {
            $dhl_user_countries = $oDhlUser->GetAvailableCountries();

            if (!isset($dhl_user_countries[$sCountryCode])) {
                break;
            }

            $country_services = $dhl_user_countries[$sCountryCode]->AvailableServices;

            if (!$country_services) {
                break;
            }

            /* @var $service DataModels\DhlCountryService */
            if ($oParams->SendToParcelShop || $oParams->SendToParcelLocker) {
                foreach ($country_services as $service) {
                    if (($oParams->SendToParcelShop && $service->AllowParcelShop)
                        || ($oParams->SendToParcelLocker && $service->AllowParcelLocker)
                    ) {
                        return $service;
                    }
                }
            } else {
                foreach ($country_services as $service) {
                    if (!$service->ParcelShopOnlyService) {
                        return $service;
                    }
                }

                foreach ($country_services as $service) {
                    return $service;
                }
            }

            break;
        }

        throw new Exceptions\SourceLoggedException("Kraj docelowy ({$sCountryCode}) nie jest dostępny!");
    }

    /**
     * @return array
     */
    public static function GetPaymentModules()
    {
        $result = [];
        $payment_modules = \Module::getPaymentModules();

        if ($payment_modules) {
            foreach ($payment_modules as $module_info) {
                $module = \Module::getInstanceByName($module_info['name']);

                if (\Validate::isLoadedObject($module)) {
                    $result[$module_info['name']] = $module->displayName;
                }
            }
        }

        return $result;
    }

    /**
     * @param \Order $oOrder
     * @return bool
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    protected static function _IsCodOrder(\Order $oOrder)
    {
        if (!\Validate::isLoadedObject($oOrder)) {
            throw new Exceptions\SourceLoggedException("Wybrany obiekt Order nie jest dostępny!");
        }

        $carrierCode = DbWrapper::GetCodeCarrier((int)$oOrder->id_carrier);
        if ('_COD' == substr((string)$carrierCode, -4)) {
            return true;
        }

        return in_array($oOrder->module, ConfigWrapper::Get('CodPaymentModules'));
    }

    /**
     * @param DataModels\DhlUser $oDhlUser
     * @param \Cart $oCart
     * @return bool
     * @throws Exceptions\SourceLoggedException
     */
    public static function CanCartBeCod(DataModels\DhlUser $oDhlUser, \Cart $oCart)
    {
        if (!\Validate::isLoadedObject($oCart)) {
            throw new Exceptions\SourceLoggedException("Obiekt Cart nie jest załadowany!");
        }

        $id_address = (int)$oCart->id_address_delivery;
        $address = new \Address($id_address);

        if (!\Validate::isLoadedObject($address)) {
            throw new Exceptions\SourceLoggedException("Błąd ładowania adresu! (Address #{$id_address})");
        }

        $id_country = (int)$address->id_country;
        $country = new \Country($id_country);

        if (!\Validate::isLoadedObject($country)) {
            throw new Exceptions\SourceLoggedException("Błąd ładowania kraju! (Country #{$id_country})");
        }

        $params_obj = self::GetSourceOrderAdditionalParams((int)$oCart->id, $country->iso_code);
        $dcs = self::DetermineDhlCountryService($oDhlUser, $country->iso_code, $params_obj);

        if (!$dcs->AvailableSpecialServices->S_COD
            || ($params_obj->SendToParcelShop && !$dcs->AllowCodForParcelShop)
            || ($params_obj->SendToParcelLocker && !$dcs->AllowCodForParcelLocker)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param $iCartId
     * @param $sCountryCode
     * @return DataModels\SourceOrderAdditionalParams|false|mixed
     */
    public static function GetSourceOrderAdditionalParams($iCartId, $sCountryCode)
    {
        $obj_info = DataModels\SourceOrderAdditionalParams::GetTreeDataObjectInfo();
        $sql_result = DbWrapper::SearchAndLoad(
            $obj_info,
            'IdSourceObject = :cart_id AND CountryCode = :country_code',
            [
                ':cart_id' => (int)$iCartId,
                ':country_code' => $sCountryCode
            ],
            'Id DESC', 1
        );

        $params_obj = $sql_result ? reset($sql_result) : new DataModels\SourceOrderAdditionalParams();

        return $params_obj;
    }

    /**
     * @param \Order $oOrder
     * @return DataModels\Shipment
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    protected static function _ImportShipment(\Order $oOrder)
    {
        $error_msgs = [];
        $result = new DataModels\Shipment();
        $address = new \Address((int)$oOrder->id_address_delivery);
        $country_code = self::_GetCountryCode($address->id_country);
        $cart_id = (int)$oOrder->id_cart;
        $params_obj = self::GetSourceOrderAdditionalParams($cart_id, $country_code);

        $carrierCode = DbWrapper::GetCodeCarrier($oOrder->id_carrier);
        $carrierOptions = array_key_exists($carrierCode, Enums\Carrier::$Options) ? Enums\Carrier::$Options[$carrierCode] : [];
        $carrierSupportSendToParcelShop = array_key_exists('support_send_to_parcelshop', $carrierOptions) ? $carrierOptions['support_send_to_parcelshop'] : false;
        $carrierSupportSendToParcelLocker = array_key_exists('support_send_to_parcellocker', $carrierOptions) ? $carrierOptions['support_send_to_parcellocker'] : false;
        if ($params_obj->SendToParcelShop && !$carrierSupportSendToParcelShop) {
            $params_obj->SendToParcelShop = false;
        }
        if ($params_obj->SendToParcelLocker && !$carrierSupportSendToParcelLocker) {
            $params_obj->SendToParcelLocker = false;
        }

        /* @var $dhl_user DataModels\DhlUser */
        $dhl_user = ConfigWrapper::Get('DefaultDhlUser');
        $dcs = self::DetermineDhlCountryService($dhl_user, $country_code, $params_obj);

        $result->ServiceType = $dcs->Code;
        $result->IdDhlUser = $dhl_user->Id;
        $result->IdSource = (int)$oOrder->id;

        $result->DropOffType = Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
            Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->DropOffType :
            Enums\DropOffType::REGULAR_PICKUP;

        $defaultLabelType = Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
            Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->LabelType :
            Enums\LabelType::BLP;
        $result->LabelType = in_array($defaultLabelType, $dcs->AvailableLabelTypes)
            ? $defaultLabelType
            : reset($dcs->AvailableLabelTypes);

        $content = '.';
        if ($cart_id) {
            $products = (new \Cart((int)$cart_id))->getProducts();
            $first_product = reset($products);
            $content = mb_substr($first_product['name'], 0, 30, 'utf-8');
        }

        $result->Content = $content;
        $result->Reference = $oOrder->reference;
        $result->ShippingPaymentType = array_keys($dcs->AvailableShippingPaymentTypes)[0];
        $result->PaymentType = reset($dcs->AvailableShippingPaymentTypes)[0];
        $result->ShipmentDate = (new \DateTime("now"));

        $result->SendToParcelShop = $params_obj->SendToParcelShop;
        $result->SendToParcelLocker = $params_obj->SendToParcelLocker;
        $result->ParcelIdent = $params_obj->ParcelIdent;
        $result->Postnummer = $params_obj->Postnummer;
        $result->ParcelPostalCode = $params_obj->ParcelPostalCode;

        $currency_code = self::_GetCurrencyCode($oOrder->id_currency);
        $result->SpecialServices->OriginalCurrencyUnit = $currency_code;
        $result->SpecialServices->COD_CurrencyUnitAlert = $dcs->GetCountry()->Currency && $currency_code != $dcs->GetCountry()->Currency;
        $result->SpecialServices->COD_Value = $oOrder->total_paid;
        $result->SpecialServices->OriginalCODValue = Core\Filters::FloatToStringWith2Dec($oOrder->total_paid);
        $result->SpecialServices->UBEZP_Value = $country_code == 'PL' ? $oOrder->total_paid : null;
        $result->SpecialServices->OriginalUBEZPValue = Core\Filters::FloatToStringWith2Dec($oOrder->total_paid);

        if (self::_IsCodOrder($oOrder)) {
            if ($dcs->AvailableSpecialServices->S_COD) {
                $result->SpecialServices->S_COD = true;
                $result->SpecialServices->S_UBEZP = ($country_code == 'PL');
            } else {
                $error_msgs[] = "Wybrano płatność przy pobraniu (COD: {$result->SpecialServices->OriginalCODValue} {$result->SpecialServices->OriginalCurrencyUnit}), która nie jest dostępna dla tej usługi";
            }
        }

        $result->Package->Type = in_array(Enums\PackageType::PACKAGE, $dcs->AvailablePackageTypes)
            ? Enums\PackageType::PACKAGE
            : reset($dcs->AvailablePackageTypes);
        $result->Receiver->Address = self::_ParseAddress($address, $dcs);
        $result->Receiver->Contact = self::_ParseContact($address);
        $result->Receiver->Preaviso = clone $result->Receiver->Contact;

        if ($result->Receiver->Address->ParseAlert) {
            $error_msgs[] = 'Nie udało się automatycznie skonwertować adresu odbiorcy do wymaganego formatu. Proszę uzupełnić adres ręcznie.';
        }

        $result->HasError = count($error_msgs) > 0;
        $result->ErrorMessage = $error_msgs ? implode("\n", $error_msgs) : '';

        return $result;
    }

    /**
     * @param $iSourceId
     * @return bool
     * @throws Exceptions\SourceLoggedException
     */
    public static function SourceExists($iSourceId)
    {
        $sql = 'SELECT COUNT(*) AS `qty` FROM `'
            . _DB_PREFIX_
            . 'orders` WHERE `id_order` = '
            . (int)$iSourceId . ' LIMIT 1';

        $sql_result = \Db::getInstance()->executeS($sql, true, false);

        if (!$sql_result) {
            throw new Exceptions\SourceLoggedException(
                self::GetConnection()->getNumberError() . ': ' . self::GetConnection()->getMsgError()
            );
        }

        return $sql_result[0]['qty'] == 1;
    }

    /**
     * @param $iSourceId
     * @return bool
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public static function HasSourceShipment($iSourceId)
    {
        $sql = 'SELECT COUNT(*) AS `qty` FROM `'
            . ConfigWrapper::Get('DbPrefix')
            . 'shipment` WHERE `IdSource` = '
            . (int)$iSourceId . ' LIMIT 1';

        $sql_result = \Db::getInstance()->executeS($sql, true, false);

        if (!$sql_result) {
            throw new Exceptions\SourceLoggedException(
                self::GetConnection()->getNumberError() . ': ' . self::GetConnection()->getMsgError()
            );
        }

        return $sql_result[0]['qty'] == 1;
    }

    /**
     * @param $iSourceId
     * @return mixed
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public static function GetShipmentForSource($iSourceId)
    {
        $sql = 'SELECT `Id` AS `Id` FROM `'
            . ConfigWrapper::Get('DbPrefix')
            . 'shipment` WHERE `IdSource` = '
            . (int)$iSourceId . ' LIMIT 1';

        $sql_result = \Db::getInstance()->executeS($sql, true, false);

        if (!$sql_result) {
            if (is_array($sql_result)) {
                throw new Exceptions\SourceLoggedException(
                    "Błąd ładowania przesyłki dla zamówienia #{$iSourceId}!"
                );
            } else {
                throw new Exceptions\SourceLoggedException(
                    self::GetConnection()->getNumberError() . ': ' . self::GetConnection()->getMsgError()
                );
            }
        }

        return DbWrapper::Load(DataModels\Shipment::GetTreeDataObjectInfo(), (int)$sql_result[0]['Id']);
    }

    /**
     * @param $iCurrencyId
     * @return mixed
     */
    protected static function _GetCurrencyCode($iCurrencyId)
    {
        $cur = new \Currency((int)$iCurrencyId);

        return $cur->iso_code;
    }

    /**
     * @param $iCountryId
     * @return mixed
     */
    protected static function _GetCountryCode($iCountryId)
    {
        $country = new \Country((int)$iCountryId);

        return $country->iso_code;
    }

    /**
     * @param DataModels\Address $oAddress
     * @param $sStreetLongString
     * @return bool
     */
    protected static function _ParseLongStreet(DataModels\Address $oAddress, $sStreetLongString)
    {
        if (mb_strlen($sStreetLongString, 'utf-8')
            > (22 + 7 + 7)
            || (mb_strlen($sStreetLongString, 'utf-8') == 0)
        ) {
            $oAddress->ParseAlert = true;

            return false;
        }

        $house_part_filler = '.';
        $street = '';
        $house = '';
        $apartment = '';

        $house_part_index = -1;

        if (preg_match('([0123456789])', $sStreetLongString, $matches, PREG_OFFSET_CAPTURE)) {
            $house_part_index = mb_strlen(substr($sStreetLongString, 0, $matches[0][1]), 'utf-8');
        }

        if (mb_strlen($sStreetLongString, 'utf-8') > 22 && $house_part_index >= 0) {
            $street = mb_substr($sStreetLongString, 0, $house_part_index, 'utf-8');
            $house_part = mb_substr($sStreetLongString, $house_part_index, null, 'utf-8');

            if ($house_part_index >= (22 - 1) || mb_strlen($house_part, 'utf-8') > (7 + 7)) {
                $parts = self::_CutStringToParts($sStreetLongString, array(22, 7, 7));
                $street = $parts[0];
                $house = $parts[1];
                $apartment = $parts[2];
            } else {
                if (mb_strlen($house_part, 'utf-8') <= 7) {
                    $house = $house_part;
                } else {
                    $apartment_part_index = -1;

                    if (preg_match('([0123456789])', $house_part, $matches, PREG_OFFSET_CAPTURE)) {
                        $apartment_part_index = mb_strlen(substr($house_part, 0, $matches[0][1]), 'utf-8');
                    }

                    if ($apartment_part_index < 0
                        || $apartment_part_index >= (7 - 1)
                        || ((mb_strlen($house_part, 'utf-8') - $apartment_part_index) > 7)
                    ) {
                        $parts = self::_CutStringToParts($house_part, array(7, 7));
                        $house = $parts[0];
                        $apartment = $parts[1];
                    } else {
                        $house = mb_substr($house_part, 0, $apartment_part_index, 'utf-8');
                        $apartment = mb_substr($house_part, $apartment_part_index, null, 'utf-8');
                    }
                }
            }
        } else {
            if (mb_strlen($sStreetLongString, 'utf-8') > 22) {
                if (mb_strlen($sStreetLongString, 'utf-8') <= (22 + 7 + 7)) {
                    $parts = self::_CutStringToParts($sStreetLongString, [22, 7, 7]);
                    $street = $parts[0];
                    $house = $parts[1];
                    $apartment = $parts[2];
                } else {
                    $Address->ParseAlert = true;
                }
            } else {
                $street = $sStreetLongString;
            }
        }

        if ($house == '') {
            $house = $house_part_filler;
        }

        $oAddress->Street = $street;
        $oAddress->HouseNumber = $house;
        $oAddress->ApartmentNumber = $apartment;
        $oAddress->ParseAlert = (mb_strlen($street, 'utf-8') == 0) || $oAddress->ParseAlert;

        return !$oAddress->ParseAlert;
    }

    /**
     * @param $sStringToCut
     * @param array $aStringsLenght
     * @return array
     */
    protected static function _CutStringToParts($sStringToCut, $aStringsLenght = array())
    {
        $cut_index = 0;
        $cut_length;
        $result = [];

        foreach ($aStringsLenght as $substring_length) {
            $cut_length = $substring_length;

            if ($cut_index + $cut_length > mb_strlen($sStringToCut, 'utf-8')) {
                $cut_length = mb_strlen($sStringToCut, 'utf-8') - $cut_index;
            }

            $result[] = mb_substr($sStringToCut, $cut_index, $cut_length, 'utf-8');
            $cut_index += $cut_length;
        }

        return $result;
    }

    /**
     * @param \Address $oAddress
     * @param DataModels\DhlCountryService $oDcs
     * @return DataModels\Address
     */
    protected static function _ParseAddress(\Address $oAddress, DataModels\DhlCountryService $oDcs)
    {
        /* @var $oDcs DataModels\DhlCountryService */
        $result = new DataModels\Address();

        if ($oAddress->company) {
            $result->Name = trim($oAddress->company);
            if ($oAddress->firstname || $oAddress->lastname) {
                $result->Name .= ' (' . trim($oAddress->firstname . ' ' . $oAddress->lastname) . ')';
            }
        } else {
            $result->Name = trim($oAddress->firstname . ' ' . $oAddress->lastname);
        }

        if (mb_strlen($result->Name, 'utf-8') > 60) {
            $result->Name = mb_substr($result->Name, 0, 60, 'utf-8');
            $result->ParseAlert = true;
        }

        $streetlong = trim(trim($oAddress->address1) . ' ' . trim($oAddress->address2));
        $orig_addr = sprintf(
            '%s, %s, %s %s, %s',
            $result->Name,
            $streetlong,
            trim($oAddress->postcode),
            trim($oAddress->city),
            trim($oAddress->country)
        );

        $result->OriginalAddressString = $orig_addr;
        if (mb_strlen($streetlong, 'utf-8') > 22) {
            self::_ParseLongStreet($result, $streetlong);
        } else {
            $result->Street = $streetlong;
            $result->HouseNumber = '.';
        }

        if ($oDcs->GetCountry()->Code !== 'PT') {
            $result->PostalCode = str_replace(['-', ' '], '', trim($oAddress->postcode));
        } else {
            $result->PostalCode = $oAddress->postcode;
        }

        if (mb_strlen($result->PostalCode, 'utf-8') > $oDcs->PostalCodeMaxLength) {
            $result->PostalCode = mb_substr($result->PostalCode, 0, $oDcs->PostalCodeMaxLength, 'utf-8');
            $result->ParseAlert = true;
        }

        $result->City = trim($oAddress->city);
        if (mb_strlen($result->City, 'utf-8') > 17) {
            $result->City = mb_substr($result->City, 0, 17, 'utf-8');
            $result->ParseAlert = true;
        }

        $result->Country = self::_GetCountryCode($oAddress->id_country);
        if (mb_strlen($result->HouseNumber, 'utf-8') == 0 && mb_strlen($result->Street, 'utf-8') > 0) {
            $result->HouseNumber = '.';
        }

        $result->ParseAlert = $result->ParseAlert
            || strlen($result->Name) == 0
            || strlen($result->HouseNumber) == 0
            || (mb_strlen($result->PostalCode, 'utf-8') > $oDcs->PostalCodeMaxLength)
            || strlen($result->City) == 0;

        return $result;
    }

    /**
     * @param \Address $oAddress
     * @return DataModels\Contact
     */
    protected static function _ParseContact(\Address $oAddress)
    {
        $result = new DataModels\Contact();

        if ($oAddress->company) {
            $result->Name = trim($oAddress->company);
            if ($oAddress->firstname || $oAddress->lastname) {
                $result->Name .= ' (' . trim($oAddress->firstname . ' ' . $oAddress->lastname) . ')';
            }
        } else {
            $result->Name = trim($oAddress->firstname . ' ' . $oAddress->lastname);
        }

        if (mb_strlen($result->Name, 'utf-8') > 50) {
            $result->Name = mb_substr($result->Name, 0, 50, 'utf-8');
        }

        $phone = '';
        if ($oAddress->phone_mobile) {
            $phone = $oAddress->phone_mobile;
        } else {
            if ($oAddress->phone) {
                $phone = $oAddress->phone;
            }
        }

        if ($phone) {
            $phone = strtr($phone, array(' ' => '', '-' => '', '.' => '', '(' => '', ')' => '', '+' => ''));
        }

        if ($phone && mb_substr($phone, 0, 1, 'utf-8') == '0') {
            $phone = mb_substr($phone, 1, null, 'utf-8');
        }

        if ($phone && mb_strlen($phone, 'utf-8') > 9) {
            $phone = mb_substr($phone, 0, 9, 'utf-8');
        }

        $result->Phone = $phone;
        $result->Email = (new \Customer((int)$oAddress->id_customer))->email;

        if (mb_strlen($result->Email, 'utf-8') > 100) {
            $result->Email = mb_substr($result->Email, 0, 100, 'utf-8');
        }

        return $result;
    }

    /**
     * @return bool
     * @throws Exceptions\LoggedException
     */
    public static function IsModuleActive()
    {
        $ps_module = \Module::getInstanceByName(ConfigWrapper::Get('ModuleName'));

        if (!$ps_module || !$ps_module->active) {
            return false;
        }

        return true;
    }

    /**
     * @throws Exceptions\LoggedException
     */
    public static function CheckIsModuleActive()
    {
        if (!self::IsModuleActive()) {
            throw new Exceptions\LoggedException("Moduł nie jest zainstalowany lub aktywny!");
        }
    }

    /***
     * @return bool
     */
    public static function IsUserAuthenticated()
    {
        $employee = \Context::getContext()->employee;

        return $employee
            && ($employee->id !== null)
            && $employee->active
            && $employee->isLoggedBack();
    }

    /**
     * @return bool
     * @throws Exceptions\LoggedException
     */
    public static function HasUserUsingRight()
    {
        $ps_module = \Module::getInstanceByName(ConfigWrapper::Get('ModuleName'));

        return $ps_module && $ps_module->getPermission('configure');
    }

    /**
     * @param bool $bCheckUsingRight
     * @throws Exceptions\LoggedException
     * @throws Exceptions\SourceLoggedException
     */
    public static function CheckIsUserAuthenticated($bCheckUsingRight = true)
    {
        if (!self::IsUserAuthenticated()) {
            throw new Exceptions\SourceLoggedException("Brak autoryzacji!");
        }

        if ($bCheckUsingRight && !self::HasUserUsingRight()) {
            throw new Exceptions\SourceLoggedException("Brak uprawinień do korzystania z modułu!");
        }
    }

    /**
     * @return bool
     */
    public static function InstallSql()
    {
        $result = true;
        $sql_queries = [];

        require(\DhlAssistant\Core::$BASEDIR . 'Procedures/Install/SqlQueries.php');

        if ($sql_queries && is_array($sql_queries)) {
            $result = self::_MakeSqlQueries($sql_queries);
        }

        return $result;
    }

    /**
     * @return bool
     */
    public static function UninstallSql()
    {
        $result = true;
        $sql_queries = [];

        require(\DhlAssistant\Core::$BASEDIR . 'Procedures/Uninstall/SqlQueries.php');

        if ($sql_queries && is_array($sql_queries)) {
            $result = self::_MakeSqlQueries($sql_queries);
        }

        return $result;
    }

    protected static function _MakeSqlQueries($aSqlQueries)
    {
        foreach ($aSqlQueries as $sql) {
            if (!\Db::getInstance()->execute($sql)) {
                return false;
            }
        }
        return true;
    }

    public function createCarriers()
    {
        $res = $this->createCarrier('Kurier zagraniczny Parcelshop', 'FOREIGN_CARRIER_PARCELSHOP');
        $res &= $this->createCarrier('Kurier zagraniczny', 'FOREIGN_CARRIER_STANDARD');
        $res &= $this->createCarrier('Punkty DHL POP - płatność przy odbiorze', 'PL_CARRIER_POP_COD');
        $res &= $this->createCarrier('Punkty i automaty DHL POP', 'PL_CARRIER_POP');
        $res &= $this->createCarrier('Kurier DHL - płatność przy odbiorze', 'PL_CARRIER_STANDARD_COD');
        $res &= $this->createCarrier('Kurier DHL', 'CARRIER_STANDARD');
        return $res;
    }

    public static function createCarrier($name, $carrierCode)
    {
        $delay = array();
        $carrier = new \Carrier();
        $carrier->name = $name;
        $carrier->id_tax_rules_group = 0;
        $carrier->active = true;
        $carrier->deleted = false;
        $carrier->delay = $delay;
        $carrier->is_free = false;
        $carrier->shipping_method = 1; //ew. 0
        $carrier->shipping_handling = true;
        $carrier->shipping_external = true;
        $carrier->is_module = true;
        $carrier->external_module_name = ConfigWrapper::Get('ModuleName');
        $carrier->need_range = true;
        $carrier->range_behavior = true;
        $carrier->grade = 0;
        $carrier->url = str_replace('%s', '@', ConfigWrapper::Get('TrackLinkTemplate'));

        foreach (\Language::getLanguages(false) as $language) {
            $delay[$language['id_lang']] = '1 - 2 dni robocze';
        }

        $carrier->delay = $delay;


        if (!$carrier->add()) {
            return false;
        }

        $groups = \Group::getGroups(true);
        foreach ($groups as $group) {
            \Db::getInstance()->insert(
                'carrier_group'
                , array(
                    'id_carrier' => (int)$carrier->id,
                    'id_group' => (int)$group['id_group']
                )
            );
        }

        $rangePrice = new \RangePrice();
        $rangePrice->id_carrier = $carrier->id;
        $rangePrice->delimiter1 = '0';
        $rangePrice->delimiter2 = '10000';
        $rangePrice->add();

        $rangeWeight = new \RangeWeight();
        $rangeWeight->id_carrier = $carrier->id;
        $rangeWeight->delimiter1 = '0';
        $rangeWeight->delimiter2 = '10000';
        $rangeWeight->add();

        \Db::getInstance()->insert(
            Wrappers\ConfigWrapper::Get('DbPrefix') . 'carrier_codes',
            [
                'carrier_id' => $carrier->id,
                'carrier_code' => $carrierCode
            ],
            false,
            true,
            \Db::INSERT,
            false
        );

        foreach (\Zone::getZones(true) as $zone) {
            $id_zone = $zone['id_zone'];
            \Db::getInstance()->insert('carrier_zone', array('id_carrier' => (int)$carrier->id, 'id_zone' => $id_zone));
            \Db::getInstance()->insert('delivery', array(
                'id_carrier' => (int)$carrier->id,
                'id_range_price' => (int)$rangePrice->id,
                'id_range_weight' => null,
                'id_zone' => $id_zone,
                'price' => '0'
            ));
            \Db::getInstance()->insert('delivery', array(
                'id_carrier' => (int)$carrier->id,
                'id_range_price' => null,
                'id_range_weight' => (int)$rangeWeight->id,
                'id_zone' => $id_zone,
                'price' => '0'
            ));
        }

        $carrierCode = DbWrapper::GetCodeCarrier($carrier->id);

        $sPOP = "POP";

        if ($carrierCode == 'PL_CARRIER_POP') {
            if (!copy(\DhlAssistant\Core::$BASEDIR . 'Media/Images/CarrierLogoPOP.jpg',
                _PS_SHIP_IMG_DIR_ . '/' . (int)$carrier->id . '.jpg')) {
                ;
            }
        } elseif ($carrierCode == 'PL_CARRIER_POP_COD') {
            if (!copy(\DhlAssistant\Core::$BASEDIR . 'Media/Images/br4d2.png',
                _PS_SHIP_IMG_DIR_ . '/' . (int)$carrier->id . '.jpg')) {
                ;
            }
        } else {
            if (!copy(\DhlAssistant\Core::$BASEDIR . 'Media/Images/CarrierLogoDefault.jpg',
                _PS_SHIP_IMG_DIR_ . '/' . (int)$carrier->id . '.jpg')) {
                ;
            }
        }

        return true;
    }

    public static function UninstallCarrier()
    {
        $carrier_id = (int)ConfigWrapper::GetOrDefault('CarrierId', null);
        if (!$carrier_id) {
            return true;
        }
        $carrier = new \Carrier($carrier_id);
        if (!\Validate::isLoadedObject($carrier)) {
            return true;
        }
        if ($carrier->deleted) {
            return true;
        }
        //If external carrier is default set other one as default
        if (\Configuration::get('PS_CARRIER_DEFAULT') == $carrier_id) {
            $carriersD = \Carrier::getCarriers(\Context::getContext()->language->id, true, false, false, null,
                PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
            foreach ($carriersD as $carrierD) {
                if ($carrierD['active'] and !$carrierD['deleted'] and ($carrierD['id_carrier'] != $carrier_id)) {
                    \Configuration::updateValue('PS_CARRIER_DEFAULT', $carrierD['id_carrier']);
                }
            }
        }

        $carrier->deleted = true;
        if (!$carrier->save()) {
            return false;
        }
        return true;
    }

    public static function InstallTabs()
    {
        $parent_tab_id = \Tab::getIdFromClassName('AdminParentOrders');
        if (!$parent_tab_id) {
            return false;
        }

        $tabs = array();

        if (ConfigWrapper::Get('IsModuleConfigured')) {
            $tabs['Lista przesyłek'] = 'ShipmentsList';
            $tabs['Raport PNP'] = 'PnpReport';
        }
        $tabs['Predefinicje przesyłek'] = 'ShipmentPresetsList';
        $tabs['Konfiguracja'] = 'SettingsEdit';
        $tabs['Pomoc'] = 'Help';
        if ($tabs) {
            foreach ($tabs as $tab_name => $tab_controller_name) {
                $tab = new \Tab();
                $tab->name[(int)\Context::getContext()->language->id] = $tab_name;
                $tab->name[(int)\Configuration::get('PS_LANG_DEFAULT')] = $tab_name;
                $tab->class_name = 'DhlAssistant' . $tab_controller_name;
                $tab->id_parent = $parent_tab_id;
                $tab->module = ConfigWrapper::Get('ModuleName');
                if (!$tab->add()) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function UninstallTabs()
    {
        $module_tabs = \Tab::getCollectionFromModule(ConfigWrapper::Get('ModuleName'));
        if ($module_tabs) {
            foreach ($module_tabs as $tab) {
                if (!$tab->delete()) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function InstallOrderState()
    {
        $order_state = new \OrderState();
        $order_state->name[\Context::getContext()->language->id] = 'Wycofane z DHL';
        $order_state->name[(int)\Configuration::get('PS_LANG_DEFAULT')] = 'Wycofane z DHL';
        $order_state->template[\Context::getContext()->language->id] = '';
        $order_state->template[(int)\Configuration::get('PS_LANG_DEFAULT')] = '';
        $order_state->send_email = false;
        $order_state->invoice = true;
        $order_state->color = '#ffcc00';
        $order_state->unremovable = true;
        $order_state->logable = false;
        if ($order_state->add()) {
            ConfigWrapper::Set('PrestaDhlCanceledOrderState', $order_state->id);
            if (!copy(\DhlAssistant\Core::$BASEDIR . 'Media/Images/dhl_cancel_state.gif',
                _PS_ORDER_STATE_IMG_DIR_ . '/' . (int)$order_state->id . '.gif')) {
                return false;
            }
            return true;
        }

        return false;
    }

    public static function UninstallOrderState()
    {
        $order_state_id = ConfigWrapper::GetOrDefault('PrestaDhlCanceledOrderState', null);
        if (!$order_state_id) {
            return true;
        }
        $order_state = new \OrderState((int)$order_state_id);
        if (!\Validate::isLoadedObject($order_state)) {
            return true;
        }
        $filepath = _PS_ORDER_STATE_IMG_DIR_ . '/' . (int)$order_state_id . '.gif';
        if (is_file($filepath)) {
            unlink($filepath);
        }
        return $order_state->delete();
    }

    public static function ModuleConfiguredEvent()
    {
        self::UninstallTabs();
        self::InstallTabs();
    }

    public static function ShipmentSendedEvent(DataModels\Shipment $oShipment)
    {
        self::_ChangePrestaOrderState($oShipment, ConfigWrapper::Get('PrestaDefaultShippedOrderState'));
        self::_SetTrackingNumber($oShipment, $oShipment->DhlShipmentId);
        return true;
    }

    public static function ShipmentCanceledEvent(DataModels\Shipment $oShipment)
    {
        self::_ChangePrestaOrderState($oShipment, ConfigWrapper::Get('PrestaDhlCanceledOrderState'));
        self::_SetTrackingNumber($oShipment, ' '); //spacja, nie może być pusty/null
        return true;
    }

    protected static function _SetTrackingNumber(DataModels\Shipment $oShipment, $sTrackingNumber)
    {
        $exception_prefix = 'Błąd ustawiania numeru śledzenia: ';
        if (!\Validate::isTrackingNumber($sTrackingNumber)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "nieprawidłowy numer śledzenia przesyłki! ({$sTrackingNumber})");
        }
        $id_order = (int)$oShipment->IdSource;
        $order = new \Order($id_order);
        if (!\Validate::isLoadedObject($order)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd ładowania zamówienia! (Order #{$id_order})");
        }
        $id_order_carrier = (int)$order->getIdOrderCarrier();
        $order_carrier = new \OrderCarrier($id_order_carrier);
        if (!\Validate::isLoadedObject($order_carrier)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd ładowania informacji o przesyłce zamówienia! (OrderCarrier #{$id_order_carrier})");
        }
        $id_customer = (int)$order->id_customer;
        $customer = new \Customer($id_customer);
        if (!\Validate::isLoadedObject($customer)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd ładowania informacji o kliencie! (Customer #{$id_customer})");
        }
        $id_carrier = (int)$order->id_carrier;
        $carrier = new \Carrier($id_carrier);
        if (!\Validate::isLoadedObject($carrier)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd ładowania informacji o dostawcy! (Carrier #{$id_carrier})");
        }
        $order->shipping_number = $sTrackingNumber;
        if (!$order->update()) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd aktualizacji (zapisu) zamówienia! (Order #{$id_order})");
        }
        $order_carrier->tracking_number = $sTrackingNumber;
        if (!$order_carrier->update()) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd aktualizacji (zapisu) przesyłki zamówienia! (OrderCarrier #{$id_order_carrier})");
        }
        \Hook::exec('actionAdminOrdersTrackingNumberUpdate',
            array('order' => $order, 'customer' => $customer, 'carrier' => $carrier));
        return true;
    }

    protected static function _SendInTransitEmail(DataModels\Shipment $oShipment)
    {
        $exception_prefix = 'Błąd wysyłania maila o zmianie statusu zamówienia do zamawiającego: ';
        $id_order = (int)$oShipment->IdSource;
        $order = new \Order($id_order);
        if (!\Validate::isLoadedObject($order)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd ładowania zamówienia! (Order #{$id_order})");
        }
        $id_customer = (int)$order->id_customer;
        $customer = new \Customer($id_customer);
        if (!\Validate::isLoadedObject($customer)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd ładowania informacji o kliencie! (Customer #{$id_customer})");
        }
        $templateVars = array(
            '{followup}' => DhlWrapper::GetTrackLink($oShipment),
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{id_order}' => $order->id,
            '{shipping_number}' => $order->shipping_number,
            '{order_name}' => $order->getUniqReference()
        );
        self::testTrySendMail((int)$order->id_lang, 'in_transit', \Mail::l('Package in transit', (int)$order->id_lang),
            $templateVars,
            $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null,
            _PS_MAIL_DIR_, false, (int)$order->id_shop); //test wysłania maila dla wyłapania ew. błędów

        if
        (
            !@\Mail::Send((int)$order->id_lang, 'in_transit', \Mail::l('Package in transit', (int)$order->id_lang),
                $templateVars,
                $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null,
                _PS_MAIL_DIR_, false, (int)$order->id_shop)
        ) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd wysyłki maila! (proszę sprawdzić logi PrestaShop)");
        }
        return true;
    }

    protected static function _ChangePrestaOrderState(DataModels\Shipment $oShipment, $iOrderStateId)
    {
        $exception_prefix = 'Błąd zmiany statusu: ';
        /* @var $oShipment DataModels\Shipment */
        $id_order_state = (int)$iOrderStateId;
        $order_state = new \OrderState($id_order_state);
        if (!\Validate::isLoadedObject($order_state)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd ładowania statusu zamówienia! (OrderState #{$id_order_state})");
        }
        $id_order = (int)$oShipment->IdSource;
        $order = new \Order($id_order);
        if (!\Validate::isLoadedObject($order)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd ładowania zamówienia! (Order #{$id_order})");
        }
        $history = new \OrderHistory();
        $history->id_order = $order->id;
        $history->id_employee = (int)\Context::getContext()->employee->id;
        $use_existings_payment = !$order->hasInvoice();
        $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);
        if ($history->add(true)) {
            if (\Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                foreach ($order->getProducts() as $product) {
                    if (\StockAvailable::dependsOnStock($product['product_id'])) {
                        \StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                    }
                }
            }
        } else {
            throw new Exceptions\SourceLoggedException($exception_prefix . "błąd aktualizacji (zapisu) statusu! (OrderHistory #new)");
        }
        return true;
    }

    protected static function testTrySendMail(
        $id_lang,
        $template,
        $subject,
        $template_vars,
        $to,
        $to_name = null,
        $from = null,
        $from_name = null,
        $file_attachment = null,
        $mode_smtp = null,
        $template_path = _PS_MAIL_DIR_,
        $die = false,
        $id_shop = null,
        $bcc = null,
        $reply_to = null
    ) {
        $exception_prefix = 'Błąd wysyłania maila o zmianie statusu zamówienia do zamawiającego: ';

        if (!$id_shop) {
            $id_shop = \Context::getContext()->shop->id;
        }

        $configuration = \Configuration::getMultiple(array(
            'PS_MAIL_METHOD',
            'PS_MAIL_SERVER',
            'PS_MAIL_SMTP_PORT',
            'PS_MAIL_TYPE'
        ), null, null, $id_shop);

        // Returns immediatly if emails are deactivated
        if ($configuration['PS_MAIL_METHOD'] == 3) {
            return true;
        }

        $theme_path = _PS_THEME_DIR_;

        // Get the path of theme by id_shop if exist
        if (is_numeric($id_shop) && $id_shop) {
            $shop = new \Shop((int)$id_shop);
            $theme_name = $shop->getTheme();

            if (_THEME_NAME_ != $theme_name) {
                $theme_path = _PS_ROOT_DIR_ . '/themes/' . $theme_name . '/';
            }
        }

        if (!isset($configuration['PS_MAIL_SMTP_PORT'])) {
            $configuration['PS_MAIL_SMTP_PORT'] = 'default';
        }

        // It would be difficult to send an e-mail if the e-mail is not valid, so this time we can die if there is a problem
        if (!is_array($to) && !\Validate::isEmail($to)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . " adres odbiorcy jest nieprawidłowy! ({$to})");
        }

        if (!\Validate::isTplName($template)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . " nieprawidłowy szablon maila! ({$template})");
        }

        if (!\Validate::isMailSubject($subject)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . " nieprawidłowy tytuł maila! ({$subject})");
        }

        if (is_array($to) && isset($to)) {
            foreach ($to as $key => $addr) {
                $addr = trim($addr);
                if (!\Validate::isEmail($addr)) {
                    throw new Exceptions\SourceLoggedException($exception_prefix . " adres odbiorcy jest nieprawidłowy! ({$addr})");
                }
            }
        }

        if ($configuration['PS_MAIL_METHOD'] == 2) {
            if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT'])) {
                throw new Exceptions\SourceLoggedException($exception_prefix . " nieprawidłowy serwer lub port SMTP!");
            }
        }

        /* Get templates content */
        $iso = \Language::getIsoById((int)$id_lang);
        if (!$iso) {
            throw new Exceptions\SourceLoggedException($exception_prefix . " nie udało się ustalić kodu ISO! (id_lang #{$id_lang})");
        }
        $iso_template = $iso . '/' . $template;

        $module_name = false;
        $override_mail = false;

        // get templatePath
        if (preg_match('#' . $shop->physical_uri . 'modules/#',
                str_replace(DIRECTORY_SEPARATOR, '/', $template_path)) && preg_match('#modules/([a-z0-9_-]+)/#ui',
                str_replace(DIRECTORY_SEPARATOR, '/', $template_path), $res)) {
            $module_name = $res[1];
        }

        if ($module_name !== false && (file_exists($theme_path . 'modules/' . $module_name . '/mails/' . $iso_template . '.txt') ||
                file_exists($theme_path . 'modules/' . $module_name . '/mails/' . $iso_template . '.html'))) {
            $template_path = $theme_path . 'modules/' . $module_name . '/mails/';
        } elseif (file_exists($theme_path . 'mails/' . $iso_template . '.txt') || file_exists($theme_path . 'mails/' . $iso_template . '.html')) {
            $template_path = $theme_path . 'mails/';
            $override_mail = true;
        }
        if (!file_exists($template_path . $iso_template . '.txt') && ($configuration['PS_MAIL_TYPE'] == \Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == \Mail::TYPE_TEXT)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . " brak szablonu maila! ({$template_path}{$iso_template}.txt)");
        } elseif (!file_exists($template_path . $iso_template . '.html') && ($configuration['PS_MAIL_TYPE'] == \Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == \Mail::TYPE_HTML)) {
            throw new Exceptions\SourceLoggedException($exception_prefix . " brak szablonu maila! ({$template_path}{$iso_template}.html)");
        }

        if ($override_mail && file_exists($template_path . $iso . '/lang.php')) {
            include_once($template_path . $iso . '/lang.php');
        } elseif ($module_name && file_exists($theme_path . 'mails/' . $iso . '/lang.php')) {
            include_once($theme_path . 'mails/' . $iso . '/lang.php');
        } elseif (file_exists(_PS_MAIL_DIR_ . $iso . '/lang.php')) {
            include_once(_PS_MAIL_DIR_ . $iso . '/lang.php');
        } else {
            throw new Exceptions\SourceLoggedException($exception_prefix . " brak pliku językowego! ({$iso})");
        }

        return true;
    }

    public static function Log($sMessage, $iSeverity = null)
    {
        $severity = ($iSeverity !== null) ? (int)$iSeverity : ConfigWrapper::Get('DefaultSeverity');
        if ($severity < 1 || $severity > 4) {
            $severity = null;
        }
        $message = '[' . ConfigWrapper::Get('ModuleName') . '] ' . $sMessage;
        $allow_duplicate = ConfigWrapper::Get('AllowDuplicateLogs');
        \PrestaShopLogger::addLog($message, $severity, null, null, null, $allow_duplicate);
    }
}

?>
