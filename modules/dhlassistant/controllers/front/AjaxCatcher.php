<?php
require_once dirname(__FILE__) . '/../../Core.php';

use DhlAssistant\Core;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

/**
 *
 */
class dhlassistantAjaxCatcherModuleFrontController extends ModuleFrontController
{
    /**
     *
     */
    public function initContent()
    {
        parent::initContent();

        $result = 'Error';

        if ($this->_handleAjaxData()) {
            $result = 'Success';
        }

        echo json_encode($result);
        exit;
    }

    /**
     * @return bool
     */
    private function _handleAjaxData()
    {
        $params_obj = new DataModels\SourceOrderAdditionalParams();

        try {
            if (!Wrappers\ConfigWrapper::Get('IsModuleConfigured')) {
                return false;
            }

            $cart = \Context::getContext()->cart;

            if (!\Validate::isLoadedObject($cart)) {
                return $this->_tryDeleteParamsObject(
                    $params_obj,
                    'AjaxCatcher: błąd ładowania obiektu Cart'
                );
            }

            $cart_id = (int)$cart->id;
            $address = new \Address((int)$cart->id_address_delivery);

            if (!\Validate::isLoadedObject($address)) {
                return $this->_tryDeleteParamsObject(
                    $params_obj,
                    'AjaxCatcher: błąd ładowania obiektu Address #' . ((int)$cart->id_address_delivery)
                );
            }

            $country = new \Country((int)$address->id_country);
            if (!\Validate::isLoadedObject($country)) {
                return $this->_tryDeleteParamsObject(
                    $params_obj,
                    'AjaxCatcher: błąd ładowania obiektu Country #' . (int)$address->id_country
                );
            }

            $params_obj = Wrappers\SourceWrapper::GetSourceOrderAdditionalParams((int)$cart->id, $country->iso_code);
            $params_obj->IdSourceObject = $cart_id;
            $params_obj->CountryCode = $country->iso_code;
            $params_obj->SendToParcelShop = isset($_POST['ParcelShop']);
            $params_obj->SendToParcelLocker = isset($_POST['ParcelLocker']) && !$params_obj->SendToParcelShop;
            $params_obj->ParcelIdent = '';
            $params_obj->Postnummer = '';
            $params_obj->ParcelPostalCode = '';

            if ($params_obj->SendToParcelShop || $params_obj->SendToParcelLocker) {
                if (!isset($_POST['ParcelIdent'])) {
                    return $this->_tryDeleteParamsObject($params_obj);
                }

                $parcel_ident = $_POST['ParcelIdent'];

                if (strlen($parcel_ident) == 0) {
                    return $this->_tryDeleteParamsObject($params_obj);
                }

                $params_obj->ParcelIdent = $parcel_ident;

                $dcs = Wrappers\SourceWrapper::DetermineDhlCountryService(
                    Wrappers\ConfigWrapper::Get('DefaultDhlUser'),
                    $country->iso_code,
                    $params_obj
                );

                if ($params_obj->SendToParcelLocker && $dcs->RequirePostnummerForParcelLocker && isset($_POST['Postnummer'])) {
                    $postnummer = $_POST['Postnummer'];
                    if (!Core\Validators::IsInt($postnummer) || strlen($parcel_ident) == 0 || strlen($parcel_ident) > 10) {
                        return $this->_tryDeleteParamsObject($params_obj);
                    }
                    $params_obj->Postnummer = $postnummer;
                }

                if ($dcs->RequirePostalCodeForParcel) {
                    if (!isset($_POST['ParcelPostalCode'])) {
                        return $this->_tryDeleteParamsObject($params_obj);
                    }

                    $parcel_postal_code = str_replace('-', '', $_POST['ParcelPostalCode']);

                    if (strlen($parcel_postal_code) == 0 || strlen($parcel_postal_code) > $dcs->PostalCodeMaxLength) {
                        return $this->_tryDeleteParamsObject($params_obj);
                    }

                    $params_obj->ParcelPostalCode = $parcel_postal_code;
                }
            }

            Wrappers\DbWrapper::Save($params_obj);
        } catch (\Exception $Ex) {
            return $this->_tryDeleteParamsObject($params_obj);
        }

        return true;
    }

    /**
     * @param DataModels\SourceOrderAdditionalParams $oObject
     * @param null $sLogMessage
     * @return false
     */
    private function _tryDeleteParamsObject(DataModels\SourceOrderAdditionalParams $oObject, $sLogMessage = null)
    {
        if ($sLogMessage !== null) {
            Wrappers\SourceWrapper::Log($sLogMessage);
        }

        try {
            if ($oObject->GetObjectId()) {
                Wrappers\DbWrapper::Delete($oObject);
            }
        } catch (\Exception $Ex) {
        }

        return false;
    }
}

?>