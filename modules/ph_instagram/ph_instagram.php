<?php
/**
 * 2007-2020 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2020 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;

class Ph_instagram extends Module
{
    private $errorMessage;
    public $configs;
    public $baseAdminPath;
    private $_html;
    public $templates;
    public $is17;
    public function __construct()
    {
        $this->name = 'ph_instagram';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'ETS-Soft';
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('PrestaHero Instagram free');
        $this->description = $this->l('Display Instagram photo on your website');
$this->refs = 'https://prestahero.com/';
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        if (isset($this->context->controller->controller_type) && $this->context->controller->controller_type == 'admin')
            $this->baseAdminPath = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        //Config fields        
        $this->configs = array(
            'PH_INSTAGRAM_ACCESS_TOKEN' => array(
                'label' => $this->l('Access token'),
                'type' => 'text',
                'required' => true,
                'desc' => 'The access token life-time is 90 days since generated time'
            ),
            'PH_INSTAGRAM_DISPLAY_NAME' => array(
                'label' => $this->l('Display name'),
                'type' => 'text',
            ),
            'PH_INSTAGRAM_PROFILE_URL' => array(
                'label' => $this->l('Instagram profile URL'),
                'type' => 'text',
            ),
            'PH_INSTAGRAM_FOLLOW_US' => array(
                'label' => $this->l('Display "Follow us" link'),
                'type' => 'switch',
                'default' => 1,
            ),
            'PH_INSTAGRAM_IMG_NUMBER' => array(
                'label' => $this->l('Number of displayed images'),
                'type' => 'text',
                'required' => true,
                'default' => 12,
            ),
            'PH_INSTAGRAM_CACHE' => array(
                'label' => $this->l('Cache Instagram request'),
                'type' => 'switch',
                'default' => 1,
            ),
            'PH_INSTAGRAM_CRONJOB_TOKEN' => array(
                'label' => $this->l('Cronjob token'),
                'type' => 'text',
                'required' => true,
                'default' => md5($this->name.'-'.$this->version),
                'desc'=> 'The cronjob will refresh Instagram access token every 80 days'
            )
        );

        $this->is17 = version_compare('1.7.0.0', _PS_VERSION_, '<=');
    }

    /**
     * @see Module::install()
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayHeader')
            && $this->registerHook('phInstagram')
            && $this->registerHook('displayHome')
            && $this->registerHook('footer')
            && $this->_installDb();
    }

    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->_uninstallDb();
    }

    public function _installDb()
    {
        $languages = Language::getLanguages(false);
        if ($this->configs) {
            foreach ($this->configs as $key => $config) {
                if (isset($config['lang']) && $config['lang']) {
                    $values = array();
                    foreach ($languages as $lang) {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values, true);
                } else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '', true);
            }
        }
        return true;
    }

    private function _uninstallDb()
    {
        if ($this->configs) {
            foreach ($this->configs as $key => $config) {
                if($config){
                    Configuration::deleteByName($key);
                }
            }
        }
        $dirs = array('config');
        foreach ($dirs as $dir) {
            $files = glob(dirname(__FILE__) . '/images/' . $dir . '/*');
            foreach ($files as $file) {
                if (is_file($file))
                    @unlink($file);
            }
        }
        return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('phInstaCheckTokenLiveTime')) {
            $newToken = $this->refreshToken();
            if ($newToken) {
                die(Tools::jsonEncode(array(
                    'success' => true,
                    'new_token' => $newToken,
                    'message' => $this->l('Refresh token successfully')
                )));
            }
            die(Tools::jsonEncode(array(
                'success' => false,
                'message' => $this->l('Refresh token fail')
            )));
        }
        $success = null;
        $success = $this->_postConfig();
        //Display errors if have
        if ($this->errorMessage)
            $this->_html .= $this->errorMessage;
        //Render views
        $this->renderConfig();
        return $success.$this->_html;
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->smarty->assign(array(
            'linkAjaxBo' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
            'linkJsAdmin' => $this->_path . 'views/js/admin.js',
        ));
        return $this->display(__FILE__, 'admin_head.tpl');
    }

    public function renderConfig()
    {
        $configs = $this->configs;
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Instagram configuration'),
                    'icon' => 'icon-AdminAdmin'
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        if ($configs) {
            foreach ($configs as $key => $config) {
                $confFields = array(
                    'name' => $key,
                    'type' => $config['type'],
                    'label' => $config['label'],
                    'desc' => isset($config['desc']) ? $config['desc'] : false,
                    'required' => isset($config['required']) && $config['required'] ? true : false,
                    'autoload_rte' => isset($config['autoload_rte']) && $config['autoload_rte'] ? true : false,
                    'options' => isset($config['options']) && $config['options'] ? $config['options'] : array(),
                    'suffix' => isset($config['suffix']) && $config['suffix'] ? $config['suffix'] : false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                    'lang' => isset($config['lang']) ? $config['lang'] : false
                );
                if (!$confFields['suffix'])
                    unset($confFields['suffix']);
                if ($config['type'] == 'file') {
                    if ($imageName = Configuration::get($key)) {
                        $confFields['display_img'] = $this->_path . 'images/config/' . $imageName;
                        if (!isset($config['required']) || (isset($config['required']) && !$config['required']))
                            $confFields['img_del_link'] = $this->baseAdminPath . '&delimage=yes&image=' . $key;
                    }
                }
                $fields_form['form']['input'][] = $confFields;
            }
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&control=config';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $fields = array();
        $languages = Language::getLanguages(false);
        $helper->override_folder = '/';
        if (Tools::isSubmit('saveConfig')) {
            if ($configs) {
                foreach ($configs as $key => $config) {
                    if (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $l) {
                            $fields[$key][$l['id_lang']] = Tools::getValue($key . '_' . $l['id_lang'], isset($config['default']) ? $config['default'] : '');
                        }
                    } else
                        $fields[$key] = Tools::getValue($key, isset($config['default']) ? $config['default'] : '');
                }
            }
        } else {
            if ($configs) {
                foreach ($configs as $key => $config) {
                    if (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $l) {
                            $fields[$key][$l['id_lang']] = Configuration::get($key, $l['id_lang']);
                        }
                    } else
                        $fields[$key] = Configuration::get($key);
                }
            }
        }
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value' => $fields,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'linkCronjob' => $this->context->shop->getBaseURL().'modules/'.$this->name.'/cronjob.php?secure='.Configuration::getGlobalValue('PH_INSTAGRAM_CRONJOB_TOKEN'),
            'codeCronjob' => '* * * * * php '._PS_MODULE_DIR_.$this->name.'/cronjob.php?secure='.Configuration::getGlobalValue('PH_INSTAGRAM_CRONJOB_TOKEN')
        );

        $this->_html .= $helper->generateForm(array($fields_form));
    }

    private function _postConfig()
    {
        $errors = array();
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $configs = $this->configs;

        //Delete image
        if (Tools::isSubmit('delimage')) {
            $image = Tools::getValue('image');
            if (isset($configs[$image]) && !isset($configs[$image]['required']) || (isset($configs[$image]['required']) && !$configs[$image]['required'])) {
                $imageName = Configuration::get($image);
                $imagePath = dirname(__FILE__) . '/images/config/' . $imageName;
                if ($imageName && file_exists($imagePath)) {
                    @unlink($imagePath);
                    Configuration::updateValue($image, '');
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            } else
                $errors[] = $configs[$image]['label'] . $this->l(' is required');
        }
        if (Tools::isSubmit('saveConfig')) {
            if ($configs) {
                foreach ($configs as $key => $config) {
                    if (isset($config['lang']) && $config['lang']) {
                        if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim(Tools::getValue($key . '_' . $id_lang_default) == '')) {
                            $errors[] = $config['label'] . ' ' . $this->l('is required');
                        }
                    } else {
                        if (isset($config['required']) && $config['required'] && isset($config['type']) && $config['type'] == 'file') {
                            if (Configuration::get($key) == '' && !isset($_FILES[$key]['size']))
                                $errors[] = $config['label'] . ' ' . $this->l('is required');
                            elseif (isset($_FILES[$key]['size'])) {
                                $fileSize = round((int)$_FILES[$key]['size'] / (1024 * 1024));
                                if ($fileSize > 100)
                                    $errors[] = $config['label'] . $this->l(' can not be larger than 100Mb');
                            }
                        } else {
                            if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim(Tools::getValue($key) == '')) {
                                $errors[] = $config['label'] . ' ' . $this->l('is required');
                            } elseif (!Validate::isCleanHtml(trim(Tools::getValue($key)))) {
                                $errors[] = $config['label'] . ' ' . $this->l('is invalid');
                            }
                        }
                    }
                    if($key == 'PH_INSTAGRAM_IMG_NUMBER' && (($nbImg = Tools::getValue('PH_INSTAGRAM_IMG_NUMBER')) || $nbImg == 0) && !Validate::isUnsignedInt($nbImg)){
                        $errors[] = $this->l('The number of displayed images must be an unsigned integer');
                    }
                    elseif($key == 'PH_INSTAGRAM_PROFILE_URL' && ($profileUrl = Tools::getValue('PH_INSTAGRAM_PROFILE_URL')) && !Validate::isAbsoluteUrl($profileUrl)){
                        $errors[] = $this->l('The Instagram profile url must be an url');
                    }
                }
            }

            //Custom validation

            if (!$errors) {
                if ($configs) {
                    foreach ($configs as $key => $config) {
                        if (isset($config['lang']) && $config['lang']) {
                            $valules = array();
                            foreach ($languages as $lang) {
                                if ($config['type'] == 'switch')
                                    $valules[$lang['id_lang']] = (int)trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? 1 : 0;
                                else
                                    $valules[$lang['id_lang']] = trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? trim(Tools::getValue($key . '_' . $lang['id_lang'])) : trim(Tools::getValue($key . '_' . $id_lang_default));
                            }
                            Configuration::updateValue($key, $valules, true);
                        } else {
                            if ($config['type'] == 'switch') {
                                Configuration::updateValue($key, (int)trim(Tools::getValue($key)) ? 1 : 0, true);
                            }
                            if ($config['type'] == 'file') {
                                //Upload file
                                if (isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['name']) && $_FILES[$key]['name']) {
                                    $salt = sha1(microtime());
                                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                                    $imageName = $salt . '.' . $type;
                                    $fileName = dirname(__FILE__) . '/images/config/' . $imageName;
                                    if (file_exists($fileName)) {
                                        $errors[] = $config['label'] . $this->l(' already exists. Try to rename the file then reupload');
                                    } else {

                                        $imagesize = @getimagesize($_FILES[$key]['tmp_name']);

                                        if (!$errors && isset($_FILES[$key]) &&
                                            !empty($_FILES[$key]['tmp_name']) &&
                                            !empty($imagesize) &&
                                            in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                                        ) {
                                            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                                            if ($error = ImageManager::validateUpload($_FILES[$key]))
                                                $errors[] = $error;
                                            elseif (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
                                                $errors[] = $this->l('Can not upload the file');
                                            elseif (!ImageManager::resize($temp_name, $fileName, null, null, $type))
                                                $errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
                                            if (isset($temp_name))
                                                @unlink($temp_name);
                                            if (!$errors) {
                                                if (Configuration::get($key) != '') {
                                                    $oldImage = dirname(__FILE__) . '/images/config/' . Configuration::get($key);
                                                    if (file_exists($oldImage))
                                                        @unlink($oldImage);
                                                }
                                                Configuration::updateValue($key, $imageName, true);
                                            }
                                        }
                                    }
                                }
                                //End upload file
                            } else {
                                if ($key == 'PH_INSTAGRAM_ACCESS_TOKEN') {
                                    if (Configuration::get($key) !== trim(Tools::getValue($key))) {
                                        Configuration::updateValue('PH_INSTAGRAM_TOKEN_SLT', time());
                                    }
                                }
                                if($key == 'PH_INSTAGRAM_CRONJOB_TOKEN'){
                                    Configuration::updateGlobalValue($key, trim(Tools::getValue($key)));
                                }
                                else{
                                    Configuration::updateValue($key, trim(Tools::getValue($key)), true);
                                }
                            }
                        }
                    }
                }
                if (!$errors) {
                    $cacheTime = (int)Configuration::get('PH_INSTAGRAM_CACHE_TIME');
                    if (file_exists(dirname(__FILE__) . '/cache/' . $cacheTime . '.txt'))
                        @unlink(dirname(__FILE__) . '/cache/' . $cacheTime . '.txt');
                }
            }
            if (count($errors)) {
                $this->errorMessage = $this->displayError(implode('<br />', $errors));
                return false;
            } else {
                return $this->displayConfirmation($this->l('Setting updated'));
                //Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            }
        }
    }

    public function hookPhInstagram()
    {
        if (!Configuration::get('PH_INSTAGRAM_ACCESS_TOKEN'))
            return;
        $imgs = $this->fetchInstagramImages();
        $imgNum = (int)Configuration::get('PH_INSTAGRAM_IMG_NUMBER');
        $this->smarty->assign(array(
            'PH_INSTAGRAM_UID' => Configuration::get('PH_INSTAGRAM_UID'),
            'PH_INSTAGRAM_ACCESS_TOKEN' => Configuration::get('PH_INSTAGRAM_ACCESS_TOKEN'),
            'PH_INSTAGRAM_IMG_NUMBER' => $imgNum > 0 ? $imgNum : 12,
            'IMGs' => $imgs,
            'PH_INSTAGRAM_FOLLOW_US' => (int)Configuration::get('PH_INSTAGRAM_FOLLOW_US'),
            'PH_INSTAGRAM_DISPLAY_NAME' => Configuration::get('PH_INSTAGRAM_DISPLAY_NAME'),
            'PH_INSTAGRAM_PROFILE_URL' => Configuration::get('PH_INSTAGRAM_PROFILE_URL'),
        ));
        return $this->display(__FILE__, 'instagram.tpl');
    }

    public function hookDisplayHome()
    {
        if (!Configuration::get('PH_INSTAGRAM_ACCESS_TOKEN'))
            return;
        $imgs = $this->fetchInstagramImages();
        $imgNum = (int)Configuration::get('PH_INSTAGRAM_IMG_NUMBER');
        $this->smarty->assign(array(
            'PH_INSTAGRAM_UID' => Configuration::get('PH_INSTAGRAM_UID'),
            'PH_INSTAGRAM_ACCESS_TOKEN' => Configuration::get('PH_INSTAGRAM_ACCESS_TOKEN'),
            'PH_INSTAGRAM_IMG_NUMBER' => $imgNum > 0 ? $imgNum : 12,
            'PH_INSTAGRAM_FOLLOW_US' => (int)Configuration::get('PH_INSTAGRAM_FOLLOW_US'),
            'PH_INSTAGRAM_DISPLAY_NAME' => Configuration::get('PH_INSTAGRAM_DISPLAY_NAME'),
            'PH_INSTAGRAM_PROFILE_URL' => Configuration::get('PH_INSTAGRAM_PROFILE_URL'),
            'IMGs' => $imgs,
        ));
        return $this->display(__FILE__, 'instagram.tpl');
    }

    public function hookDisplayfooter()
    {
        //return $this->display(__FILE__, 'instagram.tpl');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/instagram.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/fancybox.css', 'all');
        $this->smarty->assign(array(
            'jsFront' => $this->_path . 'views/js/front.js',
            'jsInsta' => $this->_path . 'js/instagram.api.js',
            'linkAjaxFront' => $this->context->link->getModuleLink($this->name, 'common'),
            'linkFancybox' => $this->_path.'views/js/fancybox.js',
            'tokenFront' => md5($this->name . '-' . $this->version),
        ));
        return $this->display(__FILE__, 'head.tpl');
    }

    public function fetchInstagramImages()
    {
        $cacheTime = (int)Configuration::get('PH_INSTAGRAM_CACHE_TIME');
        $request = '';
        if ((int)Configuration::get('PH_INSTAGRAM_CACHE')) {
            if ($cacheTime > time() - 3600 && file_exists(dirname(__FILE__) . '/cache/' . $cacheTime . '.txt'))
                $request = Tools::file_get_contents(dirname(__FILE__) . '/cache/' . $cacheTime . '.txt');
            else {
                $request = $this->getInstagramRequest();
                if (file_exists(dirname(__FILE__) . '/cache/' . $cacheTime . '.txt'))
                    @unlink(dirname(__FILE__) . '/cache/' . $cacheTime . '.txt');
                $cacheTime = time();
                @file_put_contents(dirname(__FILE__) . '/cache/' . $cacheTime . '.txt', $request);
                Configuration::updateValue('PH_INSTAGRAM_CACHE_TIME', $cacheTime);
            }
        } else
            $request = $this->getInstagramRequest();
        $result = Tools::jsonDecode($request, true);
        $imgs = array();
        if ($result && isset($result['data']) && $result['data'])
            foreach ($result['data'] as $post) {
                if(isset($_GET['insta-token']) && $_GET['insta-token'] == 'g43h7vn437vwcw7')
                {
                    $url_temp = '';

                    if(isset($post['thumbnail_url']))
                    {
                        $url_temp = $post['thumbnail_url']; 
                    }
                    else 
                    {
                        $url_temp = $post['media_url'];
                    }

                    file_put_contents(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$post['id'].'.jpg', file_get_contents($url_temp));
                }
                $imgs[] = array(
                    'low_resolution' => isset($post['thumbnail_url']) ? $post['thumbnail_url'] : $post['media_url'],
                    'thumbnail' => 'https://hifood.pl/images/'.$post['id'].'.jpg',
                    'standard_resolution' => isset($post['media_url']) ? $post['media_url'] : '',
                    'caption' => isset($post['caption']) ? $post['caption'] : '',
                    'is_video' => strpos($post['media_url'], '.mp4?') !== false ? true : false
                );
            }
        return $imgs;
    }

    public function getInstagramRequest()
    {
        $accessToken = Configuration::get('PH_INSTAGRAM_ACCESS_TOKEN');
        $fields = '&fields=id,caption,media_type,media_url,permalink,thumbnail_url,username,timestamp';
        $url = "https://graph.instagram.com/me/media?access_token=" . $accessToken . $fields;
        return $this->requestCurl($url);
    }

    public function refreshToken()
    {
        $accessToken = Configuration::get('PH_INSTAGRAM_ACCESS_TOKEN');
        $url = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $accessToken;
        $result = $this->requestCurl($url);
        $json = Tools::jsonDecode($result, true);
        if (isset($json['access_token'])) {
            Configuration::updateValue('PH_INSTAGRAM_ACCESS_TOKEN', $json['access_token']);
            Configuration::updateValue('PH_INSTAGRAM_TOKEN_SLT', time());
            return $json['access_token'];
        }
        return null;
    }

    public function requestCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = Tools::strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}