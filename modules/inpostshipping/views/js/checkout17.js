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
    const inpostCustomerEmailSelector = '.js-inpost-shipping-email';
    const inpostCustomerPhoneSelector = '.js-inpost-shipping-phone';
    const inpostCustomerInfoEmail = $('.js-inpost-shipping-customer-info-email');
    const inpostCustomerInfoPhone = $('.js-inpost-shipping-customer-info-phone');
    const map = new ModalMap();

    $(document).on('click', inpostChooseMachineButtonSelector, function (e) {
        e.preventDefault();

        const $that = $(this);
        const $paczkomatInput = $that.parents('.carrier-extra-content').find('.js-inpost-shipping-input');
        const payment = parseInt($that.attr('data-inpost-shipping-payment'));
        const weekendDelivery = parseInt($that.attr('data-inpost-shipping-weekend-delivery'));

        map.openMap($paczkomatInput, payment, weekendDelivery);
    });

    $(document).on('click', inpostCustomerChangeButtonSelector, function () {
        const $that = $(this);
        const $inpostCustomerChangeForm = $that.parents('.carrier-extra-content').find('.inpost-shipping-customer-change-form');

        $inpostCustomerChangeForm.slideToggle(300);
    })

    $(document).on('click', inpostCustomerSaveButtonSelector, function () {
        const $that = $(this);
        const $inpostCustomerChangeForm = $that.parents('.carrier-extra-content').find('.inpost-shipping-customer-change-form');

        $inpostCustomerChangeForm.slideUp(300);
    });

    $(document).on('input', inpostCustomerEmailSelector, function () {
        let val = $(this).val();

        $(inpostCustomerEmailSelector).val(val);
        inpostCustomerInfoEmail.html(val);
    });

    $(document).on('input', inpostCustomerPhoneSelector, function () {
        let val = $(this).val();

        $(inpostCustomerPhoneSelector).val($(this).val());
        inpostCustomerInfoPhone.html(val);
    })
});

class ModalMap {
    constructor() {
        this.payment = false;
        this.weekendDelivery = false;
    }

    openMap(selector, payment, weekendDelivery) {
        let pointName = selector.val();
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
            const $choosedMethod = selector.parents('.carrier-extra-content');
            const $machineInfo = $choosedMethod.find('.js-inpost-shipping-machine-info');
            const $customerInfo = $choosedMethod.find('.js-inpost-shipping-machine-customer-info');
            const $machineName = $choosedMethod.find('.js-inpost-shipping-machine-name');
            const $machineAddress = $choosedMethod.find('.js-inpost-shipping-machine-address');
            const $inpostChooseMachineButton = $choosedMethod.find('.js-inpost-shipping-choose-machine');
            const inpostChooseMachineButtonSelectorText = $inpostChooseMachineButton.attr('data-inpost-shipping-existing-text');

            $machineName.html(point.name);
            $machineAddress.html(`${point.address.line1}, ${point.address.line2}`);
            $machineInfo.removeClass('hidden');
            $customerInfo.removeClass('hidden');
            $inpostChooseMachineButton.html(inpostChooseMachineButtonSelectorText);

            selector.val(point.name);
            modal.closeModal();
        }, {});

        $(widgetSelector).parent('div').addClass('inpost-shipping-backdrop');

        if (pointName) {
            modal.searchLockerPoint(pointName);
        }
    }
}
