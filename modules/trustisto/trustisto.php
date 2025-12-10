<?php
/**
 *  2019 Fones Software
 *
 *  @author    Fones Software <ja@fones.pl>
 *  @copyright 2019 Fones Software
 *  @license   https://opensource.org/licenses/MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Trustisto extends Module implements WidgetInterface
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'trustisto';
        $this->version = '1.3.0';
        $this->author = 'Fones Software';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Integration with Trustisto.com');
        $this->description = $this->l('Allows you to integrate your shop with Trustisto.com');

        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:trustisto/views/templates/hook/trustisto.tpl';
    }

    public function install()
    {
        return (parent::install() && Configuration::updateValue('TRUSTISTO_SID', '') &&
            $this->registerHook('displayBeforeBodyClosingTag'));
    }

    public function uninstall()
    {
        Configuration::deleteByName('TRUSTISTO_SID');
        return parent::uninstall();
    }

    protected function _clearCache($template, $cacheId = null, $compileId = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitModule')) {
            Configuration::updateValue('TRUSTISTO_SID', Tools::getValue('trustisto_sid', ''));

            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules').
                '&configure='.$this->name.'&conf=4&module_name='.$this->name
            );
        }

        return $this->renderForm();
    }


    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Trustisto Site ID'),
                        'name' => 'trustisto_sid',
                        'desc' => $this->l('Your site id, find it on Trustisto.com panel'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->submit_action = 'submitModule';
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
        );

        return $helper->generateForm(array($fields_form));
    }

    public function renderWidget($hookName, array $params)
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('trustist'))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $params));
        }
        else
        {
            // clearing template cache
            $this->_clearCache($this->templateFile, $this->getCacheId('trustist'));
            $this->smarty->assign($this->getWidgetVariables($hookName, $params));
        }

        return $this->fetch($this->templateFile);
    }

    
    public function getWidgetVariables($hookName, array $params)
    {
        $variables = array();

        if ($sid = Configuration::get('TRUSTISTO_SID')) {
            $variables['sid'] = $sid;
        }

        return array(
            'trustisto' => $variables
        );
    }


    public function getConfigFieldsValues()
    {
        return array(
            'trustisto_sid' => Tools::getValue('trustisto_sid', Configuration::get('TRUSTISTO_SID'))
        );
    }


    public static function getProduct($id = null)
    {
        if (!$id) {
            return false;
        }

        $trustisto_product = new Product((int) $id);
        $trustisto_link = new Link();
        $trustisto_product_url = $trustisto_link->getProductLink($trustisto_product);
        $image = $trustisto_product->getCover($id);
        $image_type= 'large_default';
        $imagePath = $trustisto_link->getImageLink(isset($trustisto_product->link_rewrite) ? $trustisto_product->link_rewrite[1] : $trustisto_product->name[1], (int)$image['id_image'], $image_type);

        return array(
            'name' => $trustisto_product->name[1],
            'link' => $trustisto_product_url,
            'image_url' => $imagePath
        );
    }

}