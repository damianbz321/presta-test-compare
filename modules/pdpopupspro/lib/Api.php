<?php
/**
* 2012-2020 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Pop-Ups Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2020 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD PopUps Pro - PrestaShop 1.6.x and 1.7.x Module
* @version   1.3.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      15-12-2020
*/

include_once dirname(__FILE__).'/REST-API/class.rest.php';

class FreshMailApi extends FreshMailRestApi
{
    public function __construct()
    {
        $this->setApiKey(Configuration::get('PD_PP_FM_API_KEY'));
        $this->setApiSecret(Configuration::get('PD_PP_FM_API_SECRET'));
    }
    
    public function getAllList()
    {
        $data = array();
        
        try {
            $responseArray = $this->doRequest('subscribers_list/lists', $data);
            
            $response[0] = array(
                'key' => 0,
                'name' => '---'
            );
            
            usort($responseArray['lists'], function ($a, $b) {
                return strtotime($a['creation_date'])<strtotime($b['creation_date'])?1:-1;
            });
            
            foreach ($responseArray['lists'] as $k => $v) {
                $response[$k+1] = array(
                    'key' => $v['subscriberListHash'],
                    'name' => $v['name'] . ' (' . $v['subscribers_number'] . ')'
                );
            }
            
            return $response;
        } catch (Exception $e) {
            syslog(3, 'Code: '.$e->getCode().' Message: '.$e->getMessage());
        }
    }
    
    public function getAllHashList()
    {
        $res = $this->getAllList();
        $hashList = array();
        foreach ($res as $k => $v) {
            $hashList[] = $v['key'];
        }
        
        if (isset($hashList[0]) && $hashList[0] == 0) {
            unset($hashList[0]);
        }
        
        return $hashList;
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
    
    public function getAllHashListSQL()
    {
        $hashList = $this->getAllHashList();
        $result =  implode(',', array_map(function ($str) {
            return sprintf("'%s'", $str);
        }, $hashList));
        
        return $result;
    }
    
    public function getAllFieldsByIdHashList($idHash = null)
    {
        if (empty($idHash)) {
            return false;
        }
        
        $data = array('hash' => $idHash);
        
        try {
            $responseArray = $this->doRequest('subscribers_list/getFields', $data);
            
            if (isset($responseArray['fields']) && !empty($responseArray['fields'])) {
                return $responseArray['fields'];
            }
            
            return array();
        } catch (Exception $e) {
            echo 'Code: '.$e->getCode().' Message: '.$e->getMessage()."\n";
        }
    }
     
    
    public function addSubscriber($data = null)
    {
        if (empty($data) || empty($data['email']) || empty($data['list'])) {
            throw new Exception('Brak wymaganych danych !');
        }
        
        $response = $this->doRequest('subscriber/add', $data);
        return $response;
    }
    
    public function addFields($fields = array())
    {
        if (empty($fields)) {
            return false;
        }
        
        foreach ($fields as $k => $data) {
            try {
                $response = $this->doRequest('subscribers_list/addField', $data);
            } catch (Exception $e) {
                throw new Exception('Wystąpił błąd podczas dodawania nowego pola!');
            }
        }
    }
}
