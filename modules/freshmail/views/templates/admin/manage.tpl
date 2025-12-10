
<div class="panel">
	<h3><i class="icon-cogs"></i> {l s='Synchronization' mod='freshmail'}</h3>
	<div id="indexing-warning" class="alert alert-warning" style="display: none">
		{l s='Synchronization is in progress.' mod='freshmail'}
	</div>

	<div class="row">
		<div class="alert alert-info">
			{l s='You can set a cron job that will synchronize subscribers using the following URL:' mod='freshmail'}
			<br>
			<strong>{$synchronization_url}</strong>
			<br>
			{l s='or use cli' mod='freshmail'}:
			<strong>{$synchronization_cron}</strong>
		</div>
	</div>
</div>
