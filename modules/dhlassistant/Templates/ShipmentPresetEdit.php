<?php if(!isset($is_template)) die(); ?>
<?php
	use DhlAssistant\Core;
	use DhlAssistant\Wrappers;
	Core\Storage::Add(
	        'Js',
            Wrappers\ConfigWrapper::Get('BaseUrl').'Media/Js/ShipmentPresetEdit.js',
        true,
        true);
	/* @var $form DhlAssistant\Classes\Forms\ShipmentPreset */
	$form = $aVars['Form'];
	$enums = new DhlAssistant\Classes\Dhl\Enums\SettingsUserData();
?>
<?php
	echo Core\Template::Render('Elements/FormErrorsAndNotices', array('Form'=>$form));
?>
<form
        action="<?php echo Core\Storage::Get('RulingController')->GetLink(); ?>"
        method="post" class="form-horizontal">
<div id="fieldset_0" class="panel">
<div class="panel-heading">
    <?php echo $enums->Headings('EditShipmentPValue'); ?>
</div>
<div class="form-wrapper">
<?php
	foreach ($form->Fields as $field_name => $field)
	{
		echo Core\Template::Render('FormFields/'.$field->Type,
                array('field_name' => $field_name,
                    'field' => $field))."\n";
	}
?>
</div>
</div>
</form>