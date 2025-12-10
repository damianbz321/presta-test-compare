<?php if(!isset($is_template)) die(); ?>
<?php 
//Select
/* @var $field \DhlAssistant\Core\Models\FormField */
$field = $aVars['field'];
$field_name = $aVars['field_name'];
$id = str_replace(':', '_', $field_name);
$value = $field->Value === null && $field->DefaultValue !== null ? $field->DefaultValue : $field->Value;
$tooltip = (isset($field->Params['tooltip']) && $field->Params['tooltip'] != '') ? $field->Params['tooltip'] : '';
// $field->PossibleValues;
?>
<div class="form-group field <?php echo $field->Type; ?>" id="field_div_<?php echo $id; ?>">
<?php if ($field->Errors) { ?>
<div class="errors">
<?php foreach ($field->Errors as $error) { ?>
	<span><?php echo $error; ?></span>
<?php } ?>
</div>
<?php } ?>
<?php if ($field->Notices) { ?>
<div class="notices">
<?php foreach ($field->Notices as $notice) { ?>
	<span><?php echo $notice; ?></span>
<?php } ?>
</div>
<?php } ?>
<label class="control-label col-lg-3 field_name<?php echo ((isset($field->Params['Required']) && $field->Params['Required'])?' required':''); ?>">
<?php if($tooltip != '')
{
?>
<span class="label-tooltip" data-toggle="tooltip" title="<?php echo $tooltip; ?>"><?php echo $field->FullName; ?></span>
<?php 
}
else{ echo $field->FullName; }
?>
</label>

<div class="col-lg-4 ">
<select id="field_<?php echo $id; ?>" name="<?php echo $field_name; ?>">
<?php 
	if ($field->PossibleValues)
	{
		foreach ($field->PossibleValues as $possible_value => $possible_description)
		{
			?><option value="<?php echo $possible_value; ?>"<?php if ($possible_value == $value) { ?> selected="selected"<?php } ?>><?php echo htmlspecialchars($possible_description); ?></option><?php
		}
	}
?>
</select>
</div>
</div>