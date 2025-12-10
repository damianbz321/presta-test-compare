<?php if(!isset($is_template)) die(); ?>
<?php
//Hidden
/* @var $field \DhlAssistant\Core\Models\FormField */
$field = $aVars['field'];
$field_name = $aVars['field_name'];
$id = str_replace(':', '_', $field_name);
$value = $field->Value === null && $field->DefaultValue !== null ? $field->DefaultValue : $field->Value;
?>
<div class="field <?php echo $field->Type; ?>" id="field_div_<?php echo $id; ?>">
<input id="field_<?php echo $id; ?>" name="<?php echo $field_name; ?>" type="hidden" value="<?php echo htmlspecialchars($value); ?>"/>
</div>