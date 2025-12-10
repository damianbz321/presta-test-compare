<?php if(!isset($is_template)) die(); ?>
<?php
//LinkHref
/* @var $field \DhlAssistant\Core\Models\FormField */
$field = $aVars['field'];
$field_name = $aVars['field_name'];
$id = str_replace(':', '_', $field_name);
$value = $field->Value === null && $field->DefaultValue !== null ? $field->DefaultValue : $field->Value;
$class = (isset($field->Params['class']) && $field->Params['class'] ? ' '.$field->Params['class'] : '');
$tooltip = (isset($field->Params['tooltip']) && $field->Params['tooltip'] != '') ? $field->Params['tooltip'] : '';
?>
<div class="form-group field <?php echo $field->Type; ?>" id="field_div_<?php echo $id; ?>">
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
<div class="col-lg-4">
<a href="<?php echo htmlspecialchars($value); ?>" id="field_<?php echo $id; ?>" class="<?php echo $class; ?>"<?php echo (isset($field->Params['target_blank']) && $field->Params['target_blank'] ? ' target="_blank"' : '');?>><?php echo htmlspecialchars($value); ?></a>
</div>
</div>