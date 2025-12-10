<?php
/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */

/**
 * Create my deliveries list with bulk actions
 */
class AdminRuchController extends ModuleAdminController
{
    public $asso_type = 'shop';
    protected $statuses_array = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = "order";
        $this->className = 'Order';
        $this->lang = false;
        $this->module = 'ruch';
        $this->addRowAction('view');
        $this->explicitSelect = true;
        $this->allow_export = false;
        $this->deleted = false;
        $this->context = Context::getContext();

        $this->_select = '
                l.id_label,
                l.api_parcel_id,
                l.target_point,
                l.target_point as target_point2,
                CONCAT(IF(l.odb_naz != \'\', l.odb_naz,'.
                ' CONCAT(l.odb_osoba_imie, \' \', l.odb_osoba_nazw)),'.
                ' \' \', l.odb_adr1, IF(l.odb_adr2 != \'\','.
                ' CONCAT(\' \', l.odb_adr2), \'\'), \', \', l.odb_miasto, \','.
                ' tel: \', l.odb_tel, \', \', l.odb_mail) AS `odb`';

        $this->_join = '
               	LEFT JOIN `'._DB_PREFIX_.'ruch_label` l ON (l.`id_order` = a.`id_order`)
                LEFT JOIN `'._DB_PREFIX_.'carrier` d ON (d.`id_carrier` = a.`id_carrier`)';
        $this->_where = ' AND l.id_label is not null';
        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';
        $this->_use_found_rows = true;

        $this->fields_list = array(
            'api_parcel_id' => array(
                'title' => ('Numer przesyłki'),
                'callback' => 'trackingDecode',
                'class' => 'fixed-width-xs',
                'remove_onclick' => true
            ),
            'id_order' => array(
                'title' => ('Zamówienie'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'havingFilter' => true,
            ),
            'target_point' => array(
                'title' => ('Punkt'),
                'callback' => 'targetDecode',
                'class' => 'fixed-width-xs',
                'remove_onclick' => true
            ),
            'target_point2' => array(
                'title' => ('Usługa'),
                'callback' => 'targetDecode2',
                'class' => 'fixed-width-xs',
                'havingFilter' => false,
                'remove_onclick' => true
            ),
            'parcels' => array(
                'class' => 'fixed-width-xs',
                'title' => ('Cechy paczki'),
                'havingFilter' => false,
                'callback' => 'packDecode',
                'orderby' => false
            ),
            'odb' => array(
                'title' => ('Odbiorca'),
                'havingFilter' => true,
            ),
            'date_add' => array(
                'title' => ('Data'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'id_label' => array(
                'title' => ('PDF'),
                'align' => 'text-center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true
            )
        );
        
        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_ORDER;

        $this->bulk_actions = array(
            'printLabels' => array('text' => 'Drukuj etykiety', 'icon' => 'icon-print')
        );
        
        parent::__construct();
    }

    public function renderList()
    {
        $this->context->smarty->assign(array(
            'ruch_token' => sha1(_COOKIE_KEY_.'ruch'),
            'ruch_ajax_uri' => _RUCH_AJAX_URI_,
            'ruch_pdf_uri' => _RUCH_PDF_URI_
        ));
        if (Tools::isSubmit('submitBulkprintLabels'.$this->table)) {
            if (Tools::getIsset('cancel')) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }
            $this->tpl_list_vars['printLabels_mode'] = true;
            $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $ids = array();
            foreach (Tools::getValue('orderBox') as $id) {
                $ids[] = $id;
            }
            $this->tpl_list_vars['ids'] = join(',', $ids);
        }
        return parent::renderList();
    }

    public function trackingDecode($f)
    {
        //RuchLib::log(print_r($tr, true));
        return $f;
    }
    
    public function targetDecode($f)
    {
        $tmp = explode(':', $f);
        if(count($tmp) == 1) return $tmp[0];
        return $tmp[1];
    }
    
    public function targetDecode2($f)
    {
        $tmp = explode(':', $f);
        if(count($tmp) == 1) $t = 'R';
        else $t = $tmp[0];
        if(count($tmp) == 3) $s = $tmp[2];
        else $s = 'M';
        
        if($t == 'A') return '-';
        return ($s == 'M') ? 'Mini' : 'Standard';
//        return $t . ' ' . $s;
    }
    
    public function packDecode($f, $r)
    {
        $tmp = explode(':', $r['target_point']);
        if(count($tmp) == 1) $t = 'R';
        else $t = $tmp[0];
        
        $o = unserialize($f);
//        RuchLib::log(print_r($o, true));
        if (count($o) > 1) {
            $nums = true;
        } else {
            $nums = false;
        }
        $ret = array();
        $i = 1;
        foreach ($o as $p) {
            if($t == 'A') $ret[] = ($nums ? $i . ': ': '') . 'Gabaryt ' . $p['tpl'];
            else
                $ret[] = ($nums ? $i . ': ': '') . $p['waga'] . ' kg, ' . $p['dlug'] . 'x'  .
                $p['szer'] . 'x' . $p['wys'] . ' cm' . ($p['nst'] ? ' (NST)' : '');
            $i++;
        }
        return join('<br/>', $ret);
    }

    public function printPDFIcons($f)
    {
        $tpl = $this->context->smarty->createTemplate(_RUCH_TPL_DIR_ . 'admin/_print_pdf_icon.tpl');
        $tpl->assign(array(
            'tr' => $f
        ));
        return $tpl->fetch();
    }

    public function initToolbar()
    {
        $res = parent::initToolbar();
        unset($this->toolbar_btn['new']);
        return $res;
    }

    public function processBulkPrintLabels()
    {
    }

    public function renderView()
    {
        $id_order = Tools::getValue('id_order');
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminOrders').
        '&vieworder&id_order='.(int)$id_order);
    }
}
