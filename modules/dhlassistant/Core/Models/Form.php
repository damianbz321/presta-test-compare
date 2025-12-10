<?php

namespace DhlAssistant\Core\Models;

class Form
{
    public $Name = null;

    /**
     * @var FormField[]
     */
    public $Fields = [];
    public $GeneralErrors = [];
    public $GeneralNotices = [];
    public $Params = [];
    public $HasError = false;

    /**
     * @param $sName
     */
    public function __construct($sName)
    {
        $this->Name = $sName;
    }

    /**
     * @param $aValues
     * @return $this
     */
    public function PopulateWithValues($aValues)
    {
        if ((is_array($aValues) && $aValues)) {
            foreach ($aValues as $field_name => $field_value) {
                if (isset($this->Fields[$field_name])) {
                    $this->Fields[$field_name]->SetValue($field_value);
                }
            }
        }

        return $this;
    }

    /**
     * @param ValidationResult $oValidationResult
     * @return $this
     */
    public function PopulateWithValidationResult(ValidationResult $oValidationResult)
    {
        if ((is_array($oValidationResult->Errors) && $oValidationResult->Errors)) {
            foreach ($oValidationResult->Errors as $field_name => $field_errors) {
                if (isset($this->Fields[$field_name])) {
                    $this->Fields[$field_name]->SetErrors($field_errors);
                }
            }
        }

        if ((is_array($oValidationResult->Notices) && $oValidationResult->Notices)) {
            foreach ($oValidationResult->Notices as $field_name => $field_notices) {
                if (isset($this->Fields[$field_name])) {
                    $this->Fields[$field_name]->SetNotices($field_notices);
                }
            }
        }

        if ((is_array($oValidationResult->GeneralErrors) && $oValidationResult->GeneralErrors)) {
            $this->SetGeneralErrors($oValidationResult->GeneralErrors);
        }

        if ((is_array($oValidationResult->GeneralNotices) && $oValidationResult->GeneralNotices)) {
            $this->SetGeneralNotices($oValidationResult->GeneralNotices);
        }

        if (!$oValidationResult->Status) {
            $this->HasError = true;
        }

        return $this;
    }

    /**
     * @param FormField $oFormField
     * @param string $sPrefix
     * @return $this
     */
    public function AddField(FormField $oFormField, $sPrefix = '')
    {
        $field_name = ($sPrefix != '' ? $sPrefix . ':' : '') . $oFormField->Name;
        $this->Fields[$field_name] = $oFormField;

        return $this;
    }

    /**
     * @param Form $aForm
     * @param string $sPrefix
     * @return $this
     */
    public function Merge(Form $aForm, $sPrefix = '')
    {
        if (is_array($aForm->Fields) && $aForm->Fields) {
            foreach ($aForm->Fields as $field_name => $field_value) {
                if ($sPrefix != '') {
                    $field_name = $sPrefix . ':' . $field_name;
                }

                $this->Fields[$field_name] = $field_value;
            }
        }

        if (is_array($aForm->GeneralErrors && $aForm->GeneralErrors)) {
            $this->GeneralErrors = array_merge($this->GeneralErrors, $aForm->GeneralErrors);
        }

        if (is_array($aForm->GeneralNotices && $aForm->GeneralNotices)) {
            $this->GeneralNotices = array_merge($this->GeneralNotices, $aForm->GeneralNotices);
        }

        return $this;
    }

    /**
     * @param $sError
     * @return $this
     */
    public function AddGeneralError($sError)
    {
        $this->GeneralErrors[] = $sError;
        $this->HasError = true;

        return $this;
    }

    /**
     * @param $aErrors
     * @return $this
     */
    public function SetGeneralErrors($aErrors)
    {
        $this->GeneralErrors = $aErrors;
        if ($aErrors) {
            $this->HasError = true;
        }

        return $this;
    }

    /**
     * @param $sNotice
     * @return $this
     */
    public function AddGeneralNotice($sNotice)
    {
        $this->GeneralNotices[] = $sNotice;

        return $this;
    }

    public function SetGeneralNotices($aNotices)
    {
        $this->GeneralNotices = $aNotices;

        return $this;
    }

    /**
     * @param $sParamName
     * @param $mParamValue
     * @return $this
     */
    public function AddParam($sParamName, $mParamValue)
    {
        $this->Params[$sParamName] = $mParamValue;

        return $this;
    }

    /**
     * @return array
     */
    public function GetValues()
    {
        $result = [];

        if (is_array($this->Fields) && $this->Fields) {
            foreach ($this->Fields as $field_name => $field) {
                $result[$field_name] = $field->Value;
            }
        }

        return $result;
    }

    /**
     * @return $this
     */
    public function PopulateWithPostValues()
    {
        return $this->PopulateWithValues($_POST);
    }

    /**
     * @return $this
     */
    public function PopulateWithGetValues()
    {
        return $this->PopulateWithValues($_GET);
    }

    /**
     * @return bool
     */
    public function Validate()
    {
        $result = true;

        if (is_array($this->Fields) && $this->Fields) {
            foreach ($this->Fields as $field) {
                if (!$field->Validate()) {
                    $result = false;
                }
            }
        }

        $this->HasError = !$result;

        return $result;
    }

    /**
     * @return int
     */
    public function CountFieldsErrors()
    {
        $result = 0;

        if (is_array($this->Fields) && $this->Fields) {
            foreach ($this->Fields as $field) {
                if (is_array($field->Errors) && $field->Errors) {
                    $result += count($field->Errors);
                }
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function CountFieldsNotices()
    {
        $result = 0;

        if (is_array($this->Fields) && $this->Fields) {
            foreach ($this->Fields as $field) {
                if (is_array($field->Notices) && $field->Notices) {
                    $result += count($field->Notices);
                }
            }
        }

        return $result;
    }
}

?>