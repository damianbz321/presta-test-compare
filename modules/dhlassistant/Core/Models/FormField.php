<?php

namespace DhlAssistant\Core\Models;

/**
 *
 */
class FormField
{
    public $Name = '';
    public $FullName = '';
    public $Type = null;
    public $DefaultValue = null;
    public $PossibleValues = null;
    public $MaxLen = null;
    public $Value = null;
    public $Errors = [];
    public $Notices = [];
    public $Params = [];
    public $Description = '';
    public $Validators = [];
    public $HasError = false;

    /**
     * @param string $sName
     * @param string $sFullName
     * @param null $sType
     * @param null $mDefaultValue
     * @param null $mPossibleValues
     * @param null $iMaxLen
     */
    public function __construct(
        $sName = '',
        $sFullName = '',
        $sType = null,
        $mDefaultValue = null,
        $mPossibleValues = null,
        $iMaxLen = null
    ) {
        $this->Name = $sName;
        $this->FullName = $sFullName;
        $this->Type = $sType;
        $this->DefaultValue = $mDefaultValue;
        $this->Value = $mDefaultValue;
        $this->PossibleValues = $mPossibleValues;
        $this->MaxLen = $iMaxLen;
    }

    /**
     * @param $mValue
     * @return $this
     */
    public function SetValue($mValue)
    {
        $this->Value = $mValue;

        return $this;
    }

    /**
     * @param $sError
     * @return $this
     */
    public function AddError($sError)
    {
        $this->Errors[] = $sError;

        return $this;
    }

    /**
     * @param $aErrors
     * @return $this
     */
    public function SetErrors($aErrors)
    {
        $this->Errors = $aErrors;

        return $this;
    }

    public function AddNotice($sNotice)
    {
        $this->Notices[] = $sNotice;

        return $this;
    }

    /**
     * @param $aNotices
     * @return $this
     */
    public function SetNotices($aNotices)
    {
        $this->Notices = $aNotices;

        return $this;
    }

    public function AddParam($sParamName, $mParamValue)
    {
        $this->Params[$sParamName] = $mParamValue;
        return $this;
    }

    /**
     * @param $mValidator
     * @param array $mParams
     * @param null $sErrorMessage
     * @param null $sNoticeMessage
     * @return $this
     */
    public function AddValiadtor($mValidator, $mParams = [], $sErrorMessage = null, $sNoticeMessage = null)
    {
        $params = is_array($mParams) ? $mParams : [$mParams];

        $this->Validators[] = [
            'Validator' => $mValidator,
            'ErrorMsg' => $sErrorMessage,
            'NoticeMsg' => $sNoticeMessage,
            'Params' => $params
        ];

        return $this;
    }

    /**
     * @return bool
     */
    public function Validate()
    {
        if (is_array($this->Validators) && $this->Validators) {
            foreach ($this->Validators as $validator) {
                $func = $validator['Validator'];
                $params = array_merge(array($this->Value), $validator['Params']);

                if (is_string($func) && substr($func, 0, 1) === '|') {
                    $func = str_replace('|', '\DhlAssistant\Core\Validators::', $func);
                }

                if (!call_user_func_array($func, $params)) {
                    if ($validator['ErrorMsg'] !== null) {
                        $this->AddError($validator['ErrorMsg']);
                    }

                    if ($validator['NoticeMsg'] !== null) {
                        $this->AddNotice($validator['NoticeMsg']);
                    }

                    $this->HasError = true;

                    return false;
                }
            }
        }

        $this->HasError = false;

        return true;
    }
}

?>