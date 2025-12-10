<?php

namespace FreshMail\Service;

use Configuration;
use Tools;

class AccountService
{
    /**
     * @var ApiService
     */
    private $apiService;
    private $customFields = [];

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function addCustomFields($data)
    {
        if (Configuration::get('FRESHMAIL_ADD_RECIPIENT_TO_LIST')) {
            $this->customFields['presta_newsletter'] = (int)$data['newsletter'];
        }

        if (Configuration::get('FRESHMAIL_ADD_RECIPIENT_TO_LIST_EMAIL_PARTNERS')) {
            $this->customFields['presta_partners_newsletter'] = (int)$data['optin'];
        }
    }

    public function add($email)
    {
        if (empty($this->customFields)) {
            return false;
        }

        $fieldsFromList = $this->getFieldsFromList($this->apiService);
        return $this->apiService->addSubscriber([
            'email' => $email,
            'list' => Configuration::get('FRESHMAIL_SIGNUP_HASHLIST'),
            'state' => (int)Configuration::get('FRESHMAIL_SUBSCRIBER_ACTIVE'),
            'custom_fields' => array_merge($this->customFields, $fieldsFromList)
        ]);
    }

    private function getFieldsFromList()
    {
        $fields = [];
        $fieldsArray = $this->apiService->getAllFieldsByIdHashList(Configuration::get('FRESHMAIL_SIGNUP_HASHLIST'));
        foreach ($fieldsArray as $key => $field) {
            if ($value = Tools::getValue($field['tag'])) {
                $fields[$field['tag']] = $value;
            }
        }
        return $fields;
    }
}