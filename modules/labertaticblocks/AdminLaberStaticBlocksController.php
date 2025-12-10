<?php

class AdminLaberStaticBlocksController extends ModuleAdminController {
    protected $id_banner;
    public function __construct() {
        $this->table = 'laber_staticblock';
        $this->className = 'LaberStaticblock';
        $this->identifier = 'id_labertaticblock';
        $this->bootstrap = true;
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
        Shop::addTableAssociation($this->table, array('type' => 'shop'));
        $this->context = Context::getContext();

        parent::__construct();
    }

    

    public function renderList() {
         
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            )
        );

        $this->fields_list = array(
            'id_labertaticblock' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
                'lang' => false
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'width' => 90,
                'lang' => false
            )
            'hook_position' => array(
                'title' => $this->l('Hook Position'),
                'width' => '300',
                'lang' => false
            )
        );
        $lists = parent::renderList();
        parent::initToolbar();

        return $lists;
    }
    
  

    public function renderForm() {
        
        $mod = new posstaticblocks();
        $listModules = $mod->getListModuleInstalled();
        
        
        $listHookPosition = array(	
			array('hook_position'=> 'displayNav'),
            array('hook_position'=> 'displayNav1'),
            array('hook_position'=> 'displayNav2'),
            array('hook_position'=> 'displayTop'),
            array('hook_position'=> 'displayMegamenu'),
            array('hook_position'=> 'displayImageSliderTop'),
            array('hook_position'=> 'displayImageSlider'),
            array('hook_position'=> 'displayImageSliderLight'),
            array('hook_position'=> 'displayImageSliderRight'),
            array('hook_position'=> 'displayImageSliderBottom'),
            array('hook_position'=> 'displayLeftColumn'),
            array('hook_position'=> 'displayLeftColumnProduct'),
            array('hook_position'=> 'displayRightColumn'),
            array('hook_position'=> 'displayRightColumnProduct'),
            array('hook_position'=> 'displayWrapperTop'),
            array('hook_position'=> 'displayHome'),
            array('hook_position'=> 'displayPosition1'),
            array('hook_position'=> 'displayPosition2'),
            array('hook_position'=> 'displayPosition3'),
            array('hook_position'=> 'displayPosition4'),
            array('hook_position'=> 'displayPosition5'),
            array('hook_position'=> 'displayPosition6'),
            array('hook_position'=> 'displayBlog'),
            array('hook_position'=> 'displayFooterBefore'),
            array('hook_position'=> 'displayFooter'),
            array('hook_position'=> 'displayFooter2'),
            array('hook_position'=> 'displayFooter3'),
            array('hook_position'=> 'displayFooterAfter'),
			array('hook_position'=>'logoFooter')


        );
        
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Custom HTML'),
            ),
            
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title:'),
                    'name' => 'title',
					'required' => TRUE,
                    'size' => 40,
                    'lang' => true
                ),
               	array(
						'type' => 'switch',
						'label' => $this->l('Displayed title:'),
						'name' => 'active',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
               array(
                'type' => 'select',
                'label' => $this->l('Hook Position:'),
                'name' => 'hook_position',
                'required' => true,
                'options' => array(
                    'query' => $listHookPosition,
                    'id' => 'hook_position',
                    'name' => 'hook_position'
                ),
             
                'desc' => $this->l('Choose the type of the Hooks')
            ),
            
  
				array(
						'type' => 'switch',
						'label' => $this->l('Displayed:'), 
						'name' => 'showhook',
						'required' => TRUE,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),		
			    array(
                    'type' => 'textarea',
                    'label' => $this->l('Custom HTML'),
                    'name' => 'description',
                    'autoload_rte' => TRUE,
                    'lang' => true,
                    'required' => TRUE,
					'class' => 'rte',
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}'
                ),
				
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }

        if (!($obj = $this->loadObject(true)))
            return;


        return parent::renderForm();
    }
    
}
