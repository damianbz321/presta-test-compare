/*globals productSearchUrl, productListDetails*/

function createProductListItem(fieldNamePrefix, item) {
  if (typeof createProductListItem.counter == 'undefined') {
    createProductListItem.counter = 0;
  }

  if (typeof item === 'undefined') {
    console.error(fieldNamePrefix, item);
    return '';
  }

  ++createProductListItem.counter;

  let combinations = [];
  combinations.push('<option value="0">Any</option>');
  for (let comb in item.combinations) {
    combinations.push('<option value="' + item.combinations[comb].id_product_attribute + '">'
      + item.combinations[comb].attributes + '</option>');
  }

  let li = $('.' + fieldNamePrefix + 'list_item_template').clone().html()
    .replace(/\|#item_num#\|/g, createProductListItem.counter)
    .replace(/\|#id_product#\|/g, item.id_product)
    .replace(/\|#reference#\|/g, item.reference)
    .replace(/\|#name#\|/g, item.name)
    .replace(/\|#price#\|/g, item.price_tax_incl)
    .replace(/\|#combinations#\|/g, combinations.join(''))
    .replace(/\|#list_prefix#\|/g, fieldNamePrefix)
  ;

  return '<tr>' + li + '</tr>';
}

function updateProductCombinationListItem(obj, product) {
  obj.find('.product-combination-selector').val(product.id_product_attribute);
}

$(document).ready(function () {
  let fieldNamePrefixes = ['action_product_', 'condition_product_'];
  for (let i in fieldNamePrefixes) {
    let fieldNamePrefix = fieldNamePrefixes[i];

    let productList = window[fieldNamePrefix + 'item_list'];
    for (let i in productList) {
      let product = productList[i];
      console.log('tbody#' + fieldNamePrefix + 'list');
      $('tbody#' + fieldNamePrefix + 'list').append(
        createProductListItem(fieldNamePrefix, productListDetails[product.id_product])
      );
      let li = $('tbody#' + fieldNamePrefix + 'list tr:last-child');
      updateProductCombinationListItem(li, product);
    }

    $('#' + fieldNamePrefix + 'search').autocomplete(productSearchUrl, {
      minChars: 1,
      max: 20,
      dataType: 'json',
      formatItem: function (data, i, max, value, term) {
        return value;
      },
      parse: function (data) {
        console.log(data);
        let result = [];
        if (typeof (data.products) !== 'undefined') {
          let x = 0;
          for (let i in data.products) {
            let item = data.products[i];
            result[x++] = {
              data: item,
              value: [
                '#' + item.id_product,
                item.name
              ].join(' ')
            };
          }
        }
        return result;
      },
      extraParams: {
        // excludeIds: $('#product_items_ids').val().slice(0, -1), // slice last dash
        // product_search: function () {
        //     return $('input#' + fieldNamePrefix + 'search').val();
        // },                // excludeIds: $('#product_items_ids').val().slice(0, -1), // slice last dash
        search_phrase: function () {
          return $('input#' + fieldNamePrefix + 'search').val();
        },
        // action: 'searchProducts',
        // ajax: 1,
        // id_currency: 1,
      }
    }).result(function (event, item, formatted) {
      $('#' + fieldNamePrefix + 'list').append(createProductListItem(fieldNamePrefix, item));
    });

    $(document).on('click', '#' + fieldNamePrefix + 'list .del-product', function (e) {
      e.preventDefault();
      $(this).closest('tr').remove();
    });
  }

  let elem = 'input[name="action_price_type"]:checked';
  $(document).on('change', elem, function () {
    let setAmount = $('input[name="set_product_price_amount"]').closest('.form-group');
    let reducePercentage = $('input[name="reduce_product_price_percentage"]').closest('.form-group');
    let reduceAmount = $('input[name="reduce_product_price_amount"]').closest('.form-group');
    let actionProductPrice = $('.product_item_list_price');

    setAmount.hide();
    reducePercentage.hide();
    reduceAmount.hide();
    actionProductPrice.hide();

    switch ($(this).attr('id')) {
      case 'ACTION_PRICE_TYPE_SET':
        setAmount.show();
        break;
      case 'ACTION_PRICE_TYPE_REDUCE_PERCENTAGE':
        reducePercentage.show();
        break;
      case 'ACTION_PRICE_TYPE_REDUCE_AMOUNT':
        reduceAmount.show();
        break;
      case 'ACTION_PRICE_TYPE_SET_INDIVIDUALLY':
        actionProductPrice.show();
        break;
    }
  });
  $(elem).trigger('change');

  $('#condition_num_of_products_in_cart').addClass('form-control').attr('type', 'number').attr('min', '1');
  $('#condition_cart_total_value').addClass('form-control').attr('type', 'number').attr('min', '0');
  $('#set_product_price_amount').addClass('form-control').attr('type', 'number').attr('min', '0');
  $('#reduce_product_price_percentage').addClass('form-control').attr('type', 'number').attr('min', '0');
  $('#reduce_product_price_amount').addClass('form-control').attr('type', 'number').attr('min', '0');

  // hide loader
  setTimeout(function () {
    $('#pshowupsell-js-loader').hide();
    $('.filter-blur').removeClass('filter-blur');
  }, 500);
});

$(document).ready(function () {
  $('.tab-content #conditions').prepend('<div id="conditionsExplanationBox" class="alert alert-info"></div>');
  let conditionsExplanationBox = $('#conditionsExplanationBox');
  let updateConditionsExplanationText = function () {
    let txt = '<strong>This cross-sell rule applies only when</strong> ';

    let cartTotalValue = parseFloat($('input#condition_cart_total_value').val().replace(',', '.'));
    if (cartTotalValue > 0) {
      txt += 'the shopping cart total value is equal to or exceeds ' + cartTotalValue + ' ' + currency.sign + ' and ';
    }

    let cartNumOfProducts = parseInt($('input#condition_num_of_products_in_cart').val());
    if (cartTotalValue <= 0) {
      txt += 'the shopping cart ';
    }
    txt += 'contains at least ' + cartNumOfProducts + ' product(s)';

    let cartProductIds = [];
    for (let i = 0; i < 100; ++i) {
      let conditionProductIdInput = $('input[name="condition_product_item_list[' + i + '][id_product]"]');
      if (conditionProductIdInput.length) {
        cartProductIds.push(conditionProductIdInput.val());
      }
    }

    let cartCategoryName = [];
    let categoryExplanationLimit = 3;
    let categoryExplanationQty = 0;
    $('#conditionCategories .tree-selected label').each(function () {
      if (++categoryExplanationQty <= categoryExplanationLimit) {
        cartCategoryName.push($(this).text());
      }
    });
    if (cartProductIds) {
      txt += ' with one of the ID [' + cartProductIds.join(', ') + ']';
    }
    if (cartCategoryName) {
      if (cartProductIds) {
        txt += ' or ';
      }
      txt += ' from one of the category: <i>"' + cartCategoryName.join('", "') + '"';
      if (categoryExplanationQty > categoryExplanationLimit) {
        txt += '... (and ' + (categoryExplanationQty - categoryExplanationLimit) + ' more)';
      }
      txt += '</i> ';
    }

    conditionsExplanationBox.html(txt);
  };
  $(document).on('click', 'a[href="#conditions"], .tree-panel-heading-controls a', function () {
    updateConditionsExplanationText();
  });
  $(document).on('change', '.tab-content #conditions input', function () {
    updateConditionsExplanationText();
  });
  $(document).on('keyup', '.tab-content #conditions input', function () {
    updateConditionsExplanationText();
  });
});

$(document).ready(function () {
  $('.tab-content #action').prepend('<div id="actionsExplanationBox" class="alert alert-info"></div>');
  let actionsExplanationBox = $('#actionsExplanationBox');
  let updateActionsExplanationText = function () {
    let txt = '<strong>Once the conditions of this cross-sell rule are met</strong>, ';

    let cartProductIds = [];
    let cartProductIdsLimit = 3;
    let cartProductIdsQty = 0;
    for (let i = 0; i < 100; ++i) {
      let conditionProductIdInput = $('input[name="action_product_item_list[' + i + '][id_product]"]');
      if (conditionProductIdInput.length && ++cartProductIdsQty <= cartProductIdsLimit) {
        cartProductIds.push(conditionProductIdInput.val());
      }
    }
    if (cartProductIds.length) {
      txt += 'the customers may purchase <i>"' + cartProductIds.join('", "') + '"';
      if (cartProductIdsQty > cartProductIdsLimit) {
        txt += '... (and ' + (cartProductIdsQty - cartProductIdsLimit) + ' more)';
      }
      txt += '</i> ';
    } else {
      txt += 'the customers will not gain any benefits :(';
    }

    if (cartProductIds.length) {
      switch (parseInt($('input[name="action_price_type"]:checked').val())) {
        case 1: // ACTION_PRICE_TYPE_SET
          txt += 'with new price: ' + $('input[name="set_product_price_amount"]').val() + ' ' + currency.sign;
          break;
        case 2: // ACTION_PRICE_TYPE_REDUCE_PERCENTAGE
          txt += 'with reduced price: -' + $('input[name="reduce_product_price_percentage"]').val() + '%';
          break;
        case 3: // ACTION_PRICE_TYPE_REDUCE_AMOUNT
          txt += 'with reduced price: -' + $('input[name="reduce_product_price_amount"]').val() + ' ' + currency.sign;
          break;
        case 4: // ACTION_PRICE_TYPE_SET_INDIVIDUALLY
          txt += 'with new prices set below';
          break;
      }
    }

    actionsExplanationBox.html(txt);
  };
  $(document).on('click', 'a[href="#action"]', function () {
    updateActionsExplanationText();
  });
  $(document).on('change', '.tab-content #action input', function () {
    updateActionsExplanationText();
  });
  $(document).on('keyup', '.tab-content #action input', function () {
    updateActionsExplanationText();
  });

  let updateRuleType = function() {
    let activeType = $('[name="rule_type"]:checked').attr('id');
    let indiProductOptions = $('#rule_type_indi_pro_default_label').closest('.form-group');
    switch (activeType) {
      case 'RULE_TYPE_WHOLE_CART':
        indiProductOptions.hide();
        break;
      case 'RULE_TYPE_INDIVIDUAL_PRODUCTS':
        indiProductOptions.show();
        break;
    }

  };
  $(document).on('change', '[name="rule_type"]', updateRuleType);
  updateRuleType();
});
