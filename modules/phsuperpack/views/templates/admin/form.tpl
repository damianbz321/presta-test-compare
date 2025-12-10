<div id="product_pack_form">
    <div role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{l s='New product pack' mod='phsuperpack'}</h4>
            </div>
            <div class="modal-body">

				
		<div id="pack-product" class="form-group">
            <label class="control-label col-lg-3">Wyszukaj i dodaj produkt do zestawu:</label>
            <div class="col-xl-8 col-lg-11">
				<fieldset class="form-group">
						<div class="autocomplete-search" data-formid="form_pack_products" data-fullname="pack_products" data-mappingvalue="id" 
						data-mappingname="name" data-remoteurl="{$base_admin}ajax_products_list.php?forceJson=1&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20&q=%QUERY" data-limit="0">
					
						<div class="search search-with-icon">
							<span class="twitter-typeahead" style="position: relative; display: inline-block;">
							<input type="text" data-autocomplete="{$base_admin}ajax_products_list.php?forceJson=1&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20" id="form_pack_products" class="form-control search typeahead form_pack_products tt-input" placeholder="Wyszukaj i dodaj powiązany produkt" autocomplete="off" 
							spellcheck="false" dir="auto" style="position: relative; vertical-align: top;">
							<pre aria-hidden="true" style="position: absolute; visibility: hidden; white-space: pre; font-family: &quot;Open Sans&quot;, Helvetica, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"></pre><div class="tt-menu" style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none;"><div class="tt-dataset tt-dataset-3"></div></div></span>
						</div>
						<ul id="form_pack_products-data" class="typeahead-list nostyle col-sm-12 product-list">
						</ul>
						<div class="invisible" id="tplcollection-form_pack_products">
						  <span class="label">%s</span><i class="material-icons delete">clear</i>
						</div>
					</div>
					<script type="text/javascript">
					  $('#form_pack_products').on('focusout', function resetSearchBar() {
						$('#form_pack_products').typeahead('val', '');
					  });
					</script>

				</fieldset>
			</div>
			
			<div class="col-md-6">
				<label class="form-control-label">Cena całego zestawu:</label>
				<div class="input-group money-type">
					<input type="text" id="pack_products_price" name="pack_products_price" class="form-control" value="100">
					<div class="input-group-append">
						<span class="input-group-text"> zł</span>
					</div>
				</div>
            </div>
			
			<div class="row" style="margin-left: 0;">
				<div class="col-xl-2 col-lg-3">
				  <fieldset class="form-group">
					<label>Zastosuj zniżkę</label>					
					<div class="input-group money-type">
						<input type="text" id="pack_products_reduction" name="pack_products_reduction" class="form-control" value="0,000000">
						<div class="input-group-append">
							<span class="input-group-text"> zł</span>
						</div>
						</div>
				  </fieldset>
				</div>
				<div class="col-xl-2 col-lg-3">
				  <fieldset class="form-group">
					<label>&nbsp;</label>					
					<select id="pack_products_reduction_type" name="pack_products_reduction_type" class="custom-select"><option value="amount">€</option><option value="percentage">%</option></select>
				  </fieldset>
				</div>
			</div>
        </div>
				
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="dismiss_product_pack_form">{l s='Close' mod='phsuperpack'}</button>
                <button type="button" class="btn btn-primary" data-id_superpack_product="{$id_superpack_product|intval}" id="save_product_pack">{l s='Save tab' mod='phsuperpack'}</button>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    (function ($) {
        $(function () {

            $('#dismiss_product_pack_form').on('click', function () {
                try {
                    productPack.updateProductPackList();
                } catch (e) {
                    console.trace(e);
                }
            });

            $('#save_product_pack').on('click', function () {
                try {

                    var product_pack_form = $('#product_pack_form');

					var pack_products = product_pack_form.find('#pack_products');
					
					if(typeof pack_products === "undefined" || pack_products.length <= 0) {
						showErrorMessage("Należy dodać produkt do zestawu");
						return;
					}
					
					var pack_products_price = product_pack_form.find('#pack_products_price');
					var pack_products_reduction = product_pack_form.find('#pack_products_reduction');
					var pack_products_reduction_type = product_pack_form.find('#pack_products_reduction_type');

                    var id_superpack_product = $(this).data('id_superpack_product');                    

                    productPack.saveProductPack(id_superpack_product, pack_products, pack_products_price, pack_products_reduction, pack_products_reduction_type);

                } catch (e) {
                    console.trace(e);
                }
            });
        });
    })(jQuery);
</script>