<?php if(!isset($is_template)) die(); ?><!DOCTYPE html>
<?php
	use DhlAssistant\Core;
	use DhlAssistant\Wrappers;
?><html>
<head>
<meta charset="UTF-8">
<?php
	//headers
	if (isset($aVars['Title']))
		echo '<title>'.htmlspecialchars($aVars['Title']).'</title>'."\n";
	if (isset($aVars['JQueryUrl']) && $aVars['JQueryUrl'])
			echo '<script type="text/javascript" src="'.$aVars['JQueryUrl'].'"></script>'."\n";
	if (isset($aVars['Css']) && is_array($aVars['Css']) && $aVars['Css'])
		foreach ($aVars['Css'] as $file)
			echo '<link rel="stylesheet" href="'.$file.'">'."\n";
	if (isset($aVars['Js']) && is_array($aVars['Js']) && $aVars['Js'])
		foreach ($aVars['Js'] as $file)
			echo '<script type="text/javascript" src="'.$file.'"></script>'."\n";
	if (isset($aVars['CssInline']) && is_array($aVars['CssInline']) && $aVars['CssInline'])
		foreach ($aVars['CssInline'] as $style)
			echo "<style>\n{$style}\n</style>\n";
	if (isset($aVars['JsInline']) && is_array($aVars['JsInline']) && $aVars['JsInline'])
		foreach ($aVars['JsInline'] as $script)
			echo "<script type=\"text/javascript\">\n{$script}\n</script>\n";
?>
</head>
<body>

<div id="content" class="bootstrap custom-bootstrap">

<fieldset class="panel" id="content_header">
	<a title="DHL" href="http://www.dhl.com.pl/pl.html" rel=”noopener” target="_blank">
		<img class="imgm img-thumbnail" alt="DHL" src="<?php echo Wrappers\ConfigWrapper::Get('BaseUrl');?>Media/Images/CarrierLogoDefault.jpg" />
	</a>
	<p><strong>DHL - Firma logistyczna dla całego świata</strong></p>
	<br>
	<p>DHL jest światowym liderem w branży logistycznej i „firmą logistyczną dla całego świata”. Firma DHL wykorzystuje swoją wiedzę w dziedzinie międzynarodowych przesyłek ekspresowych, frachtu lotniczego i morskiego, transportu drogowego i kolejowego, logistyki kontraktowej oraz międzynarodowych usług pocztowych na rzecz swoich klientów. Ogólnoświatowa sieć, w skład której wchodzi ponad 220 krajów i terytoriów oraz około 285,000 pracowników na całym świecie, oferuje klientom najwyższej jakości usługi i znajomość lokalnych warunków, co pozwala jej sprostać wymaganiom związanym z łańcuchem dostaw. Kierownictwo i pracownicy firmy DHL mają świadomość odpowiedzialności społecznej związanej z działaniem na rzecz ochrony środowiska, zarządzaniem kryzysowym i edukacją.</p>
	<br>
	<p>DHL przybliża świat małym i średnim firmom. Jako światowy ekspert w dziedzinie usług ekspresowych, oferuje klientom specjalistyczne doradztwo oraz zapewnia kompleksowe rozwiązania spełniające ich potrzeby biznesowe. Obecnie aż 92 % klientów DHL na całym świecie należy do sektora MŚP. Firma DHL jest częścią Grupy Deutsche Post DHL. W 2014 roku Grupa osiągnęła przychody przekraczające 56 mld EUR.</p>
	<div class="clear clearfix"></div>
</fieldset>


<nav class="navbar navbar-default">
<div id="navbar-collapse" class="collapse navbar-collapse">
	<ul class="nav navbar-nav">
		<?php 
			$ruling_controller = Core\Storage::Get('RulingController');
			foreach ($aVars['NavbarLinks'] as $link_name => $link_controller)
			{	
				$active = $link_controller['object'] instanceof $ruling_controller;
				echo '<li'.($active ? ' class="active"':'').'><a href="'.$link_controller['object']->GetLink().'"><span class="'.$link_controller['icon'].'"></span>'.htmlspecialchars($link_name).'</a></li>';
			}
		?>
		
	</ul>
</div>
</nav>

	<?php
		if (isset($aVars['Body']))
			echo $aVars['Body'];
	?>

</div>

<script type="text/javascript" src="<?php echo Wrappers\ConfigWrapper::Get('BaseUrl');?>Media/Js/iframeResizer.contentWindow.min.js"></script>
</body>
</html>