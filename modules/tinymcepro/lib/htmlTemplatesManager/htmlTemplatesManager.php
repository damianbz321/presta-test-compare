<?PHP
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * html Templates Manager
 * version 1.7.2
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

class tinymceprohtmlTemplatesManager extends tinymcepro
{

    public $addon;
    public $availableTemplateVars;

    public function __construct($addon = null, $availableTemplateVars = false)
    {
        //@ini_set('display_errors', 'on');
        //@error_reporting(E_ALL | E_STRICT);
        $this->availableTemplateVars = $availableTemplateVars;
        $this->addon = $addon;
        if (Tools::getValue('ajax') != 1 && Tools::getValue('configure') != $addon && Tools::getValue('htmlTemplatesManager') != 1) {
            return;
        }


        if (Tools::getValue('htmlTemplatesManager')) {
            $this->assignSmartyVariables();
        }

        if (Tools::getValue('htmlTemplatesManager') == 1 && Tools::getValue('ajax') == 1 && Tools::getValue('name', 'false') != 'false' && Tools::getValue('updateconfiguration', 'false') != 'false') {
            echo $this->generateEditTemplateForm(Tools::getValue('name'));
        } else if (Tools::getValue('htmlTemplatesManager') == 1 && Tools::getValue('ajax') == 1 && Tools::getValue('name', 'false') != 'false' && Tools::getValue('deleteconfiguration', 'false') != 'false') {
            $removed_languages = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $removed_languages[$lang['iso_code']] = true;
                $this->removeTemplate(Tools::getValue('name', 'template-name'));
            }
            if (!isset($removed_languages['en'])) {
                $this->removeTemplate(Tools::getValue('name', 'template-name'));
            }
        } else if (Tools::getValue('createNewTemplatehtml') == 1 && Tools::getValue('ajax') == 1) {
            $created_languages = array();
            foreach (Language::getLanguages(false) AS $lang) {
                $created_languages[$lang['iso_code']] = true;
                $this->createNewTemplatehtml(Tools::getValue('name', 'template-name'));
            }
            if (!isset($created_languages['en'])) {
                $this->createNewTemplatehtml(Tools::getValue('name', 'template-name'));
            }
        } else if (Tools::getValue('refreshListOfTemplateshtml') == 1 && Tools::getValue('ajax') == 1) {
            echo $this->getFilesArray();
        } else if (Tools::getValue('refreshListOfTemplateshtmlSelecthtml') == 1 && Tools::getValue('ajax') == 1) {
            echo $this->generaterefreshListOfTemplateshtmlSelecthtml();
        } else if (Tools::getValue('htmlTemplateSave') == 1 && Tools::getValue('ajax') == 1) {
            foreach (Language::getLanguages(false) AS $lang) {
                $this->saveTemplate(Tools::getValue('ptm_name', 'template-name'), Tools::getValue('ptm_html'), Tools::getValue('ptm_txt'));
            }
        } else if (Tools::getValue('htmlTemplatesManager') == 1 && Tools::getValue('ajax') == 1) {
            echo $this->generateForm();
        }
        return;
    }

    public function saveTemplate($name = 'template-name', $html, $txt)
    {
        $file_txt = "../modules/" . $this->addon . "/html/" . $name . '.txt';
        if (file_exists($file_txt)) {
            $file = fopen($file_txt, "w");
            fwrite($file, (isset($txt) ? $txt : ''));
            fclose($file);
        }
    }

    public function removeTemplate($name = 'template-name')
    {
        $file_txt = "../modules/" . $this->addon . "/html/" . $name . '.txt';
        if (file_exists($file_txt)) {
            unlink($file_txt);
        }

    }

    public function createNewTemplatehtml($name = 'template-name')
    {
        $file_txt = "../modules/" . $this->addon . "/html/" . $name . '.txt';
        if (!file_exists($file_txt)) {
            $file = fopen($file_txt, "w");
            fwrite($file, '');
            fclose($file);
        }
    }

    public function gethtmlFilesArray()
    {
        $dir = "../modules/" . $this->addon . "/html/";
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
        $helper->table_id = 'ptm';
        $helper->_default_pagination = 50;
        $helper->no_link = true;
        $helper->simple_header = true;
        $helper->shopLinkType = '';
        $helper->actions = array('edit', 'delete');
        $helper->specificConfirmDelete = false;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->module = $this;
        $helper->list_id = 'ptm_list';
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

        $helper->listTotal = count($this->gethtmlFilesArray());
        return $helper->generateList($this->gethtmlFilesArray(), $helper_fields->fields_list);
    }

    public function gethtmlTemplatesContents($name)
    {
        $contents = array();
        $this->createNewTemplatehtml(Tools::getValue('name', 'template-name'));
        $contents[$name]['txt'] = $this->getExactTemplateContents('txt', $name);
        return $contents;
    }

    public function getExactTemplateContents($format, $name)
    {
        $file_txt = "../modules/" . $this->addon . "/html/" . $name . '.' . $format;
        if (file_exists($file_txt) && $format == 'txt') {
            return file_get_contents($file_txt);
        }
    }

    public function generateForm()
    {
        $context = Context::getContext();
        echo $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/htmlTemplatesManager/views/mainForm.tpl');
    }

    public function generatehtmlTemplatesManagerButton()
    {
        $context = Context::getContext();
        $this->assignSmartyVariables();
        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/htmlTemplatesManager/views/buttonManager.tpl');
    }

    public function generateCreateTemplateForm()
    {
        $context = Context::getContext();
        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/htmlTemplatesManager/views/createTemplateForm.tpl');
    }

    public function generateEditTemplateForm($name)
    {
        $context = Context::getContext();
        $context->smarty->assign('ptm_template', $this->gethtmlTemplatesContents($name));
        $context->smarty->assign('ptm_template_name', $name);
        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/htmlTemplatesManager/views/editTemplateForm.tpl');
    }

    public function generaterefreshListOfTemplateshtmlSelecthtml()
    {
        $context = Context::getContext();
        $context->smarty->assign('ptm_select', $this->gethtmlFilesArray());
        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/htmlTemplatesManager/views/selectInput.tpl');
    }

    public static function returnhtmlContents($format, $contents, $name)
    {
        return (isset($contents[$name][$format]) ? $contents[$name][$format] : '');
    }

    public function assignSmartyVariables()
    {
        if (defined('_PS_ADMIN_DIR_')) {
            $context = Context::getContext();
            $context->smarty->assign('ptm', $this);
            $context->smarty->assign('ptm_additional_variables', (isset($this->availableTemplateVars) ? $this->availableTemplateVars : false));
            $context->smarty->assign('ptm_addon', $this->addon);
            $context->smarty->assign('ptm_templates', $this->getFilesArray());
            $context->smarty->assign('ptm_create_template', $this->generateCreateTemplateForm());
            $context->smarty->assign('ptm_module_url', $context->link->getAdminLink('AdminModules', true) . '&htmlTemplatesManager=1&ajax=1&module=' . $this->addon . '&configure=' . $this->addon);
            $context->smarty->assign('ptm_iso', file_exists(_PS_CORE_DIR_ . '/js/tiny_mce/langs/' . $context->language->iso_code . '.js') ? $context->language->iso_code : 'en');
            $context->smarty->assign('ptm_path_css', _THEME_CSS_DIR_);
            $context->smarty->assign('ptm_ad', __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_));
        }
    }
}

?>