<?php if(!isset($is_template)) die(); ?>
<?php
use DhlAssistant\Core;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Controllers;
?>
<?php 
    $enums = new DhlAssistant\Classes\Dhl\Enums\SettingsUserData();

	if ($aVars['Message'])
	{
		echo '<div class="general_notices bootstrap">'."\n";
		echo '<div class="notice alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button>'.htmlspecialchars($aVars['Message']).'</div></div>'."\n";
	}
?>
<div class="panel col-lg-12">
<div class="panel-heading">
    <?php echo $enums->ValueTabsMenu('TabShipmentListValue'); ?>
</div>
<form
        action="<?php echo Core\Storage::Get('RulingController')->GetLink(array('per_page'=>$aVars['Pagination']['PerPage'])); ?>"
        method="post">
	<div class="table-responsive-row clearfix">
		<table class="table customer">
			<thead>
				<tr class="nodrag nodrop">
					<th class="fixed-width-xs text-center">
						<span class="title_box">Id</span>
					</th>
					<th>
						<span class="title_box"><?php echo $enums->ShipmentList('RecipientValue'); ?></span>
					</th>
					<th>
						<span class="title_box"><?php echo $enums->ShipmentList('ContentsValue'); ?></span>
					</th>
					<th>
						<span class="title_box"><?php echo $enums->ShipmentList('CreationDateValue'); ?></span>
					</th>
					<th>
						<span class="title_box"><?php echo $enums->ShipmentList('StatusValue'); ?></span>
					</th>
					<th>
						<span class="title_box"><?php echo $enums->ShipmentList('ActionValue'); ?></span>
					</th>
				</tr>
				<tr class="nodrag nodrop filter row_hover">
					<th class="text-center"> <?php /*  Id */ ?>
						<input type="text" value="<?php echo htmlspecialchars($aVars['Filters']['Filter:Id']); ?>" name="Filter:Id" class="filter">
					</th>
					<th class="text-center"><?php /* Odbiorca */ ?>
						--
					</th>
					<th><?php /* Zawartość */ ?>
						<input type="text" value="<?php echo htmlspecialchars($aVars['Filters']['Filter:Content']); ?>" name="Filter:Content" class="filter">
					</th>
					<th class="text-right"><?php /* Data utworzenia */ ?>
						<div class="date_range row">
		 					<div class="input-group fixed-width-md">
								<input type="text" value="<?php echo htmlspecialchars($aVars['Filters']['Filter:DateFrom']); ?>" placeholder="" name="Filter:DateFrom" class="filter datepicker date-input form-control">
<!--								<span class="input-group-addon">-->
<!--									<i class="icon-calendar"></i>-->
<!--								</span>-->
							</div>
		 					<div class="input-group fixed-width-md">
								<input type="text" value="<?php echo htmlspecialchars($aVars['Filters']['Filter:DateTo']); ?>" placeholder="" name="Filter:DateTo" class="filter datepicker date-input form-control">
<!--								<span class="input-group-addon">-->
<!--									<i class="icon-calendar"></i>-->
<!--								</span>-->
							</div>
						</div>
					</th>
					<th class="text-center"> <?php /*  Status */ ?>
						<select name="Filter:Status" class="filter fixed-width-sm">
							<option value="">-</option>
							<option value="Waiting"<?php echo ($aVars['Filters']['Filter:Status'] == 'Waiting' ? ' selected="selected"' : ''); ?>><?php echo $enums->ShipmentList('WaitingValue'); ?><</option>
							<option value="Sended"<?php echo ($aVars['Filters']['Filter:Status'] == 'Sended' ? ' selected="selected"' : ''); ?>><?php echo $enums->ShipmentList('SentValue'); ?></option>
						</select>
					</th>
					<th class="actions"> <?php /*  Search Button */ ?>
						<span class="">
							<button class="btn btn-default" name="Action:Search" type="submit">
								<i class="icon-search"></i> <?php echo $enums->ShipmentList('SearchValue'); ?>
							</button>
							<?php 
								if ($aVars['UsingFilters'])
								{
									echo '
										<button class="btn btn-warning" name="Action:ResetSearch" type="submit">
											<i class="icon-eraser"></i> '.$enums->ShipmentList('ClearValue').'
										</button>'."\n";
								}
							?>
						</span>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				$ind = 0;
				if ($aVars['Shipments'])
				{
					/* @var $shipment DataModels\Shipment */
					foreach ($aVars['Shipments'] as $shipment)
					{
		
						echo '<tr class="'.($ind % 2 == 0 ? 'odd' : '').'">';
						echo '<td class="pointer">'.$shipment->Id.'</td>';
						echo '<td class="pointer">'.htmlspecialchars($shipment->Receiver->Address->Name).'</td>';
						echo '<td class="pointer">'.htmlspecialchars($shipment->Content).'</td>';
						echo '<td class="pointer">'.$shipment->CreationDateTime->format('Y-m-d H:i').'</td>';
		
						$status = $shipment->IsSended() ? $enums->ShipmentList('SentValue') : $enums->ShipmentList('WaitingValue') ;
						echo '<td class="pointer"><span class="label color_field '.($shipment->IsSended()? 'sended' : 'waiting').'">'.$status.'</span></td>';
						
						echo '<td class="pointer"><span class="btn-group-action"><span class="btn-group">';
						
						if ($shipment->IsSended())
						{
							echo '<a class="btn btn-default"  
							href="'.(new Controllers\ShipmentEdit())->GetLink(array('id'=>$shipment->Id)).'">
							<i class="icon-search-plus"></i> Szczegóły</a>';
							$get_label_controller = new Controllers\GetLabel();

							foreach ($shipment->GetAvailableLabelTypes() as $label_code)
								echo '<a class="btn btn-default"  
								href="'.$get_label_controller->GetLink(array('id'=>$shipment->Id, 'type'=>$label_code)).'" 
								rel=”noopener” target="_blank"><i class="icon-file-text"></i> Etykieta '.$label_code.'</a>';
							//echo '<a class="btn btn-default"  href="'.Wrappers\ConfigWrapper::Get('BaseUrl').Wrappers\ConfigWrapper::Get('LabelsPath').$shipment->GetLabelFileName().'" target="_blank"><i class="icon-search-plus"></i> Pobierz etykietę</a>';
							echo '<a class="btn btn-default"
							  href="'.(new Controllers\ShipmentEdit())->GetLink(array('action'=>'cancel','id'=>$shipment->Id)).'" 
							  onclick="return confirm(\'Czy na pewno wycofać zlecenie?\');">
							  <i class="icon-arrow-left"></i>
							   '.$enums->ShipmentList('BackOffValue').'</a>';
						}
						else
						{
							echo '<a class="btn btn-default"  
							href="'.(new Controllers\ShipmentEdit())->GetLink(array('id'=>$shipment->Id)).'">
							<i class="icon-pencil"></i> '.$enums->ShipmentList('EditValue').'</a>';
						}
		
            			echo '<a class="btn btn-default"  
						href="'.(new Controllers\ShipmentsList())->GetLink(array('action'=>'delete','id'=>$shipment->Id)).'" 
						onclick="return confirm(\'Czy na pewno usunąć zlecenie przesyłki?\nW przypadku zleconych przesyłek nie powoduje to ich wycofania.\');">
						<i class="icon-trash"></i> '.$enums->ShipmentList('DeleteValue').'</a>';
						echo '</span></span></td>';
		
						echo '</tr>';
						$ind++;
					}
				}
			?>
			</tbody>
		</table>
		<?php echo Core\Template::Render('Elements/Pagination', array('Pagination' => $aVars['Pagination'])); ?>
	</div>
</form>
</div>
<div id="dropdown-hidden"></div>