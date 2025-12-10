<?php if(!isset($is_template)) die(); ?>
<?php
//SectionStart
/* @var $field \DhlAssistant\Core\Models\FormField */
$field = $aVars['field'];
$field_name = $aVars['field_name'];
$id = str_replace(':', '_', $field_name);
?>
<div class="section <?php echo $field->Type; ?> <?php echo $field->Name; ?>" id="section_div_<?php echo $id; ?>"<?php echo (isset($field->Params['hidden']) && $field->Params['hidden'] ? ' style="display: none;"' : '');?>>
<?php 
	if (!isset($field->Params['silent']) || !$field->Params['silent'])
		echo '<h2>'.$field->FullName.'</h2>'."\n";
?>