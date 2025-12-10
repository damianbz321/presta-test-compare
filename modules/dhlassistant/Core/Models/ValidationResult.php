<?php

namespace DhlAssistant\Core\Models;

/**
 *
 */
class ValidationResult
{
    public $Status = true;
    public $Errors = [];
    public $Notices = [];
    public $GeneralErrors = [];
    public $GeneralNotices = [];

    /**
     * @param $sFieldName
     * @param $sErrorMessage
     * @return $this
     */
    public function AddError($sFieldName, $sErrorMessage)
    {
        if (!isset($this->Errors[$sFieldName])) {
            $this->Errors[$sFieldName] = [];
        }

        $this->Errors[$sFieldName][] = $sErrorMessage;

        return $this;
    }

    /**
     * @param $sFieldName
     * @param $sNoticeMessage
     * @return $this
     */
    public function AddNotice($sFieldName, $sNoticeMessage)
    {
        if (!isset($this->Notices[$sFieldName])) {
            $this->Notices[$sFieldName] = [];
        }

        $this->Notices[$sFieldName][] = $sNoticeMessage;

        return $this;
    }

    /**
     * @param $sErrorMessage
     * @return $this
     */
    public function AddGeneralError($sErrorMessage)
    {
        $this->GeneralErrors[] = $sErrorMessage;

        return $this;
    }

    /**
     * @param $sNoticeMessage
     * @return $this
     */
    public function AddGeneralNotice($sNoticeMessage)
    {
        $this->GeneralNotices[] = $sNoticeMessage;

        return $this;
    }

    /**
     * @return $this
     */
    public function Fail()
    {
        $this->Status = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function IsSuccess()
    {
        return $this->Status;
    }

    /**
     * @param ValidationResult $oObjToMerge
     * @param string $sPrefix
     * @return $this
     */
    public function Merge(ValidationResult $oObjToMerge, $sPrefix = '')
    {
        if (!$oObjToMerge->Status) {
            $this->Status = false;
        }

        if ($oObjToMerge->Errors) {
            foreach ($oObjToMerge->Errors as $field_name => $field_errors) {
                if ($sPrefix != '') {
                    $field_name = $sPrefix . ':' . $field_name;
                }

                if (isset($this->Errors[$field_name])) {
                    $this->Errors[$field_name] = array_merge($this->Errors[$field_name], $field_errors);
                } else {
                    $this->Errors[$field_name] = $field_errors;
                }
            }
        }

        if ($oObjToMerge->Notices) {
            foreach ($oObjToMerge->Notices as $field_name => $field_notices) {
                if ($sPrefix != '') {
                    $field_name = $sPrefix . ':' . $field_name;
                }

                if (isset($this->Notices[$field_name])) {
                    $this->Notices[$field_name] = array_merge($this->Notices[$field_name], $field_notices);
                } else {
                    $this->Notices[$field_name] = $field_notices;
                }
            }
        }

        if ($oObjToMerge->GeneralErrors) {
            $this->GeneralErrors = array_merge($this->GeneralErrors, $oObjToMerge->GeneralErrors);
        }

        if ($oObjToMerge->GeneralNotices) {
            $this->GeneralNotices = array_merge($this->GeneralNotices, $oObjToMerge->GeneralNotices);
        }

        return $this;
    }
}

?>