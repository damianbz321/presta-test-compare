<?php if(!isset($is_template)) die(); ?>
<?php
	use DhlAssistant\Core;
	use DhlAssistant\Wrappers;
	Core\Storage::Add(
	        'Js',
            Wrappers\ConfigWrapper::Get('BaseUrl').'Media/Js/PnpReport.js',
            true,
            true
    );
	/* @var $form DhlAssistant\Classes\Forms\PnpReport */
	$form = $aVars['Form'];
?>
<?php
    $enums = new DhlAssistant\Classes\Dhl\Enums\SettingsUserData();
	echo Core\Template::Render('Elements/FormErrorsAndNotices', array('Form'=>$form));
?>
<?php 
	if ($aVars['ReportLink'])
	{
		echo '<div class="pointer">
				<span class="btn-group-action">
					<span class="btn-group">
						<a href="'.$aVars['ReportLink'].'" target="_blank" class="btn btn-default">
						<i class="icon-file-text"></i> '.$enums->ValuePnpReport('PNPReportDownloadValue').'
						</a>
					</span>
				</span>																																							
			</div><br />';
	}
?>
<form
        action="<?php echo Core\Storage::Get('RulingController')->GetLink(); ?>"
        method="post"
        class="form-horizontal">
<div id="fieldset_0" class="panel">
<div class="panel-heading">
    <?php echo $enums->ValueTabsMenu('ReportPNP'); ?>
</div>
<div class="form-wrapper">
<?php
	foreach ($form->Fields as $field_name => $field)
	{
		echo Core\Template::Render(
		        'FormFields/'.$field->Type,
                array('field_name' => $field_name, 'field' => $field)
            )."\n";
	}
?>
</div>
</div>
</form>