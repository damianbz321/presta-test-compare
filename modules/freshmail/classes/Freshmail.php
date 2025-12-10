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
use FreshMail\Repository\AsyncJobs;
use FreshMail\Repository\EmailsSynchronized;
use Validate;
use ZipArchive;

require_once __DIR__ . '/../lib/freshmail-api/vendor/autoload.php';

class Freshmail
{

    private $token = '';

    protected $freshmailApiV2 = null;

    protected $freshmailApiV3 = null;

    const SYNC_SUCCESS_ID_STATUS = 2;

    const SYNC_FAIL_ID_STATUS = 3;

    public function __construct($token)
    {
        $this->token = $token;
        $this->freshmailApiV2 = new Client($token);
        $this->freshmailApiV3 = new FreshmailApiV3($token);
    }

    public function getLists()
    {
        try {
            $list = $this->freshmailApiV2->doRequest('subscribers_list/lists');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!empty($list['lists'])) {
            usort($list['lists'], function ($a, $b) {
                return strcmp($a["name"], $b["name"]);
            });

            return $list['lists'];
        }

        return [];
    }

    public function addList($name, $description = '')
    {
        $data = [
            'name'        => $name,
            'description' => $description,
            'custom_fields' => [
                [
                    'name' => \Context::getContext()->getTranslator()->trans('First name', [],'Admin.Global'),
                    'tag' => \Freshmail::NAME_TAG
                ],
            ]
        ];

        try {
            $result = $this->freshmailApiV2->doRequest('subscribers_list/create', $data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if ('OK' == $result['status']) {
            return $result['hash'];
        }

        return false;
    }

    public function hasFieldWithTag($hashList, $tag) {

        $fields = $this->getAllFieldsByIdHashList($hashList);
        if(empty($fields)){
            return false;
        }

        foreach ($fields as $field){
            if($tag == $field['tag']) {
                return true;
            }
        }
        return false;
    }

    public function addFieldToList($hashList, $tag, $name) {

        $fieldData = array(
            'hash'  => $hashList,
            'name'  => $name,
            'tag'   => $tag
        );
        $customFields = $this->getAllFieldsByIdHashList($hashList);

        if (is_array($customFields) && !in_array($fieldData['tag'], array_column($customFields, 'tag'))) {
            return $this->addField($fieldData);
        }

        return false;
    }

    public function addField($fields = array()) {

        if(empty($fields)) {
            return false;
        }
        try {
            return $this->freshmailApiV2->doRequest('subscribers_list/addField', $fields);
        } catch (Exception $e) {
            throw new Exception('Wystąpił błąd podczas dodawania nowego pola!');
        }
    }

    public function addSubscriber($data = null)
    {
        if (empty($data) || empty($data['email']) || empty($data['list'])) {
            throw new Exception(Configuration::get('FRESHMAIL_SUBMISSION_FAILURE_MESSAGE'));
        }

        try {
            $msg['response'] = $this->freshmailApiV2->doRequest('subscriber/add', $data);

            $subscriber = new \FreshMail\Entity\Email();
            $subscriber->email = $data['email'];
            $subscriber->hash_list = $data['list'];
            $subscriber->status = 'Active';
            $subscriber->add_date = date('Y-m-d H:i:s');
            $subscriber->last_synchronization = date('Y-m-d H:i:s');
            $subscriber->save();

            $synchronized = new \FreshMail\Entity\EmailsSynchronized();
            $synchronized->email = $data['email'];
            $synchronized->hash_list = $data['list'];
            $synchronized->save();

            if(!empty($data['custom_fields'])){
                
            }

            $msg['success'] = Configuration::get('FRESHMAIL_SUBMISSION_SUCCESS_MESSAGE');
        } catch (Exception $exception) {
            if ($exception->getCode() == 1304) {
                $msg['error'] = Configuration::get('FRESHMAIL_ALREADY_SUBSCRIBED_MESSAGE');
            } else {
                $msg['error'] = Configuration::get('FRESHMAIL_SUBMISSION_FAILURE_MESSAGE');
            }
        }

        return $msg;
    }

    public function addSubscribers($listHash, SubscriberCollection $collection, int $state, bool $confirm = false)
    {
        $subscribers = [];
        foreach ($collection as $sub) {
            $subscriber = [
                'email' => $sub->email,
            ];

            if (!empty($sub->custom_fields)) {
                foreach ($sub->custom_fields as $name => $value){
                    if($this->hasFieldWithTag($listHash, $name)){
                        $subscriber['custom_fields'][$name] = $value;
                    }
                }
            }
            $subscribers[] = $subscriber;
        }

        if (empty($subscribers)) {
            return;
        }


        $data = [
            'list' => $listHash,
            'subscribers' => $subscribers,
            'confirm' => (int)$confirm,
            'state' => $state
        ];
        return $this->freshmailApiV2->doRequest("subscriber/addMultiple", $data);
    }

    public function getAllFieldsByIdHashList($idHash = null)
    {
        if (empty($idHash)) {
            return false;
        }

        $data = array('hash' => $idHash);

        try {
            $responseArray = $this->freshmailApiV2->doRequest('subscribers_list/getFields', $data);
            if (isset($responseArray['fields']) && !empty($responseArray['fields'])) {
                return $responseArray['fields'];
            }

            return array();
        } catch (Exception $e) {
            echo 'Code: ' . $e->getCode() . ' Message: ' . $e->getMessage() . "\n";
        }
    }

    public function getAllHashList()
    {
        $res = $this->getAllList();
        $hashList = array();
        foreach ($res as $k => $v) {
            $hashList[] = $v['key'];
        }

        return $hashList;
    }

    public function getAllList()
    {
        $data = array();

        try {
            $responseArray = $this->freshmailApiV2->doRequest('subscribers_list/lists', $data);

            /*$response[0] = array(
                'key' => 0,
                'name' => '---'
            );*/

            usort($responseArray['lists'], function ($a, $b) {
                return strtotime($a['creation_date']) < strtotime($b['creation_date']) ? 1 : -1;
            });

            $response = [];
            foreach ($responseArray['lists'] as $k => $v) {
                $response[] = array(
                    'key' => $v['subscriberListHash'],
                    'name' => $v['name'] . ' (' . $v['subscribers_number'] . ')'
                );
            }

            return $response;
        } catch (Exception $e) {
            syslog(3, 'Code: ' . $e->getCode() . ' Message: ' . $e->getMessage());
        }
    }

    public function getAllFieldHashByHashList($id = 0)
    {
        $res = $this->getAllFieldsByIdHashList($id);
        $hashList = array();
        foreach ($res as $k => $v) {
            if (!empty($v) && isset($v['hash']) && isset($v['tag'])) {
                $hashList[$v['hash']] = $v;
            }
        }

        return $hashList;
    }

    public function check()
    {
        try {
            $response = $this->freshmailApiV2->doRequest('ping');
        }
        catch (UnauthorizedException $e) {
            $response['status'] = false;
        }catch (Exception $e) {
            $response['status'] = 'Error message: ' . $e->getMessage() . ', Error code: ' . $e->getCode() /*. ', HTTP code: ' . $response-> ->getHttpCode()*/;
        }

        return $response;
    }


    public function getFileFromJob($idJob)
    {
        $file = md5(time());

        try {
            $zipStream = $this->freshmailApiV2->doRequest("/rest/async_result/getFile", [
                'id_job' => $idJob
            ], true);


            Tools::checkTmpDir();

            if (!mkdir(\Freshmail::TMP_DIR . $file)) {
                throw new Exception('Error creating dir: ' . \Freshmail::TMP_DIR . $file);
            }

            file_put_contents(\Freshmail::TMP_DIR . $file . '.zip', $zipStream);

            $zip = new ZipArchive;
            if (TRUE === $zip->open(\Freshmail::TMP_DIR . $file . '.zip')) {
                // Check if destination is writable
                $zip->extractTo(\Freshmail::TMP_DIR . $file);
                $zip->close();

            } else {
                throw new Exception('Error processing archive');
            }

        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

        return $file;
    }

    public function triggerExport($listHash)
    {
        $data = [
            'list' => $listHash,
        ];

        $response = $this->freshmailApiV2->doRequest("/rest/async_subscribers_list/export", $data);
        if ('OK' == $response['status']) {
            $aj = new AsyncJob();
            $aj->hash_list = $listHash;
            $aj->id_job = $response['data']['id_job'];
            $aj->save();
            return $aj;
        }
        return false;
    }

    public function getSubscriber($listHash, $email){

        $uri = sprintf('subscriber/get/%s/%s', $listHash, $email);
        try {
            $response = $this->freshmailApiV2->doRequest($uri);
        } catch (Exception $e) {
            $response['status'] = 'Error message: '.$e->getMessage().', Error code: '.$e->getCode().', HTTP code: '.$this->freshmailApiV2->getHttpCode();
        }catch (UnauthorizedException $e) {
            $response['status'] = false;
        }

        return $response;
    }

    public function triggerSendSubscribers($hashList, SubscriberCollection $subscribers)
    {
        foreach ($subscribers as $subsciber) {
            $ets = new EmailToSynchronize();
            $ets->email = $subsciber->email;
            $ets->name = $subsciber->name;
            $ets->hash_list = $hashList;
            try {
                $ets->save();
            } catch (Exception $e) {
            }
        }

        $query = new DbQuery();
        $query
            ->select('*')
            ->from('customer')
            ->where('newsletter = 1');

        $db = Db::getInstance();
        foreach (Db::getInstance()->executeS($query) as $customer) {
            $db->insert(
                'freshmail_emails_to_synchronize',
                [
                    'hash_list' => $hashList,
                    'email' => $customer['email'],
                    'name' => $customer['name']
                ]
            );
        }
    }

    public function pingAsyncJobStatus($job)
    {
        if (is_int($job)) {
            $job = (new AsyncJobs(Db::getInstance()))->findByIdJob($job);
        }

        if (!Validate::isLoadedObject($job)) {
            return false;

        }
        /*        if(!empty($job->parts)){
                    return true;
                }*/

        $response = $this->freshmailApiV2->doRequest("/rest/async_result/get", [
            'id_job' => $job->id_job
        ]);

        if ('OK' != $response['status']) {
            return false;
        }

        if (!empty($response['data']['job_status'])
            && in_array($response['data']['job_status'], [self::SYNC_SUCCESS_ID_STATUS, self::SYNC_FAIL_ID_STATUS])
            && !empty($response['data']['parts'])) {
            $job->parts = (int)$response['data']['parts'];

        }
        $job->job_status = (int)$response['data']['job_status'];
        $job->last_sync = date('Y-m-d H:i:s');
        $job->save();
    }


    public function getEmailsTemplates($directory_name = '')
    {
        $list = [];
        $params = [];
        if(!empty($directory_name)){
            $params['directory_name'] = $directory_name;
        }

        try{
            $response = $this->freshmailApiV2->doRequest("/rest/templates/lists", $params);
            if(!empty($response['data'])){
                $list = $response['data'];
            }
        } catch (Exception $e){}

        return $list;
    }

    private static $templateCache = [];

    private function getTemplate($hash){
        if(!isset(self::$templateCache[$hash])){
            try {
                self::$templateCache[$hash] = $this->freshmailApiV2->doRequest("/rest/templates/template", ['hash' => $hash]);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
        return self::$templateCache[$hash];
    }

    public function getTemplateHtml($hash){
        $response = $this->getTemplate($hash);
        if(isset($response['data']['content_reb'])){
            return $response['data']['content_reb'];
        }
        return '';
    }

    public function getProductHtml($hash){
        $response = $this->getTemplate($hash);
        if(isset($response['data']['product'])){
            return $response['data']['product'];
        }
        return '';
    }

    public function getForms($listHash = ''){
        $response = $this->freshmailApiV2->doRequest("/rest/form_library/get");
        foreach ($response['data'] as &$data){
            if('popup' == $data['form_type']){
                $data['list_name'] = 'Pop Up: '.$data['list_name'];
            }
        }
        $result = $response['data'];
        if(!empty($listHash)){
            $result = $this->filterFormByListHash($result, $listHash);
        }
        return array_combine(array_column($result, 'id_hash'),$result);
    }

    private function filterFormByListHash($forms, $listHash){
        $result = [];
        foreach ($forms as $form){
            if($listHash == $form['list_hash']){
                $result[] = $form;
            }
        }
        return $result;
    }
}