/**
 * Copyright 2021-2021 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright 2021-2021 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 */
$(function () {
    const inpostChooseMachineButtonSelector = '.js-inpost-shipping-choose-machine';
    const inpostCustomerChangeButtonSelector = '.js-inpost-shipping-customer-change';
    const inpostCustomerSaveButtonSelector = '.js-inpost-shipping-customer-form-save-button';
    const inpostInputSelector = '.js-inpost-shipping-input';
    const $carrierAreaFormSubmitButton = 'button[name=processCarrier]';
    const map = new ModalMap();

    $(document).on('click', inpostChooseMachineButtonSelector, function (e) {
        e.preventDefault();

        const $that = $(this);
        const payment = parseInt($that.attr('data-inpost-shipping-payment'));
        const weekendDelivery = parseInt($that.attr('data-inpost-shipping-weekend-delivery'));

        map.openMap(inpostInputSelector, payment, weekendDelivery);
    });

    $(document).on('click', inpostCustomerChangeButtonSelector, function () {
        const $that = $(this);
        const $inpostCustomerChangeForm = $('.inpost-shipping-customer-change-form');

        $inpostCustomerChangeForm.slideToggle(300);
    });

    $(document).on('click', inpostCustomerSaveButtonSelector, function () {
        const $inpostErrorsWrapper = $('.js-inpost-shipping-errors');
        const $inpostCustomerEmailInfo = $('.js-inpost-shipping-customer-info-email');
        const $inpostCustomerPhoneInfo = $('.js-inpost-shipping-customer-info-phone');
        const $inpostCustomerEmail = $('.js-inpost-shipping-email').val();
        const $inpostCustomerPhone = $('.js-inpost-shipping-phone').val();
        const $inpostCustomerChangeForm = $('.inpost-shipping-customer-change-form');
        const $inpostCustomerInputs = $inpostCustomerChangeForm.find('input.form-control');
        const formData = new FormData();

        $.each($inpostCustomerInputs, function (index, element) {
            formData.append(element.name, element.value);
        });
        formData.append('action', 'updateReceiverDetails');

        $.ajax({
            method: 'post',
            url: inPostAjaxController,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $inpostErrorsWrapper.html('');
                    $inpostCustomerEmailInfo.html($inpostCustomerEmail);
                    $inpostCustomerPhoneInfo.html($inpostCustomerPhone);
                    $inpostCustomerChangeForm.slideUp(300);
                } else {
                    const errors = getInPostErrors(response, ['phone', 'email']);

                    $inpostErrorsWrapper.html(`<article class="alert alert-danger"><ul>${errors}</ul></article>`);
                }
            },
        });
    });

    $(document).on('change', inpostInputSelector, function () {
        const $that = $(this);
        const $inpostErrorsWrapper = $('.js-inpost-shipping-errors');
        const formData = new FormData();
        formData.append($that.attr('name'), $that.val());
        formData.append('action', 'updateTargetLocker');

        $.ajax({
            method: 'post',
            url: inPostAjaxController,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $inpostErrorsWrapper.html('');
                } else {
                    const errors = getInPostErrors(response);
                    $inpostErrorsWrapper.html(`<article class="alert alert-danger"><ul>${errors}</ul></article>`);
                }
            },
        });
    });

    $(document).on('click', $carrierAreaFormSubmitButton, function () {
        const $inpostInput = $(inpostInputSelector);

        if ($inpostInput.length && !$inpostInput.val().length) {
            const errorText = $inpostInput.attr('data-error-text')

            if (!!$.prototype.fancybox)
                $.fancybox.open([
                        {
                            type: 'inline',
                            autoScale: true,
                            minHeight: 30,
                            content: `<p class="fancybox-error">${errorText}</p>`
                        }],
                    {
                        padding: 0
                    });
            else {
                alert(errorText);
            }
        } else {
            return true;
        }
        return false;
    });

    const $paymentLinkSelector = '#opc_payment_methods-content a';
    $(document).on('click', $paymentLinkSelector, function (e) {
        const $inpostCustomerChangeForm = $('.js-inpost-shipping-container');
        if ($inpostCustomerChangeForm.length) {
            e.preventDefault();

            const $that = $(this);
            const $inpostCustomerInputs = $inpostCustomerChangeForm.find('input.form-control');
            const formData = new FormData();

            $.each($inpostCustomerInputs, function (index, element) {
                formData.append(element.name, element.value);
            });
            formData.append('action', 'updateChoice');

            $.ajax({
                method: 'post',
                url: inPostAjaxController,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        window.location.href = $that.attr('href');
                    } else {
                        const errors = getInPostErrors(response);

                        if (!!$.prototype.fancybox)
                            $.fancybox.open([
                                    {
                                        type: 'inline',
                                        autoScale: true,
                                        minHeight: 30,
                                        minWidth: 280,
                                        content: `<article class="alert alert-danger" style="margin-bottom: 0" "><ul>${errors}</ul></article>`
                                    }],
                                {
                                    padding: 0
                                });
                        else {
                            alert(errors);
                        }
                    }
                },
            });
        }
    });

    function getInPostErrors(response, types = ['phone', 'email', 'locker']) {
        let errors = '';
        $(types).each(function (idx, value) {
            if (value in response.errors) {
                errors += `<li>${response.errors[value]}</li>`;
            }
        });

        return errors;
    }
});

class ModalMap {
    constructor() {
        this.payment = false;
        this.weekendDelivery = false;
    }

    openMap(selector, payment, weekendDelivery) {
        let pointName = $(selector).val();
        const widgetSelector = '.widget-modal';

        if (this.payment !== payment || this.weekendDelivery !== weekendDelivery) {
            this.payment = payment;
            this.weekendDelivery = weekendDelivery;

            const widget = $('#widget-modal');
            if (widget.length) {
                widget.remove();
            }

            const type = weekendDelivery ? 'parcel_locker_only' : 'parcel_locker';
            const config = {
                map: {
                    initialTypes: [type],
                },
                points: {
                    types: [type],
                    functions: ['parcel', 'parcel_collect'],
                },
                display: {
                    showTypesFilters: false,
                    showSearchBar: true,
                },
            };

            if (payment) {
                config.paymentFilter = {
                    showOnlyWithPayment: true,
                };
            }

            easyPack.init(config);
        }

        const modal = easyPack.modalMap(function (point, modal) {
            const $machineInfo = $('.js-inpost-shipping-machine-info');
            const $customerInfo = $('.js-inpost-shipping-machine-customer-info');
            const $machineName = $('.js-inpost-shipping-machine-name');
            const $machineAddress = $('.js-inpost-shipping-machine-address');
            const $inpostChooseMachineButton = $('.js-inpost-shipping-choose-machine');
            const inpostChooseMachineButtonSelectorText = $inpostChooseMachineButton.attr('data-inpost-shipping-existing-text');

            $machineName.html(point.name);
            $machineAddress.html(`${point.address.line1}, ${point.address.line2}`);
            $machineInfo.removeClass('hidden');
            $customerInfo.removeClass('hidden');
            $inpostChooseMachineButton.html(inpostChooseMachineButtonSelectorText);

            $(selector).val(point.name).trigger('change');
            modal.closeModal();
        }, {});

        $(widgetSelector).parent('div').addClass('inpost-shipping-backdrop');

        if (pointName) {
            modal.searchLockerPoint(pointName);
        }
    }
}
