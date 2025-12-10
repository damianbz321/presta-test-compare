<?php if(!isset($is_template)) die(); ?>
<?php
//Submit
/* @var $field \DhlAssistant\Core\Models\FormField */
$field = $aVars['field'];
$field_name = $aVars['field_name'];
$id = str_replace(':', '_', $field_name);
$value = $field->Value === null && $field->DefaultValue !== null ? $field->DefaultValue : $field->Value;
?>
<input id="field_<?php echo $id; ?>" name="<?php echo $field_name; ?>" type="submit" value="<?php echo htmlspecialchars($field->FullName); ?>" />
