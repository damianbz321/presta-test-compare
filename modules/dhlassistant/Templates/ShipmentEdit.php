<?php if(!isset($is_template)) die(); ?>
<?php
	use DhlAssistant\Core;
	use DhlAssistant\Wrappers;
	use DhlAssistant\Classes\Controllers;
	
	Core\Storage::Add('Js', Wrappers\ConfigWrapper::Get('BaseUrl').'Media/Js/ShipmentEdit.js', true, true);
	Core\Storage::Add('JsInline', Core\Template::Render('ShipmentEdit_JsData'), true, false);
	/* @var $form DhlAssistant\Classes\Forms\Shipment */
	$form = $aVars['Form'];
	$shipment = $aVars['Shipment'];

    $enums = new DhlAssistant\Classes\Dhl\Enums\SettingsUserData();

?>
<?php
	echo Core\Template::Render('Elements/FormErrorsAndNotices', array('Form'=>$form));
?> 
<?php
$link_params = array();
if (isset($_GET['source_id']))
	$link_params['source_id']=$_GET['source_id'];
if (isset($_GET['id']))
	$link_params['id']=$_GET['id'];
?>
<form action="<?php echo Core\Storage::Get('RulingController')->GetLink($link_params); ?>" method="post" class="form-horizontal">
<div id="fieldset_0" class="panel">
<div class="panel-heading">
	<i class="icon-user"></i>	
			<?php echo ($aVars['ViewMode'] ? 'Podgląd' : 'Edycja');?> <?php echo $enums->Headings('ShipmentValue'); ?>
</div>
<div class="form-wrapper">
<?php
	foreach ($form->Fields as $field_name => $field)
	{
		echo Core\Template::Render('FormFields/'.$field->Type, array('field_name' => $field_name, 'field' => $field))."\n";	
	}
?>
</div>
</div>
</form>
<script type="text/javascript">
	shipment_view_mode = <?php echo ((int)$aVars['ViewMode']); ?>;
	<?php 
		if ($aVars['ViewMode'])
		{
			$get_label_controller = new Controllers\GetLabel();
			$url_labels = array();
			foreach ($shipment->GetAvailableLabelTypes() as $label_code)
				$url_labels[$label_code] = $get_label_controller->GetLink(array('id'=>$shipment->Id, 'type'=>$label_code));
	?>
		url_labels = <?php echo json_encode($url_labels); ?>;
		url_cancel_shipment = '<?php echo (new Controllers\ShipmentEdit())->GetLink(array('action'=>'cancel','id'=>$shipment->Id)); ?>';

	<?php 
		} else {
    ?>
    SetDropOffTime = function (pickupDate, pickupFrom, pickupTo)
    {
        $('#field_NearestAvailableShipmentDate').val(pickupDate);
        $('#field_ShipmentDate').val(pickupDate);
        $('#field_ShipmentStartHour').val(pickupFrom);
        $('#field_ShipmentEndHour').val(pickupTo);
    }

    DropOffTypeUpdate = function()
    {
        if ($('#field_DropOffType').val() == '<?= \DhlAssistant\Classes\Dhl\Enums\DropOffType::REGULAR_PICKUP ?>') {
            return;
        }

        var shippingPaymentType = $('#field_ShippingPaymentType').val();
        var selectedShipmentDate = $('#field_ShipmentDate').val();
        var requestData = {
            ajax: true,
            method: 'nearestExPickup',
            date: selectedShipmentDate
        }

        if (shippingPaymentType == '<?= \DhlAssistant\Classes\Dhl\Enums\ShippingPaymentType::USER ?>') {
            var userPostCode = $('#field_SenderPostalCode').val();
            if (userPostCode == '') {
                return;
            }
            requestData.postCode = userPostCode;
        }
        $.ajax({
            type: 'POST',
            cache: true,
            dataType: 'json',
            url: '<?php echo $aVars['PostalCodeServices'] ?>',
            data: requestData,
            success: function (data) {
                if (data.success) {
                    var pickupDate = data.service.PickupDate.date.slice(0, 10);
                    var pickupFrom = data.service.ExPickupFrom;
                    var pickupTo = data.service.ExPickupTo;
                    SetDropOffTime(pickupDate, pickupFrom, pickupTo);
                }

            }
        });
    }

    <?php
		if ($aVars['UpdatePostalServicesOnLoad']) {
            echo 'DropOffTypeUpdate();';
		}
    ?>

    $("#field_div_DropOffType").on('change', '#field_DropOffType', DropOffTypeUpdate);
    $("#field_div_ShippingPaymentType").on('change', '#field_ShippingPaymentType', DropOffTypeUpdate);
    $('#field_ShipmentDate').on('change', DropOffTypeUpdate);
    $('#field_SenderPostalCode').on('change', DropOffTypeUpdate);


    <?php
    }
	?>
	url_delete_shipment = '<?php echo (new Controllers\ShipmentsList())->GetLink(array('action'=>'delete','id'=>$shipment->Id)); ?>';

    <?php
        if ($downloadLabel = $aVars['DownloadLabel']) {
            echo "setTimeout(function() {
                var downloadUrl = $('#field_Action_GetLabel_$downloadLabel').attr('href');
                if (downloadUrl != 'undefined')  {
                    window.location = downloadUrl;
                }              
            }, 1000);";
        }
    ?>


</script>
