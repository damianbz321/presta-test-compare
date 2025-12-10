<?php

namespace DhlAssistant\Classes\Forms;

use DhlAssistant\Core;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\DataModels;
use DhlAssistant\Classes\Dhl\Enums;

/**
 *
 */
class PnpReport extends Models\Form
{
    /**
     * @throws Core\Exceptions\LoggedException
     */
    public function __construct()
    {
        parent::__construct('PnpReport');

        $settings = new Settings();
        $getSettingsUserData = $settings->GetSettingsUserData();

        $available_apis = Core\Tools::ArraySortWithExampleByKey(
            Enums\DhlWebApi::$Descriptions,
            Wrappers\ConfigWrapper::Get('DefaultDhlUser')->GetSupportedApiCodesList(),
            false
        );

        $this->AddField(
            (new Models\FormField(
                'ApiCode', $getSettingsUserData->ValuePnpReport('PNPTypeApiValue'),
                'Select',
                null,
                $available_apis
            ))->AddValiadtor(
                '|IsInArray',
                [array_keys($available_apis)],
                'Nieprawidłowa wartość'
            )
        );
        $this->AddField(
            (new Models\FormField(
                'PackageType',
                $getSettingsUserData->ValuePnpReport('PNPTypeShippingValue'),
                'Select',
                null,
                Enums\PnpReportPackageType::$Descriptions
            ))->AddValiadtor(
                '|IsInArray',
                [array_keys(Enums\PnpReportPackageType::$Descriptions)],
                'Nieprawidłowa wartość'
            )
        );
        $this->AddField(
            (new Models\FormField(
                'ReportDate', $getSettingsUserData->ValuePnpReport('PNPReportDayValue'),
                'DatePicker',
                Core\Filters::ToDateString(new \DateTime("now"))
            ))->AddValiadtor(
                '|IsDateString',
                null,
                'Nieprawidłowa wartość'
            )->AddParam(
                'tooltip',
                'Pole musi być poprawną datą'
            )
        );


        $Action = new Models\Form('Action');
        $Action->AddField(
            (new Models\FormField('panel-footer', '', 'SectionStart'))
                ->AddParam(
                    'silent',
                    true
                )
        );
        $Action->AddField(
            (new Models\FormField('Request', 'Pobierz', 'Button'))
                ->AddParam('class', 'pull-right')
                ->AddParam('icon_class', 'icon-arrow-down')
        );
        $Action->AddField((new Models\FormField('End', '', 'EndDiv')));
        $this->Merge($Action, 'Action');
    }
}

?>