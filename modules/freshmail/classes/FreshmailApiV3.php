<?php

namespace FreshMail;

require_once _PS_MODULE_DIR_ . 'freshmail/lib/freshmail-api/vendor/autoload.php';

use FreshMail\Api\Client\FreshMailApiClient;

class FreshmailApiV3 extends FreshMailApiClient
{
    private $bearerToken;

    public function __construct(string $bearerToken)
    {
        $this->bearerToken = $bearerToken;
        parent::__construct($bearerToken);
    }
    public function sendIntegrationInfo(){

        $data = new class() implements \JsonSerializable {
            public function jsonSerialize (){
                return  [
                    'type' => 'plugin',
                    'data' => [
                        'vendor' => 'PrestaShop',
                        'version' => _PS_VERSION_,
                        'ip' => $_SERVER['SERVER_ADDR'],
                        'url' => \Context::getContext()->shop->domain
                    ]
                ];
            }
        };

        try {
            $response = $this->requestExecutor->post('integrations', $data);
            \PrestaShopLogger::addLog('FM ( '.$this->bearerToken.' ) -> Success endpoint response code: '. $response->getStatusCode(), 1, null, null, null , true);

            if( 200 == $response->getStatusCode() ){
                return true;
            }

        } catch (\Exception $e){
            \PrestaShopLogger::addLog('FM ( '.$this->bearerToken.' ) ->endpoint exception: '. $e->getMessage());
        }

        return false;
    }

    public function sendTransactionalEmail(TransactionalEmail $transactionalEmail){
        try {
             $response = $this->requestExecutor->post('messaging/emails', $transactionalEmail);
            \PrestaShopLogger::addLog('FM ( '.$this->bearerToken.' ) -> Success endpoint response code: '. $response->getStatusCode(), 1, null, null, null , true);

            if( 201 == $response->getStatusCode() ){
                return true;
            }

        } catch (\Exception $e){
            \PrestaShopLogger::addLog('FM ( '.$this->bearerToken.' ) ->endpoint exception: '. $e->getMessage());
        }

        return false;
    }



}