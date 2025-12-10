<?php if (!isset($is_template)) {
    die();
} ?>
<?php
//FormErrorsAndNotices
$form = $aVars['Form'];
?>
<?php
$fields_notices = $form->CountFieldsNotices();
if ($form->GeneralNotices || $fields_notices) {
    echo '<div class="general_notices bootstrap">' . "\n";
    echo '<div class="notice alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button>' . "\n";

    if ($form->GeneralNotices) {
        foreach ($form->GeneralNotices as $notice_msg) {
            echo $notice_msg . "<br />";
        }
    }
    if ($fields_notices) {
        echo "Napotkano {$fields_notices} ostrzeżeń<br />";
        echo "<ol>";
        foreach ($form->Fields as $field) {
            if (is_array($field->Notices) && $field->Notices) {
                foreach ($field->Notices as $notice_msg) {
                    $notice_msg = lcfirst($notice_msg);
                    echo "<li>Pole {$field->FullName}: {$notice_msg}.</li>";
                }
            }
        }
        echo "</ol>";
    }
    echo '</div></div>' . "\n";
}

$fields_errors = $form->CountFieldsErrors();
if ($form->GeneralErrors || $fields_errors) {
    echo '<div class="general_errors bootstrap">' . "\n";
    echo '<div class="error alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button>' . "\n";

    if ($form->GeneralErrors) {
        foreach ($form->GeneralErrors as $error_msg) {
            echo $error_msg . "<br />";
        }
    }

    if ($fields_errors) {
        echo "Napotkano {$fields_errors} błędów<br />";
        echo "<ol>";
        foreach ($form->Fields as $field) {
            if (is_array($field->Errors) && $field->Errors) {
                foreach ($field->Errors as $error_msg) {
                    $error_msg = lcfirst($error_msg);
                    echo "<li>Pole {$field->FullName}: {$error_msg}.</li>";
                }
            }
        }
        echo "</ol>";
    }

    echo '</div></div>' . "\n";
}
?>