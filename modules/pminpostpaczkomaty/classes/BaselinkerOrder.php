<?php
/**
* 2014-2020 Presta-Mod.pl Rafał Zontek
*
* NOTICE OF LICENSE
*
* Obiekt automatycznie wygenerowany za pomocą narzędzia ObjectGenerator
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

class PmBaselinkerOrder extends ObjectModel
{
    /* Classic fields */
    public $id;
    /** @var string id_cart */
    public $id_order;
    /** @var string machine */
    public $sended;
    /** @var string nr_listu */
    public static $definition = array(
        'table' => 'pminpostpaczkomatyorder',
        'primary' => 'id',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            /* Classic fields */
            'id_order' => array(
                'type' => self::TYPE_STRING,
                'size' => 100,
            ),
            'sended' => array(
                'type' => self::TYPE_INT,
                'size' => 2,
            ),
        ),
    );

    public static $token = '';

    public static function setToken($token)
    {
        self::$token = $token;
    }

    public static function installSql()
    {
        $sql = array();
        $sql[] = '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pminpostpaczkomatyorder`
            (
              `id_pminpostpaczkomatyorder` INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
              `id_order` CHAR(100),
              `sended` INT(2)          
            ) CHARACTER SET utf8 COLLATE utf8_general_ci;';

        foreach ($sql as $item) {
            if (!Db::getInstance()->execute($item)) {
                return false;
            }
        }
        return true;
    }

    public static function uninstallSql()
    {
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'pminpostpaczkomatyorder`';

        foreach ($sql as $item) {
            if (!Db::getInstance()->execute($item)) {
                return false;
            }
        }
        return true;
    }

    public static function setOrderParams($id_order, $point, $point_address, $post_code, $city)
    {
        $methodParams = '{
            "order_id": '.$id_order.',
            "delivery_point_id" : "'.$point.'",
            "delivery_point_name" : "'.$point.'",
            "delivery_point_address" : "'.$point_address.'",
            "delivery_point_postcode" : "'.$post_code.'",
            "delivery_point_city" : "'.$city.'"
        }';
        $apiParams = [
            "token" => self::$token,
            "method" => "setOrderFields",
            "parameters" => $methodParams
        ];

        $curl = curl_init("https://api.baselinker.com/connector.php");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiParams));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        return json_decode($response);
    }


    public static function getOrdersByEmail($email, $reference)
    {
        $methodParams = '{
            "email": "'.$email.'"
        }';
        $apiParams = [
            "token" => self::$token,
            "method" => "getOrdersByEmail",
            "parameters" => $methodParams
        ];

        $curl = curl_init("https://api.baselinker.com/connector.php");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiParams));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $json = json_decode($response);
        if ($json) {
            if (sizeof($json->orders)) {
                foreach ($json->orders as $order) {
                    $bl_order = PmBaselinkerOrder::getOrder($order->order_id);
                    if (sizeof($bl_order->orders)) {
                        foreach ($bl_order->orders as $bl_simple_order) {
                            if ($bl_simple_order->external_order_id == $reference) {
                                return $bl_simple_order->order_id;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function getOrder($id_order)
    {
        $methodParams = '{    
        "order_id": '.$id_order.'
    }';
        $apiParams = [
            "token" => self::$token,
            "method" => "getOrders",
            "parameters" => $methodParams
        ];

        $curl = curl_init("https://api.baselinker.com/connector.php");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiParams));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($curl);
        return (json_decode($response));
    }

    public static function getPointInfo($point_name)
    {
        $point = json_decode(
            file_get_contents('https://api-pl-points.easypack24.net/v1/points/'.$point_name)
        );
        if (isset($point->address_details)) {
            $point_address = $point->address_details;
            return array(
                'delivery_point_id' => $point_name,
                'delivery_point_address' => $point_address->street.' '.$point_address->building_number,
                'delivery_point_postcode' => $point_address->post_code,
                'delivery_point_city' => $point_address->city,
            );
        }
        return false;
    }

    public static function getLastOrders($limit)
    {
        self::setToken(Configuration::get('PMINPOSTPACZKOMATY_BL_TOKEN'));
        $limit = (int)$limit;
        $id_order = (int)Configuration::get('PMINPOSTPACZKOMATY_BL_ORDER');

        if ($limit == 0) {
            $limit = 15;
        }
        $sql = '
            SELECT      o.id_order, machine, c.email
            FROM        `'._DB_PREFIX_.'orders` o
            JOIN        `'._DB_PREFIX_.'pminpostpaczkomatylist` pl
                        ON pl.id_cart = o.id_cart
            JOIN        `'._DB_PREFIX_.'customer` c
                        ON o.id_customer = c.id_customer
            WHERE       o.id_order > '.$id_order.'
                        AND machine <> ""
                        AND o.id_order NOT IN 
                        (
                            SELECT      id_order 
                            FROM        `'._DB_PREFIX_.'pminpostpaczkomatyorder`
                        )
            ORDER BY    o.id_order ASC
            LIMIT '.$limit;
        $result = Db::getInstance()->executeS($sql);
        
        foreach ($result as $result_item) {
            $result_item['point'] = self::getPointInfo($result_item['machine']);
            $order = self::getOrdersByEmail($result_item['email'], $result_item['id_order']);
            if ($order) {
                $r = self::setOrderParams(
                    $order,
                    $result_item['machine'],
                    $result_item['point']['delivery_point_address'],
                    $result_item['point']['delivery_point_postcode'],
                    $result_item['point']['delivery_point_city']
                );
                echo $result_item['id_order'].': '.$r->status;
                if ($r->status == 'SUCCESS') {
                    $blo = new PmBaselinkerOrder();
                    $blo->id_order = $result_item['id_order'];
                    $blo->sended = true;
                    $blo->save();
                }
            }
        }
    }
}
