<?php

namespace DhlAssistant\Classes\Controllers;

use DhlAssistant\Core;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Forms;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Traits;

class PnpReport extends Models\Controller
{
    use Traits\ControllerWrappedOutput;

    /**
     * @inheritDoc
     */
    public function Go()
    {
        Wrappers\ConfigWrapper::CheckIsModuleConfigured();

        $view_mode = false;
        $report_form = new Forms\PnpReport();
        $report_link = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $values = $report_form->PopulateWithPostValues()->GetValues();
            if ($report_form->Validate()) {
                if (isset($values['Action:Request'])) {
                    try {
                        $api = Managers\DhlApiManager::GetDhlApiByCode($values['ApiCode']);
                        $params = [];

                        if ($api->IsFeatureSupported('PnpAdditionalOptions')) {
                            $params['PackageType'] = $values['PackageType'];
                        }

                        $date = new \DateTime($values['ReportDate']);
                        $filename = Wrappers\DhlWrapper::GetPnpReport(
                            $api,
                            Wrappers\ConfigWrapper::Get('DefaultDhlUser'),
                            $date,
                            $params
                        );

                        $report_form->AddGeneralNotice('Raport został wygenerowany');
                        $report_link = Wrappers\ConfigWrapper::Get('BaseUrl')
                            . Wrappers\ConfigWrapper::Get('ReportsPath')
                            . $filename;
                    } catch (\Exception $Ex) {
                        $report_form->AddGeneralError($Ex->getMessage());
                    }
                } else {
                    throw new Exceptions\LoggedException('Nieznana akcja!');
                }
            }
        }

        $this->MakeWrappedOutput(
            Wrappers\ConfigWrapper::Get('FullName') . ' - generowanie raportu PNP',
            Core\Template::Render(
                'PnpReport',
                [
                    'Form' => $report_form,
                    'ReportLink' => $report_link,
                ]
            )
        );
    }
}
