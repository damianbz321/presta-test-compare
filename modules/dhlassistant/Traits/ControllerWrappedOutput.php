<?php

namespace DhlAssistant\Traits;

use DhlAssistant\Core;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Controllers;
use DhlAssistant\Classes\Dhl\Enums;

/**
 *
 */
trait ControllerWrappedOutput
{
    /**
     * @return Enums\SettingsUserData
     */
    public function GetSettingsUserData()
    {
        return new Enums\SettingsUserData();
    }

    /**
     * @param $sTitle
     * @param $sBody
     * @throws Core\Exceptions\LoggedException
     */
    public function MakeWrappedOutput($sTitle, $sBody)
    {
        $ps_uri = Wrappers\ConfigWrapper::Get('PsUri');
        $module_path = $ps_uri . Wrappers\ConfigWrapper::Get('ModulePath');
        $link_admin = $ps_uri . basename(_PS_ADMIN_DIR_);

        Core\Storage::Add('Js', $module_path . 'Media/Js/main.js', true, true);
        Core\Storage::Add(
            'Js',
            $ps_uri
            . Wrappers\ConfigWrapper::Get('PsPanelDefaultThemePath')
            . 'js/vendor/bootstrap.min.js',
            true,
            true
        );
        Core\Storage::Add('Js', $ps_uri . 'js/jquery/ui/jquery.ui.core.min.js');
        Core\Storage::Add('Js', $ps_uri . 'js/jquery/ui/jquery.ui.widget.min.js');
        Core\Storage::Add('Js', $ps_uri . 'js/jquery/ui/jquery.ui.mouse.min.js');
        Core\Storage::Add('Js', $ps_uri . 'js/jquery/ui/jquery.ui.slider.min.js');
        Core\Storage::Add('Js', $ps_uri . 'js/jquery/ui/jquery.ui.datepicker.min.js');
        Core\Storage::Add('Js', $ps_uri . 'js/jquery/ui/i18n/jquery.ui.datepicker-pl.js');
        Core\Storage::Add('Js', $ps_uri . 'js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');

        Core\Storage::Add('Css', $module_path . 'Media/Css/Styles.css', true, true);
        Core\Storage::Add('Css', $module_path . 'Media/Css/theme.css', true, true);
        Core\Storage::Add(
            'Css',
            $ps_uri . Wrappers\ConfigWrapper::Get('PsPanelThemeCssPath'),
            true,
            true
        );
        Core\Storage::Add('Css', $ps_uri . 'js/jquery/ui/themes/base/jquery.ui.core.css');
        Core\Storage::Add('Css', $ps_uri . 'js/jquery/ui/themes/base/jquery.ui.slider.css');
        Core\Storage::Add(
            'Css',
            $link_admin . '/themes/default/public/theme.css',
            true,
            true
        );
        Core\Storage::Add('Css', $ps_uri . 'js/jquery/ui/themes/base/jquery.ui.datepicker.css');

        $available_controllers_list = [];

        if (Wrappers\ConfigWrapper::Get('IsModuleConfigured')) {
            $available_controllers_list = [
                $this->GetSettingsUserData()->ValueTabsMenu('TabShipmentListValue') => [
                    'object' => new Controllers\ShipmentsList(),
                    'icon' => 'icon-list'
                ],
                $this->GetSettingsUserData()->ValueTabsMenu('ReportPNP') => [
                    'object' => new Controllers\PnpReport(),
                    'icon' => 'icon-file-text'
                ],
            ];
        }

        $available_controllers_list = array_merge(
            $available_controllers_list,
            [
                $this->GetSettingsUserData()->ValueTabsMenu('TabShipmentPredefinitionsValue') => [
                    'object' => new Controllers\ShipmentPresetsList(),
                    'icon' => 'icon-list'
                ],
                $this->GetSettingsUserData()->ValueTabsMenu('TabConfigurationValue') => [
                    'object' => new Controllers\SettingsEdit(),
                    'icon' => 'icon-cogs'
                ],
                $this->GetSettingsUserData()->ValueTabsMenu('TabHelpValue') => [
                    'object' => new Controllers\Help(),
                    'icon' => 'icon-info-circle'
                ],
            ]
        );

        echo Core\Template::Render(
            'Wrappers/MainWrapper',
            [
                'Title' => $sTitle,
                'Body' => $sBody,
                'JQueryUrl' => $ps_uri . 'js/jquery/jquery-' . Wrappers\ConfigWrapper::Get('JQueryVersion') . '.min.js',
                'Css' => Core\Storage::Get('Css'),
                'Js' => Core\Storage::Get('Js'),
                'CssInline' => Core\Storage::Get('CssInline'),
                'JsInline' => Core\Storage::Get('JsInline'),
                'NavbarLinks' => $available_controllers_list,
            ]
        );
    }
}
