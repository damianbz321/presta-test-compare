<?php if(!isset($is_template)) die(); ?>
<?php
//SubSection
/* @var $field \DhlAssistant\Core\Models\FormField */
$field = $aVars['field'];
$field_name = $aVars['field_name'];
$id = str_replace(':', '_', $field_name);
?>
<div class="form-group section <?php echo $field->Type; ?>" id="section_div_<?php echo $id; ?>">
<label><?php echo $field->FullName; ?></label>
</div>