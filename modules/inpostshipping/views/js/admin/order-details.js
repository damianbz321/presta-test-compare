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
$(document).ready(function () {
  $('#inpost-create-shipment-modal').on('shown.bs.modal', function() {
    $(document).off('focusin.modal');
  });

  $(document).on('click', '.js-submit-shipment-form', function (e) {
    e.preventDefault();

    const button = $(this);
    const form = $('#inpost-shipment-form');
    const formData = new FormData(form.get(0));
    formData.append('ajax', '1');

    inPostShippingXhr({
      type: 'POST',
      url: form.attr('action'),
      data: formData,
      callbackJson: function (response) {
        if (response.status === true) {
          window.location.reload();
        } else {
          const errorsWrapper = $('#inpost-shipment-form-errors');
          const errors = response.errors.join('</li><li>');

          errorsWrapper.html(`<article class="alert alert-danger"><ul><li>${errors}</li></ul></article>`);
          scrollElementIntoView(errorsWrapper);

          if ('shipmentId' in response) {
            form.find('.form-wrapper').remove();
            button.remove();
            $('#inpost-create-shipment-modal').on('hidden.bs.modal', function () {
              window.location.reload();
            });
          }
        }
      },
    });
  });

  $(document).on('click', '.js-submit-dispatch-order-form', function (e) {
    e.preventDefault();

    submitDispatchOrderForm();
  });

  $(document).on('click', '.js-printDispatchOrder', function(e) {
    e.preventDefault();

    inPostShippingXhr({
      url: $(this).attr('href'),
      callbackBlob: blobFileDownload,
      callbackJson: function (response) {
        if ('errors' in response) {
          showErrorMessage(response.errors.join('<br/>'));
        }
      },
    });
  });

  $(document).on('click', '.js-submit-print-label-form', function (e) {
    e.preventDefault();

    submitPrintLabelForm(function (response) {
      if ('errors' in response) {
        showErrorMessage(response.errors.join('<br/>'));
      }
    });
  });

  let id_shipment = null;
  $(document).on('click', '.js-view-inpost-shipment-details', function (e) {
    e.preventDefault();

    const dataId = $(this).data('id-shipment');

    if (id_shipment !== dataId) {
      $.ajax({
        url: $(this).attr('href'),
        dataType: 'json',
        success: function (response) {
          if (response.status === true) {
            id_shipment = dataId;
            const contentWrapper = $('#inpost-shipment-details-content-wrapper');
            contentWrapper.html(response.content);
            if (shopIs177) {
              contentWrapper.find('[data-toggle="pstooltip"]').pstooltip();
            }
            $('#inpost-shipment-details-modal').modal('show');
          } else {
            showErrorMessage(response.errors.join('<br/>'));
          }
        },
      });
    } else {
     $('#inpost-shipment-details-modal').modal('show');
    }
  });

  $(document).on('click', '.js-inpost-new-dispatch-order', function (e) {
    e.preventDefault();

    const url = new URL($(this).attr('href'));
    url.searchParams.set('back', window.location.href);

    window.location.href = url.toString();
  });

  $(document).on('click', '.js-inpost-clear-point', function (e) {
    e.preventDefault();

    const input = $($(this).data('target'));
    input.data('point', '');
    input.val('');
  });

  $(document).on('change', '#service', changeShippingService);
  $(document).on('change', '#sending_method', changeSendingMethod);
  $(document).on('change', 'input[name="use_template"]', toggleTemplate);
  $(document).on('change', 'input[name="cod"]', toggleCashOnDelivery);
  $(document).on('change', 'input[name="insurance"]', toggleInsurance);

  changeShippingService();
  toggleCashOnDelivery();

  const map = new ModalMap();

  $(document).on('click', '.js-inpost-show-map-input', function (e) {
    e.preventDefault();

    const input = $($(this).data('target-input'));

    let payment, weekendDelivery = false;
    if (input.data('payment')) {
      payment = $('#cod_on').is(':checked');
      weekendDelivery = $('#weekend_delivery_on').is(':checked');
    }

    map.openMap(input, payment, weekendDelivery, function (point) {
      input.data('point', point.name);
      input.val(point.name);
    });
  });

  $(document).on('click', '.js-inpost-show-map', function () {
    map.openMap($(this));
  });
});

function toggleTemplate() {
  if ($('input[name="use_template"]:checked').val() === '1') {
    $('#template').closest('.form-group').show();
    $('#js-inpost-package-dimensions').hide();
  } else {
    $('#template').closest('.form-group').hide();
    $('#js-inpost-package-dimensions').show();
  }
}

function toggleCashOnDelivery() {
  if ($('input[name="cod"]:checked').val() === '1') {
    $('input[name="cod_amount"]').closest('.form-group').show();
  } else {
    $('input[name="cod_amount"]').closest('.form-group').hide();
  }

  updateInsuranceDisplay();
}

function toggleInsurance() {
  if ($('input[name="insurance"]:checked').val() === '1') {
    $('input[name="insurance_amount"]').closest('.form-group').show();
  } else {
    $('input[name="insurance_amount"]').closest('.form-group').hide();
  }
}

function updateInsuranceDisplay() {
  if ($('#service').val() !== inpostLockerStandard && $('#cod_on').is(':checked')) {
    $('#insurance_on').prop('checked', true).trigger('change');
    $('input[name="insurance"]').closest('.form-group').hide();
    $('label[for="insurance_amount"]').addClass('required');
  } else {
    $('input[name="insurance"]').closest('.form-group').show();
    $('label[for="insurance_amount"]').removeClass('required');
  }
}

function changeShippingService() {
  const service = $('#service').val();

  if (service === inpostLockerStandard) {
    $('#inpost-locker-content-wrapper').show();
  } else {
    $('#inpost-locker-content-wrapper').hide();
  }

  if (inpostLockerServices.indexOf(service) !== -1) {
    $('#js-inpost-dimension-template-content-wrapper').show();
  } else {
    $('#js-inpost-dimension-template-content-wrapper').hide();
    $('#template_off').prop('checked', true);
    toggleTemplate();
  }

  updateSendingMethodOptions();
  updateInsuranceDisplay();
}

function updateSendingMethodOptions() {
  const selectedService = $('#service option:selected');
  const availableSendingMethods = selectedService.data('sending-methods');

  $('#sending_method option').each(function () {
    const disable = availableSendingMethods.indexOf($(this).val()) === -1;
    $(this)
      .prop('disabled', disable)
      .prop('hidden', disable);
  });

  if ($('#sending_method option:selected').is(':disabled')) {
    const defaultSendingMethod = selectedService.data('default-sending-method');
    if (defaultSendingMethod) {
      $(`#sending_method option[value="${defaultSendingMethod}"]`).prop('selected', true);
    } else {
      $('#sending_method option:not(:disabled):first').prop('selected', true);
    }
  }

  changeSendingMethod();
}

function updateTemplateOptions() {
  const unavailableTemplates = $('#sending_method option:selected').data('unavailable-templates') || [];
  const selectedService = $('#service option:selected');
  let availableTemplates = selectedService.data('templates') || [];
  if (unavailableTemplates.length) {
    availableTemplates = availableTemplates.filter(function (template) {
      return unavailableTemplates.indexOf(template) === -1
    });
  }

  $('#template option').each(function () {
    const disable = availableTemplates.indexOf($(this).val()) === -1;
    $(this)
      .prop('disabled', disable)
      .prop('hidden', disable);
  });

  if ($('#template option:selected').is(':disabled')) {
    const defaultTemplate = selectedService.data('default-template');
    if (defaultTemplate && unavailableTemplates.indexOf(defaultTemplate) === -1) {
      $(`#template option[value="${defaultTemplate}"]`).prop('selected', true);
    } else {
      $('#template option:not(:disabled):first').prop('selected', true);
    }
  }
}

function changeSendingMethod() {
  const popGroup = $('#dropoff_pop').closest('.form-group');
  const lockerGroup = $('#dropoff_locker').closest('.form-group');
  const sendingMethod = $('#sending_method').val();

  popGroup.hide();
  lockerGroup.hide();

  if (sendingMethod === 'parcel_locker') {
    lockerGroup.show();
  } else if (
    sendingMethod === 'pop' &&
    inpostLockerServices.indexOf($('#service').val()) !== -1
  ) {
    popGroup.show();
  }

  updateTemplateOptions();
}

class ModalMap {
  constructor() {
    this.currentMap = '';
    this.payment = false;
    this.weekendDelivery = false;
  }

  openMap(selector, payment = false, weekendDelivery = false, callback = null) {
    let pointName = selector.data('point');
    const id = selector.attr('id');

    if (this.currentMap !== id || this.payment !== payment || this.weekendDelivery !== weekendDelivery) {
      this.currentMap = id;
      this.payment = payment;
      this.weekendDelivery = weekendDelivery;

      const widget = $('#widget-modal');
      if (widget.length) {
        widget.remove();
      }

      const type = weekendDelivery ? 'parcel_locker_only' : selector.data('type');
      const config = {
        map: {
          initialTypes: [type],
        },
        points: {
          types: [type],
          functions: ['parcel', selector.data('function')],
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

    const modal = easyPack.modalMap(function(point, modal) {
      modal.closeModal();
      if (typeof callback === 'function') {
        callback(point);
      }
    }, {});

    $('#widget-modal').css('max-height', '80%');

    if (pointName) {
      modal.searchLockerPoint(pointName);
    }
  }
}
