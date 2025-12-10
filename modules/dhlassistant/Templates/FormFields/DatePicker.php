<?php if(!isset($is_template)) die(); ?>
<?php
//DatePicker
/* @var $field \DhlAssistant\Core\Models\FormField */
$field = $aVars['field'];
$field_name = $aVars['field_name'];
$id = str_replace(':', '_', $field_name);
$value = $field->Value === null && $field->DefaultValue !== null ? $field->DefaultValue : $field->Value;
$maxlen = $field->MaxLen !== null && is_int($field->MaxLen) ? (int)$field->MaxLen : null;
$tooltip = (isset($field->Params['tooltip']) && $field->Params['tooltip'] != '') ? $field->Params['tooltip'] : '';

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
<div class="col-lg-4 input-group">
<input id="field_<?php echo $id; ?>" name="<?php echo $field_name; ?>" type="text" value="<?php echo htmlspecialchars($value); ?>" maxlength="10" class="datepicker" />
<!--<span class="input-group-addon">-->
<!--	<i class="icon-calendar"></i>-->
<!--</span>-->
</div>
<?php if (isset($field->Params['after_html'])) echo $field->Params['after_html']; ?>
</div>