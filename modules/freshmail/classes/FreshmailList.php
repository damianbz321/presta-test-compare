<?php

namespace FreshMail;

use Configuration;
use Db;
use DbQuery;
use Exception;
use FreshMail\Api\Client\Service\RequestExecutor;
use FreshMail\ApiV2\Client;
use FreshMail\ApiV2\UnauthorizedException;
use FreshMail\Entity\AsyncJob;
use FreshMail\Entity\Email;
use FreshMail\Entity\EmailToSynchronize;
use Freshmail\Entity\FreshmailSetting;
use FreshMail\Repository\AsyncJobs;
use FreshMail\Repository\FreshmailSettings;
use FreshMail\Repository\Subscribers;
use Validate;
use ZipArchive;

require_once __DIR__ . '/../lib/freshmail-api/vendor/autoload.php';

class FreshmailList extends Freshmail
{
    private $fmSettings = null;
    public function __construct(FreshmailSetting $settings)
    {
        parent::__construct($settings->api_token);
        $this->fmSettings = $settings;
    }

    public function addSubscriber($data = null)
    {
        if($data instanceof Subscriber){
            if (empty($data->email) || empty($this->fmSettings->subscriber_list_hash)) {
                throw new Exception(Configuration::get('FRESHMAIL_SUBMISSION_FAILURE_MESSAGE'));
            }
            $data->list = $this->fmSettings->subscriber_list_hash;
            $data->confirm = $this->fmSettings->send_confirmation;
            $data->state = (!$this->fmSettings->send_confirmation) ? 1 : 2;

            if(!empty($data->custom_fields)){
                foreach ($data->custom_fields as $name => $value){
                    if(!$this->hasField($name)){
                        unset($data->custom_fields[$name]);
                    }
                }
            }

            try {
                $msg['response'] = $this->freshmailApiV2->doRequest('subscriber/add', (array)$data);

                $subscriber = new \FreshMail\Entity\Email();
                $subscriber->email = $data->email;
                $subscriber->hash_list = $this->hashList;
                //$subscriber->status = 'Active';
                $subscriber->add_date = date('Y-m-d H:i:s');
                $subscriber->last_synchronization = date('Y-m-d H:i:s');
                $subscriber->save();

                $synchronized = new \FreshMail\Entity\EmailsSynchronized();
                $synchronized->email = $data->email;
                $synchronized->hash_list = $this->hashList;
                $synchronized->save();

                $msg['success'] = Configuration::get('FRESHMAIL_SUBMISSION_SUCCESS_MESSAGE');
            } catch (Exception $exception) {
                if ($exception->getCode() == 1304) {
                    $msg['error'] = Configuration::get('FRESHMAIL_ALREADY_SUBSCRIBED_MESSAGE');
                } else {
                    $msg['error'] = Configuration::get('FRESHMAIL_SUBMISSION_FAILURE_MESSAGE');
                }
            }

            return $msg;

        } else {
            return parent::addSubscriber($data);
        }

    }

    public function deleteSubscriber($data = null)
    {
        if($data instanceof Subscriber){
            if (empty($data->email) || empty($this->fmSettings->subscriber_list_hash)) {
                throw new Exception(Configuration::get('FRESHMAIL_SUBMISSION_FAILURE_MESSAGE'));
            }
            $data->list = $this->hashList;

            if(!empty($data->custom_fields)){
                foreach ($data->custom_fields as $name => $value){
                    if(!$this->hasField($name)){
                        unset($data->custom_fields[$name]);
                    }
                }
            }

            try {
                $msg['response'] = $this->freshmailApiV2->doRequest('subscriber/delete', (array)$data);

                $repository = new Subscribers(Db::getInstance());
                $repository->deleteByEmailAndList($data->email, $data->list);
            } catch (Exception $exception) {
                return false;
            }

            return $msg;
        }
    }

    public function hasField($tag)
    {
        return parent::hasFieldWithTag($this->fmSettings->subscriber_list_hash, $tag);
    }


}