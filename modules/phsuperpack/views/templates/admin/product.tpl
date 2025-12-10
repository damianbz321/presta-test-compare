<style>
#pack-product .typeahead-list li {
    border: 1px solid #bbcdd2;
}
#pack-product .typeahead-list li, .p-1 {
    padding: 5px!important;
    padding: .3125rem!important;
}
#pack-product .typeahead-list li, .mt-1, .my-1 {
    margin-top: 5px!important;
    margin-top: .3125rem!important;
}
#pack-product .typeahead-list li, .align-items-center {
    -webkit-box-align: center!important;
    -ms-flex-align: center!important;
    align-items: center!important;
}
#pack-product .typeahead-list li, .d-flex {
    display: -webkit-box!important;
    display: -ms-flexbox!important;
    display: flex!important;
}
#pack-product .typeahead-list li>.media-left img,
#pack-product .typeahead-list li>.media-left,
.ac_results > ul > li > div.media-left > img {
    max-width: 50px;
    max-height: 50px;
}
#pack-product .delete_product_pack, #product_pack_list .delete_product_pack {
	background: none;
}
</style>
<div class="panel product-tab" id="product-informations">
    <h3>{l s='Sets' mod='phsuperpack'}</h3>
	<div class="row">
		<div class="col-lg-3">
            <div class="form-group">
                <button type="button" class="btn btn-primary-outline sensitive add" id="pack-product-add" style="margin-top: 35px;">
                    <i class="material-icons">add_circle</i> {l s='New product pack' mod='phsuperpack'}
                </button>
            </div>
        </div>
	</div>
	<div id="pack-product" class="form-group" style="display:none">
		<div id="product_pack_form">
			<div role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel">{l s='New product pack' mod='phsuperpack'}</h4>
					</div>
					<div class="modal-body">
						<div id="pack-product" class="form-group">
							<div class="control-label col-lg-8" style="margin-bottom: 20px;">
								<label class="">{l s='Set title' mod='phsuperpack'}</label>
								<input type="text" id="pack_name" class="form-control">
							</div>

							<label class="control-label col-lg-3">{l s='Choose product:' mod='phsuperpack'}</label>
							<div class="col-xl-8 col-lg-11">
								<fieldset class="form-group">
										<div id="autocomplete-search" class="autocomplete-search" data-formid="form_pack_products" data-fullname="pack_products" data-mappingvalue="id" 
										data-mappingname="name" data-remoteurl="{$products_list}&q=%QUERY" data-limit="0">
										<div class="search search-with-icon">
											<span class="twitter-typeahead" style="position: relative; display: inline-block; min-width: 50%">
											<input type="text" data-autocomplete="{$products_list}" id="form_pack_products" class="form-control search typeahead form_pack_products tt-input" placeholder="{l s='Choose product:' mod='phsuperpack'}" autocomplete="off" 
											spellcheck="false" dir="auto" style="position: relative; vertical-align: top;">
											<pre aria-hidden="true" style="position: absolute; visibility: hidden; white-space: pre; font-family: &quot;Open Sans&quot;, Helvetica, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"></pre><div class="tt-menu" style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none;"><div class="tt-dataset tt-dataset-3"></div></div></span>
										</div>
										<ul id="form_pack_products-data" class="typeahead-list nostyle col-sm-12 product-list">
										</ul>
										<input type="hidden" name="inputAccessoriesPack" id="inputAccessoriesPack" value="">
										<input type="hidden" name="nameAccessoriesPack" id="nameAccessoriesPack" value="">										
										<div class="invisible" id="tplcollection-form_pack_products">
										  <span class="label">%s</span><i class="material-icons delete">clear</i>
										</div>
									</div>
									{if $psVersion}
									<script type="text/javascript">
										
										$('#form_pack_products')
											.autocomplete($('#autocomplete-search').attr("data-remoteurl"), {
												minChars: 1,
												autoFill: true,
												max:20,
												matchContains: true,
												mustMatch:false,
												scroll:false,
												cacheLength:0,
												
												parse: function(data) {													
													var obj = jQuery.parseJSON( data );
												
													var mytab = [];
													for (var i = 0; i < obj.length; i++)
														mytab[mytab.length] = { data: obj[i], value: obj[i].name };
													return mytab;
												},
												formatItem: function(data, i, max, value, term) {
																		
													var res = '';			
													res += '<div class="media-left">';
													res += '<img class="media-object image" src="'+data.image+'"></div>';
													res += '<div class="media-body media-middle"><span class="label">'+data.name+'</span><i class="material-icons delete">clear</i> </div>';
													//res += '<input type="hidden" name="pack_products[data][]" value="'+data.id+','+data.id_product_attribute+'">';
													//<button type="button" class="delAccessory btn btn-default" name="11"><i class="icon-remove text-danger"></i></button>
													return res;
												},
												
											}).result(function(event, data, formatted){
											
											if (data == null)
												return false;
											var productId = data.id+','+data.id_product_attribute;
											var productName = data.name;											

											var $divAccessories = $('#form_pack_products-data');
											var $inputAccessories = $('#inputAccessoriesPack');
											var $nameAccessories = $('#nameAccessoriesPack');

											$divAccessories.html($divAccessories.html() + '<div class="form-control-static"><button type="button" class="delAccessory btn btn-default" name="' + productId + '"><i class="icon-remove text-danger"></i></button>&nbsp;'+ productName +'<input type="hidden" name="pack_products[data][]" value="' + productId + '"></div>');
											$nameAccessories.val($nameAccessories.val() + productName + '¤');
											$inputAccessories.val($inputAccessories.val() + productId + '-');
											
											$('#form_pack_products').val('');
											
											});
											
									</script>
									{else}
									<script type="text/javascript">
										$('#form_pack_products').on('focusout', function resetSearchBar() {
											$('#form_pack_products').typeahead('val', '');
										});
									</script>
									{/if}
								</fieldset>
							</div>
							
							<div class="row" style="margin-left: 0;">
								<div class="col-md-6">
								
									<label><input type="checkbox" id="pack_add_to_all" name="pack_add_to_all" value="1"> {l s='Dodaj zestaw dla wszystkich kombinacji produktów' mod='phsuperpack'}</label>
								
								</div>
							</div>
                            <div class="row" style="margin-left: 0;">
                                <div class="col-md-6">

                                    <label><input type="checkbox" id="pack_add_to_all_product" name="pack_add_to_all_product" value="1"> {l s='Dodaj zestaw dla wszystkich produktów z zestawu' mod='phsuperpack'}</label>

                                </div>
                            </div>
							<div class="row" style="margin-left: 0;">
								<div class="col-md-6" style="color: red;" id="product_pack_price">									
								</div>
							</div>
							
							<div class="row" style="margin-left: 0;">
								<div class="col-md-6">
									<label class="form-control-label">{l s='Net selling price' mod='phsuperpack'}</label>
									<div class="input-group money-type">
										<input type="text" id="pack_products_price" name="pack_products_price" class="form-control" value="" autocomplete="off">
										<div class="input-group-append">
											<span class="input-group-text"> zł</span>
										</div>
									</div>
								</div>
							</div>
							
							<div class="row" style="margin-left: 0;">
								<div class="col-xl-2 col-lg-3">
								  <fieldset class="form-group">
									<label>{l s='Apply the discount' mod='phsuperpack'}</label>					
									<div class="input-group money-type">
										<input type="text" id="pack_products_reduction" name="pack_products_reduction" class="form-control" autocomplete="off" value="0,000000">
										<div class="input-group-append">
											<span class="input-group-text"> zł</span>
										</div>
										</div>
								  </fieldset>
								</div>
								<div class="col-xl-2 col-lg-3">
								  <fieldset class="form-group">
									<label>&nbsp;</label>					
									<select id="pack_products_reduction_type" name="pack_products_reduction_type" class="custom-select"><option value="amount">zł</option><option value="percentage">%</option></select>
								  </fieldset>
								</div>
							</div>
												
							<div class="row" style="margin-left: 0;">
								<div class="col-md-6" style="color: red;" id="product_pack_price_disc">								
								</div>
							</div>
							
							<div class="row" style="margin-left: 0;">
								<div class="col-md-6">
									<label class="form-control-label">{l s='Ikona zestawu' mod='phsuperpack'}</label>
									<div>
										<input id="product_image" type="file" name="product_image" />								
									</div>
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
	</div>
	<div class="row" id="edit-spinner">
		<div class="col-lg-5"></div>
		<div class="col-lg-1">
			<div class="js-spinner spinner btn-primary-reverse onclick pull-left m-r-1"></div>
		</div>
		<div class="col-lg-5"></div>
	</div>
	<div class="row">
		<div class="col-lg-12" id="product_pack_body">
		</div>
	</div>
</div>
<script type="application/javascript">
    (function($){
        $(function () {
            $('#module_phsuperpack h2.name').text('Module Super Pack');
            productPack = {
                product_pack_get_list: '{$product_pack_get_list}',
                product_pack_get_form: '{$product_pack_get_form}',
                save_product_pack: '{$save_product_pack}',
				get_product_pack_price: '{$get_product_pack_price}',
				get_product_pack_price_disc: '{$get_product_pack_price_disc}',
                delete_product_pack: '{$delete_product_pack}',
				active_product_pack: '{$active_product_pack}',
				position_product_pack: '{$position_product_pack}',                
                id_product: '{$id_product|intval}',
                //get_product_pack_form: $('#pack-product-add'),
                product_pack_body: $('#product_pack_body'),
				product_pack_price: $('#product_pack_price'),
				product_pack_price_disc: $('#product_pack_price_disc'),
                init: function () {
                    productPack.updateProductPackList();
                    /*productPack.get_product_pack_form.on('click', function () {
                        productPack.getProductPack(0);
                    });*/
                },
                updateProductPackList: function () {
					$("#pack-product").hide();
                    var data = {
                        id_product: productPack.id_product,
                    };
                    productPack.showSinner();
                    productPack.query(productPack.product_pack_get_list, data, function (data) {
                        productPack.product_pack_body.html(data);
                        productPack.hideSinner();
						productPack.setSortable();
                    });
                },
                getProductPack: function (id_superpack_product) {
                    var data = {
                        id_superpack_product: id_superpack_product,
                    };
                    productPack.showSinner();
                    productPack.query(productPack.product_pack_get_form, data, function (data) {
                        productPack.product_pack_body.html(data);
                        productPack.hideSinner();
                    });
                },
                saveProductPack: function (id_superpack_product, pack_products, pack_products_price, pack_products_reduction, pack_products_reduction_type, product_image, pack_add_to_all, pack_add_to_all_product, pack_products_obj, pack_name) {
					var formData = new FormData();
					formData.append('id_product', productPack.id_product);
					formData.append('id_superpack_product', id_superpack_product);
					//formData.append('pack_products', pack_products);
					formData.append('pack_products_price', pack_products_price);
					formData.append('pack_products_reduction', pack_products_reduction);
					formData.append('pack_products_reduction_type', pack_products_reduction_type);
					formData.append('product_image', product_image);
					formData.append('pack_add_to_all', pack_add_to_all);
					formData.append('pack_add_to_all_product', pack_add_to_all_product);
					formData.append('pack_name', pack_name);
					formData.append('pack_products_obj', JSON.stringify(pack_products_obj));
					productPack.createFormData(formData, 'pack_products', pack_products);
					
                    productPack.showSinner();
                    productPack.queryFile(productPack.save_product_pack, formData, function (data) {
                        productPack.product_pack_body.html(data);
                        productPack.hideSinner();
						productPack.setSortable();
                    });
                },
				createFormData: function (formData, key, data) {
					if (data === Object(data) || Array.isArray(data)) {
						for (var i in data) {
							productPack.createFormData(formData, key + '[' + i + ']', data[i]);
						}
					} else {
						formData.append(key, data);
					}
				},
				getProductPackPrice: function (pack_products) {
					var data = {
                        id_product: productPack.id_product,                        
                        pack_products: pack_products
                    };

                    productPack.query(productPack.get_product_pack_price, data, function (data) {
						var obj = jQuery.parseJSON( data );
						if (obj.success == true) {
							productPack.product_pack_price.html(obj.line);
							var price = parseFloat(obj.price);

							$("#product_pack_price").html(obj.line);

							if (price > 0)
								$("#pack_products_price").val(price);

						}						
                    });
				},
				getProductPackPriceDisc: function (pack_products, pack_products_price, pack_products_reduction, pack_products_reduction_type, pack_products_obj, reduction_change) {
					var data = {
                        id_product: productPack.id_product,                        
                        pack_products: pack_products,
                        pack_products_obj: pack_products_obj,
						pack_products_price: pack_products_price,
                        pack_products_reduction: pack_products_reduction,
                        pack_products_reduction_type: pack_products_reduction_type,
						reduction_change: reduction_change
                    };

                    productPack.query(productPack.get_product_pack_price_disc, data, function (data) {


						var obj = jQuery.parseJSON( data );
						if (obj.success == true) {
							productPack.product_pack_price_disc.html(obj.line);
							var price = parseFloat(obj.price);

							let firstPrice = productPack.product_pack_price.text();
							firstPrice = firstPrice.replace(/\s/g, "");
							//if(firstPrice == '')
							if(reduction_change == false)
								productPack.product_pack_price.html(obj.line);
							if (price > 0){
								$("#product_pack_price").val(price);
								$("#product_pack_price_disc").val(price);
								if(reduction_change == false)
									$("#pack_products_price").val(price);

							}


						}
					                        
                    });
				},				
                deleteProductPack: function (id_superpack_product) {
                    var data = {
                        id_superpack_product: id_superpack_product,
                        id_product: productPack.id_product,
                    };
                    productPack.showSinner();
                    productPack.query(productPack.delete_product_pack, data, function (data) {
                        productPack.product_pack_body.html(data);
                        productPack.hideSinner();
						productPack.setSortable();
                        showSuccessMessage(translate_javascripts['Delete']);
                    });
                },
				setActive: function (id_superpack_product, active) {
                    var data = {
                        id_superpack_product: id_superpack_product,
                        active: active,
                    };

                    productPack.query(productPack.active_product_pack, data, function (data) {
                        if(parseInt(data)) {
                            showSuccessMessage(translate_javascripts['Form update success']);
                        } else {
                            showErrorMessage(translate_javascripts['Form update errors']);
                        }
                    });
                },
				setPosition: function (pack_positions) {
                    var data = {
                        product_pack_positions: pack_positions,
                    };

                    productPack.query(productPack.position_product_pack, data, function (data) {
                        if(parseInt(data)) {
                            showSuccessMessage(translate_javascripts['Form update success']);
                        } else {
                            showErrorMessage(translate_javascripts['Form update errors']);
                        }
                    });
                },
                showSinner: function () {
                    productPack.productPackBodyClean();
                    $('#edit-spinner').show();
                },
                hideSinner: function () {
                    $('#edit-spinner').hide();
                },
				setSortable: function () {
                    $('#sortable').sortable({
                        stop: function( event, ui ) {
                            var position = 0;
                            var product_pack_positions = {};
                            $('#product_pack_list tbody tr').each(function () {
                                var id_product_pack = $(this).attr('data-id_superpack_product');
                                product_pack_positions[id_product_pack] = ++position;
                            });

                            productPack.setPosition(product_pack_positions);
                        }
                    });
                    $('#sortable').disableSelection();
                },
                productPackBodyClean: function () {
                    productPack.product_pack_body.html('');
                },
				queryFile: function (path, data, success) {
                    $.ajax({
						type: "POST",
                        url: path,
                        data: data,											
                        success: success,
                        cache: false,
						processData: false,
						contentType: false
                    });
                },
                query: function (path, data, success) {
                    $.ajax({
                        url: path,
                        data: data,
                        success: success,
                        dataType: 'html',
                        method: 'POST',
                    });
                }
            };
            productPack.init();
        });
    })(jQuery);
</script>
<script type="text/javascript">
	(function($){
	 $('document').ready(function() {
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
					var pack_products = {};
					var size = 0;
					var pack_products_obj = {};
					product_pack_form.find('input[name="pack_products[data][]"]').each(function () {
						var id_group = $(this).val();
						pack_products[id_group] = id_group;

						// pack_products['qty-'+id_group] = $(this).parent().find('input[name="pack_products_qty[data]"]').val();

						pack_products_obj[id_group] = {
							id: id_group,
							quantity: $(this).parent().find('input[name="pack_products_qty[data]"]').val()
						};
						// 		.each(function () {
						// 	var qty_prod = $(this).val();
						//
						// 	pack_products[id_group]['qty'] = qty_prod;
						// 	return qty_prod;
						//
						// });
						size++;
					});
					if (size > 0) {

						var pack_products_price = product_pack_form.find('#pack_products_price').val();						
						var pack_products_reduction = product_pack_form.find('#pack_products_reduction').val();
						var pack_products_reduction_type = product_pack_form.find('#pack_products_reduction_type').val();
						var product_image = $('#product_image')[0].files[0];
						var pack_name = $('#pack_name').val();
						var pack_add_to_all = 0;
						if (product_pack_form.find('#pack_add_to_all').prop('checked'))
						{
							pack_add_to_all = 1;
						}
						//pack_add_to_all_product
						if (product_pack_form.find('#pack_add_to_all_product').prop('checked'))
						{
							pack_add_to_all_product = 1;
						}
						
						var id_superpack_product = $(this).data('id_superpack_product');                    
						productPack.saveProductPack(id_superpack_product, pack_products, pack_products_price, pack_products_reduction, pack_products_reduction_type, product_image, pack_add_to_all, pack_add_to_all_product,pack_products_obj, pack_name);
					
					} 
					else
					{
						showErrorMessage("Należy dodać produkt do zestawu");
						return;
					}
					
				} catch (e) {
					console.trace(e);
				}
			});
			$("#pack-product-add").click(function(){
			  $("#form_pack_products-data").html('');
			  $("#pack_products_price").val('');
			  $("#pack_products_reduction").val('');
			  $("#product_pack_price").val('');
			  $("#product_pack_price_disc").val('');			  
			  $("#product_image").val('');
			  $("#pack-product").show();
			});
			
			function handleProductList(){
			
				if ($("#pack-product").is(":visible")) {
					var product_pack_form = $('#product_pack_form');
					var pack_products = {};
					var pack_products_obj = {};
					var size = 0; excludeIds = '{$id_product|intval}';
					var link = '{$products_list}&q=%QUERY';

					product_pack_form.find('input[name="pack_products[data][]"]').each(function () {
						var id_group = $(this).val();
						pack_products[id_group] = id_group;
						size++;
						excludeIds = ',' + id_group;
						// pack_products['qty-'+id_group] = product_pack_form.find('input[name="pack_products_qty[data]"]').val();
						// pack_products['qty-'+id_group] = $(this).parent().find('input[name="pack_products_qty[data]"]').val();
						let id_item_p = id_group.split(",");
						pack_products_obj[id_group] = {
							id: id_item_p[0],
							quantity: $(this).parent().find('input[name="pack_products_qty[data]"]').val()
						};
					});
					link = link.replace('xexcludeIdsx', excludeIds);
					$('#autocomplete-search').attr( "data-remoteurl", link );
					
					var pack_products_price = product_pack_form.find('#pack_products_price').val();
					var pack_products_reduction = product_pack_form.find('#pack_products_reduction').val();
					var pack_products_reduction_type = product_pack_form.find('#pack_products_reduction_type').val();					
										
					if ((size > 0 && size !== lastSize) || (pack_products_price !== lastpack_products_price) || (parseFloat(pack_products_reduction) != parseFloat(lastpack_products_reduction)) || (pack_products_reduction_type !== lastpack_products_reduction_type)) {
					// if ((size > 0 && size !== lastSize) && (pack_products_price !== lastpack_products_price) && (parseFloat(pack_products_reduction) != parseFloat(lastpack_products_reduction)) && (pack_products_reduction_type !== lastpack_products_reduction_type)) {
						lastSize = size;
						
						if (pack_products_price === '')
						{
							productPack.getProductPackPrice(pack_products);							
						}

						if(parseFloat(pack_products_reduction) !== parseFloat(lastpack_products_reduction)){

							productPack.getProductPackPriceDisc(pack_products, pack_products_price, pack_products_reduction, pack_products_reduction_type,pack_products_obj, true);
							lastpack_products_reduction = pack_products_reduction;
							lastpack_products_reduction_type = pack_products_reduction_type;
							lastpack_products_obj = pack_products_obj;
							return;
						}
						if(parseFloat(pack_products_price) != parseFloat(lastpack_products_price)){

							productPack.getProductPackPriceDisc(pack_products, pack_products_price, pack_products_reduction, pack_products_reduction_type,pack_products_obj, false);
							lastpack_products_price = pack_products_price;
							return;
						}



					}
				}
				
			}

			var lastSize = -1;
			var lastpack_products_price = -1;
			var lastpack_products_reduction = -1;
			var lastpack_products_reduction_type = -1;
			var lastpack_pack_products_obj = -1;

			var excludeIds = '';
			// setInterval(handleProductList, 1000);
		 	$("#pack_products_price").on("change", function() {

				handleProductList();
			});
			 $("#pack_products_reduction").on("change", function() {

				 handleProductList();
			 });
			 $("#pack_products_reduction_type").on("change", function() {

				 handleProductList();
			 });
			
			
			$('#form_pack_products-data').delegate('.delAccessory', 'click', function(){
				
				var id = $(this).attr('name');
				var div = $('#form_pack_products-data');
				var input = $('#inputAccessoriesPack');
				var name = $('#nameAccessoriesPack');

				// Cut hidden fields in array
				var inputCut = input.val().split('-');
				var nameCut = name.val().split('¤');

				if (inputCut.length != nameCut.length)
					return jAlert('Bad size');

				// Reset all hidden fields
				input.val('');
				name.val('');
				div.html('');				
				for (i in inputCut)
				{
					// If empty, error, next
					if (!inputCut[i] || !nameCut[i])
						continue ;
					
					if (inputCut[i] != id)
					{
						var productId = inputCut[i];
						var productName = nameCut[i];
						name.val(name.val() + productName + '¤');
						input.val(input.val() + productId + '-');
						
						//div.innerHTML += '<div class="form-control-static"><button type="button" class="delAccessory btn btn-default" name="' + productId + '"><i class="icon-remove text-danger"></i></button>&nbsp;'+ productName +'<input type="hidden" name="pack_products[data][]" value="' + productId + '"></div>'
						div.html(div.html() + '<div class="form-control-static"><button type="button" class="delAccessory btn btn-default" name="' + productId + '"><i class="icon-remove text-danger"></i></button>&nbsp;'+ productName +'<input type="hidden" name="pack_products[data][]" value="' + productId + '"></div>');
					}
				}


			});

			/*var timer;		
			var responders = $('*:input[type=text][data-autocomplete]');
			responders.keyup(function() {
				var inputSearch = $(this);
				
				clearTimeout(timer);
				timer = setTimeout(function() {
				
					if (excludeIds=='')
					{
						excludeIds = '{$id_product|intval}';
					}
					
					var search_key = inputSearch.val();
					var url = inputSearch.attr('data-autocomplete') + '&q=' + search_key;
					url = url.replace('xexcludeIdsx', excludeIds);
									
					$.ajax({
						type: 'GET',
						url: url,
						headers: { "cache-control": "no-cache" },
						async: true,					
						success: function(data) {
						}
					}).done(function( msg ) {
					});
				}, 700);
			});
			
			$('html').click(function() {
				responders.html('');
			});
			responders.click(function(event){
				event.stopPropagation();
			});*/


		});

	})(jQuery);
	function sendQty(qty) {
		// alert("setn");
		document.getElementById("pack_products_reduction").value="";
		var product_pack_form = $('#product_pack_form');
		var pack_products = {};
		var pack_products_obj = {};
		var size = 0; excludeIds = '{$id_product|intval}';
		var link = '{$products_list}&q=%QUERY';
		product_pack_form.find('input[name="pack_products[data][]"]').each(function () {
			var id_group = $(this).val();
			pack_products[id_group] = id_group;
			size++;
			excludeIds = ',' + id_group;

			let id_item_p = id_group.split(",");
			pack_products_obj[id_group] = {
				id: id_item_p[0],
				quantity: $(this).parent().find('input[name="pack_products_qty[data]"]').val()
			};
		});

		var pack_products_price = product_pack_form.find('#pack_products_price').val();
		var pack_products_reduction = product_pack_form.find('#pack_products_reduction').val();
		var pack_products_reduction_type = product_pack_form.find('#pack_products_reduction_type').val();

		lastSize = size;



		productPack.getProductPackPriceDisc(pack_products, pack_products_price, pack_products_reduction, pack_products_reduction_type,pack_products_obj, false);

		lastpack_products_price = pack_products_price;
		lastpack_products_reduction = pack_products_reduction;
		lastpack_products_reduction_type = pack_products_reduction_type;

	}
</script>