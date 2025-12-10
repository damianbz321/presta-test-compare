<span class="btn-group-action">
	<span class="btn-group">
		{if $dhlassistant_sended}
				<a class="btn btn-default" href="{$dhlassistant_edit_link}"><i class="icon-search-plus"></i> {l s='Details' mod='dhlassistant'}</a>
				{*<a class="btn btn-default _blank" href="{$dhlassistant_label_link}" target="_blank"><i class="icon-text"></i> Etykieta</a>*}
		{elseif $order->id_carrier}
			<a class="btn btn-default" href="{$dhlassistant_edit_link}"><i class="icon-truck"></i> {l s='Send' mod='dhlassistant'}</a>
		{else}
			--
		{/if}
	</span>
</span>