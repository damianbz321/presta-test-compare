<?PHP
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2023 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 *
 * version 1.2.0
 * VERSION TO EDIT TPL FILES
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

class ppbsmartyTemplatesManager extends ppb
{
    public $addon;
    public $availableTemplateVars;
    public $pathtotpl = '/lib/smartyTemplatesManager/tpl/';

    public function __construct($addon = null, $availableTemplateVars = false)
    {
        $this->pathtotpl = '/views/templates/hook/';
        if (Tools::getValue('ajax') != 1 && Tools::getValue('configure') != $addon && Tools::getValue('smartyTemplatesManager') != 1) {
            return;
        }
        $this->availableTemplateVars = $availableTemplateVars;
        $this->addon = $addon;
        $this->assignVariables();
        if (Tools::getValue('smartyTemplatesManager') == 1 && Tools::getValue('ajax') == 1 && Tools::getValue('name', 'false') != 'false' && Tools::getValue('updateconfiguration', 'false') != 'false') {
            echo $this->generateEditTemplateForm(Tools::getValue('name'));
        } elseif (Tools::getValue('smartyTemplatesManager') == 1 && Tools::getValue('ajax') == 1 && Tools::getValue('name', 'false') != 'false' && Tools::getValue('deleteconfiguration', 'false') != 'false') {
            $this->removeTemplate(Tools::getValue('name', 'template-name'));
        } elseif (Tools::getValue('createNewTemplate') == 1 && Tools::getValue('ajax') == 1) {
            $this->createNewTemplate(Tools::getValue('name', 'template-name'));
        } elseif (Tools::getValue('refreshListOfTemplates') == 1 && Tools::getValue('ajax') == 1) {
            echo $this->getFilesArray();
        } elseif (Tools::getValue('refreshListOfTemplatesSelect') == 1 && Tools::getValue('ajax') == 1) {
            echo $this->generaterefreshListOfTemplatesSelect();
        } elseif (Tools::getValue('smartyTemplateSave') == 1 && Tools::getValue('ajax') == 1) {
            $this->saveTemplate(Tools::getValue('etm_name', 'template-name'), Tools::getValue('etm_txt'));
        } elseif (Tools::getValue('smartyTemplatesManager') == 1 && Tools::getValue('ajax') == 1) {
            echo $this->generateFormm();
        }
    }

    public function saveTemplate($name = 'template-name', $txt ='')
    {
        $file_tpl = "../modules/" . $this->addon . $this->pathtotpl . $name . '.tpl';
        if (file_exists($file_tpl)) {
            if (file_exists($file_tpl)) {
                $file = fopen($file_tpl, "w");
                fwrite($file, (isset($txt) ? $txt : ''));
                fclose($file);
            }
        }
    }

    public function removeTemplate($name = 'template-name')
    {
        $file_tpl = "../modules/" . $this->addon . $this->pathtotpl . $name . '.tpl';
        if (file_exists($file_tpl)) {
            if (file_exists($file_tpl)) {
                unlink($file_tpl);
            }
        }
    }

    public function createNewTemplate($name = 'template-name')
    {
        $file_tpl = "../modules/" . $this->addon . $this->pathtotpl . $name . '.tpl';
        if (!file_exists($file_tpl)) {
            $file = fopen($file_tpl, "w");
            fwrite($file, '');
            fclose($file);
        }
    }

    public function getSmartyFilesArray()
    {
        $dir = "../modules/ppb" . $this->pathtotpl;
        $dh = opendir($dir);
        $files = array();
        $exists = array();
        while (false !== ($filename = readdir($dh))) {
            if ($filename != ".." && $filename != "." && $filename != "" && $filename != "index.php") {
                $explode = explode(".", $filename);
                if (!isset($exists[$explode[0]])) {
                    $exists[$explode[0]] = true;
                    $files[$explode[0]]['name'] = $explode[0];
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
        $helper->actions = array(
            'edit',
        );
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->module = $this;
        $helper->list_id = 'etm_list';
        $helper->_pagination = array(
            50,
            100,
        );
        $helper->identifier = 'name';
        $helper->currentIndex = '';

        $helper_fields = new StdClass();
        $helper_fields->fields_list = array();
        $helper_fields->fields_list['name'] = array(
            'title' => $this->l('Name'),
            'align' => 'left',
            'type' => 'text',
            'filter' => false,
        );

        $helper->listTotal = count($this->getSmartyFilesArray());

        return $helper->generateList($this->getSmartyFilesArray(), $helper_fields->fields_list);
    }

    public function formatShortcode($value)
    {
        return '{smartyTemplate:' . $value . '}';
    }

    public function getSmartyTemplatesContents($name)
    {
        $contents = array();
        $contents[$name]['tpl'] = $this->getExactTemplateContents('tpl', $name);

        return $contents;
    }

    public function getExactTemplateContents($format, $name)
    {
        $file_tpl = "../modules/" . $this->addon . $this->pathtotpl . $name . '.' . $format;
        if (file_exists($file_tpl) && $format == 'tpl') {
            return file_get_contents($file_tpl);
        }
    }

    public function generateFormm()
    {
        $this->assignVariables();
        $context = Context::getContext();
        echo $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/smartyTemplatesManager/views/mainForm.tpl');
    }

    public function generateSmartyTemplatesManagerButton()
    {
        $context = Context::getContext();
        $this->assignVariables();

        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/smartyTemplatesManager/views/buttonManager.tpl');
    }

    public function generateCreateTemplateForm()
    {
        $context = Context::getContext();

        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/smartyTemplatesManager/views/createTemplateForm.tpl');
    }

    public function generateEditTemplateForm($name)
    {
        $context = Context::getContext();
        $context->smarty->assign('etm_template', $this->getSmartyTemplatesContents($name));
        $context->smarty->assign('etm_template_name', $name);

        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/smartyTemplatesManager/views/editTemplateForm.tpl');
    }

    public function generaterefreshListOfTemplatesSelect()
    {
        $context = Context::getContext();
        $context->smarty->assign('etm_select', $this->getSmartyFilesArray());

        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/smartyTemplatesManager/views/selectInput.tpl');
    }

    public function returnSmartyContents($format, $contents, $name)
    {
        return (isset($contents[$name][$format]) ? $contents[$name][$format] : '');
    }

    public function assignVariables()
    {
        if (defined('_PS_ADMIN_DIR_')) {
            $context = Context::getContext();
            $context->smarty->assign('etm', $this);
            $context->smarty->assign('etm_additional_variables', (isset($this->availableTemplateVars) ? $this->availableTemplateVars : false));
            $context->smarty->assign('etm_addon', $this->addon);
            $context->smarty->assign('etm_templates', $this->getFilesArray());
            $context->smarty->assign('etm_create_template', $this->generateCreateTemplateForm());
            $context->smarty->assign('etm_module_url', $context->link->getAdminLink('AdminModules', true) . '&smartyTemplatesManager=1&ajax=1&module=' . $this->addon . '&configure=' . $this->addon);

        }
    }
}

?>