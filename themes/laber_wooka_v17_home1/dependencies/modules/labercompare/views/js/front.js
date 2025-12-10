$(document).ready(function() {
	var timeout_compare=null;

	$('body').on('click', '.comment_compare', function (event) {
		
      var data_conten = $(this).attr('rel');
	  $(data_conten).fadeToggle();
	  
	});
	
	compareRefreshStatus();
	
	$('body').on('shown.bs.modal','.quickview',(function() {
		compareRefreshStatus();
	}));
	
	prestashop.on(
		  'updateProductList',
		  function (event) {
			  compareRefreshStatus();
		}
	);	
	
	prestashop.on(
		  'updateProductLoadMore',
		  function (event) {
			  compareRefreshStatus();
		}
	);	
	
	
	$('body').on('click', '.js-compare-add', function (event) {
		var self = this;
		prestashop.emit('clickCompareAdd', {
			dataset: self.dataset,
			self: self
		});
		event.preventDefault();
	});

	$('body').on('click', '.js-compare-remove', function (event) {

		var self = this;
		prestashop.emit('clickCompareRemove', {
			dataset: self.dataset
		});
		event.preventDefault();
	});

	$('body').on('click', '.js-compare-remove-all', function (event) {

		var self = this;
		prestashop.emit('clickCompareRemoveAll', {
			dataset: self.dataset
		});
		event.preventDefault();
	});
	
	$('body').on('click', '.js-show-modal-compare', function (event) {

		var self = this;
		prestashop.emit('clickShowModalCompare', {
			dataset: self.dataset
		});
		event.preventDefault();
	});

	prestashop.on('clickCompareAdd', function (elm) {
		
		clearTimeout(timeout_compare);
		
		$(".laber_Popup").addClass('active');
		$("#laber_add_to_compare").removeClass('loader').addClass('active').removeClass('alert_error');
		
		if (timeout_compare) {
			clearTimeout(timeout_compare);
			timeout_compare = null;
		}
		
		var data = {
			'process': 'add',
			'ajax': 1,
			'idProduct': elm.dataset.idProduct
		};

		$.post(elm.dataset.url, data, null, 'json').then(function (resp) {

			var urlProduct  = elm.dataset.urlProduct;
			var nameProduct  = elm.dataset.nameProduct;
			var imageProduct  = elm.dataset.imageProduct;

			if (resp.success) {
				
				compareProductsIdsAdd(elm.dataset.idProduct);
				
				compareRefreshStatus();
				
				$("#laber_add_to_compare").addClass('loader');
				
				$("#laber_add_to_compare .loader_content").html("<div class='compare_add_success'><h4>"+resp.data.message+"</h4></div><div class='thumbnail-container'><a href='"+urlProduct+"' title='"+nameProduct+"'><img src='"+imageProduct+"' alt=''></a></div><h2 class='nameProduct'><a href='"+urlProduct+"' title='"+nameProduct+"'>"+nameProduct+"</a></h2><div class='laber_button'><a class='js-show-modal-compare' title='"+resp.data.view+"' href='javascript:void(0)'  data-url='"+prestashop.urls.base_url+"module/labercompare/comparator'>"+resp.data.view+"</a></div>");
				
			}else{
				
				$("#laber_add_to_compare").addClass('loader').addClass('alert_error');
				
				$("#laber_add_to_compare .loader_content").html("<h4 class='error'>"+resp.data.message+"</h4><a class='js-show-modal-compare' title='"+resp.data.view+"' href='javascript:void(0)'  data-url='"+prestashop.urls.base_url+"module/labercompare/comparator'>"+resp.data.view+"</a>");
	
			}
			
			if (timeout_compare) {
				clearTimeout(timeout_compare);
				timeout_compare = null;
			}
			
			timeout_compare = setTimeout(function() {
					$(".laber_Popup").removeClass('active');         
					$("#laber_add_to_compare").removeClass('active');         
			}, 1500);
			
		}).fail(function (resp) {
			prestashop.emit('handleError', { eventType: 'clickCompareAdd', resp: resp });
		});
	});

	prestashop.on('clickCompareRemove', function (elm) {

		var data = {
			'process': 'remove',
			'ajax': 1,
			'idProduct': elm.dataset.idProduct
		};

		$.post(elm.dataset.url, data, null, 'json').then(function (resp) {
			$('.js-compare-' + elm.dataset.idProduct).remove();
			
			compareProductsIdsRemove(elm.dataset.idProduct);
			compareRefreshStatus();
			
			if (compareProductsIds.length == 0) {
				$('#js-compare-table').remove();
				$('#js-compare-warning').show();
			}
		}).fail(function (resp) {
			prestashop.emit('handleError', { eventType: 'clickCompareRemove', resp: resp });
		});
	});

	prestashop.on('clickCompareRemoveAll', function (elm) {

		var data = {
			'process': 'removeAll',
			'ajax': 1
		};

		$.post(elm.dataset.url, data, null, 'json').then(function (resp) {
			compareProductsIds = [];
			compareRefreshStatus();
			$('#js-compare-table').remove();
			$('#js-compare-warning').show();
		}).fail(function (resp) {
			prestashop.emit('handleError', { eventType: 'clickCompareRemoveAll', resp: resp });
		});
	});
	
	prestashop.on('clickShowModalCompare', function (elm) {
		$('#moda_compare').modal('hidden');
		 $("body > div.loading").remove();
		 $("body").append("<div class='loading'><span><i class='zmdi zmdi-spinner zmdi-hc-spin'></i></span></div>");
		$.post(elm.dataset.url, null, null, 'json').then(function (resp) {

			$('body').append(resp.data.message);
			$('#moda_compare').attr('data-nb-item',compareProductsIds.length);
			$('#moda_compare').modal('show');
		}).fail(function (resp) {
			
			prestashop.emit('handleError', { eventType: 'clickShowModalCompare', resp: resp });
		});
	});
	
	$('body').on('show.bs.modal','#moda_compare',(function() {
		$("body > div.loading").remove();
		$("body > div.modal-backdrop").remove();
	}));		

	$('body').on('hidden.bs.modal','#moda_compare',(function() {
		$('#moda_compare').remove();
	}));

	function compareProductsIdsAdd(id)
	{
		if ($.inArray(parseInt(id),compareProductsIds) == -1)
			compareProductsIds.push(parseInt(id));
	}
	
	function compareProductsIdsRemove(id)
	{
		compareProductsIds.splice($.inArray(parseInt(id),compareProductsIds), 1);
		
	}

	function compareRefreshStatus()
	{
		$('.js-compare').each(function(){
			if ($.inArray(parseInt($(this).data('id-product')),compareProductsIds)!= -1){
				$(this).addClass('checked').addClass('js-compare-remove').removeClass('js-compare-add');
				$(this).attr('title', 'Usuń z porównania');
				$(this).find("span").text('Usuń z porównania');
			}
			else{
				$(this).removeClass('checked').addClass('js-compare-add').removeClass('js-compare-remove');
				$(this).attr('title','Dodaj do porównania');
				$(this).find("span").text('Dodaj do porównania');
			}
		});
		$('.js-compare-nb').text(compareProductsIds.length);
		
		$('#moda_compare').attr('data-nb-item',compareProductsIds.length);
		
	}
});
