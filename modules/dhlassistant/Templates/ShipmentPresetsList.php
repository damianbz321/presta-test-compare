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
    echo '<div class="notice alert alert-success">
            <button 
            data-dismiss="alert" 
            class="close" 
            type="button">×</button>'
        .htmlspecialchars($aVars['Message']).
        '</div>
            </div>'
        ."\n";
}
?>
<?php if (!$aVars['ShipmentPresets']) { ?>
    <h3><?php echo $enums->ShipmentPresetList('NoPreOfShipmentsValue') ?></h3>
    <?php
}
else /* są predefinicje */
{
    ?>
    <div class="panel col-lg-12">
        <div class="panel-heading">
            <?php echo $enums->ValueTabsMenu('TabShipmentPredefinitionsValue') ?>
        </div>
        <div class="table-responsive-row clearfix">
            <table class="table customer">
                <thead>
                <tr class="nodrag nodrop">

                    <th>
                        <span class="title_box">Id</span>
                    </th>
                    <th>
                        <span class="title_box"><?php echo $enums->Shipment('NameValue') ?></span>
                    </th>
                    <th>
                        <span class="title_box"><?php echo $enums->Shipment('ActionValue') ?></span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                $ind = 0;
                /* @var $shipment_preset DataModels\ShipmentPreset */
                foreach ($aVars['ShipmentPresets'] as $shipment_preset)
                {
                    echo '<tr class="'.($ind % 2 == 0 ? 'odd' : '').'">';
                    echo '<td class="pointer">'.$shipment_preset->Id.'</td>';
                    echo '<td class="pointer">'.htmlspecialchars($shipment_preset->Name).'</td>';
                    echo '<td class="pointer"><span class="btn-group-action"><span class="btn-group">';
                    echo '<a 
					class="btn btn-default"  
					href="'.(new Controllers\ShipmentPresetEdit())->GetLink(array('id'=>$shipment_preset->Id)).'">
					<i class="icon-search-plus"></i>'
                        .$enums->ShipmentPresetList('EditValue').
                        '</a>';
                    echo '<a 
					class="btn btn-default"
					href="'.(new Controllers\ShipmentPresetsList())->GetLink(array('action'=>'delete','id'=>$shipment_preset->Id)).'" 
					onclick="return confirm(\'Czy na pewno usunąć predefinicję przesyłki?\');">
					<i class="icon-search-plus"></i>'
                        .$enums->ShipmentPresetList('DeleteValue').
                        '</a>';
                    echo '</span></span></td>';
                    echo '</tr>';
                    $ind++;
                }
                ?>
                </tbody>
            </table>
            <?php echo Core\Template::Render('Elements/Pagination', array('Pagination' => $aVars['Pagination'])); ?>
        </div>
    </div>
    <?php
}
?>
    <span class="btn-group-action">
	<span class="btn-group">
		<a
                href="<?php echo (new Controllers\ShipmentPresetEdit())->GetLink(); ?>"
                class="btn btn-default"><i class="icon-search-plus"></i>
            <?php echo $enums->ShipmentPresetList('AddNewValue') ?>
        </a>
	</span>
</span>
<?php
if ($aVars['ShipmentPresets'])
    echo '<div id="dropdown-hidden"></div>'."\n";
?>