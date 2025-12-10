<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-truck"></i>{l s='Shipment DHL' mod='dhlassistant'}
			</div>
			{if $dhlassistant_configuration_link}
				<div class="alert alert-warning">
					{l s='The module has not been configured. It can do it' mod='dhlassistant'}<a href="{$dhlassistant_configuration_link}">{l s='here' mod='dhlassistant'}</a>.
				</div>
			{else}
				{if $dhlassistant_sended}
					{l s='Shipment sent' mod='dhlassistant'}
					<br>
					{l s='Link for tracking shipment' mod='dhlassistant'}: <a href="{$dhlassistant_tracking_link}" class="blank" rel=”noopener” target="_blank">{$dhlassistant_tracking_link}</a>
					<br>
					<a class="btn btn-default" href="{$dhlassistant_edit_link}"><i class="icon-search-plus"></i> {l s='Show details shipment' mod='dhlassistant'}</a>
					<br>
					<span class="btn-group">
						{foreach from=$dhlassistant_label_links key='name' item='label_link'}
							<a class="btn btn-default _blank" href="{$label_link}" rel=”noopener” target="_blank"><i class="icon-file-text"></i> {l s='Label' mod='dhlassistant'} {$name}</a>
						{/foreach}
					</span>
				{else}
					{if $dhlassistant_country_not_supported}
						{l s='Destination country is not available.' mod='dhlassistant'}
					{else}
						<a class="btn btn-primary" href="{$dhlassistant_edit_link}"> {l s='Edit Shipment Request' mod='dhlassistant'}</a>
					{/if}
				{/if}
			{/if}
		</div>
	</div>
</div>