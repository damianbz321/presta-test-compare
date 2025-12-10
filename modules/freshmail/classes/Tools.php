<?php

namespace FreshMail;

use Configuration;
use Context;
use Db;
use DbQuery;
use Exception;
use FreshMail\Entity\Email;
use FreshMail\Entity\EmailToSynchronize;
use Mail;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class Tools
{
    public static function setShopSmtp($apiKey)
    {
        Configuration::updateValue('PS_MAIL_METHOD', 2);
        Configuration::updateValue('PS_MAIL_SERVER', 'smtp.freshmail.com');
        Configuration::updateValue('PS_MAIL_USER', 'smtp@freshmail.com');
        Configuration::updateValue('PS_MAIL_PASSWD', $apiKey);
        Configuration::updateValue('PS_MAIL_SMTP_ENCRYPTION', 'tls');
        Configuration::updateValue('PS_MAIL_SMTP_PORT', '587');
    }

    public static function getSpecificPriceRules($idShop)
    {
        $query = new DbQuery();
        $query
            ->select('id_specific_price_rule, name ')
            ->from('specific_price_rule', 's')
            ->where('id_shop = ' . (int)$idShop)
            ->orderBy('name');

        return Db::getInstance()->executeS($query);
    }

    public static function sendWizardSuccessMail()
    {
        $context = Context::getContext();
        $employee = Context::getContext()->employee;


        return Mail::Send(
            $context->language->id,
            'wizard_success',
            $context->getTranslator()->trans(
                'Welcome!',
                array(),
                'Modules.Freshmail.Emails.'
            ),
            array(
                '{firstname}' => $employee->firstname,
                '{lastname}' => $employee->lastname,
                '{email}' => $employee->email,
            ),
            $employee->email,
            $employee->firstname . ' ' . $employee->lastname,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . 'freshmail/mails'
        );
    }

    public static function checkTmpDir()
    {
        if (!is_dir(\Freshmail::TMP_DIR)) {
            mkdir(\Freshmail::TMP_DIR);
        }
        if (!is_writeable(\Freshmail::TMP_DIR)) {
            throw new Exception('Error: Directory ' . \Freshmail::TMP_DIR . ' not writeable by webserver.');
        }
    }

    public static function addEmailsToSynchronize(int $idShop, $hashList)
    {
        $sql = str_replace(
            ['PREFIX_', '##HASH##', '##ID_SHOP##'],
            [_DB_PREFIX_, pSQL($hashList), $idShop],
            'INSERT IGNORE INTO PREFIX_freshmail_emails_to_synchronize (`email`, `name`, `hash_list`) 
                SELECT `email`, `firstname`, "##HASH##" 
                FROM PREFIX_customer 
                WHERE id_shop = ##ID_SHOP## 
                    AND newsletter = 1  
                    AND `email` NOT IN (SELECT email FROM PREFIX_freshmail_emails_synchronized WHERE hash_list = "##HASH##") 
                
                    '
        );
        Db::getInstance()->execute($sql);

        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        if ($moduleManager->isInstalled('ps_emailsubscription') && $moduleManager->isEnabled('ps_emailsubscription')) {
            $sql = str_replace(
                ['PREFIX_', '##HASH##', '##ID_SHOP##'],
                [_DB_PREFIX_, pSQL($hashList), $idShop],
                'INSERT IGNORE INTO PREFIX_freshmail_emails_to_synchronize (`email`, `hash_list`) 
                SELECT `email`, "##HASH##" 
                FROM PREFIX_emailsubscription 
                WHERE id_shop = ##ID_SHOP## 
                    AND active = 1 
                    AND `email` NOT IN (SELECT email FROM PREFIX_freshmail_emails_synchronized WHERE hash_list = "##HASH##") 
                '
            );
            Db::getInstance()->execute($sql);
        }
    }

    public static function getEmailsToSynchronize($hashList, $limit = 100)
    {
        $query = new DbQuery();
        $query
            ->select('*, 1 as state')
            ->from('freshmail_emails_to_synchronize')
            ->where('hash_list = "' . pSQL($hashList) . '"');
        if (!empty($limit)) {
            $query->limit($limit);
        }
        return Db::getInstance()->executeS($query);
    }

    public static function convertToSubcriberCollection(array $data): SubscriberCollection
    {
        $collection = new SubscriberCollection();
        foreach ($data as $item) {
            $state = !empty($item['state']) ? (int)$item['state'] : 0;
            $subscriber = new Subscriber($item['email']);
            $subscriber->addCustomField('imie', $item['name'] );
            $subscriber->state = $state;
            $collection->append($subscriber);
        }
        return $collection;
    }

    public static function importSubscribersFromCsv($listHash, $dir)
    {
        $settings = (new \FreshMail\Repository\FreshmailSettings(Db::getInstance()))->getByHash($listHash);

        foreach (scandir(\Freshmail::TMP_DIR . $dir) as $importFile) {
            $filePath = \Freshmail::TMP_DIR . $dir . '/' . $importFile;
            if (is_file($filePath)) {
                $fh = fopen($filePath, 'r+');

                while ($line = fgetcsv($fh, 0, ';')) {
                    Email::addBySimpleInsert($listHash, $line);
                    Email::updateStatusInShop($line, $settings);

                }
            }
        }
    }

    public static function removeEmailsFromSynchronization($hashList, SubscriberCollection $subscribers)
    {
        foreach ($subscribers as $subscriber){
            Db::getInstance()->delete(
                'freshmail_emails_to_synchronize',
                'hash_list = "'.pSQL($hashList).'" AND email = "'.pSQL($subscriber->email).'"',
                1
            );
        }
    }

    public static function triggerSynchronization($listHash){
        $url = Context::getContext()->link->getBaseLink().'modules/freshmail/cron/synchronize.php?hash='.$listHash;
        self::sendRequest($url);
    }

    public static function asyncJobPing(){
        $url = Context::getContext()->link->getBaseLink().'modules/freshmail/cron/synchronize_subscribers.php';
        self::sendRequest($url);
    }

    public static function sendRequest($url){
        $module = \Module::getInstanceByName('freshmail');
        $ch = curl_init();
        $glue = false !== strpos($url, '?') ? '&' : '?';

        $url .= $glue . 'token='.$module->getCronToken();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,1);


        curl_exec($ch);
        curl_close($ch);
    }


    public static function getGuestEmailByToken($token)
    {
        $sql = 'SELECT `email`
                FROM `'._DB_PREFIX_.'emailsubscription`
                WHERE MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.pSQL($token).'\'
                AND `active` = 0';

        return Db::getInstance()->getValue($sql);
    }

    public static function getUserEmailByToken($token)
    {
        $sql = 'SELECT `email`
                FROM `'._DB_PREFIX_.'customer`
                WHERE MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.pSQL($token).'\'
                AND `newsletter` = 0';

        return Db::getInstance()->getValue($sql);
    }

    public static function checkDirPermission($dir)
    {
        $separator = substr($dir,-1) == '/' ? '' : '/';
        $file = md5(time());
        $result = file_put_contents($dir. $separator .$file, time());
        if(!$result){
            return false;
        }
        unlink($dir.'/'.$file);
        return true;
    }

    public static function clearShopSettings($idShop)
    {
        $fs = (new \FreshMail\Repository\FreshmailSettings(Db::getInstance()))->findForShop($idShop);
        $sql = [
            'DELETE FROM PREFIX_freshmail_setting WHERE id_shop = '.(int)$idShop,
            'DELETE FROM PREFIX_freshmail_form WHERE hash_list = "'.pSQL($fs->subscriber_list_hash).'"',
            'DELETE FROM PREFIX_freshmail_async_job WHERE hash_list = "'.pSQL($fs->subscriber_list_hash).'"',
        ];

        foreach ($sql as $query){
            Db::getInstance()->execute(
                str_replace( 'PREFIX_', _DB_PREFIX_, $query)
            );
        }

    }

    public static function writeEmailTpl($body, $lang)
    {
        $tpl = str_replace(
            '[[BODY]]',
            $body,
            file_get_contents(_PS_MODULE_DIR_.'freshmail/views/templates/email/abandoned-cart.tpl')
        );
        if(!is_dir(_PS_MODULE_DIR_.'freshmail/mails/'.$lang.'/')){
            try {
                mkdir(_PS_MODULE_DIR_ . 'freshmail/mails/' . $lang . '/');
            } catch (Exception $e){
                throw new Exception(_PS_MODULE_DIR_.'freshmail/mails/'.$lang.'/abandoned-cart.html');
            }
        }
        if( false === file_put_contents(_PS_MODULE_DIR_.'freshmail/mails/'.$lang.'/abandoned-cart.html', $tpl)){
            throw new Exception(_PS_MODULE_DIR_.'freshmail/mails/'.$lang.'/abandoned-cart.html');
        }
    }

    public static function is_cli()
    {
        if( defined('STDIN') )
        {
            return true;
        }

        if( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0)
        {
            return true;
        }
        return false;
    }

    public static function filterTplByCategory(&$list, $category){
        foreach ($list as $k => $item){
            if($category != $item['category']){
                unset($list[$k]);
            }
        }
    }

}