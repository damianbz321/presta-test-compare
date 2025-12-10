<?php if (!isset($is_template)) {
    die();
} ?>
<?php
//Button
/* @var $field \DhlAssistant\Core\Models\FormField */
$field = $aVars['field'];
$field_name = $aVars['field_name'];
$id = str_replace(':', '_', $field_name);
$value = $field->Value === null && $field->DefaultValue !== null ? $field->DefaultValue : $field->Value;
$class = (isset($field->Params['class']) && $field->Params['class'] ? ' ' . $field->Params['class'] : '');
$icon_class = (isset($field->Params['icon_class']) && $field->Params['icon_class'] ? $field->Params['icon_class'] : 'process-icon-save');
?>
<button class="btn btn-default<?php echo $class; ?>"
        id="field_<?php echo $id; ?>"
        name="<?php echo $field_name; ?>"
        type="submit">
    <i class="process-icon- <?php echo $icon_class; ?>"></i><?php echo htmlspecialchars($field->FullName); ?>
</button>
