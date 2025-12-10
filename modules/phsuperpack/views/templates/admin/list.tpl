
{if !empty($pack)}
    <table class="table table-striped" id="product_pack_list">
        <thead>
        <tr>
            <th width="5%"></th>
            <th width="40%">{l s='Title' mod='phsuperpack'}</th>
			<th width="10%">{l s='Price net' mod='phsuperpack'}</th>
			<th width="10%">{l s='Price gross' mod='phsuperpack'}</th>
			<th width="10%">{l s='Price' mod='phsuperpack'}</th>
			<th width="10%">{l s='Cena po rabacie netto' mod='phsuperpack'}</th>
			<th width="5%">{l s='Rabat' mod='phsuperpack'}</th>
			<th width="5%">{l s='Ilość' mod='phsuperpack'}</th>            
            <th width="2%">{l s='Czy aktywny' mod='phsuperpack'}</th>
			<th width="1%">&nbsp;</th>
			<th width="2%">&nbsp;</th>
        </tr>

        </thead>
        <tbody id="sortable">
        {foreach $pack as $key => $product_pack}
            <tr class="ui-state-default" data-id_superpack_product="{$product_pack.id_superpack_product|intval}" {if $product_pack.quantity <= 0}style="background-color:#f9caca"{/if}>
                <td>{$product_pack.id_superpack_product|intval}</td>
                <td>{*$product_pack.name*}
				
				{foreach from=$product_pack.items item=p}
					{$p.name} <b>({$p.quantity})</b> |
				{/foreach}
				
				</td>
				<td>{$product_pack.price_net}</td>
				<td>{$product_pack.regular_price}</td>
				<td>{$product_pack.price}</td>
				<td>{$product_pack.price2}</td>
				<td>
				{if $product_pack.has_discount}
					{$product_pack.discount}
				{/if}				
				</td>
				<td>{$product_pack.quantity}</td>				
                <td>
                    <input name="active" class="product_pack_active" data-toggle="active-switch" type="checkbox" data-id_superpack_product="{$product_pack.id_superpack_product|intval}" {if $product_pack.active}value="1" checked="checked"{else}value="0"{/if}>
                </td>
				<td><i style="font-size: 30px; cursor: move;">&#8597;</i></td>
                <td>
                    <button type="button" class="btn btn-primary-outline sensitive delete_product_pack" data-id_superpack_product="{$product_pack.id_superpack_product|intval}">                        						
						{if $psVersion}
							<i class="icon-trash"></i>
						{else}
							<i class="material-icons">delete</i>
						{/if}
                    </button>					
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <div class="modal fade in" id="product_pack_deletion_modal" tabindex="-1" style="display: none;">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">{l s='Delete pack' mod='phsuperpack'}?</h4>
                </div>

                <div class="modal-body">
                    {l s='These pack will be deleted for good. Please confirm.' mod='phsuperpack'}
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-tertiary-outline btn-lg" data-dismiss="modal">{l s='Close' mod='phsuperpack'}</button>
                    <button type="button" value="confirm" class="btn btn-primary btn-lg">
                        {l s='Delete now' mod='phsuperpack'}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        (function ($) {
            $(function () {

				$('[data-toggle="active-switch"]').on('change', function () {

					try {
                        var id_superpack_product = $(this).data('id_superpack_product');
						if ($(this).is(':checked')) {
							productPack.setActive(id_superpack_product, 1);
						} else {
							productPack.setActive(id_superpack_product, 0);
						}
                    } catch (e) {
                        console.trace(e);
                    }
                });

                $('.edit_product_pack').on('click', function () {

                    try {
                        var id_superpack_product = $(this).data('id_superpack_product');
                        productPack.getProductPack(id_superpack_product);
                    } catch (e) {
                        console.trace(e);
                    }
                });

                $('.delete_product_pack').on('click', function () {

                    var id_superpack_product = $(this).data('id_superpack_product');                    
                    $('#product_pack_deletion_modal').modal('show');
                    $('#product_pack_deletion_modal button[value="confirm"]').off('click');
                    $('#product_pack_deletion_modal button[value="confirm"]').on('click', function () {
                        $('#product_pack_deletion_modal').modal('hide');

                        setTimeout(function () {
                            try {
                                productPack.deleteProductPack(id_superpack_product);
                            } catch (e) {
                                console.trace(e);
                            }
                        }, 500);
                    });
                });
            });
        })(jQuery);
    </script>
{/if}
