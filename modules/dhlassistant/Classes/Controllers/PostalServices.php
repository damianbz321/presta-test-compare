<?php

namespace DhlAssistant\Classes\Controllers;

use DhlAssistant\Classes\Managers\DhlApiManager;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;

class PostalServices extends Models\Controller
{
    /**
     * @inheritDoc
     */
    public function Go()
    {
        $method = $this->getParam('method');
        $postCode = $this->getParam('postCode');
        if (empty($postCode)) {
            $postCode = $this->getShipperPostCode();
        }

        $message = '';
        try {
            switch ($method) {
                case 'nearestExPickup':
                    $service = $this->getNearestExPickup($postCode, $this->getParam('date'));
                    break;
                case 'postalCodeServices':
                    $service = $this->getPostalCodeServices($postCode, $this->getParam('date'));
                    break;
                default:
                    $service = null;
            }
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
        }

        echo json_encode(
            [
                'success' => (bool)$service,
                'service' => $service,
                'message' => $message
            ]
        );
    }

    protected function getPostalCodeServices($postCode, $dateString)
    {
        $postcode = preg_replace("/[^0-9]/", "", $postCode);
        $ps_shop_address = new \Address();
        $ps_shop_address->postcode = $postcode;
        $date = new \DateTime($dateString);

        $postCode = str_replace('-', '', $ps_shop_address->postcode);
        if (empty($postCode)) {
            throw new \Exception('Please set store postal code in Shop Parameters / Contact / Stores.');
        }

        $cacheKey = $postCode . '_' . $date->format('Y-m-d');
        if (array_key_exists($cacheKey, $this->postalCodeServices)) {
            return $this->postalCodeServices[$cacheKey];
        }

        $api = DhlApiManager::GetDhlApiByCode('DHL24');
        $service = Wrappers\DhlWrapper::GetPostalCodeServices(
            $api,
            Wrappers\ConfigWrapper::Get('DefaultDhlUser'),
            $postCode,
            $date
        );

        $this->postalCodeServices[$cacheKey] = $service;

        return $service;
    }

    protected function getNearestExPickup($postCode, $startDate = 'now')
    {
        $date = new \DateTime($startDate);
        if ('now' != $startDate) {
            $nowDate = new \DateTime();
            if ($nowDate > $date) {
                $date = $nowDate;
            }
        }

        $i = 0;
        while ($services = $this->getPostalCodeServices($postCode, $date->format('Y-m-d'))) {
            if (7 < $i++) {
                return false;
            }

            if ($services->ExPickupFrom != 'brak') {
                return $services;
            }

            $date->add(new \DateInterval('P1D'));
        }

        return false;
    }

    protected function getParam($key)
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        return null;
    }

    protected function getShipperPostCode()
    {
        return Wrappers\ConfigWrapper::Get('HasDefaultShipperPreset') ?
            Wrappers\ConfigWrapper::Get('DefaultShipperPreset')->Shipper->Address->PostalCode : null;
    }

}
