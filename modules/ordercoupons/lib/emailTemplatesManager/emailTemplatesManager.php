<?PHP
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * Email Templates Manager
 * version 1.7.1
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

class ordercouponsEmailTemplatesManager extends ordercoupons
{
    public $addon;
    public $availableTemplateVars;

    public function __construct($addon = null, $availableTemplateVars = false)
    {
        $this->availableTemplateVars = $availableTemplateVars;
        $this->addon = $addon;
        if (Tools::getValue('ajax') != 1 && Tools::getValue('configure') != $addon && Tools::getValue('emailTemplatesManager') != 1) {
            return;
        }
        if (Tools::getValue('emailTemplatesManager')) {
            $this->assignSmartyVariables();
        }

        if (Tools::getValue('emailTemplatesManagerTestEmail') == 1 && Tools::getValue('ajax') == 1 && Tools::getValue('name', 'false') != 'false') {
            echo $this->sendTestEmailTemplate(Tools::getValue('name', 'reminder'));
        }
        if (Tools::getValue('emailTemplatesManager') == 1 && Tools::getValue('ajax') == 1 && Tools::getValue('name', 'false') != 'false' && Tools::getValue('updateconfiguration', 'false') != 'false') {
            echo $this->generateEditTemplateForm(Tools::getValue('name'));
        } else if (Tools::getValue('emailTemplatesManager') == 1 && Tools::getValue('ajax') == 1 && Tools::getValue('name', 'false') != 'false' && Tools::getValue('deleteconfiguration', 'false') != 'false') {
            $removed_languages = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $removed_languages[$lang['iso_code']] = true;
                $this->removeTemplate($lang['iso_code'], Tools::getValue('name', 'template-name'));
            }
            if (!isset($removed_languages['en'])) {
                $this->removeTemplate('en', Tools::getValue('name', 'template-name'));
            }
        } else if (Tools::getValue('createNewTemplate') == 1 && Tools::getValue('ajax') == 1) {
            $created_languages = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $created_languages[$lang['iso_code']] = true;
                $this->createNewTemplate($lang['iso_code'], Tools::getValue('name', 'template-name'));
            }
            if (!isset($created_languages['en'])) {
                $this->createNewTemplate('en', Tools::getValue('name', 'template-name'));
            }
        } else if (Tools::getValue('refreshListOfTemplates') == 1 && Tools::getValue('ajax') == 1) {
            echo $this->getFilesArray();
        } else if (Tools::getValue('refreshListOfTemplatesSelect') == 1 && Tools::getValue('ajax') == 1) {
            echo $this->generaterefreshListOfTemplatesSelect();
        } else if (Tools::getValue('emailTemplateSave') == 1 && Tools::getValue('ajax') == 1) {
            foreach (Language::getLanguages(false) AS $lang) {
                $this->saveTemplate($lang['iso_code'], Tools::getValue('etm_name', 'template-name'), Tools::getValue('etm_html'), Tools::getValue('etm_txt'));
            }
        } else if (Tools::getValue('emailTemplatesManager') == 1 && Tools::getValue('ajax') == 1) {
            echo $this->generateForm();
        }
    }

    public function sendTestEmailTemplate($name)
    {
    }

    public function saveTemplate($lang = 'en', $name = 'template-name', $html, $txt)
    {
        if (file_exists("../modules/" . $this->addon . "/mails/" . $lang)) {
            $file_html = "../modules/" . $this->addon . "/mails/" . $lang . '/' . $name . '.html';
            $file_txt = "../modules/" . $this->addon . "/mails/" . $lang . '/' . $name . '.txt';
            if (file_exists($file_html)) {
                $file = fopen($file_html, "w");
                fwrite($file, (isset($html[$lang]) ? $html[$lang] : ''));
                fclose($file);
            }
            if (file_exists($file_txt)) {
                $file = fopen($file_txt, "w");
                fwrite($file, (isset($txt[$lang]) ? $txt[$lang] : ''));
                fclose($file);
            }
        }
    }

    public function removeTemplate($lang = 'en', $name = 'template-name')
    {
        if (file_exists("../modules/" . $this->addon . "/mails/" . $lang)) {
            $file_html = "../modules/" . $this->addon . "/mails/" . $lang . '/' . $name . '.html';
            $file_txt = "../modules/" . $this->addon . "/mails/" . $lang . '/' . $name . '.txt';
            if (file_exists($file_html)) {
                unlink($file_html);
            }
            if (file_exists($file_txt)) {
                unlink($file_txt);
            }
        }
    }

    public function createNewTemplate($lang = 'en', $name = 'template-name')
    {
        if (!file_exists("../modules/" . $this->addon . "/mails/" . $lang)) {
            mkdir("../modules/" . $this->addon . "/mails/" . $lang, 0777, true);
        } else {
            $file_html = "../modules/" . $this->addon . "/mails/" . $lang . '/' . $name . '.html';
            $file_txt = "../modules/" . $this->addon . "/mails/" . $lang . '/' . $name . '.txt';
            if (!file_exists($file_html)) {
                $file = fopen($file_html, "w");
                fwrite($file, '');
                fclose($file);
            }
            if (!file_exists($file_txt)) {
                $file = fopen($file_txt, "w");
                fwrite($file, '');
                fclose($file);
            }
        }
    }

    public function getMailFilesArray()
    {
        $dir = "../modules/" . $this->addon . "/mails/" . Context::getContext()->language->iso_code . "/";
        $dh = opendir($dir);
        $files = array();
        $exists = array();
        while (false !== ($filename = readdir($dh))) {
            if ($filename != ".." && $filename != "." && $filename != "" && $filename != "index.php") {
                $explode = explode(".", $filename);
                if (!isset($exists[$explode[0]])) {
                    $exists[$explode[0]] = true;
                    $files[]['name'] = $explode[0];
                }
            }
        }
        return $files;
    }

    public function getFilesArray()
    {
        if (Tools::getValue('ajax', 'false') == 'false') {
            return;
        }
        $helper = new HelperList();
        $helper->table_id = 'etm';
        $helper->_default_pagination = 50;
        $helper->no_link = true;
        $helper->simple_header = true;
        $helper->shopLinkType = '';
        $helper->actions = array('edit', 'delete');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->module = $this;
        $helper->list_id = 'etm_list';
        $helper->_pagination = array(
            50,
            100,
        );
        $helper->currentIndex = '';
        $helper->identifier = 'name';

        $helper_fields = new StdClass();
        $helper_fields->fields_list = array();
        $helper_fields->fields_list['name'] = array(
            'title' => $this->l('Name'),
            'align' => 'left',
            'type' => 'text',
            'filter' => false,
        );

        $helper->listTotal = count($this->getMailFilesArray());
        return $helper->generateList($this->getMailFilesArray(), $helper_fields->fields_list);
    }

    public function getMailTemplatesContents($name)
    {
        $contents = array();
        foreach (Language::getLanguages(false) AS $lang) {
            $this->createNewTemplate($lang['iso_code'], Tools::getValue('name', 'template-name'));
            $contents[$name]['txt'][$lang['iso_code']] = $this->getExactTemplateContents('txt', $lang['iso_code'], $name);
            $contents[$name]['html'][$lang['iso_code']] = $this->getExactTemplateContents('html', $lang['iso_code'], $name);
        }
        return $contents;
    }

    public function getExactTemplateContents($format, $iso_code, $name)
    {
        $file_html = "../modules/" . $this->addon . "/mails/" . $iso_code . '/' . $name . '.' . $format;
        $file_txt = "../modules/" . $this->addon . "/mails/" . $iso_code . '/' . $name . '.' . $format;
        if (file_exists($file_html) && $format == 'html') {
            return file_get_contents($file_html);
        } else if (file_exists($file_txt) && $format == 'txt') {
            return file_get_contents($file_txt);
        }
    }

    public function generateForm()
    {
        $context = Context::getContext();
        echo $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/emailTemplatesManager/views/mainForm.tpl');
    }

    public function generateEmailTemplatesManagerButton()
    {
        $context = Context::getContext();
        $this->assignSmartyVariables();
        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/emailTemplatesManager/views/buttonManager.tpl');
    }

    public function generateCreateTemplateForm()
    {
        $context = Context::getContext();
        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/emailTemplatesManager/views/createTemplateForm.tpl');
    }

    public function generateEditTemplateForm($name)
    {
        $context = Context::getContext();
        $context->smarty->assign('etm_template', $this->getMailTemplatesContents($name));
        $context->smarty->assign('etm_template_name', $name);
        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/emailTemplatesManager/views/editTemplateForm.tpl');
    }

    public function generaterefreshListOfTemplatesSelect()
    {
        $context = Context::getContext();
        $context->smarty->assign('etm_select', $this->getMailFilesArray());
        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/emailTemplatesManager/views/selectInput.tpl');
    }

    public static function returnEmailContents($format, $iso_code, $contents, $name)
    {
        return (isset($contents[$name][$format][$iso_code]) ? $contents[$name][$format][$iso_code] : '');
    }

    public function assignSmartyVariables()
    {
        if (defined('_PS_ADMIN_DIR_')) {
            $context = Context::getContext();
            $context->smarty->assign('etm', $this);
            $context->smarty->assign('etm_additional_variables', (isset($this->availableTemplateVars) ? $this->availableTemplateVars : false));
            $context->smarty->assign('etm_addon', $this->addon);
            $context->smarty->assign('etm_templates', $this->getFilesArray());
            $context->smarty->assign('etm_create_template', $this->generateCreateTemplateForm());
            $context->smarty->assign('etm_module_url', $context->link->getAdminLink('AdminModules', true) . '&emailTemplatesManager=1&ajax=1&module=' . $this->addon . '&configure=' . $this->addon);
            $context->smarty->assign('etm_iso', file_exists(_PS_CORE_DIR_ . '/js/tiny_mce/langs/' . $context->language->iso_code . '.js') ? $context->language->iso_code : 'en');
            $context->smarty->assign('etm_path_css', _THEME_CSS_DIR_);
            $context->smarty->assign('etm_ad', __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_));
        }
    }
}

?>