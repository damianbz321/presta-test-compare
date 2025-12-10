<?php

namespace FreshMail\Entity;

use ObjectModel;

class Email extends ObjectModel
{
    public $id;
    public $email;
    public $last_synchronization;
    public $add_date;
    public $deletion_date;
    public $status;
    public $resigning_reason;
    public $hash_list;


    public static $definition = array(
        'table' => 'freshmail_list_email',
        'primary' => 'id',
        'fields' => array(
            'email' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'class' => 'lg'
            ),
           'hash_list' => array(
                'type' => self::TYPE_STRING,
                'required' => true
            ),
            'add_date' => [
                'type' => self::TYPE_DATE
            ],
            'last_synchronization' => [
                'type' => self::TYPE_DATE
            ],
            'deletion_date' => [
                'type' => self::TYPE_DATE
            ],
            'status' => [
                'type' => self::TYPE_STRING,
            ],
            'resigning_reason' => array(
                'type' => self::TYPE_STRING,
            ),
        ),
    );

    public static function addBySimpleInsert($listHash, $data){
        if(\Validate::isEmail($data[0])){
            \Db::getInstance()->insert(
                Email::$definition['table'],
                [
                    'email' => pSQL($data[0]),
                    'hash_list' => pSQL($listHash),
                    'last_synchronization' => date('Y-m-d H:i:s'),
                    'add_date' => pSQL($data[1]),
                    'deletion_date' => pSQL($data[2]),
                    'status' => pSQL($data[3]),
                    'resigning_reason' => pSQL($data[4])
                ],
                false,
                false,
                \Db::ON_DUPLICATE_KEY
            );

            return \Db::getInstance()->Insert_ID();
        }
    }

    public static function updateStatusInShop($data, FreshmailSetting $fs)
    {
        $actionCustomer = strtolower($data[3]) == 'active' ? 'registerCustomer' : 'unregisterCustomer';

        $email = pSQL($data[0]);
        $customers = \Customer::getCustomersByEmail($email);
        foreach ($customers as $c){
            if($fs->id_shop == $c['id_shop']){
                self::$actionCustomer($email, $fs->id_shop);
            }
        }
        if('active'  == strtolower($data[3])){
            self::registerGuest($email, $data[1], $fs->id_shop);
        } else {
            self::unregisterGuest($email, $data[1], $fs->id_shop);
        }
    }

    protected static function registerCustomer($email, $idShop )
    {
        $sql = 'UPDATE '._DB_PREFIX_.'customer
                SET `newsletter` = 1, newsletter_date_add = NOW(), `ip_registration_newsletter` = \'freshmail\'
                WHERE `email` = \''.pSQL($email).'\'
                AND id_shop = '.$idShop;

        return \Db::getInstance()->execute($sql);
    }

    protected static function unregisterCustomer($email, $idShop)
    {
        $sql = 'UPDATE '._DB_PREFIX_.'customer
                SET `newsletter` = 0
                WHERE `email` = \''.pSQL($email).'\'
                AND id_shop = '.$idShop;

        return \Db::getInstance()->execute($sql);
    }

    protected static function registerGuest($email, $dateAdd, $idShop)
    {
        $query = (new \DbQuery())->select('*')->from('emailsubscription')->where('email = "'.pSQL($email).'"')->where('id_shop = '.(int)$idShop);
        $guests = \Db::getInstance()->executeS($query);
        if( !empty($guests) ){
            foreach ($guests as $guest){
                if($guest['active']){
                    continue;
                }
                \Db::getInstance()->update(
                    'emailsubscription',
                    ['active' => 1],
                    'id = '.(int)$guest['id']
                );
            }
        } else {
            \Db::getInstance()->insert(
                'emailsubscription',
                [
                    'email' => pSQL($email),
                    'id_shop' => (int)$idShop,
                    'active' => 1,
                    'newsletter_date_add' => pSQL($dateAdd),
                    'ip_registration_newsletter' => 'freshmail'
                ]
            );
        }
    }

    protected static function unregisterGuest($email, $id_shop)
    {
        \Db::getInstance()->delete(
            'emailsubscription',
            'email = "'.pSQL($email).'" AND id_shop = '.(int)$id_shop
        );
    }


}