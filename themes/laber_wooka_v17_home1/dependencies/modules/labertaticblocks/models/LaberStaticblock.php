<?php
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
class LaberStaticblock extends ObjectModel
{
    /** @var string Name */
    public $description;
    public $title;
    public $hook_position;
    public $position;
    public $active;
    public $showhook;
    public $posorder;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'laber_staticblock',
        'multishop' => true,
		'multilang' => TRUE,
        'primary' => 'id_labertaticblock',
        'fields' => array(
            'posorder' =>           array('type' => self::TYPE_INT,'lang' => false),
            'active' =>           array('type' => self::TYPE_INT,'lang' => false),
            'showhook' =>           array('type' => self::TYPE_INT,'lang' => false),
			'hook_position' =>          array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => false, 'size' => 128),
            
            // Lang fields
            'title' =>          array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
			'description' =>            array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'size' => 9999999999999),
        ),
    );
    
    public  function getStaticblockLists($id_shop = NULL, $hook_position= 'displayTop') { 
		
				$id_lang = (int)Context::getContext()->language->id;
				$object =  Db::getInstance()->executeS('
							SELECT * FROM '._DB_PREFIX_.'laber_staticblock AS psb 
							LEFT JOIN '._DB_PREFIX_.'laber_staticblock_lang AS psl ON psb.id_labertaticblock = psl.id_labertaticblock
							LEFT JOIN '._DB_PREFIX_.'laber_staticblock_shop AS pss ON psb.id_labertaticblock = pss.id_labertaticblock
							WHERE id_shop ='.$id_shop.' 
								AND id_lang ='.$id_lang.'
								AND `hook_position` = "'.$hook_position.'" 
								AND `showhook` = 1 ORDER BY `posorder` ASC
				');
			
                $newObject = array();
                if(count($object) >0) {
					$blockModule= null;
                    foreach($object as $key=>$ob) {

						$ob['block_module'] = $blockModule;
						$description = $ob['description'];
						$description = str_replace('/pos_volga/',__PS_BASE_URI__,$description);
						$ob['description'] = $description;

                       $newObject[$key] = $ob;
                    }
                  return $newObject;

                }
                return null;
                
    }
    
    
    public  function getModuleAssign( $module_name = '', $name_hook = '' ){
		
		if(!$module_name || !$name_hook || $module_name =='Chose Module')  return ;
			$module = Module::getInstanceByName($module_name);	
			$module_id = $module->id;
			$id_hook = Hook::getIdByName($name_hook);
			$hook_name = $name_hook;
			if(!$module) return ;
			$module_name = $module->name;
		if( Validate::isLoadedObject($module) && $module->id ){
			$array = array();
			$array['id_hook']   = $id_hook;
			$array['module'] 	= $module_name;
			$array['id_module'] = $module->id;

			return self::renderModuleByHook( $hook_name, array(), $module->id, $array );
			
		}
		return '';			
	}

	
	public static function renderModuleByHook( $hook_name, $hookArgs = array(), $id_module = NULL, $array = array() ){
		global $cart, $cookie;
               
        if(!$hook_name || !$id_module) return ;
		if ((!empty($id_module) AND !Validate::isUnsignedId($id_module)) OR !Validate::isHookName($hook_name))
			die(Tools::displayError());
		
		if (!isset($hookArgs['cookie']) OR !$hookArgs['cookie'])
			$hookArgs['cookie'] = $cookie;
		if (!isset($hookArgs['cart']) OR !$hookArgs['cart'])
			$hookArgs['cart'] = $cart;
		
		if ($id_module AND $id_module != $array['id_module'])
			return ;
		if (!($moduleInstance = Module::getInstanceByName($array['module'])))
			return ;
		$retro_hook_name = Hook::getRetroHookName($hook_name);
		
		$hook_callable = is_callable(array($moduleInstance, 'hook'.$hook_name));
		$hook_retro_callable = is_callable(array($moduleInstance, 'hook'.$retro_hook_name));
		
		$output = '';
		if (($hook_callable || $hook_retro_callable))
		{ 
			
			if ($hook_callable)
				$output = $moduleInstance->{'hook'.$hook_name}($hookArgs);
			else if ($hook_retro_callable)
				$output = $moduleInstance->{'hook'.$retro_hook_name}($hookArgs);
		}else { 

			     if ($moduleInstance instanceof WidgetInterface) { 
                    $output = $moduleInstance->renderWidget($hook_name, $hookArgs);
				 }
		}
		return $output;
	}

}