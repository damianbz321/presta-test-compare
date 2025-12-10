<?php if(!isset($is_template)) die(); ?>
<?php
use DhlAssistant\Core;
use DhlAssistant\Wrappers;


// Core\Storage::Add('Js', Wrappers\ConfigWrapper::Get('BaseUrl').'Js/test.js');
// Core\Storage::Add('JsInline', "alert('test2');");
// Core\Storage::Add('CssInline', "body { background-color: red; }");
?>

<pre>
<?php echo Wrappers\ConfigWrapper::Get('FullName'); ?> (<?php echo Wrappers\ConfigWrapper::Get('Version'); ?>)
</pre>
