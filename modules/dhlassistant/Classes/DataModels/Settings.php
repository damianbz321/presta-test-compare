<?php

namespace DhlAssistant\Classes\DataModels;

use DhlAssistant\Wrappers;
use DhlAssistant\Core\Models;

class Settings
{
    protected $aSettingsValues = [];
    protected $aCodModules = [];

    /**
     * @throws \DhlAssistant\Core\Exceptions\LoggedException
     */
    public function __construct()
    {
        $payment_modules = Wrappers\SourceWrapper::GetPaymentModules();
        $cod_modules = Wrappers\ConfigWrapper::Get('CodPaymentModules');

        if ($payment_modules) {
            foreach ($payment_modules as $module_name => $module_display_name) {
                $this->aCodModules[$module_name] = (bool)in_array($module_name, $cod_modules);
            }
        }
    }

    /**
     * @param $sContextName
     * @param false $bWithDependedObjects
     * @param false $bWithKeys
     * @return array
     */
    public function GetTreeDataValues($sContextName, $bWithDependedObjects = false, $bWithKeys = false)
    {
        $result = $this->aSettingsValues;

        foreach ($this->aCodModules as $module_name => $value) {
            $result['Setting_Cod_' . $module_name] = $value;
        }

        return $result;
    }

    /**
     * @param $sContextName
     * @param $aValues
     * @param false $bWithDependedObjects
     */
    public function SetTreeDataValues($sContextName, $aValues, $bWithDependedObjects = false)
    {
        if ($aValues) {
            $this->aCodModules = [];
            $payment_modules = Wrappers\SourceWrapper::GetPaymentModules();

            if ($payment_modules) {
                foreach ($payment_modules as $module_name => $module_display_name) {
                    $this->aCodModules[$module_name] = (isset($aValues['Setting_Cod_' . $module_name]) && $aValues['Setting_Cod_' . $module_name]);
                }
            }
        }

    }

    /**
     * @return Models\ValidationResult
     */
    public function Validate()
    {
        return new Models\ValidationResult();
    }

    /**
     * @throws \DhlAssistant\Core\Exceptions\LoggedException
     */
    public function Save()
    {
        $cod_modules = [];

        if ($this->aCodModules) {
            foreach ($this->aCodModules as $module_name => $value) {
                if ($value) {
                    $cod_modules[] = $module_name;
                }
            }
        }

        Wrappers\ConfigWrapper::Set('CodPaymentModules', $cod_modules);
    }
}

?>