<?php

namespace FreshMail;

use FreshMail\Repository\FormRepository;

trait HooksForms{

    public function getHooksForm()
    {
        return [
            'displayTop',
            'displayLeftColumnProduct',
            'displayRightColumnProduct',
            'displayFooterProduct',
            'displayHome',
            'displayHeader',
            'freshmailForm'
        ];
    }

    public function hookDisplayTop($params)
    {
        return $this->renderForms('displayTop');
    }

    public function hookDisplayLeftColumnProduct($params)
    {
        return $this->renderForms('displayLeftColumnProduct');
    }

    public function hookDisplayRightColumnProduct($params)
    {
        return $this->renderForms('displayRightColumnProduct');
    }

    public function hookDisplayFooterProduct($params)
    {
        return $this->renderForms('displayFooterProduct');
    }

    public function hookDisplayHome($params)
    {
        return $this->renderForms('displayHome');
    }

    public function hookDisplayHeader($params)
    {
        return
            $this->renderForms('pop_up') .
            $this->renderForms('displayHeader');
    }

    public function hookFreshmailForm($params)
    {
        return $this->renderForms('freshmailForm');
    }

    private function renderForms($hook)
    {
        $forms = (new FormRepository(\Db::getInstance()))->getByHooks(
            $this->context->shop->id,
            $hook
        );

        $fs = (new \FreshMail\Repository\FreshmailSettings(\Db::getInstance()))->findForShop($this->context->shop->id);
        if(empty($fs->api_token)){
            return '';
        }

        $html = '';
        foreach($forms as $form){
            $html .= $this->cache($fs->api_token, $form['form_hash']);
        }

        return $html;
    }

    private function cache($apiToken, $formHash){
        $cache_file =_PS_CACHE_DIR_.'freshmailform_'.$formHash;
        $form = '';

        if (file_exists($cache_file) && (filemtime($cache_file) > (time() - \Freshmail::CACHE_FORM_LIFETIME ))) {
            $form = file_get_contents($cache_file);
        } else {

            $fm = new \FreshMail\Freshmail($apiToken);
            $fmForms = $fm->getForms();
            if(!empty($fmForms[$formHash])){
                $form = $fmForms[$formHash]['html_code'];
            }

            $tmp_file = _PS_CACHE_DIR_.md5( time().$cache_file );
            file_put_contents($tmp_file, $form, LOCK_EX);
            rename( $tmp_file, $cache_file);
        }

        return $form;
    }

    public function clearFormCache(){
        foreach (scandir(_PS_CACHE_DIR_) as $file){
            if(false !== strpos($file, 'freshmailform_')){
                unlink(_PS_CACHE_DIR_. $file);
            }
        }
    }
}