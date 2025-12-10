<?php

namespace FreshMail\Sender;

use FreshMail\Freshmail;
use FreshMail\FreshmailApiV3;
use FreshMail\Repository\Birthdays;
use FreshMail\TransactionalEmail;

class Birthday
{
    private $bearer_token;

    public function __construct($bearer_token)
    {
        $this->bearer_token = $bearer_token;
    }

    private function getMailHtml(\FreshMail\Entity\Birthday $birthday) : string
    {
        if(!empty($birthday->tpl)){
            $fm = new Freshmail($this->bearer_token);
            $html = $fm->getTemplateHtml($birthday->tpl);

            if(!empty($html)){
                return $html;
            }
        }

        return '';
    }

    public function send(\Customer $customer, \FreshMail\Entity\Birthday $birthday) : bool
    {
        $shop = new \Shop($birthday->id_shop);

        $html = str_replace(
            ['{firstname}', '{lastname}', '{content}', '{shop_url}'],
            [$customer->firstname, $customer->lastname, $birthday->content[$customer->id_lang]],
            $this->getMailHtml($birthday)
        );

        $recipient = new Email($customer->email, $customer->firstname);
        $sender = new Email(\Configuration::get('PS_SHOP_EMAIL'), $shop->name);

        $fmApi = new FreshmailApiV3($this->bearer_token);

        return $fmApi->sendTransactionalEmail(
            new TransactionalEmail($recipient, $sender, $birthday->email_subject[$customer->id_lang], $html)
        );
    }

}