<?php

use DhlAssistant\Wrappers;

/**
 *
 */
class AdminOrdersController extends AdminOrdersControllerCore
{
    /**
     * @throws \DhlAssistant\Core\Exceptions\LoggedException
     */
    public function __construct()
    {
        require_once dirname(__FILE__) . '/../../../modules/dhlassistant/Core.php';
        parent::__construct();

        if (Wrappers\ConfigWrapper::Get('IsModuleConfigured') && Wrappers\SourceWrapper::HasUserUsingRight()) {
            $this->fields_list['module'] = [
                'title' => 'DHL',
                'callback' => 'renderDhlInfo',
                'width' => 500,
                'orderby' => false,
                'search' => false,
                'align' => 'text-center',
                'remove_onclick' => true,
            ];
        }
    }

    /**
     * @param $mSth
     * @param $params
     * @return string
     * @throws \DhlAssistant\Core\Exceptions\LoggedException
     * @throws \DhlAssistant\Core\Exceptions\SourceLoggedException
     */
    public function renderDhlInfo($mSth, $params)
    {
        $order_id = (int)$params['id_order'];

        $smarty_params = [
            'dhlassistant_shipment' => null,
            'dhlassistant_sended' => false,
            'dhlassistant_edit_link' => $this->context->link->getAdminLink('AdminModules') . '&configure=dhlassistant&page=Edit&id=' . $order_id,
        ];

        if (Wrappers\SourceWrapper::HasSourceShipment($order_id)) {
            /* @var $shipment DataModels\Shipment */
            $shipment = Wrappers\SourceWrapper::GetShipmentForSource($order_id);
            $smarty_params['dhlassistant_sended'] = $shipment->IsSended();

            if ($smarty_params['dhlassistant_sended']) {
                $smarty_params['dhlassistant_shipment'] = $shipment;
            }
        } else {
            $order = new \Order((int)$order_id);
            $address = new \Address((int)$order->id_address_delivery);
            $country = new \Country((int)$address->id_country);

            if (!in_array($country->iso_code, Wrappers\ConfigWrapper::Get('AvailableCountryCodes'))) {
                return '--';
            }
        }

        $this->context->smarty->assign($smarty_params);

        return $this->context->smarty->fetch(
            \DhlAssistant\Core::$BASEDIR . 'LocalTemplates/AdminOrdersControllerDhlInfo.tpl'
        );
    }
}

?>