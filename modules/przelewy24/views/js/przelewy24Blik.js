/*
* @author Przelewy24
* @copyright Przelewy24
* @license https://www.gnu.org/licenses/lgpl-3.0.en.html
*/

$(function(){
    var $paymentBox;
    var $configElement;
    var armed = false;

    var blikError = function() {
        var $modal = $('#p24-blik-modal');
        $modal.removeClass('loading');
        $modal.find('.error').show();
    };

    var executePaymentByBlikCode = function(currencySuffix, token, blikCode) {
        var request = {
            'action': 'executePaymentByBlikCode',
            'currencySuffix': currencySuffix,
            'token': token,
            'blikCode': blikCode
        };
        $.ajax($configElement.data('ajaxurl'), {
            method: 'POST', type: 'POST',
            data: request,
        }).success(function (response) {
            var response = JSON.parse(response);

            if (response.success) {
                var returnUrl = $configElement.data('returnurl');
                /* We are giving few seconds for user to accept transaction. */
                setTimeout(function() {window.location = returnUrl;}, 3000);
            } else {
                blikError();
            }
        }).error(blikError);
    };

    var trnRegister = function(cartId) {
        var request = {
            'action': 'trnRegister',
            'cartId': cartId
        };
        /* The xhr from jQuery do not have responseURL. */
        var redirectHackXhr = new XMLHttpRequest();
        $.ajax($configElement.data('ajaxurl'), {
            method: 'POST', type: 'POST',
            data: request,
            xhr: function() {return redirectHackXhr;}
        }).success(function (response) {
            try {
                response = JSON.parse(response);
            } catch (e) {
                /* Assume 302 or something like that. */
                location = redirectHackXhr.responseURL;
                return;
            }
            if (response.success) {
                var $modal = $('#p24-blik-modal');
                $modal.removeClass('loading');
                var $button = $modal.find('button');
                $button.on('click', function (e) {
                    e.preventDefault();
                    var $input = $modal.find('input');
                    var code = $input.val();
                    if (/^\d{6}$/.test(code)) {
                        $modal.addClass('loading');
                        executePaymentByBlikCode(response.currencySuffix, response.token, code);
                    } else {
                        $modal.find('.error').show();
                    }
                });
            }
        });
    };

    var tryArmBlikBoxPaymentGraphical = function($configElement) {
        var $paymentBox = $('a.bank-box[data-id=181]');
        if ($paymentBox.length) {
            var clickFunction = function (e) {
                if (!$paymentBox.hasClass('selected')) {
                    /* Nothing to do. */
                    return;
                }
                var $regulation = $('#p24_regulation_accept');
                if ($regulation.length) {
                    var regulationAccepted = $regulation.prop('checked');
                    if (!regulationAccepted) {
                        /* Nothing to do. */
                        return;
                    }
                }
                e.preventDefault();
                var $modal = $('#p24-blik-modal');
                $modal.addClass('loading');
                $('#p24-blik-modal-background').show();
                var cartId = $configElement.data('cartid');
                trnRegister(cartId);
            }
            $paymentBox.on('click', function (e) {
                /* There may be an event we want to execute first. */
                setTimeout(clickFunction, 100, e);
            });

            armed = true;
        }
    }

    var tryArmBlikBoxPaymentTextual = function($configElement) {
        var $paymentInput = $('input#paymethod-bank-id-181');
        if ($paymentInput.length) {
            $paymentInput.on('change', function (e) {
                var $regulation = $('#p24_regulation_accept');
                if ($regulation.length) {
                    var regulationAccepted = $regulation.prop('checked');
                    if (!regulationAccepted) {
                        /* Nothing to do. */
                        return;
                    }
                }
                if ($paymentInput.prop('checked')) {
                    var $modal = $('#p24-blik-modal');
                    $modal.addClass('loading');
                    $('#p24-blik-modal-background').show();
                    var cartId = $configElement.data('cartid');
                    trnRegister(cartId);
                }
            });

            armed = true;
        }
    };

    var tryArmBlikBoxConfirmation = function($configElement) {
        /* The id is too random to use. */
        var $input = $('input[data-module-name=przelewy24-method-181]');
        if ($input.length) {
            var $regulation = $('#conditions_to_approve\\[terms-and-conditions\\]');
            $input.on('change', function (e) {
                if (!$input.prop('checked')) {
                    /* Nothing to do. */
                    return;
                }
                if ($regulation.length) {
                    var regulationAccepted = $regulation.prop('checked');
                    if (!regulationAccepted) {
                        /* Nothing to do. */
                        return;
                    }
                }
                var $modal = $('#p24-blik-modal');
                $modal.addClass('loading');
                $('#p24-blik-modal-background').show();
                var cartId = $configElement.data('cartid');
                trnRegister(cartId);
            });
            if ($regulation.length) {
                $regulation.on('change', function (e) {
                    $input.trigger('change');
                });
            }

            armed = true;
        }
    };

    var tryArmBlikBox = function(retries) {
        if (armed || retries <= 0) {
            return;
        }

        $configElement = $('#p24-blik-config-element');
        if ($configElement.length) {
            var pageType = $configElement.data('pagetype');
            console.log(pageType);
            switch (pageType) {
                case 'payment':
                    /* We have to check both methods. */
                    tryArmBlikBoxPaymentGraphical($configElement);
                    tryArmBlikBoxPaymentTextual($configElement);
                    break;
                case 'confirmation':
                    tryArmBlikBoxConfirmation($configElement);
                    break;
            }
        }

        if (!armed) {
            setTimeout(tryArmBlikBox, 1000, retries - 1);
        }
    };

    tryArmBlikBox(10);
});
