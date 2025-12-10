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

$(document).ready(function () {
    function disablePaczkomatyInputs() {
        $('.paczkomaty-form').find('input[type="text"],select,input[type="radio"]').attr('disabled','disabled');
        $('#pminpostorder_format').removeAttr('disabled');
        $('#pminpostorder_size_l').removeAttr('disabled');
    }

    function assignEvents() {
        $('.paczkomaty-form').find('.switch').addClass('ps-switch');
        $('.pminpostbutton-ajax').parent().addClass('text-center');

        if ($('input[name="pminpostorder_pobranie"]:checked').val() == 0) {
            $('#pminpostorder_pobranie_value').attr('disabled','disabled');
        }

        if ($('input[name="pminpostorder_ubezpieczenie"]:checked').val() == 0) {
            $('#pminpostorder_ubezpieczenie_value').attr('disabled','disabled');
        }    

        $('.btnpaczkomaty-size input:checked').parent().parent().addClass('active');    

        $('input[name="PMINPOSTPACZKOMATY_SHIPX"]:checked').click();
        var paczkomatLabel = 'Paczkomat: ';

        if (wpzl == 1) {
            var paczkomatPlaceholder = 'Wprowadź miejscowość, ulicę, lub nazwę paczkomatu';
            $('#pminpostorder_selected').hide().parent().append('<input class="paczkomaty_input form-control" type="text" placeholder="'+paczkomatPlaceholder+'" id="pminpostorder_selected_COPY">');
            $('#pminpostorder_selected_COPY').val($('#pminpostorder_selected').val());
            $('#pminpostorder_selected_COPY').paczkomaty(
                'https://maps.googleapis.com/maps/api/geocode/json',
                {
                    formatItem: function(data, i, max, value, term) {
                        return '<span class="paczkomat_name">'+paczkomatLabel+data.name+'</span> <span>'+data.address.line1+', '+data.address.line2+'</span>';
                    },       
                    parse: function(data) {
                        var mytab = [];
                        for (var i = 0; i < data.length; i++){
                            mytab[mytab.length] = { data: data[i], value: paczkomatLabel+ data.name+' '+data[i].address.line1 + ': ' + data[i].address.line2+ ' '+data[i].location_description};
                        }
                        return mytab;
                    },
                }
            )
            .result(function(event, data, formatted) {
                if (data.status != '404') {
                    $(this).val(data.name+': '+data.address.line1+', '+data.address.line2);
                    $('#pminpostorder_selected').val(data.name);                    
                }
            }).getPaczkomatName();

            $('#PMINPOSTPACZKOMATY_MACHINE').hide().parent().append('<input type="text" placeholder="'+paczkomatPlaceholder+'" id="PMINPOSTPACZKOMATY_MACHINE_COPY">');
            $('#PMINPOSTPACZKOMATY_MACHINE_COPY').val($('#PMINPOSTPACZKOMATY_MACHINE').val());
            $('#PMINPOSTPACZKOMATY_MACHINE_COPY').paczkomaty(
                'https://maps.googleapis.com/maps/api/geocode/json',
                {
                    formatItem: function(data, i, max, value, term) {
                        return '<span class="paczkomat_name">'+paczkomatLabel+data.name+'</span> <span>'+data.address.line1+' '+data.address.line2+'</span>';
                    },       
                    parse: function(data) {
                        var mytab = [];
                        for (var i = 0; i < data.length; i++){
                            mytab[mytab.length] = { data: data[i], value: paczkomatLabel+ data.name+' '+data[i].address.line1 + ': ' + data[i].address.line2+ ' '+data[i].location_description};
                        }
                        return mytab;
                    },
                }
            )
            .result(function(event, data, formatted) {
                $(this).val(data.name+': '+data.address.line1);
                $('#PMINPOSTPACZKOMATY_MACHINE').val(data.name);
            }).getPaczkomatName();
        }

        if ($('#PMINPOSTPACZKOMATY_SHIPPING_METHOD').val() == 1) { //Redy hide machine list
            $('#PMINPOSTPACZKOMATY_MACHINE').parent().parent().show();
        } else {
            $('#PMINPOSTPACZKOMATY_MACHINE').parent().parent().hide();
        }

        if ($('#PMINPOSTPACZKOMATY_GGMAP').val() == 1) { //Redy hide machine list
            $('#PMINPOSTPACZKOMATY_GEO_on').parent().parent().parent().hide();
            $('#PMINPOSTPACZKOMATY_MAP_KEY').parent().parent().show();
            $('#PMINPOSTPACZKOMATY_MAP_KEY2').parent().parent().show();
        } else {
            $('#PMINPOSTPACZKOMATY_GEO_on').parent().parent().parent().show();
            $('#PMINPOSTPACZKOMATY_MAP_KEY').parent().parent().hide();
            $('#PMINPOSTPACZKOMATY_MAP_KEY2').parent().parent().hide();
        }

        if ($('#PMINPOSTPACZKOMATY_LABEL_FORMAT').val() == 'Pdf') {  //Ready hide format list
            $('#PMINPOSTPACZKOMATY_LABEL_SIZE').parent().parent().show();
        } else {
            $('#PMINPOSTPACZKOMATY_LABEL_SIZE').parent().parent().hide();
        }

        $('.module_tab.active').click();
    }

    $(document).on('click','.show-paczkomaty-form',function () {
        if (!$('.paczkomaty-form').is(':visible')) {
            $('.paczkomaty-form').slideDown();
        } else {
            $('.paczkomaty-form').slideUp();
        }
    });    

    $(document).on('click','.pminpostbutton-ajax',function () {
        if ($(this).is('.send')) {
            $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
            var ppmform = $('.paczkomaty-form').find('form').serialize();
            var urlpost = $(this).attr('href');
            $('.pminpostbutton-ajax').attr('disabled','disabled');
            $.ajax({
                url: urlpost,
                method: 'post',
                data: ppmform,
                dataType: 'json',
                success: function (d) {
                    if (d.error !== false) {
                        $('.alert-paczkomaty').addClass('alert-danger').html(d.error);
                        $('.alert-paczkomaty').slideDown();
                    } else {
                        $('.send').addClass('hidden');
                        $('.sendandprint').addClass('hidden');
                        $('.printlabel').removeClass('hidden');
                        disablePaczkomatyInputs();
                        $('.alert-paczkomaty').addClass('alert-success').html(d.confirmation);
                        $('.alert-paczkomaty').slideDown();
                        $('.createnew').removeClass('hidden');
                        $('.others').append('<a class="btn" href="'+d.link2+'"><i class="material-icons" aria-hidden="true">print</i> <i class="icon-print"></i></a> <a target="_blank" href="'+d.link+'">'+d.packcode+'</a><br/>');
                        $('.others').find('a:last').hide().fadeIn().fadeOut().fadeIn();
                    }

                    $('.pminpostbutton-ajax').removeAttr('disabled');
                }
            });
        } else if ($(this).is('.send2')) {
            $(this).attr('disabled','disabled');
            var t = this;
            var ppmform = $('.paczkomaty-form').find('form').serialize();
            var urlpost = $(this).attr('href');
            
            $.ajax({
                url: urlpost,
                method: 'post',
                data: ppmform,
                dataType: 'json',
                success: function (d) {
                    if (d.error !== false) {
                        $('.alert-paczkomaty').append('<div class="alert remove-if-exists alert-danger">'+d.error+'</div>');
                        setTimeout(function () {
                            $('.remove-if-exists').first().fadeOut('slow',function(){
                                $(this).remove();
                            });
                        },4000)
                        $('.alert-paczkomaty').slideDown();
                        $(t).removeAttr('disabled');
                    } else {
                        $('#send_order_a'+d.id_order).addClass('hidden');
                        $('#send_order_b'+d.id_order).addClass('hidden');
                        $('#send_order_c'+d.id_order).addClass('hidden');
                        $('#print_order'+d.id_order).removeClass('hidden');
                        $('#nr_listu'+d.id_order).html(d.packcode);                        
                        $(t).removeAttr('disabled');
                        setTimeout(function () {
                            $('.remove-if-exists').first().fadeOut('slow',function(){
                                $(this).remove();
                            });
                        },4000)
                    }
                    $('.pminpostbutton-ajax').removeAttr('disabled');
                }
            });
            return false;
        } else if ($(this).is('.printlabel2')) {
            $(this).attr('disabled','disabled');
            var t = this;
            $.ajax({
                url: $(t).attr('href')+'&check=1&format='+$('#pminpostorder_format').val()+'&size_l='+$('#pminpostorder_size_l').val(),
                dataType: 'json',
                success: function (d) {
                    if (d.error !== false) {
                        $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
                        $('.alert-paczkomaty').append('<div class="alert remove-if-exists alert-danger">'+d.error+'</div>');
                        setTimeout(function () {
                            $('.remove-if-exists').first().fadeOut('slow',function(){
                                $(this).remove();
                            });
                        },4000)
                        $('.alert-paczkomaty').slideDown();
                        $(t).removeAttr('disabled');
                    } else {
                        $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
                        $('.alert-paczkomaty').append('<div class="alert remove-if-exists alert-success">'+d.confirmation+'</div>').slideDown();
                        setTimeout(function () {
                            $('.remove-if-exists').first().fadeOut('slow',function(){
                                $(this).remove();
                            });
                        },4000)
                        window.location.href = $(t).attr('href');
                        $(t).removeAttr('disabled');
                    }
                }
            });
            return false;
        } else if ($(this).is('.sendandprint')) {
            $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
            var ppmform = $('.paczkomaty-form').find('form').serialize();
            var urlpost = $(this).attr('href');
            $('.pminpostbutton-ajax').attr('disabled','disabled');
            $.ajax({
                url: urlpost,
                method: 'post',
                data: ppmform,
                dataType: 'json',
                success: function (d) {
                    if (d.error !== false) {
                        $('.alert-paczkomaty').addClass('alert-danger').html(d.error);
                        $('.alert-paczkomaty').slideDown();
                        $('.pminpostbutton-ajax').removeAttr('disabled');
                    } else {
                        $('.send').addClass('hidden');
                        $('.payforpack').addClass('hidden');
                        $('.printlabel').removeClass('hidden');
                        $('.createnew').removeClass('hidden');
                        disablePaczkomatyInputs();
                        $('.sendandprint').addClass('hidden');
                        $('.alert-paczkomaty').addClass('alert-success').html(d.confirmation);
                        $('.alert-paczkomaty').slideDown();
                        $('.pminpostbutton-ajax').removeAttr('disabled');
                        $('.pminpostbutton-ajax').attr('disabled','disabled');
                        $('.createnew').removeClass('hidden');
                        $('.others').append('<a class="btn" href="'+d.link2+'"><i class="icon-print"></i></a> <a target="_blank" href="'+d.link+'">'+d.packcode+'</a><br/>');
                        $('.others').find('a:last').hide().fadeIn().fadeOut().fadeIn();
                        $.ajax({
                            url: $('.printlabel').attr('href')+'&check=1&format='+$('#pminpostorder_format').val()+'&size_l='+$('#pminpostorder_size_l').val(),
                            dataType: 'json',
                            success: function (d) {
                                if (d.error !== false) {
                                    $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
                                    $('.alert-paczkomaty').addClass('alert-danger').html(d.error);
                                    $('.alert-paczkomaty').slideDown();
                                } else {
                                    $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
                                    $('.alert-paczkomaty').addClass('alert-success').html(d.confirmation);
                                    $('.alert-paczkomaty').slideDown();
                                    window.location.href = $('.printlabel').attr('href')+'&format='+$('#pminpostorder_format').val()+'&size_l='+$('#pminpostorder_size_l').val();
                                }
                                $('.pminpostbutton-ajax').removeAttr('disabled');
                            }
                        });
                    }
                }
            });
        } else if ($(this).is('.printlabel')) {
            $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
            $.ajax({
                url: $('.printlabel').attr('href')+'&check=1&format='+$('#pminpostorder_format').val()+'&size_l='+$('#pminpostorder_size_l').val(),
                dataType: 'json',
                success: function (d) {
                    if (d.error !== false) {
                        $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
                        $('.alert-paczkomaty').addClass('alert-danger').html(d.error);
                        $('.alert-paczkomaty').slideDown();
                    } else {
                        $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
                        $('.alert-paczkomaty').addClass('alert-success').html(d.confirmation);
                        $('.alert-paczkomaty').slideDown();
                        window.location.href = $('.printlabel').attr('href')+'&format='+$('#pminpostorder_format').val()+'&size_l='+$('#pminpostorder_size_l').val();
                    }
                    $('.pminpostbutton-ajax').removeAttr('disabled');
                }
            });
        } else if ($(this).is('.createnew')) {
            $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
            $.ajax({
                url: $('.createnew').attr('href')+'&check=1',
                dataType: 'json',
                success: function (d) {
                    if (d.error !== false) {
                        $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
                        $('.alert-paczkomaty').addClass('alert-danger').html(d.error);
                        $('.alert-paczkomaty').slideDown();
                    } else {
                        $('.paczkomaty-form').find('input[type="text"],select,input[type="radio"]').removeAttr('disabled');
                        $('.alert-paczkomaty').hide().removeClass('alert-danger').removeClass('alert-success');
                        $('.alert-paczkomaty').addClass('alert-success').html(d.confirmation);
                        $('.alert-paczkomaty').slideDown();
                        $('.printlabel').addClass('hidden');
                        $('.sendandprint').removeClass('hidden');
                        $('.send').removeClass('hidden');
                        $('.createnew').addClass('hidden');
                    }
                    $('.pminpostbutton-ajax').removeAttr('disabled');
                }
            });
        }
        return false;
    });

    if (!$('.printlabel').is('.hidden')) {
        disablePaczkomatyInputs();
    }

    $(document).on('change','input[name="pminpostorder_pobranie"]', function(){
        if ($('input[name="pminpostorder_pobranie"]:checked').val() == 0) {
            $('#pminpostorder_pobranie_value').attr('disabled','disabled');
        } else {
            $('#pminpostorder_pobranie_value').removeAttr('disabled');
        }
    });

    $(document).on('change','input[name="pminpostorder_ubezpieczenie"]', function(){
        if ($('input[name="pminpostorder_ubezpieczenie"]:checked').val() == 0) {
            $('#pminpostorder_ubezpieczenie_value').attr('disabled','disabled');
        } else {
            $('#pminpostorder_ubezpieczenie_value').removeAttr('disabled');
        }
    });
    
    $(document).on('change','#pminpostorder_pobranie_value', function(){
        $(this).val($(this).val().replace(',','.'));
    });

    $(document).on('change','#pminpostorder_ubezpieczenie_value', function(){
        $(this).val($(this).val().replace(',','.'));
    });

    $(document).on('click', '.refresh_status', function(){
        var t = this;
        var urlpost = $(this).attr('href');
        var last = $(this).html();
        $(this).html('<img src="../modules/pminpostpaczkomaty/views/img/throbber.gif">');
        $.ajax({            
            url: urlpost,
            dataType: 'JSON',
            success: function(s) {
                if (s.error) {
                    $.confirm({
                        title: '',
                        content: s.error,
                        buttons: {
                            cancel: {
                                text: 'OK'
                            }
                        }
                    });
                    $(t).html(last);
                } else {
                    $(t).html(s.status);
                }
            }
        });
        return false;
    });

//Config
    $(document).on('click','.module_tab', function(e) {
        e.preventDefault();
        $('#configContainer').find($(this).data('target')).show();
        $('#selected_menu').val(($(this).index()));
        $.each($(this).siblings(), function(){
            $('#configContainer').find($(this).data('target')).hide();
        });
        $(this).addClass('active').siblings().removeClass('active');
        return false;
    });    

    $(document).on('click', '.expand_raben', function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        if($('.'+target).toggle().is(':visible')) {
            $(this).find('i').addClass('icon-angle-double-up').removeClass('icon-angle-double-down').html(' '+label_unexpand);
        } else {
            $(this).find('i').addClass('icon-angle-double-down').removeClass('icon-angle-double-up').html(' '+label_expand);
        }
        return false;
    });

    $(document).on('change','#PMINPOSTPACZKOMATY_SHIPPING_METHOD',function(){ // Hide machine list
        if ($(this).val() == 1) {
            $('#PMINPOSTPACZKOMATY_MACHINE').parent().parent().show();
        } else {
            $('#PMINPOSTPACZKOMATY_MACHINE').parent().parent().hide();
        }
    });

    $(document).on('change','#PMINPOSTPACZKOMATY_GGMAP',function(){ // Hide machine list
        if ($(this).val() == 1) {
            $('#PMINPOSTPACZKOMATY_GEO_on').parent().parent().parent().hide();
            $('#PMINPOSTPACZKOMATY_MAP_KEY').parent().parent().show();
            $('#PMINPOSTPACZKOMATY_MAP_KEY2').parent().parent().show();
        } else {
            $('#PMINPOSTPACZKOMATY_GEO_on').parent().parent().parent().show();
            $('#PMINPOSTPACZKOMATY_MAP_KEY').parent().parent().hide();
            $('#PMINPOSTPACZKOMATY_MAP_KEY2').parent().parent().hide();
        }
    });

    $(document).on('change','#PMINPOSTPACZKOMATY_LABEL_FORMAT',function(){ // Hide format list
        if ($(this).val() == 'Pdf') {
            $('#PMINPOSTPACZKOMATY_LABEL_SIZE').parent().parent().show();
        } else {
            $('#PMINPOSTPACZKOMATY_LABEL_SIZE').parent().parent().hide();
        }
    });

    $(document).on('click', 'button[name="CREATE_DELIVERY_METHOD"]', function(){
        if ($(this).is('.submit')){
            $(this).removeClass('submit');
            return true;
        } else if ($(this).is('.nosubmit')) {
            $(this).removeClass('nosubmit');
            return false;
        } else {            
            var t = this;
            $.confirm({
                title: '',
                content: pm_inpostpaczkomaty_create_text,
                buttons: {
                    custom: {
                        text: pm_inpostpaczkomaty_create,
                        btnClass: 'btn-blue',
                        action: function() {
                            $(t).addClass('submit');
                            $(t).click();
                        }
                    },
                    cancel: {
                        text: pm_inpostpaczkomaty_cancel,
                        action: function() {
                            $(t).addClass('nosubmit');
                            $(t).click();
                        }
                    }
                }
            });
        }
        return false;
    })

    $(document).on('click','#PMINPOSTPACZKOMATY_SHIPX_on',function(){ // show shipx
            $('#PMINPOSTPACZKOMATY_PASSWORD').parent().parent().hide();
            $('#PMINPOSTPACZKOMATY_LOGIN').parent().parent().hide();
            $('#PMINPOSTPACZKOMATY_ID').parent().parent().show();
            $('#PMINPOSTPACZKOMATY_TOKEN').parent().parent().show();
    });

    $(document).on('click','#PMINPOSTPACZKOMATY_SHIPX_off',function(){ // hide shipx
            $('#PMINPOSTPACZKOMATY_PASSWORD').parent().parent().show();
            $('#PMINPOSTPACZKOMATY_LOGIN').parent().parent().show();
            $('#PMINPOSTPACZKOMATY_ID').parent().parent().hide();
            $('#PMINPOSTPACZKOMATY_TOKEN').parent().parent().hide();
    });    
   

    $(document).on('click','.refresh_all_packages', function(){
        var change_state = $(this).index()-1;
        $.each($('.refresh_status'),function(){
            var t = this;
            var urlpost = $(this).attr('href')+'&change_order_state='+change_state.toString();
            var last = $(this).html();
            $(this).html('<img src="../modules/pminpostpaczkomaty/views/img/throbber.gif">');
            $.ajax({            
                url: urlpost,
                dataType: 'JSON',
                success: function(s) {
                    if (s.error) {                        
                        $(t).html(last);
                    } else {
                        $(t).html(s.status);
                    }
                }
            });
        });
    });

    $(document).on('click','.btnpaczkomaty-size', function(){
        if ($(this).find('input').is(':disabled')) {
            return true
        } else {
            $('.btnpaczkomaty-size').find('input').attr('checked', false);
            $('.btnpaczkomaty-size').removeClass('active');
            $(this).addClass('active');
            $(this).find('input[type="radio"]').prop("checked", true); 
        }

        return true;
    });   

    $(document).on('change', '#pminpostorder_format', function(){
        if ($(this).val() != 'Pdf') {
            $('#pminpostorder_size_l').attr('disabled', 'disabled');
        } else {
            $('#pminpostorder_size_l').removeAttr('disabled');
        }
    });

    if ($('#pminpostorder_format').length) {
        $('#pminpostorder_format').change();
    }

    $(document).on('click', '.print-package-label', function(){
        window.location.href = $(this).attr('href')+'&format='+$('#pminpostorder_format').val()+'&size_l='+$('#pminpostorder_size_l').val();
        return false;
    })

    assignEvents();

    if (typeof(_PS_VERSION_) != 'undefined' && (_PS_VERSION_ == '1.7.8.1' || parseInt(_PS_VERSION_.replaceAll('.','')) >= 1780) ) {
        $('.paczkomaty-form').find('.col-lg-4,.col-lg-8').css('display','inline-block');
        $('.paczkomaty-form').find('.col-lg-4').css('text-align', 'right').css('font-weight', 'bold');
        $('.paczkomaty-form').find('.col-lg-8').css('float','right');
        $('.paczkomaty-form').find('.col-lg-8').find('.switch label').css('left','0px').css('position', 'absolute');
        $('.paczkomaty-form').find('.col-lg-8').find('.switch input, .switch a').css('left','0px').css('position', 'absolute');;
    }
});