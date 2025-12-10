<?php if (!isset($is_template)) {
    die();
} ?>
<?php
//ButtonHref
/* @var $field \DhlAssistant\Core\Models\FormField */
$field = $aVars['field'];
$field_name = $aVars['field_name'];
$id = str_replace(':', '_', $field_name);
$value = $field->Value === null && $field->DefaultValue !== null ? $field->DefaultValue : $field->Value;
$class = (isset($field->Params['class']) && $field->Params['class'] ? ' ' . $field->Params['class'] : '');
$icon_class = (isset($field->Params['icon_class']) && $field->Params['icon_class'] ? $field->Params['icon_class'] : 'process-icon-cancel');
?>
<a class="btn btn-default<?php echo $class; ?>"
   href="<?php echo(isset($field->Params['href']) ? htmlspecialchars($field->Params['href']) : '#'); ?>"
   id="field_<?php echo $id; ?>"<?php echo(isset($field->Params['onclick']) ? ' onclick="' . $field->Params['onclick'] . '"' : ''); ?><?php echo(isset($field->Params['hidden']) && $field->Params['hidden'] ? ' style="display: none;"' : ''); ?><?php echo(isset($field->Params['target_blank']) && $field->Params['target_blank'] ? ' target="_blank"' : ''); ?>>
    <i class="process-icon- <?php echo $icon_class; ?>"></i> <?php echo htmlspecialchars($field->FullName); ?>
</a>