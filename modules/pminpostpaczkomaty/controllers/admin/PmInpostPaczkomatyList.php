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

include_once(dirname(__FILE__) . '/../../classes/PaczkomatyList.php');
class PmInpostPaczkomatyListController extends ModuleAdminController
{
    public function __construct()
    {
        $this->cat_cache = array();
        $this->id_lang = Context::getContext()->language->id;
        $this->bootstrap = true;
        $this->table = 'orders';
        $this->className = 'paczkomatyList';
        $this->lang = false;
        $this->languages = Language::getLanguages(false);
        $this->explicitSelect = true;
        parent::__construct();
        $this->addRowAction('view');
        $allegro = Module::getModuleIdByName('x13allegro');
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<=')) {
            $this->version_ps = '1.6';
        } else {
            $this->version_ps = '1.7';
        }
        if ($allegro) {
            $sql = '
            SELECT      * 
            FROM        `'._DB_PREFIX_.'xallegro_order` 
            ORDER BY    id_order DESC
            LIMIT 200';
            $result = Db::getInstance()->executeS($sql);

            foreach ($result as $result_item) {
                $o = new Order($result_item['id_order']);
                $id_cart = $o->id_cart;
                $paczkomatyList = PaczkomatyList::getByIdCart($id_cart);
                $this->addDeliveryAllegro($paczkomatyList, $o->id);
            }
        }
        $this->bulk_actions = array(
            'printpdfa4' => array(
                'text' => $this->l('Print all labels').' '.$this->l('A4')
            ),
            'printpdfa6' => array(
                'text' => $this->l('Print all labels').' '.$this->l('A6P')
            ),
            'zlecenie' => array(
                'text' => $this->l('Generate transport order')
            ),
        );

        if (!Configuration::get('PMINPOSTPACZKOMATY_SHIPX')) {
            unset($this->bulk_actions['zlecenie']);
        }

        if (Tools::isSubmit('without_transport_order')) {
            $this->_where = ' AND ((zlecenie = "" AND nr_listu <> "") OR (zlecenie IS null AND nr_listu <> "")) ';
        }

        $statuses = OrderState::getOrderStates((int)Context::getContext()->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }

        $this->_select = '
            l.id AS id_orders,
            l.*,
            a.*,
            a.date_add as order_date,
            concat(da.firstname," ",da.lastname) nazwa,
            osl.`name` AS `osname`,
            os.`color`,
            machine AS status,
            a.id_carrier
        ';

        $this->_join = '
            JOIN        `'._DB_PREFIX_.'pminpostpaczkomatylist` l
            ON          l.id_cart = a.id_cart';

        $this->_join .= '
            JOIN        `'._DB_PREFIX_.'address` da
            ON          da.id_address = a.id_address_delivery
            LEFT JOIN   `'._DB_PREFIX_.'order_state` os
            ON          (os.`id_order_state` = a.`current_state`)
            LEFT JOIN   `'._DB_PREFIX_.'order_state_lang` osl
            ON          (os.`id_order_state` = osl.`id_order_state`
            AND osl.`id_lang` = '.Context::getContext()->language->id.')
        ';

        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';

        $this->fields_list = array();
        $this->fields_list['id'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int',
            'remove_onclick' => true,
            'callback' => 'idordercallback'
        );

        $this->fields_list['id_order'] = array(
            'title' => $this->l('Id order'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int',
            'remove_onclick' => true,
            'callback' => 'idordercallback'
        );
        $this->fields_list['nazwa'] = array(
            'title' => $this->l('Customer'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'text',
            'remove_onclick' => true,
            'havingFilter' => true
        );

        $this->fields_list['date_add'] = array(
            'title' => $this->l('Date'),
            'type' => 'datetime',
            'remove_onclick' => true,
            'filter_key' => 'a!date_add'
        );


        $this->fields_list['osname'] = array(
            'title' => $this->l('Status'),
            'type' => 'select',
            'color' => 'color',
            'list' => $this->statuses_array,
            'filter_key' => 'os!id_order_state',
            'filter_type' => 'int',
            'remove_onclick' => true,
            'order_key' => 'osname'
        );

        $this->fields_list['nr_listu'] = array(
            'title' => $this->l('Tracking number'),
            'type' => 'text',
            'remove_onclick' => true,
            'filter_key' => 'l!nr_listu',
            'callback' => 'shippingnumbercallback'
        );

        $this->fields_list['zlecenie'] = array(
            'title' => $this->l('Transport order'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'filter' => false,
            'search' => false,
            'orderby' => false,
        );

        $this->fields_list['pack_status'] = array(
            'title' => $this->l('Pack status'),
            'type' => 'text',
            'remove_onclick' => true,
            'callback' => 'packstatuscallback'
        );

        $this->fields_list['id_carrier'] = array(
            'title' => $this->l('COD'),
            'align' => 'text-right',
            'orderby' => false,
            'havingFilter' => false,
            'callback' => 'listcallback',
            'filter' => false,
            'search' => false,
            'remove_onclick' => true,
            'callback' => 'pobraniecallback'
        );

        $this->fields_list['machine'] = array(
            'title' => $this->l('Machine'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'text',
            'remove_onclick' => true,
        );

        $this->fields_list['status'] = array(
            'title' => $this->l('Buttons'),
            'align' => 'text-right',
            'orderby' => false,
            'havingFilter' => false,
            'callback' => 'listcallback',
            'filter' => false,
            'search' => false,
            'remove_onclick' => true,
        );
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('submitBulkprintpdfa6orders')) {
            $format = 'A6P';
            $this->printPdfMerge($format);
        } elseif (Tools::isSubmit('submitBulkprintpdfa4orders')) {
            $format = 'A4';
            $this->printPdfMerge($format);
        } elseif (Tools::isSubmit('submitBulkzlecenieorders') || Tools::isSubmit('zlecenie_row')) {
            $this->zlecenie();
        }
    }

    public function checkAccess()
    {
        return true;
    }

    public function printPdfMerge($format)
    {
        if (Tools::isSubmit('ordersBox') && sizeof(Tools::getValue('ordersBox'))) {
            error_reporting(0);
            if (Configuration::get('PMINPOSTPACZKOMATY_SHIPX')) {
                $api = InpostApiShipx::getInstance();
            } else {
                $api = InpostApi::getInstance();
            }

            $api->setLabelType($format);
            $sql = '
                SELECT      nr_listu,id_cart,id
                FROM        `'._DB_PREFIX_.'pminpostpaczkomatylist` 
                WHERE id    IN (
                    '.implode(',', Tools::getValue('ordersBox')).'
                )
                AND         nr_listu <> ""';
            $result = Db::getInstance()->executeS($sql);
            $caches = array();
            $orders = array();
            foreach ($result as $list) {
                $id_cart = (int)$list['id_cart'];
                $id = (int)$list['id'];
                $id_order = Order::getOrderByCartId($id_cart);
                $sql = '
                    SELECT      nr_listu 
                    FROM        `'._DB_PREFIX_.'pminpostpaczkomatylist` 
                    WHERE       id = '.(int)$id.'
                        AND     id_cart = '.(int)$id_cart;
                $shippingNumber = Db::getInstance()->getValue($sql);
                $cache = dirname(__FILE__).'/../../cache/'.$shippingNumber.'-'.
                $format.'.pdf';
                if (file_exists($cache)) {
                    $caches[] = $cache;
                    $orders[] = $id_order;
                } else {
                    $file = $api->getSticker($shippingNumber, 'Pdf');
                    if ($file) {
                        file_put_contents($cache, $file);
                        $caches[] = $cache;
                        $orders[] = $id_order;
                    }
                }
            }

            include dirname(__FILE__).'/../../lib/pdfmerger/PDFMerger.php';
            $pdf = new PDFMerger;

            if (sizeof($caches)) {
                foreach ($caches as $item) {
                    $pdf->addPDF($item, 'all');
                }
                sort($orders);
                if (Configuration::get('PMINPOSTPACZKOMATY_DF')) {
                    $pdf->merge('download', $this->l('Paczkomaty').Date('_Y-m-d').'_'.$format.'.pdf');
                } else {
                    $pdf->merge();
                }
            } else {
                $this->errors[] = $this->l('No labels');
            }
        } else {
            $this->errors[] = $this->l('No labels');
        }
    }


    public function payforpack($id_order)
    {
        $order = new Order($id_order);
        $id_cart = (int)$order->id_cart;
        $paczkomatyList = PaczkomatyList::getByIdCart($id_cart);
        if ($paczkomatyList->status == 'Opłacona') {
            return true;
        }
        $shippingNumber = $paczkomatyList->nr_listu;
        $order = new Order($id_order);
        $id_order_carrier = (int)$order->getIdOrderCarrier();
        $order_carrier = new OrderCarrier($id_order_carrier);

        $order->shipping_number = $shippingNumber;
        $order->update();

        $order_carrier->tracking_number = pSQL($shippingNumber);
        if ($order_carrier->update()) {
            $customer = new Customer((int)$order->id_customer);
            $carrier = new Carrier((int)$order->id_carrier, $order->id_lang);

            $templateVars = array(
                '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{id_order}' => $order->id,
                '{shipping_number}' => $order->shipping_number,
                '{order_name}' => $order->getUniqReference()
            );
            if ($this->version_ps == '1.7') {
                try {
                    $order_carrier->sendInTransitEmail($order);
                } catch (Exception $exp) {
                    $exp->getMessage();
                }
            } elseif (@Mail::Send(
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
                    'carrier' => $carrier),
                    null,
                    false,
                    true,
                    false,
                    $order->id_shop
                );
            }
        }
        if (Configuration::get('PMINPOSTPACZKOMATY_OS')) {
            $status = (int)Configuration::get('PMINPOSTPACZKOMATY_STATUS');
            $order = new Order(Order::getOrderByCartId($id_cart));
            if ($status != $order->getCurrentState()) {
                $order->setCurrentState($status);
            }
        }

        $sql = 'UPDATE '._DB_PREFIX_.'order_carrier SET nr_listu = "'.$shippingNumber.'"
        WHERE id_order ='.(int)$order->id;
        Db::getInstance()->execute($sql);

        $paczkomatyList->status = 'Opłacona';
        $paczkomatyList->save();
        return true;
    }
    public function initContent()
    {
        $this->content = Context::getContext()->smarty->fetch(
            dirname(__FILE__).'/../../views/templates/hook/order_list.tpl'
        );
        parent::initContent();
    }

    public function pobraniecallback($t, $r)
    {
        $post_info = stripslashes($r['post_info']);
        $cod_serialize = false;
        if ($post_info) {
            $post_info = unserialize($post_info);
            if (is_array($post_info) && isset($post_info['pminpostorder_pobranie']) && $post_info['pminpostorder_pobranie']) {
                $cod_serialize = true;
            }
        }
        Context::getContext()->smarty->assign(
            array(
            'cod_serialize' => $cod_serialize,
            't' => $t,
            'r' => $r,
            'cod_id' => Configuration::get('PMINPOSTPACZKOMATY_DELIVERY_BW'),
            )
        );
        return Context::getContext()->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/cod_callback.tpl');
    }

    public function packstatuscallback($t, $r)
    {
        Context::getContext()->smarty->assign(
            array(
            't' => $t,
            'r' => $r,
            )
        );
        return Context::getContext()->smarty->fetch(
            dirname(__FILE__).'/../../views/templates/admin/packstatus_callback.tpl'
        );
    }

    public function shippingnumbercallback($t, $r)
    {
        Context::getContext()->smarty->assign(
            array(
            't' => $t,
            'r' => $r,
            )
        );
        return Context::getContext()->smarty->fetch(
            dirname(__FILE__).'/../../views/templates/admin/shippingnumber_callback.tpl'
        );
    }

    public function listcallback($t, $r)
    {
        Context::getContext()->smarty->assign(
            array(
            't' => $t,
            'r' => $r,
            )
        );
        return Context::getContext()->smarty->fetch(
            dirname(__FILE__).'/../../views/templates/admin/buttons_callback.tpl'
        );
    }

    public function idordercallback($t, $r)
    {
        Context::getContext()->smarty->assign(
            array(
            't' => $t,
            'r' => $r,
            )
        );
        return Context::getContext()->smarty->fetch(
            dirname(__FILE__).'/../../views/templates/admin/idorder_callback.tpl'
        );
    }

    public function renderForm()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('PmInpostPaczkomatyList'));
    }

    public function renderView()
    {
        $label = new PaczkomatyList(Tools::getValue('id_orders'));
        $order = new Order((Order::getOrderByCartId($label->id_cart)));
        $this->context->smarty->assign(
            array(
                'label' => $label,
                'post_info' => unserialize(stripcslashes($label->post_info)),
                'id_order' => $order->id
            )
        );
        $this->content = $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/labelview.tpl');
    }

    public function zlecenie()
    {
        $api = InpostApiShipx::getInstance();

        if (Tools::isSubmit('ordersBoxa')) {
            $sql = '
            SELECT      id_pack
            FROM        `'._DB_PREFIX_.'pminpostpaczkomatylist`
            WHERE       id IN ('.(int)Tools::getValue('ordersBox').') 
            AND nr_listu <> ""';
        } else {
            $sql = '
            SELECT      id_pack
            FROM        `'._DB_PREFIX_.'pminpostpaczkomatylist`
            WHERE       id IN ('.
            implode(
                ',',
                Tools::getValue('ordersBox')
            ).') AND nr_listu <> ""';
        }
        $result = Db::getInstance()->executeS($sql);
        
        $ids = array();
        if ($result) {
            foreach ($result as $item) {
                $ids[] = $item['id_pack'];
            }
        }
        
        if (sizeof($ids)) {
            $result = $api->zlecenie($ids);
            if ($result) {
                $sql = '
                    UPDATE      `'._DB_PREFIX_.'pminpostpaczkomatylist`
                    SET         zlecenie = "'.Date('Y-m-d H:i:s').'"
                    WHERE       id_pack IN('.implode(',', $ids).')';
                Db::getInstance()->execute($sql);
                Context::getContext()->smarty->assign(
                    array(
                        'alertpaczkomaty' => $this->l('The pickup order has been generated'),
                        'alerttype' => 'success'
                    )
                );
            } else {
                $errors = $api->getErrors();
                Context::getContext()->smarty->assign(
                    array(
                        'alertpaczkomaty' => implode('<br>', $this->parseErrors($errors)),
                        'alerttype' => 'warning'
                    )
                );
            }
        } else {
            Context::getContext()->smarty->assign(
                array(
                    'alertpaczkomaty' => $this->l('No package to pickup order'),
                    'alerttype' => 'warning'
                )
            );
        }
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

    public function translate(&$error)
    {
        $transin = array();
        $transout = array();
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

        $transin[] = 'receiver:';
        $transout[] = $this->l('Receiver: ');
        
        $transin[] = 'email:';
        $transout[] = $this->l('Email: ');

        $transin[] = 'required';
        $transout[] = $this->l('Required');

        $transin[] = 'invalid';
        $transout[] = $this->l('Invaild');

        $transin[] = 'phone:';
        $transout[] = $this->l('Phone:');

        $transin[] = 'sender:';
        $transout[] = $this->l('Sender:');
        
        $transin[] = 'No route matches';
        $transout[] = $this->l('No route matches');

        $transin[] = 'Tracking information about';
        $transout[] = $this->l('Tracking information about');

        $transin[] = 'shipment has not been found';
        $transout[] = $this->l('shipment has not been found');

        $transin[] = 'created';
        $transout[] = 'Przesyłka utworzona.';

        $transin[] = 'Prepared';
        $transout[] = 'Przesyłka utworzona.';

        $transin[] = 'Prepared';
        $transout[] = 'Przesyłka utworzona.';

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
        $transout[] = 'Przygotowano oferty.';
      
        $transin[] = 'offer_selected';
        $transout[] = 'Oferta wybrana.';
      
        $transin[] = 'confirmed';
        $transout[] = 'Przygotowana przez Nadawcę.';
      
        $transin[] = 'dispatched_by_sender';
        $transout[] = 'Paczka nadana w paczkomacie.';
      
        $transin[] = 'collected_from_sender';
        $transout[] = 'Odebrana od klienta.';
      
        $transin[] = 'taken_by_courier';
        $transout[] = 'Odebrana od Nadawcy.';
      
        $transin[] = 'adopted_at_source_branch';
        $transout[] = 'Przyjęta w oddziale InPost.';
      
        $transin[] = 'sent_from_source_branch';
        $transout[] = 'W trasie.';
      
        $transin[] = 'ready_to_pickup_from_pok';
        $transout[] = 'Czeka na odbiór w Punkcie Obsługi Paczek.';
      
        $transin[] = 'ready_to_pickup_from_pok_registered';
        $transout[] = 'Czeka na odbiór w Punkcie Obsługi Klienta.';
      
        $transin[] = 'oversized';
        $transout[] = 'Przesyłka ponadgabarytowa.';
      
        $transin[] = 'adopted_at_sorting_center';
        $transout[] = 'Przyjęta w Sortowni.';
      
        $transin[] = 'sent_from_sorting_center';
        $transout[] = 'Wysłana z Sortowni.';
      
        $transin[] = 'adopted_at_target_branch';
        $transout[] = 'Przyjęta w Oddziale Docelowym.';
      
        $transin[] = 'out_for_delivery';
        $transout[] = 'Przekazano do doręczenia.';
      
        $transin[] = 'ready_to_pickup';
        $transout[] = 'Umieszczona w Paczkomacie (odbiorczym).';

        $transin[] = 'pickup_reminder_sent';
        $transout[] = 'Przypomnienie o czekającej paczce.';

        $transin[] = 'delivered';
        $transout[] = 'Dostarczona.';
      
        $transin[] = 'pickup_time_expired';
        $transout[] = 'Upłynął termin odbioru.';

        $transin[] = 'avizo';
        $transout[] = 'Powrót do oddziału.';
      
        $transin[] = 'claimed';
        $transout[] = 'Zareklamowana w Paczkomacie.';
      
        $transin[] = 'returned_to_sender';
        $transout[] = 'Zwrot do nadawcy.';
      
        $transin[] = 'canceled';
        $transout[] = 'Anulowano etykietę.';
      
        $transin[] = 'other';
        $transout[] = 'Inny status.';
      
        $transin[] = 'dispatched_by_sender_to_pok';
        $transout[] = 'Nadana w Punkcie Obsługi Klienta.';
      
        $transin[] = 'out_for_delivery_to_address';
        $transout[] = 'W doręczeniu.';
      
        $transin[] = 'pickup_reminder_sent_address';
        $transout[] = 'W doręczeniu.';
      
        $transin[] = 'rejected_by_receiver';
        $transout[] = 'Odmowa przyjęcia.';
      
        $transin[] = 'undelivered_wrong_address';
        $transout[] = 'Brak możliwości doręczenia.';
      
        $transin[] = 'undelivered_incomplete_address';
        $transout[] = 'Brak możliwości doręczenia.';
      
        $transin[] = 'undelivered_unknown_receiver';
        $transout[] = 'Brak możliwości doręczenia.';
      
        $transin[] = 'undelivered_cod_cash_receiver';
        $transout[] = 'Brak możliwości doręczenia.';
      
        $transin[] = 'taken_by_courier_from_pok';
        $transout[] = 'W drodze do oddziału nadawczego InPost.';
      
        $transin[] = 'undelivered';
        $transout[] = 'Przekazanie do magazynu przesyłek niedoręczalnych.';
      
        $transin[] = 'return_pickup_confirmation_to_sender';
        $transout[] = 'Przygotowano dokumenty zwrotne.';
      
        $transin[] = 'ready_to_pickup_from_branch';
        $transout[] = 'Paczka nieodebrana – czeka w Oddziale';
      
        $transin[] = 'delay_in_delivery';
        $transout[] = 'Możliwe opóźnienie doręczenia.';
      
        $transin[] = 'redirect_to_box';
        $transout[] = 'Przekierowano do Paczkomatu.';
        $transin[] = 'canceled_redirect_to_box';
        $transout[] = 'Anulowano przekierowanie.';
      
        $transin[] = 'readdressed';
        $transout[] = 'Przekierowano na inny adres.';
      
        $transin[] = 'undelivered_no_mailbox';
        $transout[] = 'Brak możliwości doręczenia.';
      
        $transin[] = 'undelivered_not_live_address';
        $transout[] = 'Brak możliwości doręczenia.';
      
        $transin[] = 'undelivered_lack_of_access_letterbox';
        $transout[] = 'Brak możliwości doręczenia.';
      
        $transin[] = 'missing';
        $transout[] = 'translation missing: pl_PL.statuses.missing.title';
      
        $transin[] = 'stack_in_customer_service_point';
        $transout[] = 'Paczka magazynowana w POP.';
      
        $transin[] = 'stack_parcel_pickup_time_expired';
        $transout[] = 'Upłynął termin odbioru paczki magazynowanej.';
      
        $transin[] = 'unstack_from_customer_service_point';
        $transout[] = 'W drodze do wybranego paczkomatu.';
      
        $transin[] = 'courier_avizo_in_customer_service_point';
        $transout[] = 'Oczekuje na odbiór.';
      
        $transin[] = 'taken_by_courier_from_customer_service_point';
        $transout[] = 'Zwrócona do nadawcy.';

        $error = str_replace($transin, $transout, $error);
        return $error;
    }

    public function viewAccess($disable = false)
    {
        return true;
    }

    public function addDeliveryAllegro(&$paczkomatyList, $id_order)
    {
        if ($paczkomatyList->machine) {
            return;
        }
        $sql = '
        SELECT      * 
        FROM        `'._DB_PREFIX_.'xallegro_order` 
        WHERE       id_order = '.(int)$id_order;
        $row = Db::getInstance()->getRow($sql);
        if (!$row) {
            return ;
        }
        $checkout = @Tools::jsonDecode($row['checkout_form_content']);
        if ($checkout &&
            isset($checkout->delivery, $checkout->delivery->method) &&
            isset($checkout->delivery->pickupPoint, $checkout->delivery->pickupPoint->id)
        ) {
            $method = $checkout->delivery->method->name;
            $pickupPoint = $checkout->delivery->pickupPoint->id;
            if (strpos($method, 'Paczkomaty') !== false) {
                $paczkomatyList->machine = (string)$pickupPoint;
                $paczkomatyList->post_info =
                $paczkomatyList->save();
            }
        }
    }
}
