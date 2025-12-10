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

class PmInpostPaczkomatyOrderController extends ModuleAdminController
{
    public $_prefix = 'PMINPOSTPACZKOMATY';

    public function __construct()
    {
        parent::__construct();
        $this->display_column_left = false;
        $this->context = Context::getContext();
        if (Configuration::get($this->_prefix.'_SHIPX')) {
            $this->api = InpostApiShipx::getInstance();
        } else {
            $this->api = InpostApi::getInstance();
        }
        $this->module_pm = Module::getInstanceByName('pminpostpaczkomaty');
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<=')) {
            $this->version_ps = '1.6';
        } else {
            $this->version_ps = '1.7';
        }
    }

    public function initContent()
    {
        if (Tools::isSubmit('printlabel')) {
            $this->printLabel();
        } elseif (Tools::isSubmit('send')) {
            $this->send();
        } elseif (Tools::isSubmit('send2')) {
            $this->send2();
        } elseif (Tools::isSubmit('sendandprint')) {
            $this->sendAndPrint();
        } elseif (Tools::isSubmit('refreshstatus')) {
            $this->refreshStatus();
        } elseif (Tools::isSubmit('createnew')) {
            $this->createNewList();
        }
    }

    private function createList(
        $mail,
        $phone,
        $selected,
        $size,
        $innuranceAmount,
        $cod,
        $paczkomat_nadania,
        $reference,
        $end_of_week
    ) {
        $phone = $this->module->trimPhone($phone);
        if (Configuration::get($this->_prefix.'_SHIPX')) {
            $api = InpostApiShipx::getInstance();
            if ($paczkomat_nadania == '') {
                $paczkomat_nadania = null;
            }
        } else {
            $api = $this->api;
        }
        $result = $api->createList(
            $mail,
            $phone,
            $selected,
            $size,
            $innuranceAmount,
            $cod,
            $paczkomat_nadania,
            $reference,
            $end_of_week
        );

        if (!Configuration::get($this->_prefix.'_SHIPX')) {
            if ($result) {
                $packCode = (string)$result->pack->packcode;
            } else {
                $this->_errors = $api->getErrors();
                return false;
            }
            return $packCode;
        }
        if ($result['status'] != 400 && $result['status'] != 401) {
            sleep(1);
            $trackingNumber = $api->getTrackingNumber($result['id']);
            if ($trackingNumber === false) {
                return false;
            }
            return array($result['id'], $trackingNumber);
        } else {
            return false;
        }
    }

    public function payforpack()
    {
        $order = new Order(Tools::getValue('id_order'));
        $id_cart = (int)$order->id_cart;
        $paczkomatyList = PaczkomatyList::getByIdCart($id_cart);
        if ($paczkomatyList->status == 'Opłacona') {
            return true;
        }

        $shippingNumber = $paczkomatyList->nr_listu;
        $order = new Order(Tools::getValue('id_order'));
        $id_order_carrier = (int)$order->getIdOrderCarrier();
        $order_carrier = new OrderCarrier($id_order_carrier);

        $order->shipping_number = $shippingNumber;
        $order->update();

        $order_carrier->tracking_number = pSQL($shippingNumber);
        if ($order_carrier->update()) {
            if ($this->version_ps == '1.7') {
                try {
                    $order_carrier->sendInTransitEmail($order);
                } catch (Exception $exp) {
                    $exp->getMessage();
                }
            } else {
                $customer = new Customer((int)$order->id_customer);
                $carrier = new Carrier((int)$order->id_carrier, $order->id_lang);

                $templateVars = array(
                    '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                    '{firstname}' => $customer->firstname,
                    '{lastname}' => $customer->lastname,
                    '{id_order}' => $order->id,
                    '{shipping_number}' => $order->shipping_number,
                    '{order_name}' => $order->getUniqReference(),
                    'meta_products' => ''
                );
                if (@Mail::Send(
                    (int)$order->id_lang,
                    'in_transit',
                    Mail::l('Package in transit', (int)$order->id_lang),
                    $templateVars,
                    $customer->email,
                    $customer->firstname.' '.$customer->lastname,
                    null,
                    null,
                    null,
                    null,
                    _PS_MAIL_DIR_,
                    true,
                    (int)$order->id_shop
                )) {
                    Hook::exec(
                        'actionAdminOrdersTrackingNumberUpdate',
                        array(
                        'order' => $order,
                        'customer' => $customer,
                        'carrier' => $carrier
                        ),
                        null,
                        false,
                        true,
                        false,
                        $order->id_shop
                    );
                }
            }
        }

        if (Configuration::get($this->_prefix.'_OS')) {
            $status = (int)Configuration::get($this->_prefix.'_STATUS');
            if ($status != $order->getCurrentState()) {
                $order->setCurrentState($status);
            }
        }

        $sql = 'UPDATE '._DB_PREFIX_.'order_carrier SET tracking_number = "'.$shippingNumber.'"
        WHERE id_order ='.(int)$order->id;
        Db::getInstance()->execute($sql);

        $paczkomatyList->status = 'Opłacona';
        $paczkomatyList->save();
        return true;
    }

    private function getsticker($shippingNumber)
    {
        $api = $this->api;
        $api->getsticker($shippingNumber);
    }

    public function genereateReference()
    {
        $order = new Order(Tools::getValue('id_order'));
        $referenceGen = Configuration::get($this->_prefix.'_REFERENCE');
        $id_order = $order->id;
        $address = new Address($order->id_address_delivery);
        $firstname = $address->firstname;
        $lastname = $address->lastname;
        $order_reference = $order->reference;
        $products = $order->getProducts();
        $productsreference = array();
        foreach ($products as $product) {
            if (trim($product['product_reference']) != '') {
                $productsreference[] = $product['product_reference'];
            }
        }
        $productsreference = implode(' ', $productsreference);
        $reference = str_replace(
            array(
                '{id_order}',
                '{reference}',
                '{productsreference}'
            ),
            array(
                $id_order,
                $order_reference,
                $productsreference
            ),
            $referenceGen
        );
        $reference = str_replace(
            array(
                '{firstname}',
                '{lastname}'
            ),
            array(
                $firstname,
                $lastname
            ),
            $reference
        );

        $reference = trim(Tools::strtoupper($reference));
        if (Tools::strlen($reference)>100) {
            $reference = mb_substr($reference, 0, 100).'@';
        }
        return $reference;
    }

    public function checkAccess()
    {
        return true;
    }

    public function translate(&$error)
    {
        $transin = array();
        $transout = array();

        $transin[] = 'prepared';
        $transout[] = $this->l('Przesyłka utworzona');

        $transin[] = 'stored';
        $transout[] = $this->l('W Paczkomacie, w POP lub punkcie sieci Partnerskiej');

        $transin[] = 'There are some validation error';
        $transout[] = $this->l('There are some validation error');

        $transin[] = 'Check details object for more info';
        $transout[] = $this->l('Check details object for more info');

        $transin[] = 'custom_attributes';
        $transout[] = $this->l('Fields');

        $transin[] = 'target_point';
        $transout[] = $this->l('Target point');

        $transin[] = 'does_not_exist';
        $transout[] = $this->l('does not exist');
        
        $transin[] = 'Saturday & Sunday parcel delivery is not available.';
        $transout[] = 'Opcja dostarczania paczek do Paczkomatów w weekend. '.
        'Usługa dostępna od czwartku godz. 20:00 do soboty godz. 13:00.';

        $transin[] = 'shipments:';
        $transout[] = 'przesyłki:';
        
        $transin[] = 'end_of_week_collection: ';
        $transout[] = 'Proszę zaznaczyć ';

        $transin[] = 'invalid_end_of_week_collection';
        $transout[] = ': Koniec tygodnia: Nie';

        $transin[] = 'receiver:';
        $transout[] = $this->l('Receiver: ');

        $transin[] = 'email:';
        $transout[] = $this->l('Email: ');

        $transin[] = 'required';
        $transout[] = $this->l('required');


        $transin[] = 'phone: invalid';
        $transout[] = $this->l('Błędny nr telefonu');

        $transin[] = 'sender:';
        $transout[] = $this->l('Sender:');

        $transin[] = 'No route matches';
        $transout[] = $this->l('No route matches');

        $transin[] = 'E-mail invalid';
        $transout[] = $this->l('Błędny adres E-mail');

        $transin[] = 'Tracking information about';
        $transout[] = $this->l('Tracking information about');

        $transin[] = 'shipment has not been found';
        $transout[] = $this->l('shipment has not been found');

        $transin[] = 'created';
        $transout[] = $this->l('Przesyłka utworzona.');

        $transin[] = 'Prepared';
        $transout[] = $this->l('Przesyłka utworzona.');

        $transin[] = 'Prepared';
        $transout[] = $this->l('Przesyłka utworzona.');

        $transin[] = 'Sent';
        $transout[] ='Przyjęta w oddziale';

        $transin[] = 'InTransit';
        $transout[] ='W drodze do odbiorcy';

        $transin[] = 'Stored';
        $transout[] ='W Paczkomacie, w POP lub punkcie sieci Partnerskiej';

        $transin[] = 'Avizo';
        $transout[] ='Ponowne awizo';

        $transin[] = 'CustomerDelivering';
        $transout[] ='Do nadania w Paczkomacie, POP lub punkcie sieci Partnerskiej';

        $transin[] = 'CustomerStored';
        $transout[] ='Umieszczona w Paczkomacie, POP lub punkcie sieci Partnerskiej';

        $transin[] = 'LabelExpired';
        $transout[] ='Etykieta przeterminowana';

        $transin[] = 'Expired';
        $transout[] ='Nie odebrana';

        $transin[] = 'Delivered';
        $transout[] ='Dostarczona';

        $transin[] = 'RetunedToAgency';
        $transout[] ='liveredToAgency - Przekazana do oddziału';

        $transin[] = 'Cancelled';
        $transout[] ='Anulowana';

        $transin[] = 'Claimed';
        $transout[] ='Przyjęto zgłoszenie reklamacyjne';

        $transin[] = 'ClaimProcessed';
        $transout[] ='Rozpatrzono zgłoszenie reklamacyjne';

        $transin[] = 'CustomerSent';
        $transout[] ='Wyjęta przez kuriera z Paczkomatu, odebrana przez kuriera z POP lub z punktu sieci Partnerskiej';

        $transin[] = 'ReturnedToSortingCenter';
        $transout[] ='DeliveredToSortingCenter - W drodze do nadawcy';

        $transin[] = 'ReturnedToSender';
        $transout[] ='Zwrócono nadawcy';

        $transin[] = 'LabelDestroyed';
        $transout[] ='Etykieta nieczytelna lub jej brak';

        $transin[] = 'Missing';
        $transout[] ='Zagubiono';

        $transin[] = 'NotDelivered';
        $transout[] ='Nie dostarczono';


        $transin[] = 'offers_prepared';
        $transout[] = $this->l('Przygotowano oferty.');

        $transin[] = 'offer_selected';
        $transout[] = $this->l('Oferta wybrana.');

        $transin[] = 'confirmed';
        $transout[] = $this->l('Przygotowana przez Nadawcę.');

        $transin[] = 'dispatched_by_sender';
        $transout[] = $this->l('Paczka nadana w paczkomacie.');

        $transin[] = 'collected_from_sender';
        $transout[] = $this->l('Odebrana od klienta.');

        $transin[] = 'taken_by_courier';
        $transout[] = $this->l('Odebrana od Nadawcy.');

        $transin[] = 'adopted_at_source_branch';
        $transout[] = $this->l('Przyjęta w oddziale InPost.');

        $transin[] = 'sent_from_source_branch';
        $transout[] = $this->l('W trasie.');

        $transin[] = 'ready_to_pickup_from_pok';
        $transout[] = $this->l('Czeka na odbiór w Punkcie Obsługi Paczek.');

        $transin[] = 'ready_to_pickup_from_pok_registered';
        $transout[] = $this->l('Czeka na odbiór w Punkcie Obsługi Klienta.');

        $transin[] = 'oversized';
        $transout[] = $this->l('Przesyłka ponadgabarytowa.');

        $transin[] = 'adopted_at_sorting_center';
        $transout[] = $this->l('Przyjęta w Sortowni.');

        $transin[] = 'sent_from_sorting_center';
        $transout[] = $this->l('Wysłana z Sortowni.');

        $transin[] = 'adopted_at_target_branch';
        $transout[] = $this->l('Przyjęta w Oddziale Docelowym.');

        $transin[] = 'out_for_delivery';
        $transout[] = $this->l('Przekazano do doręczenia.');

        $transin[] = 'ready_to_pickup';
        $transout[] = $this->l('Umieszczona w Paczkomacie (odbiorczym).');

        $transin[] = 'pickup_reminder_sent';
        $transout[] = $this->l('Przypomnienie o czekającej paczce.');

        $transin[] = 'delivered';
        $transout[] = $this->l('Dostarczona.');

        $transin[] = 'pickup_time_expired';
        $transout[] = $this->l('Upłynął termin odbioru.');

        $transin[] = 'avizo';
        $transout[] = $this->l('Powrót do oddziału.');

        $transin[] = 'claimed';
        $transout[] = $this->l('Zareklamowana w Paczkomacie.');

        $transin[] = 'returned_to_sender';
        $transout[] = $this->l('Zwrot do nadawcy.');

        $transin[] = 'canceled';
        $transout[] = $this->l('Anulowano etykietę.');

        $transin[] = 'other';
        $transout[] = $this->l('Inny status.');

        $transin[] = 'dispatched_by_sender_to_pok';
        $transout[] = $this->l('Nadana w Punkcie Obsługi Klienta.');

        $transin[] = 'out_for_delivery_to_address';
        $transout[] = $this->l('W doręczeniu.');

        $transin[] = 'pickup_reminder_sent_address';
        $transout[] = $this->l('W doręczeniu.');

        $transin[] = 'rejected_by_receiver';
        $transout[] = $this->l('Odmowa przyjęcia.');

        $transin[] = 'undelivered_wrong_address';
        $transout[] = $this->l('Brak możliwości doręczenia.');

        $transin[] = 'undelivered_incomplete_address';
        $transout[] = $this->l('Brak możliwości doręczenia.');

        $transin[] = 'undelivered_unknown_receiver';
        $transout[] = $this->l('Brak możliwości doręczenia.');

        $transin[] = 'undelivered_cod_cash_receiver';
        $transout[] = $this->l('Brak możliwości doręczenia.');

        $transin[] = 'taken_by_courier_from_pok';
        $transout[] = $this->l('W drodze do oddziału nadawczego InPost.');

        $transin[] = 'undelivered';
        $transout[] = $this->l('Przekazanie do magazynu przesyłek niedoręczalnych.');

        $transin[] = 'return_pickup_confirmation_to_sender';
        $transout[] = $this->l('Przygotowano dokumenty zwrotne.');

        $transin[] = 'ready_to_pickup_from_branch';
        $transout[] = $this->l('Paczka nieodebrana – czeka w Oddziale');

        $transin[] = 'delay_in_delivery';
        $transout[] = $this->l('Możliwe opóźnienie doręczenia.');

        $transin[] = 'redirect_to_box';
        $transout[] = $this->l('Przekierowano do Paczkomatu.');
        $transin[] = 'canceled_redirect_to_box';
        $transout[] = $this->l('Anulowano przekierowanie.');

        $transin[] = 'readdressed';
        $transout[] = $this->l('Przekierowano na inny adres.');

        $transin[] = 'undelivered_no_mailbox';
        $transout[] = $this->l('Brak możliwości doręczenia.');

        $transin[] = 'undelivered_not_live_address';
        $transout[] = $this->l('Brak możliwości doręczenia.');

        $transin[] = 'undelivered_lack_of_access_letterbox';
        $transout[] = $this->l('Brak możliwości doręczenia.');

        $transin[] = 'missing';
        $transout[] = $this->l('translation missing: pl_PL.statuses.missing.title');

        $transin[] = 'stack_in_customer_service_point';
        $transout[] = $this->l('Paczka magazynowana w POP.');

        $transin[] = 'stack_parcel_pickup_time_expired';
        $transout[] = $this->l('Upłynął termin odbioru paczki magazynowanej.');

        $transin[] = 'unstack_from_customer_service_point';
        $transout[] = $this->l('W drodze do wybranego paczkomatu.');

        $transin[] = 'courier_avizo_in_customer_service_point';
        $transout[] = $this->l('Oczekuje na odbiór.');

        $transin[] = 'taken_by_courier_from_customer_service_point';
        $transout[] = $this->l('Zwrócona do nadawcy.');

        $transin[] = 'address';
        $transout[] = $this->l('Adres');

        $transin[] = 'street';
        $transout[] = $this->l('ulica');

        $transin[] = 'building_number';
        $transout[] = $this->l('numer budynku');

        $transin[] = 'city';
        $transout[] = $this->l('miasto');

        $transin[] = 'post_code';
        $transout[] = $this->l('kod pocztowy');

        $transin[] = 'offered';
        $transout[] = $this->l('Wystąpił błąd, API ustawione w trybie ofertowym dla tego klucza.').' '.
        $this->l('Sprawdź ustawienia nadawcy - czy wszystko jest poprawnie ustawione').' '.
        $this->l('Jeżeli błąd będzie się powtarzał wyślij maila do InPost z informacją:').' '.
        $this->l('Podczas generowania etykiet poprzez API zwracany jest status offered');

        $transin[] = 'trackempty';
        $transout[] = $this->l('Przesyłka utworzona, jednak nie udało się pobrać numeru listu przewozowego.');

        $transin[] = 'Resource not found.';
        $transout[] = $this->l('Przesyłka nieodnaleziona.');


        $error = str_replace($transin, $transout, $error);
        return $error;
    }

    public function parseErrors(&$errors)
    {
        if (is_array($errors)) {
            foreach ($errors as &$item) {
                $this->translate($item);
            }
        } else {
            $this->translate($errors);
        }
        return $errors;
    }

    public function printLabel()
    {
        $api = $this->api;
        $o = new Order(Tools::getValue('id_order'));
        $this->payforpack();
        $id_cart = $o->id_cart;
        $shippingNumber = PaczkomatyList::getNrListuByIdCart((int)$id_cart);
        $size_format = PaczkomatyList::getSizeFormat();

        $cache = dirname(__FILE__).'/../../cache/'.$shippingNumber.$size_format;
        if (file_exists($cache)) {
            $file = Tools::file_get_contents($cache);
        } else {
            $file = $api->getSticker($shippingNumber);
            if ($file) {
                file_put_contents($cache, $file);
            }
        }
        if (Tools::isSubmit('check')) {
            if ($file == false) {
                $this->displayAjaxError($api->getErrors());
            } else {
                $result = Tools::jsonEncode(
                    array(
                        'error' => false,
                        'confirmation' => $this->l('Download Label')
                    )
                );
                die($result);
            }
        }
        $format = Configuration::get($this->_prefix.'_LABEL_FORMAT');
        if (Tools::isSubmit('format')) {
            $format = Tools::getValue('format');
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        if ($shippingNumber == '') {
            $shippingNumber = Tools::getValue('id_order');
        }
        
        header('Content-Disposition: attachment; filename='.$shippingNumber.'.'.$format);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        die($file);
    }

    public function send()
    {
        $o = new Order(Tools::getValue('id_order'));
        $id_cart = $o->id_cart;
        $spost = pSql(serialize($_POST));
        $size = Tools::getValue('pminpostorder_size');
        $innuranceAmount = '';
        $cod = Tools::getValue('pminpostorder_pobranie');
        if ($cod) {
            $cod = (float)$o->total_paid_tax_incl;
            if (Tools::isSubmit('pminpostorder_pobranie_value')) {
                if ((float)Tools::getValue('pminpostorder_pobranie_value') == 0) {
                    $result = Tools::jsonEncode(
                        array(
                        'error' => $this->l('Please check COD value')
                        )
                    );
                    die($result);
                }
                $cod = (float)Tools::getValue('pminpostorder_pobranie_value');
            } else {
                $cod = (float)Tools::getValue('pminpostorder_pobranie_value');
            }
        }
        if (!$cod) {
            if (Configuration::get($this->_prefix.'_ISSURANCE_BW')) {
                $innuranceAmount = (float)$o->total_paid_tax_incl;
                if ($cod > $innuranceAmount) {
                    $innuranceAmount = $cod;
                }
                if ($innuranceAmount >= 20000) {
                    $innuranceAmount = 20000;
                }
            }
        } else {
            if (Configuration::get($this->_prefix.'_ISSURANCE_COD')) {
                $innuranceAmount = (float)$o->total_paid_tax_incl;
                if ($cod > $innuranceAmount) {
                    $innuranceAmount = $cod;
                }
                if ($innuranceAmount >= 20000) {
                    $innuranceAmount = 20000;
                }
            }
        }
        if (Tools::getValue('pminpostorder_machine')) {
            $paczkomat_nadania = Tools::getValue('pminpostorder_selected_from');
        } else {
            $paczkomat_nadania = '';
        }
        $reference = Tools::getValue('pminpostorder_reference');
        if ($cod && Configuration::get($this->_prefix.'_ADD_COD')) {
            $reference .= $cod;
        }
        $end_of_week = Tools::getValue('pminpostorder_eof') ? 'true' : 'false';
        $packCode = $this->createList(
            Tools::getValue('pminpostorder_email'),
            $this->module_pm->trimPhone(Tools::getValue('pminpostorder_phone')),
            Tools::getValue('pminpostorder_selected'),
            $size,
            $innuranceAmount,
            $cod,
            $paczkomat_nadania,
            $reference,
            $end_of_week
        );
        if (!$packCode) {
            $api = $this->api;
            $this->displayAjaxError($api->getErrors());
        }
        $paczkomatyList = PaczkomatyList::getByIdCart($id_cart);
        if ($packCode !== false && is_array($packCode)) {
            $id = pSql($packCode[0]);
            $packCode = $packCode[1];
            $paczkomatyList->id_pack = $id;
            $paczkomatyList->nr_listu = $packCode;
            $paczkomatyList->machine = Tools::getValue('pminpostorder_selected');
            $paczkomatyList->post_info = $spost;
            $paczkomatyList->save();
        } else {
            $paczkomatyList->nr_listu = $packCode;
            $paczkomatyList->machine = Tools::getValue('pminpostorder_selected');
            $paczkomatyList->post_info = $spost;
            $paczkomatyList->save();
        }
        $api = $this->api;
        $id = $paczkomatyList->id;
        $result = Tools::jsonEncode(
            array(
                'error' => false,
                'packcode' => $packCode,
                'link' => $this->context->link->getAdminLink('PmInpostPaczkomatyList').'&id_orders='.$id.'&vieworders',
                'link2' => $this->context->link->getAdminLink('PmInpostPaczkomatyOrder').
                '&printlabel=1&id_order='.$o->id.'&id='.$id,
                'confirmation' => $this->l('Delivery created')
            )
        );
        die($result);
    }

    public function send2()
    {
        $order = new Order(Tools::getValue('id_order'));
        $id_cart = $order->id_cart;
        $config = array();
        $customer = new Customer($order->id_customer);
        $address = new Address($order->id_address_delivery);
        $config['pminpostorder_email'] = $customer->email;
        $config['pminpostorder_phone'] = $address->phone_mobile;
        if ($config['pminpostorder_phone'] == '') {
            $config['pminpostorder_phone'] = $address->phone;
        }
        $module = Module::getInstanceByName('pminpostpaczkomaty');
        
        if ($module->isSelectedCarrierCod($order->id_carrier)) {
            $config['pminpostorder_size'] = Configuration::get($this->_prefix.'_SIZE_COD');
            $config['pminpostorder_pobranie'] = 1;
            $config['pminpostorder_ubezpieczenie'] = Configuration::get($this->_prefix.'_ISSURANCE_COD');
            $config['pminpostorder_eof'] = 0;
        } elseif ($module->isSelectedCarrierCod($order->id_carrier)) {
            $config['pminpostorder_pobranie'] = 0;
            $config['pminpostorder_size'] = Configuration::get($this->_prefix.'_SIZE_BW');
            $config['pminpostorder_ubezpieczenie'] = Configuration::get($this->_prefix.'_ISSURANCE_BW');
            $config['pminpostorder_eof'] = 0;
        } elseif ($module->isSelectedCarrierBwEof($order->id_carrier)) {
            $config['pminpostorder_eof'] = 1;
            $config['pminpostorder_pobranie'] = 0;
            $config['pminpostorder_size'] = Configuration::get($this->_prefix.'_SIZE_EOF');
            $config['pminpostorder_ubezpieczenie'] = Configuration::get($this->_prefix.'_ISSURANCE_EOF');
        } elseif ($module->isSelectedCarrierCodEof($order->id_carrier)) {
            $config['pminpostorder_pobranie'] = 1;
            $config['pminpostorder_size'] = Configuration::get($this->_prefix.'_SIZE_EOF_CODEOF');
            $config['pminpostorder_ubezpieczenie'] = Configuration::get($this->_prefix.'_ISSURANCE_EOF_COD');
            $config['pminpostorder_eof'] = 1;
        } else {
            $config['pminpostorder_pobranie'] = 0;
            $config['pminpostorder_size'] = Configuration::get($this->_prefix.'_SIZE_BW');
            $config['pminpostorder_ubezpieczenie'] = Configuration::get($this->_prefix.'_ISSURANCE_BW');
            $config['pminpostorder_eof'] = 0;
        }

        if ($order->module == 'pm_cashondelivery') {
            $config['pminpostorder_pobranie'] = 1;
            $config['pminpostorder_ubezpieczenie'] = Configuration::get($this->_prefix.'_ISSURANCE_COD');
        }
        $cod = $config['pminpostorder_pobranie'];
        $config['pminpostorder_selected_from'] = Configuration::get($this->_prefix.'_MACHINE');
        $config['pminpostorder_machine'] = Configuration::get($this->_prefix.'_SHIPPING_METHOD');
        $config['pminpostorder_reference'] = $this->genereateReference();

        $size = Tools::getValue('pminpostorder_size');
        $innuranceAmount = 0;

        $machine = PaczkomatyList::getSelectedMachine($id_cart);
        if ($machine) {
            $config['pminpostorder_selected'] = $machine;
        } else {
            $config['pminpostorder_selected'] = '';
        }

        $post_info = PaczkomatyList::getPostInfoByIdCart($id_cart);
        if ($post_info) {
            $post_info = unserialize($post_info);
            $config = array_merge($config, $post_info);
        }
        $spost = pSql(serialize($config));
        if (!$cod) {
            if (Configuration::get($this->_prefix.'_ISSURANCE_BW')) {
                $innuranceAmount = (float)$order->total_paid_tax_incl;
                if ($innuranceAmount >= 20000) {
                    $innuranceAmount = 20000;
                }
            }
        } else {
            $innuranceAmount = (float)$order->total_paid_tax_incl;
            if ($innuranceAmount >= 20000) {
                $innuranceAmount = 20000;
            }
        }
        if ($config['pminpostorder_machine']) {
            $paczkomat_nadania = $config['pminpostorder_selected_from'];
        } else {
            $paczkomat_nadania = '';
        }

        if ($cod) {
            $cod = (float)$order->total_paid_tax_incl;
        }

        $reference = $config['pminpostorder_reference'];
        $packCode = $this->createList(
            $config['pminpostorder_email'],
            $this->module_pm->trimPhone($config['pminpostorder_phone']),
            $config['pminpostorder_selected'],
            $size,
            $innuranceAmount,
            $cod,
            $paczkomat_nadania,
            $reference,
            $config['pminpostorder_eof']
        );

        if (!$packCode) {
            $api = $this->api;
            $this->displayAjaxError($api->getErrors());
        }
        $paczkomatyList = PaczkomatyList::getByIdCart($id_cart);
        if ($packCode !== false && is_array($packCode)) {
            $id = pSql($packCode[0]);
            $packCode = $packCode[1];
            if ($paczkomatyList->id) {
                $paczkomatyList->id_pack = $id;
                $paczkomatyList->nr_listu = $packCode;
                $paczkomatyList->post_info = $spost;
                $paczkomatyList->save();
            } else {
                $paczkomatyList->id_pack = $id;
                $paczkomatyList->nr_listu = $packCode;
                $paczkomatyList->machine = pSql(Tools::getValue('machine'));
                $paczkomatyList->post_info = $spost;
                $paczkomatyList->save();
            }
        } else {
            if ($paczkomatyList->id) {
                $paczkomatyList->nr_listu = $packCode;
                $paczkomatyList->post_info = $spost;
                $paczkomatyList->save();
            } else {
                $paczkomatyList->nr_listu = $packCode;
                $paczkomatyList->machine = pSql(Tools::getValue('machine'));
                $paczkomatyList->post_info = $spost;
                $paczkomatyList->save();
            }
        }
        $id = $paczkomatyList->id;
        $api = $this->api;
        $result = Tools::jsonEncode(
            array(
                'error' => false,
                'packcode' => $packCode,
                'confirmation' => $this->l('Delivery created'),
                'link' => $this->context->link->getAdminLink('PmInpostPaczkomatyList').'&id_orders='.$id.'&vieworders',
                'link2' => $this->context->link->getAdminLink('PmInpostPaczkomatyOrder').
                '&printlabel=1&id_order='.$order->id.'&id='.$id,
                'id_order' => $order->id,
            )
        );
        die($result);
    }

    public function sendAndPrint()
    {
        $o = new Order(Tools::getValue('id_order'));

        $id_cart = $o->id_cart;
        $spost = pSql(serialize($_POST));

        $size = Tools::getValue('pminpostorder_size');
        $innuranceAmount = '';
        $cod = 0;
        if (Tools::isSubmit('pminpostorder_pobranie_value') && Tools::getValue('pminpostorder_pobranie')) {
            $cod = Tools::getValue('pminpostorder_pobranie_value');
            if ((float)Tools::getValue('pminpostorder_pobranie_value') == 0) {
                $result = Tools::jsonEncode(
                    array(
                    'error' => $this->l('Please check COD value')
                    )
                );
                die($result);
            }
        } elseif (Tools::getValue('pminpostorder_pobranie')) {
            $cod = (float)Tools::getValue('pminpostorder_pobranie_value');
        }
        if (!$cod) {
            if (Configuration::get($this->_prefix.'_ISSURANCE_BW')) {
                $innuranceAmount = (float)$o->total_paid_tax_incl;
                if ($cod > $innuranceAmount) {
                    $innuranceAmount = $cod;
                }
                if ($innuranceAmount >= 20000) {
                    $innuranceAmount = 20000;
                }
            }
        } else {
            if (Configuration::get($this->_prefix.'_ISSURANCE_COD')) {
                $innuranceAmount = (float)$o->total_paid_tax_incl;
                if ($cod > $innuranceAmount) {
                    $innuranceAmount = $cod;
                }
                if ($innuranceAmount >= 20000) {
                    $innuranceAmount = 20000;
                }
            }
        }
        if (Tools::getValue('pminpostorder_machine')) {
            $paczkomat_nadania = Tools::getValue('pminpostorder_selected_from');
        } else {
            $paczkomat_nadania = '';
        }
        $reference = Tools::getValue('pminpostorder_reference');
        if ($cod && Configuration::get($this->_prefix.'_ADD_COD')) {
            $reference .= $cod;
        }
        $end_of_week = Tools::getValue('pminpostorder_eof') ? 'true' : 'false';
        $packCode = $this->createList(
            Tools::getValue('pminpostorder_email'),
            Tools::getValue('pminpostorder_phone'),
            Tools::getValue('pminpostorder_selected'),
            $size,
            $innuranceAmount,
            $cod,
            $paczkomat_nadania,
            $reference,
            $end_of_week
        );
        if (!$packCode) {
            $api = $this->api;
            $this->displayAjaxError($api->getErrors());
        }

        $paczkomatyList = PaczkomatyList::getByIdCart($id_cart);

        if ($packCode !== false && is_array($packCode)) {
            $id = pSql($packCode[0]);
            $packCode = $packCode[1];
            if ($paczkomatyList->id) {
                $paczkomatyList->id_pack = $id;
                $paczkomatyList->nr_listu = $packCode;
                $paczkomatyList->post_info = $spost;
                $paczkomatyList->save();
            } else {
                $paczkomatyList->id_pack = $id;
                $paczkomatyList->nr_listu = $packCode;
                $paczkomatyList->post_info = $spost;
                $paczkomatyList->machine = pSql(Tools::getValue('machine'));
                $paczkomatyList->save();
            }
        } else {
            if ($paczkomatyList->id) {
                $paczkomatyList->nr_listu = $packCode;
                $paczkomatyList->post_info = $spost;
                $paczkomatyList->save();
            } else {
                $paczkomatyList->nr_listu = $packCode;
                $paczkomatyList->machine = pSql(Tools::getValue('machine'));
                $paczkomatyList->post_info = $spost;
                $paczkomatyList->save();
            }
        }
        $api = $this->api;
        $this->payforpack();
        $api = $this->api;
        $id = $paczkomatyList->id;
        $result = Tools::jsonEncode(
            array(
                'error' => false,
                'packcode' => $packCode,
                'confirmation' => $this->l('Delivery created'),
                'link' => $this->context->link->getAdminLink('PmInpostPaczkomatyList').'&id_orders='.$id.'&vieworders',
                'link2' => $this->context->link->getAdminLink('PmInpostPaczkomatyOrder').
                '&printlabel=1&id_order='.$o->id.'&id='.$id,
            )
        );
        die($result);
    }

    public function displayAjaxError($message)
    {
        $result = Tools::jsonEncode(
            array(
                'error' => $this->parseErrors($message)
            )
        );
        die($result);
    }

    public function refreshStatus()
    {
        $api = $this->api;
        $shipping_number = Tools::getValue('nr_listu');
        $pack_status = $api->getPackStatus($shipping_number);
        if ($pack_status === false) {
            $this->displayAjaxError($api->getErrors());
        }
        if (!Configuration::get($this->_prefix.'_SHIPX')) {
            $data = strtotime((string)$pack_status->statusDate);
            $data = Date('Y-m-d H:i:s', $data);
            $status = (string)$pack_status->status;
        } else {
            $pack_status = (Tools::jsonDecode($pack_status));
            if (!isset($pack_status->tracking_details)) {
                $this->displayAjaxError($pack_status->message);
            }
            $data = strtotime((string)$pack_status->tracking_details[0]->datetime);
            $data = Date('Y-m-d H:i:s', $data);
            $status = (string)$pack_status->status;
        }

        if (isset($pack_status->status) && $pack_status->status == '404') {
            $this->displayAjaxError($pack_status->message);
        }
    
        $paczkomatyList = PaczkomatyList::getObjectByShippingNumber($shipping_number);
        $avstates = explode(',', Configuration::get($this->_prefix.'_STATUS_AV'));
        if (Configuration::get($this->_prefix.'_OS') &&
            Tools::isSubmit('change_order_state')
            ) {
            if ($status == 'Delivered') {
                $state = (int)Configuration::get($this->_prefix.'_STATUS_DEL');
            } elseif ($status == 'Prepared') {
                $state = (int)Configuration::get($this->_prefix.'_STATUS');
            } elseif ($status == 'Stored') {
                $state = (int)Configuration::get($this->_prefix.'_STATUS_PIC');
            } elseif ($status == 'InTransit' || $status == 'sent_from_source_branch'
                || $status == 'adopted_at_source_branch' || $status == 'collected_from_sender'
            ) {
                $state = (int)Configuration::get($this->_prefix.'_STATUS_PIC');
            }

            $id_cart = $paczkomatyList->id_cart;
            if ($state && $id_cart) {
                $order = new Order(Order::getOrderByCartId($id_cart));
                $changestate = false;
                $order_current_sate = $order->getCurrentState();
                if (in_array($order_current_sate, $avstates)) {
                    if ($state != $order_current_sate) {
                        $order->setCurrentState($state);
                    }
                }
            }
        }

        $status = $data.' '.$this->translate($status);
        if ($paczkomatyList) {
            $paczkomatyList->pack_status = $status;
            $paczkomatyList->save();
        }
        $result = Tools::jsonEncode(
            array(
              'error' => false,
              'status' => $status
            )
        );
        die($result);
    }

    public function createNewList()
    {
        $order = new Order(Tools::getValue('id_order'));
        $id_cart = (int)$order->id_cart;
        PaczkomatyList::createEmpty($id_cart);
        $status = true;
        $result = Tools::jsonEncode(
            array(
              'error' => false,
              'status' => $status,
              'confirmation' => $this->l('Please check all fields before create new label')
            )
        );
        die($result);
    }

    public function viewAccess($disable = false)
    {
        return true;
    }
}
