/**
 *
 * 2014-2020 Presta-Mod.pl Rafał Zontek
 *
 * NOTICE OF LICENSE
 *
 * Poniższy kod jest kodem płatnym, rozpowszechanie bez pisemnej zgody autora zabronione
 * Moduł można zakupić na stronie Presta-Mod.pl. Modyfikacja kodu jest zabroniona,
 * wszelkie modyfikacje powodują utratę gwarancji
 *
 * http://presta-mod.pl
 *
 * DISCLAIMER
 *
 *
 *  @author    Presta-Mod.pl Rafał Zontek <biuro@presta-mod.pl>
 *  @copyright 2014-2020 Presta-Mod.pl
 *  @license   Licecnja na jedną domenę
 *  Presta-Mod.pl Rafał Zontek
 *
 */

$(document).ready(function() {    
    if (typeof(pm_inpostpaczkomaty_type) !== 'undefined') { // if paczkomaty typ f
        pm_inpostpaczkomaty_selected_name = pm_inpostpaczkomaty_selected;
        pm_inpostpaczkomaty_last = false;
        pm_inpostpaczkomaty_firstload = false;
        pm_inpostpaczkomaty_resizeinterval = false;
        pm_inpostpaczkomaty_dvopt = '';
        pm_inpostpaczkomaty_multichanging = false;
        current_point = false;

        $(document).on('click','.click-pm-sender', function(){
            $('.pmsender').click();
            return false;
        });

        function resizeMap() {
            if (!pm_inpostpaczkomaty_resizeinterval) {
                pm_inpostpaczkomaty_resizeinterval = setInterval(function() {
                    if ($('.widget-modal').length && $('.modelled').length == 0) {
                        $('.widget-modal').css('width', '90%');
                        $('.widget-modal').css('height', '90%');
                        $('.widget-modal').css('left', (window.innerWidth - $('.widget-modal').width()) / 2);
                        $('.widget-modal').css('top', (window.innerHeight - $('.widget-modal').height()) / 2);
                        clearInterval(pm_inpostpaczkomaty_resizeinterval);
                        pm_inpostpaczkomaty_resizeinterval = false;
                    }
                }, 100)
            }
        }

        function setPoint(point) {
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: modulepath + '' + '?rand=' + new Date().getTime(),
                async: true,
                cache: false,
                data: 'selmachine=1&token=' + pm_inpostpaczkomaty_token + '&current_cart=' + current_cart + '&selected=' + point.name,
                success: function() {
                    setOrderEnabled(true);
                    if (pm_inpostpaczkomaty_ps == '1.6') {
                        if (typeof(updatePaymentMethodsDisplay) != 'undefined')
                            updatePaymentMethodsDisplay();
                    }
                }
            });
        }

        function setSelected() {
            if ($('.overlay_pminpost').is(':visible')) {
                $('.overlay_pminpost').fadeOut();
            }
            if ($('.tohide').length) {
                $('.tohide').remove();
            }
            if ($('.overlay_pminpost').is(':visible')) {
                $('.overlay_pminpost').fadeOut();
            }   

            if (typeof(pm_inpostpaczkomaty_last.name) == 'undefined' || pm_inpostpaczkomaty_selected && pm_inpostpaczkomaty_last.name != pm_inpostpaczkomaty_selected.name) { //if selected                
                setOrderEnabled(true);
                easyPack.points.find(pm_inpostpaczkomaty_selected.name, function(point) {
                    if (typeof(point.payment_type[0]) !== 'undefined') {
                        var add_label = '';
                    } else {
                        var add_label = '';
                    }
                    pm_inpostpaczkomaty_selected_name = point.name + ': ' + point.address.line1 + ', ' + point.address.line2 + ' ' + add_label;
                    pm_inpostpaczkomaty_selected = point.name;
                    if (checkCarrierPaczkomatyEof($(pm_inpostpaczkomaty_dvopt + ':checked').val()) && point && point.type.length > 1) {                        
                        ph = pm_inpostpaczkomaty_label_weekend;
                        $('.paczkomat-input').val('');
                    } else {
                        $('.paczkomat-input').val(point.name + ': ' + point.address.line1 + ', ' + point.address.line2 + ' ' + add_label);
                    }
                    pm_inpostpaczkomaty_last = point;
                    if (pm_inpostpaczkomaty_last !== false) {
                        current_point = point;
                        setPoint(point);
                    }
                });
            } else {
                if (pm_inpostpaczkomaty_selected.name != '' && pm_inpostpaczkomaty_last !== false) { //if selected
                    setOrderEnabled(true);
                    var point = pm_inpostpaczkomaty_last;
                    if (typeof(point.payment_type[0]) !== 'undefined') {
                        var add_label = '';
                    } else {
                        var add_label = '';
                    }
                    if (pm_inpostpaczkomaty_type == 1) {
                        $('.easypack-dropdown__select').children('span').first().html(point.address.line1 + ', ' + point.address.line2 + ' ' + point.name + add_label);
                    } else if (pm_inpostpaczkomaty_type == 2) {
                        pm_inpostpaczkomaty_selected_name = point.name + ': ' + point.address.line1 + ', ' + point.address.line2 + ' ' + add_label;
                        pm_inpostpaczkomaty_selected = point.name;
                        if (checkCarrierPaczkomatyEof($(pm_inpostpaczkomaty_dvopt + ':checked').val()) && point && point.type.length > 1) {                            
                            ph = pm_inpostpaczkomaty_label_weekend;
                            $('.paczkomat-input').val('');
                        } else {
                            $('.paczkomat-input').val(point.name + ': ' + point.address.line1 + ', ' + point.address.line2 + ' ' + add_label);
                        }
                    }
                }
            }
        }

        function loadMap(cod) {
            var types = ['parcel_locker_only'];
            var paymentFilter = [];

            if (cod == true) {
                paymentFilter = {
                    visible: true,
                    defaultEnabled: false,
                    showOnlyWithPayment: true,
                }
            }

            if (checkCarrierPaczkomatyEof($(pm_inpostpaczkomaty_dvopt + ':checked').val())) {
                types = ['parcel_locker_only'];
            }

            if(typeof(pm_inpostpaczkomaty_sandbox) != 'undefined' && pm_inpostpaczkomaty_sandbox) {
                var init_values = {                
                    points: {
                        types: types,
                    },
                    paymentFilter: paymentFilter,
                    map: {
                        initialTypes: types,
                    },
                    assetsServer: "https://sandbox-geowidget.easypack24.net",
                    apiEndpoint: "https://sandbox-api-pl-points.easypack24.net/v1",
                };
            } else {
                var init_values = {                
                    points: {
                        types: types,
                    },
                    paymentFilter: paymentFilter,
                    map: {
                        initialTypes: types,
                    },
                    allowedToolTips: ["pok"],
                };
            }

            easyPack.init(init_values);
            if ($('#popup-btn').length == 0) {
                return false;
            }
            var button = document.getElementById('popup-btn');
            winpost = window.innerWidth * 0.9;
            hinpost = window.innerHeight * 0.9;
            button.onclick = function() {
                pm_inpostpaczkomaty_overlay.fadeIn();
                resizeMap();
                map2 = easyPack.modalMap(function(point) {
                    pm_inpostpaczkomaty_selected = point;
                    setSelected();
                    $('.widget-modal__close').click();
                }, {
                    width: winpost,
                    height: hinpost
                });
            }

            if (pm_inpostpaczkomaty_typewpzl == '1') {
                $('.paczkomat-input').paczkomaty(
                        'https://maps.googleapis.com/maps/api/geocode/json', {
                            formatItem: function(data, i, max, value, term) {
                                return '<span class="paczkomat_name">' + pm_inpostpaczkomaty_paczkomatLabel + ' ' + data.name + '</span> <span>' + data.address.line1 + ', ' + data.address.line2 + '</span><br/><span>' + data.location_description + '</span>';
                            },
                            parse: function(data) {
                                var mytab = [];
                                for (var i = 0; i < data.length; i++) {
                                    mytab[mytab.length] = {
                                        data: data[i],
                                        value: data[i].name + ' ' + data[i].address.line1 + ': ' + data[i].address.line2 + ' ' + data[i].location_description
                                    };
                                }
                                return mytab;
                            },
                            types: types
                        }
                    )
                    .result(function(event, data, formatted) {
                        if (data.status != 404) {
                            $(this).val(data.name + ': ' + data.address.line1 + ', ' + data.address.line2);
                            $('#PMINPOSTPACZKOMATY_MACHINE').val(data.name);
                            pm_inpostpaczkomaty_selected = data;
                            setSelected();
                        }
                    }).getPaczkomatName();
            }
        }

        function loadWidgetMap(cod) {
            if ($('#widget-modal').length) {
                $('#widget-modal').remove();
            }

            if (typeof(window.easyPackAsyncInit) === 'undefined') {
                window.easyPackAsyncInit = function() {
                    loadMap(cod);
                    pm_inpostpaczkomaty_multichanging = false;
                }
            } else {
                loadMap(cod);
                pm_inpostpaczkomaty_multichanging = false;
            }
        }

        function getMyObject(t, value) {
            if (pm_inpostpaczkomaty_opc == 'supercheckout_17' && $('#select-widget').length == 0) {
                $('#hook-display-after-carrier').append(value);
                $('#select-widget').css('height','140px');
            }
            else if (pm_inpostpaczkomaty_opc == 'steasycheckout_17' && $('#select-widget').length == 0) {
                if (pm_inpostpaczkomaty_display_place == 1) {
                    return $('.delivery-options-list').append(value);
                } else if (pm_inpostpaczkomaty_display_place == 2) {
                    return $('#' + t).closest('.delivery-options').prepend(value);
                } else if (pm_inpostpaczkomaty_display_place == 3) {
                    return $('#' + t).parent().parent().parent().after().append(value);
                } else if (pm_inpostpaczkomaty_display_place == 4) {
                    return $('#' + t).parent().parent().parent().before().prepend(value);
                } else if (pm_inpostpaczkomaty_display_place == 5) {
                    return $('.hook-display-before-carrier').append(value);
                } else {
                    return $('#' + t).parent().closest('.carrier-extra-content').prepend(value);
                }
                return;
            } else if (typeof(OnePageCheckoutPS) != 'undefined') {
                if (pm_inpostpaczkomaty_display_place == 1) {
                    return $('#' + t).closest('.delivery-options').append(value);
                } else if (pm_inpostpaczkomaty_display_place == 2) {
                    return $('#' + t).closest('.delivery-options').prepend(value);
                } else if (pm_inpostpaczkomaty_display_place == 3) {
                    return $('#' + t).closest('.row').after(value);
                } else if (pm_inpostpaczkomaty_display_place == 4) {
                    return $('#' + t).closest('.row').before(value);
                } else if (pm_inpostpaczkomaty_display_place == 5) {
                    return $('.hook-display-before-carrier').append(value);
                } else {
                    return $('#' + t).parent().closest('.carrier-extra-content').prepend(value);
                }
            } else if (pm_inpostpaczkomaty_ps == '1.6') {
                if ($('.supercheckout_shipping_option').length) {
                    $('.opc_shipping_method').append(value);
                    $('#select-widget').css('height', '128px');
                    return '';
                }
                if (pm_inpostpaczkomaty_display_place == 1) {
                    $('#' + t).closest('table').parent().append(value);
                } else if (pm_inpostpaczkomaty_display_place == 2) {
                    return $('#' + t).closest('table').parent().prepend(value);
                } else if (pm_inpostpaczkomaty_display_place == 3) {
                    return $('#' + t).parent().parent().parent().parent().parent().find('td:eq(2)').append(value);
                } else if (pm_inpostpaczkomaty_display_place == 4) {
                    return $('#' + t).parent().parent().parent().parent().parent().find('td:eq(2)').prepend(value);
                } else if (pm_inpostpaczkomaty_display_place == 5) {
                    return $('#HOOK_BEFORECARRIER').append(value);
                } else {
                    return $('.hook_extracarrier').append(value);
                }
            } else {
                if ($('.supercheckout-blocks').length) {
                    var colspan = $('#' + t).closest('tr').find('td').length
                    if (pm_inpostpaczkomaty_display_place == 3) {
                        return $('#' + t).closest('tr').after('<tr><td style="width: 100% !important;" colspan="' + colspan + '">' + value + '</td></tr>');
                    } else if (pm_inpostpaczkomaty_display_place == 4) {
                        return $('#' + t).closest('tr').before('<tr><td style="width: 100% !important;" colspan="' + colspan + '">' + value + '</td></tr>');
                    } else if (pm_inpostpaczkomaty_display_place == 1) {
                        return $('#' + t).closest('table').after(value);
                    } else if (pm_inpostpaczkomaty_display_place == 2) {
                        return $('#' + t).closest('table').before(value);
                    }
                } else if ($('#module-thecheckout-order').length) {
                    if (pm_inpostpaczkomaty_display_place == 1) {
                        return $('#' + t).closest('.delivery-options').after(value);
                    } else if (pm_inpostpaczkomaty_display_place == 2) {
                        return $('#' + t).closest('.delivery-options').before(value);
                    } else if (pm_inpostpaczkomaty_display_place == 3) {
                        return $('#' + t).closest('.delivery-option-row').after('<div class="delivery-option-row row delivery-option">' + value + '</div>');
                    } else if (pm_inpostpaczkomaty_display_place == 4) {
                        return $('#' + t).closest('.delivery-option-row').before('<div class="delivery-option-row row delivery-option">' + value + '</div>');
                    } else if (pm_inpostpaczkomaty_display_place == 5) {
                        return $('.hook-display-before-carrier').append(value);
                    } else {
                        return $('#' + t).parent().closest('.carrier-extra-content').prepend(value);
                    }
                } else {
                    if (pm_inpostpaczkomaty_display_place == 1) {
                        return $('#' + t).closest('.delivery-options').append(value);
                    } else if (pm_inpostpaczkomaty_display_place == 2) {
                        return $('#' + t).closest('.delivery-options').prepend(value);
                    } else if (pm_inpostpaczkomaty_display_place == 3) {
                        return $('#' + t).closest('.row').append(value);
                    } else if (pm_inpostpaczkomaty_display_place == 4) {
                        return $('#' + t).closest('.row').prepend(value);
                    } else if (pm_inpostpaczkomaty_display_place == 5) {
                        return $('.hook-display-before-carrier').append(value);
                    } else {
                        return $('#' + t).parent().closest('.carrier-extra-content').prepend(value);
                    }
                }
            }
        }

        function getInpostHtml(paczkomatySelectLabel, pm_inpostpaczkomaty_selected_name, pm_inpost_placeholder, pm_inpostpaczkomaty_label) {
            var ph = pm_inpost_placeholder
            if (checkCarrierPaczkomatyEof($(pm_inpostpaczkomaty_dvopt + ':checked').val()) && current_point && current_point.type.length > 1) {
                pm_inpostpaczkomaty_selected = '';
                pm_inpostpaczkomaty_selected_name = '';
                ph = pm_inpostpaczkomaty_label_weekend;
            }

            if (checkCarrierPaczkomatyEof($(pm_inpostpaczkomaty_dvopt + ':checked').val())) { // reload page
                ph = pm_inpostpaczkomaty_label_weekend;   
            }

            var sclass = '';
            var col = 'col-50';
            if ($('.supercheckout-blocks').length || $('#module-thecheckout-order').length) {
                col = 'col-100';
            }
            if (pm_inpostpaczkomaty_opc == 'steasycheckout_17') {
                sclass = 'widget-steasycheckout_17';
                col = 'col-100';   
            }
            var html = '<div class="'+sclass+'" id="select-widget"><div class="col-100"><span class="paczkomaty-label">' + paczkomatySelectLabel + '</span></div><div class="' + col + '"><input value="' + pm_inpostpaczkomaty_selected_name + '" type="text" class="paczkomat-input" ';
            if (pm_inpostpaczkomaty_typewpzl == '1') {
                html = html + 'placeholder="' + ph;
            } else {
                html = html + 'disabled="disabled" placeholder="' + pm_inpost_placeholder2;
            }
            html = html + '" /></div><div class="' + col + '"><button class="pmsender" id="popup-btn"><span class="icon-map-marker"></span> ' + pm_inpostpaczkomaty_label + '</button></div></div>';
            return html;
        }



        function setOrderEnabled(enabled) {
            if (pm_inpostpaczkomaty_selected != '') {
                enabled = true;
            }
            if ($('#spstepcheckout').length) {
                if (enabled) {
                    $('.alert-paczkomaty').remove();
                    $('#payment-confirmation').find('button[type="submit"]').show();
                } else {
                    $('#payment-confirmation').find('button[type="submit"]').hide();
                    if ($('.alert-paczkomaty').length == 0) {
                        $('#payment-confirmation').append('<div class="alert-paczkomaty alert alert-warning">'+paczkomatySelectLabel+'</div>');
                    }
                }
                return ;
            }
            label = paczkomatySelectLabel;
            if (enabled) {
                if (pm_inpostpaczkomaty_opc == 'steasycheckout_17') {
                    $('.steasy-paczkomat-select-label').remove();
                    $('.steco_confirmation_btn').show();
                    return;
                }
                if ($('.confirm_button').length && $('.confirm_button').attr('onclick') == "$.confirm({title: '',content: '<strong>" + label + "</strong>',buttons: {cancel: {text: 'OK'}}}); $('.pmsender').click(); return false;") {
                    $('.confirm_button').attr('onclick', "paymentModuleConfirm();")
                }
                $('button[name="confirmDeliveryOption"]').removeAttr('disabled');
                $('#supercheckout_confirm_order').show();
                $('#wybierzpaczkomat').hide();
                $('#confirm_order').removeAttr('disabled').show();                
                if (typeof(OnePageCheckoutPS) != 'undefined') {
                    if ($('#wybierzpaczkomat').length == 0) {
                        $('#btn_place_order').after('<button disabled="disabled" id="wybierzpaczkomat" style="background: none; background-color: #aaa; display: block; padding: 5px; font-size: 18px; color: white; margin-top: 0px;float: right;" class="orangebutton2">' + paczkomatySelectLabel + '</button>');
                    }
                    $('#btn_place_order').show();
                }
            } else {
                if (pm_inpostpaczkomaty_opc == 'steasycheckout_17') {
                    if ($('.steasy-paczkomat-select-label').length == 0) {
                        $('.steasy-paczkomat-select-label').remove();
                        $('.steco_confirmation_btn').parent().parent().append('<div class="steasy-paczkomat-select-label"><a href="#" class="click-pm-sender btn btn-default" style="color: #fff; background-color: #8e021d; font-size: 12px; border-color: rgba(0, 0, 0, 0);">'+label+'</a></div>')
                    }
                    $('.steco_confirmation_btn').hide();
                    return;
                }
                if ($('.confirm_button').length && $('.confirm_button').attr('onclick') == 'paymentModuleConfirm();') {
                    
                    $('.confirm_button').attr('onclick', "$.confirm({title: '',content: '<strong>" + label + "</strong>',buttons: {cancel: {text: 'OK'}}}); $('.pmsender').click(); return false;")
                }
                $('#confirm_order').attr('disabled', 'disabled').hide();
                $('#supercheckout_confirm_order').hide();
                $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
                $('#supercheckout_confirm_order').hide();
                $('#wybierzpaczkomat').show();
                if (typeof(OnePageCheckoutPS) != 'undefined') {
                    if ($('#wybierzpaczkomat').length == 0) {
                        $('#btn_place_order').after('<button disabled="disabled" id="wybierzpaczkomat" style="background: none; background-color: #aaa; display: block; padding: 5px; font-size: 18px; color: white; margin-top: 0px;float: right;" class="rangebutton2">' + paczkomatySelectLabel + '</button>');
                    }
                    $('#btn_place_order').hide();
                }
            }            
        }

        function checkCarrierPaczkomatyEof(id) {
            if ($.inArray(parseInt(id).toString(), pm_inpostpaczkomaty_carrier_eof) != -1) {
                return true;
            } else {
                return false;
            }
        }

        function checkCarrierPaczkomaty(id) {
            if ($.inArray(parseInt(id).toString(), pm_inpostpaczkomaty_carrier) != -1) {
                return true;
            } else {
                return false;
            }
        }

        function checkCarrierPaczkomatyCod(id) {
            if ($.inArray(parseInt(id).toString(), pm_inpostpaczkomaty_carrier_cod) != -1) {
                return true;
            } else {
                return false;
            }
        }

        function checkCarrierPaczkomatyAll(id) {
            return checkCarrierPaczkomaty(id) || checkCarrierPaczkomatyCod(id);
        }

        function onChangeDeliveryOptionWidget(at, ajax, type) {
            if (pm_inpostpaczkomaty_multichanging == false) {                
                pm_inpostpaczkomaty_multichanging = true;
                setTimeout(function() {
                    if ($('#select-widget').length) {
                        $('#select-widget').remove();
                    }
                    var t = $(at).attr('id');
                    var cod = false;
                    selected = false;
                    if (checkCarrierPaczkomaty(parseInt($(at).val()))) {
                        enabled = pm_inpostpaczkomaty_selected == '' ? false : true;
                        setOrderEnabled(enabled);
                        selected = true;
                        cod = false;
                    } else if (checkCarrierPaczkomatyCod($(at).val())) {
                        enabled = pm_inpostpaczkomaty_selected == '' ? false : true;
                        setOrderEnabled(enabled);
                        selected = true;
                        cod = true;
                    } else {
                        setOrderEnabled(true);
                    }
                    if (selected) {
                        getMyObject(t, getInpostHtml(paczkomatySelectLabel, pm_inpostpaczkomaty_selected_name, pm_inpost_placeholder, pm_inpostpaczkomaty_label));
                        loadWidgetMap(cod);
                    } 
                    pm_inpostpaczkomaty_multichanging = false;
                }, 500)
            }
        }

        function getCheckoutElement() {
            pm_inpostpaczkomaty_dvopt = '';
            if ($('.carrier_action').find('input[name="id_carrier"]').length) {
                pm_inpostpaczkomaty_dvopt = 'input[name="id_carrier"]';
                return ;
            } else if (typeof(OnePageCheckoutPS) != 'undefined') {
                pm_inpostpaczkomaty_dvopt = '.delivery_option_radio';
            } else if ($('.delivery-options').length) {
                pm_inpostpaczkomaty_dvopt = '.delivery-options input[type="radio"]';
            } else if ($('.delivery_option_radio').length || $('.supercheckout-blocks').length) {
                pm_inpostpaczkomaty_dvopt = '.delivery_option_radio';
            } else {
                pm_inpostpaczkomaty_dvopt = '.delivery-option input[type="radio"]';
            }
        }

        getCheckoutElement();

        $(document).on('click', '#checkout-payment-step', function() {
            if (pm_inpostpaczkomaty_selected.length == 0 && checkCarrierPaczkomatyAll($(pm_inpostpaczkomaty_dvopt + ':checked').val())) {
                $('#checkout-delivery-step').click();
                $('.pmsender').fadeOut().fadeIn().fadeOut().fadeIn();
            } else return true;
        })

        if ($('.js-current-step').is('#checkout-payment-step')) {
            $('#checkout-payment-step').click();
        }

        $(document).on('change', pm_inpostpaczkomaty_dvopt + ' ', function() {
            onChangeDeliveryOptionWidget(this, true, pm_inpostpaczkomaty_type);
        })

        if (pm_inpostpaczkomaty_ps == '1.6' || pm_inpostpaczkomaty_opc == 'spstepcheckout_17') {
            $(document).ajaxComplete(function(event, xhr, settings) {
                if (typeof(settings.data) != 'undefined' && settings.data.toString().indexOf('updateCarrier') != -1 ||
                    typeof(settings.data) != 'undefined' && settings.data.toString().indexOf('updateAddressesSelected') != -1) {
                    onChangeDeliveryOptionWidget($(pm_inpostpaczkomaty_dvopt + ':checked'), true, pm_inpostpaczkomaty_type);
                }
                if ($(pm_inpostpaczkomaty_dvopt + ':checked').val() != 'undefined') {
                    if (checkCarrierPaczkomatyAll($(pm_inpostpaczkomaty_dvopt + ':checked').val()) && pm_inpostpaczkomaty_selected == '') {
                        setOrderEnabled(false);
                    }
                }
                if (checkCarrierPaczkomatyAll($(pm_inpostpaczkomaty_dvopt + ':checked').val())) {
                    setTimeout(function() {
                        if ($('.pmsender').length == 0) {
                            onChangeDeliveryOptionWidget($(pm_inpostpaczkomaty_dvopt + ':checked'), true, pm_inpostpaczkomaty_type);
                        }
                    }, 300)
                    setOrderEnabled(false);
                }
            });
        } else {
            $(document).ajaxComplete(function(event, xhr, settings) {
                if ($(pm_inpostpaczkomaty_dvopt + ':checked').val() != 'undefined') {
                    if (checkCarrierPaczkomatyAll($(pm_inpostpaczkomaty_dvopt + ':checked').val()) && pm_inpostpaczkomaty_selected == '') {
                        setOrderEnabled(false);
                    } else {
                        setOrderEnabled(true);
                    }
                    if ($('.supercheckout-blocks').length) {
                        if ($('.pmsender').length == 0) {
                            onChangeDeliveryOptionWidget($(pm_inpostpaczkomaty_dvopt + ':checked'), true, pm_inpostpaczkomaty_type);
                        }
                    }
                }
            });
            $(document).ajaxStart(function(event, xhr, settings) {
                if ($(pm_inpostpaczkomaty_dvopt + ':checked').val() != 'undefined') {
                    if (checkCarrierPaczkomatyAll($(pm_inpostpaczkomaty_dvopt + ':checked').val()) && pm_inpostpaczkomaty_selected == '') {
                        setOrderEnabled(false);
                    } else {
                        setOrderEnabled(true);
                    }
                }
            });
        }

        if (checkCarrierPaczkomatyAll($(pm_inpostpaczkomaty_dvopt + ':checked').val())) {
            onChangeDeliveryOptionWidget($(pm_inpostpaczkomaty_dvopt + ':checked'), false, pm_inpostpaczkomaty_type);
            pm_inpostpaczkomaty_firstload = true;
        }
        $(document).on('click', '.paczkomat-input', function() {
            $('#js-delivery').removeAttr('id');
            $('.delivery-option').find('').parent().attr('id', 'js-delivery');
        });

        var pm_inpostpaczkomaty_longloading2 = setInterval(function() {
            if (pm_inpostpaczkomaty_dvopt == '') {
                getCheckoutElement();
            }
            if ($('#supercheckout_confirm_order').length && $('#supercheckout_confirm_order').parent().find('#wybierzpaczkomat').length == 0) {
                $('#supercheckout_confirm_order').parent().append('<button disabled="disabled" id="wybierzpaczkomat" style="background: none; background-color: #aaa; display: none; padding: 10px; font-size: 18px; color: white; margin-top: 10px;" class="orangebutton2">' + paczkomatySelectLabel + '</button>');
            } else if ($('#module-thecheckout-order').length && $('#confirm_order').parent().find('#wybierzpaczkomat').length == 0) {
                $('#confirm_order').parent().append('<button disabled="disabled" id="wybierzpaczkomat" style="background: none; background-color: #aaa; display: none; padding: 10px; font-size: 18px; color: white; margin-top: 10px;" class="orangebutton2">' + paczkomatySelectLabel + '</button>');
            }

            if ($(pm_inpostpaczkomaty_dvopt + ':checked').val() != 'undefined') {
                if (checkCarrierPaczkomatyAll($(pm_inpostpaczkomaty_dvopt + ':checked').val())) {
                    if ($('.pmsender').length == 0) {
                        onChangeDeliveryOptionWidget($(pm_inpostpaczkomaty_dvopt + ':checked'), true, 2);
                        clearInterval(pm_inpostpaczkomaty_longloading2);
                    }
                }
            }
        }, 500)

        $(window).resize(function() {
            if ($('.widget-modal').length && $('.modelled').length == 0) {
                resizeMap();
            }
        })

        $(document).on('click', '.pmsender', function() {
            return false;
        })


        pm_inpostpaczkomaty_overlay = $('<div class="overlay_pminpost"></div>');
        $('body').append(pm_inpostpaczkomaty_overlay);
        pm_inpostpaczkomaty_overlay.hide();

        if (typeof(acceptCGV) !== 'undefined') {
            var copyacceptCGV = acceptCGV;            
            acceptCGV = function() {
                if (checkCarrierPaczkomatyAll($(pm_inpostpaczkomaty_dvopt + ':checked').val())) {
                    var label = paczkomatySelectLabel;
                    if (checkCarrierPaczkomatyEof($(pm_inpostpaczkomaty_dvopt + ':checked').val()) && current_point && current_point.type.length > 1) {
                        pm_inpostpaczkomaty_selected = '';
                        pm_inpostpaczkomaty_selected_name = '';
                        label = pm_inpostpaczkomaty_label_weekend
                    }
                    if (pm_inpostpaczkomaty_selected == '') {
                        $.confirm({
                            title: '',
                            content: '<strong>' + label + '</strong>',
                            buttons: {
                                custom: {
                                    text: pm_inpostpaczkomaty_label,
                                    btnClass: 'btn btn-primary',
                                    action: function() {
                                        this.close();
                                        $('.pmsender').click();
                                    }
                                },
                                cancel: {
                                    text: 'OK'
                                }
                            }
                        });

                        return false;
                    }

                }
                return copyacceptCGV();
            }
        }
    }

    $(document).on('click', '.widget-modal__close', function() {
        $('.overlay_pminpost').fadeOut();
    });    
});