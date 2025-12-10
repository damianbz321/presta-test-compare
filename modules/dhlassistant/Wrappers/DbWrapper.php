<?php
namespace DhlAssistant\Wrappers;

use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\Interfaces;
use DhlAssistant\Core\Models;


class DbWrapper
{
	protected static $S_bTransactionInProgress = false;
	protected static $S_TablePrefix = 'dhla_';
	
	public static function GetConnection()
	{
		
		$instance = \Db::getInstance();
		if (!$instance)
			throw new Exceptions\LoggedException("Brak połączenia z bazą danych!");
		return $instance;
	}
	public static function Load(Models\TreeDataObjectInfo $oObjectInfo, $iId)
	{
		return self::LoadMany($oObjectInfo, array((int)$iId))[(int)$iId];
	}
	public static function LoadMany(Models\TreeDataObjectInfo $oObjectInfo, $aIds)
	{
		/* @var $oObjectInfo Models\TreeDataObjectInfo */
		$loaded_values = self:: _SubLoadValuesMany ($oObjectInfo, $aIds);
		$dependend_values = array(); //id => array([string: dependend_name] => [object: depenend_object])
		/* @var $sub_obj_info Models\TreeDataObjectInfo */
		//obróbka obiektów zależnych
		if ($oObjectInfo->aDependedObjects)
		{
			foreach ($oObjectInfo->aDependedObjects as $sub_obj_name => $sub_obj_info)
			{
				$needed_ids = array();
				foreach ($loaded_values as $obj_id => $obj_values)
					$needed_ids[] = $obj_values[$oObjectInfo->aDependedObjectsKeys[$sub_obj_name]];
				$dependend_values[$sub_obj_name] = self::LoadMany($sub_obj_info, $needed_ids);
			}
		}
		$result = array();
		//uzupełnianie wartości
		foreach ($loaded_values as $obj_id => $obj_values)
		{
			$class_name = $oObjectInfo->sClassName;
			/* @var $obj Interfaces\TreeDataObject */
			$obj = new $class_name();
			$key_name = $oObjectInfo->sIdFieldName;
			$obj->$key_name = $obj_id;
			$obj->SetTreeDataValues('Db', $obj_values, false);
			if ($dependend_values)
			{
				foreach ($oObjectInfo->aDependedObjectsKeys as $sub_obj_name => $sub_obj_key_name)
					$obj->$sub_obj_name = $dependend_values[$sub_obj_name][$obj_values[$sub_obj_key_name]];
			}
			$result[$obj_id] = $obj;
		}
		return $result;
	}
	protected static function _SubLoadValuesMany(Models\TreeDataObjectInfo $oObjectInfo, $aIds)
	{
		/* @var $oObjectInfo Models\TreeDataObjectInfo */
		$table_name = ConfigWrapper::Get('DbPrefix').$oObjectInfo->sTableName;
		$key_name = $oObjectInfo->sIdFieldName;
		$fields = $oObjectInfo->GetTreeDataFields('Db', true);
		if (!$aIds || !is_array($aIds))
			throw new Exceptions\LoggedException("Błędne ID do pobrania dla tabeli '{$table_name}'!");
		$aIds = self::_ClearIds($aIds);
		$sql = 'SELECT `'.implode('`, `',$fields).'` FROM `'.$table_name.'` WHERE `'.$key_name.'` IN ('.implode(', ',$aIds).')';
		$sql_result = self::GetConnection()->executeS($sql, true, false);
		if (!$sql_result)
			throw new Exceptions\LoggedException("Brak wyników pobierania z bazy danych dla tabeli '{$table_name}'!");
		$result = array();
		$temp_result = array();
		foreach ($sql_result as $row)
			$temp_result[$row[$key_name]] = $row;
		//sortowanie w.g. kolejności
		foreach ($aIds as $id)
		{
			if (isset($temp_result[$id]))
				$result[$id]=$temp_result[$id];
			else
				throw new Exceptions\LoggedException("Nie udało się załadować rekordu o ID '{$id}' z tabeli '{$table_name}'!");
		}
		return $result;
	}
	protected static function _SubLoadValues(Models\TreeDataObjectInfo $oObjectInfo, $iId)
	{
		return self::_SubLoadValuesMany($oObjectInfo, array((int)$iId))[(int)$iId];
	}
	public static function Save(Interfaces\TreeDataObject $oObject)
	{
		self::_StartTransaction();
		try
		{
			self::_SubSave($oObject);
		}
		catch (\Exception $Ex)
		{
			self::_RollbackTransaction();
			throw new Exceptions\LoggedException('Błąd przy zapisywaniu danych: '.$Ex->getMessage());
		}
		self::_CommitTransaction();
		return self::Load($oObject::GetTreeDataObjectInfo(), $oObject->GetObjectId());
	}
	protected static function _SubSave(Interfaces\TreeDataObject $oObject)
	{
		/* @var $oObject Interfaces\TreeDataObject */
		$obj_info = $oObject::GetTreeDataObjectInfo();
		/* @var $sub_obj Models\TreeDataObjectInfo */
		if ($obj_info->aDependedObjects)
			foreach($obj_info->aDependedObjects as $sub_obj_name => $sub_obj_info)
				if ($oObject->$sub_obj_name instanceof Interfaces\TreeDataObject)
					self::_SubSave($oObject->$sub_obj_name);
		$table_name = ConfigWrapper::Get('DbPrefix').$obj_info->sTableName;
		$key_name = $obj_info->sIdFieldName;
		$new_values = $oObject->GetTreeDataValues('Db', false, true);
		if ($oObject->GetObjectId() === null) //INSERT
		{
			$sql_data = array();
			foreach ($new_values as $key => $value)
				$sql_data[] = self::_SanitizeValue($value);
			$sql = 'INSERT INTO `'.$table_name.'` (`'.implode('`, `',array_keys($new_values)).'`) VALUES ('.implode(', ', $sql_data).')';
			$sql_result = self::GetConnection()->execute($sql);
			if (!$sql_result)
				throw new Exceptions\LoggedException(self::GetConnection()->getNumberError().': '.self::GetConnection()->getMsgError());
			$last_id = self::GetConnection()->Insert_ID();
			$oObject->$key_name = $last_id;
		}
		else
		{
			$reference_values = self::_SubLoadValues($obj_info, $oObject->GetObjectId());
			if ($new_values)
			{
				foreach ($new_values as $value_name => $value)
					if ((string)$reference_values[$value_name] === (string)$value)
						unset($new_values[$value_name]);
			}
			if ($new_values)
			{
				$data_pairs = array();
				foreach ($new_values as $key => $value)
					$data_pairs[] = '`'.$key.'` = '.self::_SanitizeValue($value);
				$sql = 'UPDATE `'.$table_name.'` SET '.implode(', ', $data_pairs).' WHERE `'.$key_name.'` = '.$oObject->GetObjectId().' LIMIT 1';
				$sql_result = self::GetConnection()->execute($sql);
				if (!$sql_result)
					throw new Exceptions\LoggedException(self::GetConnection()->getNumberError().': '.self::GetConnection()->getMsgError());
			}
		}
	}
	public static function Delete(Interfaces\TreeDataObject $oObject) 
	{
		if (!$oObject->GetObjectId())
			throw new Exceptions\LoggedException("Błędne ID usuwanego elementu!");
		$obj_info = $oObject::GetTreeDataObjectInfo();
		return self::DeleteMany($oObject::GetTreeDataObjectInfo(), array((int)$oObject->GetObjectId()));
	}
	public static function DeleteMany(Models\TreeDataObjectInfo $oObjectInfo, $aIds)
	{
		self::_StartTransaction();
		try
		{
			if (!$aIds || !is_array($aIds))
				throw new Exceptions\LoggedException("Błedne ID do usunięcia z tabeli '{$table_name}'!");
			$objects = self::LoadMany($oObjectInfo, $aIds);
			self::_SubDelete($objects);
		}
		catch (\Exception $Ex)
		{
			self::_RollbackTransaction();
			throw new Exceptions\LoggedException('Błąd przy usuwaniu danych: '.$Ex->getMessage());
		}
		self::_CommitTransaction();
		return true;
	}
	protected static function _SubDelete($aObjects)
	{
		/* @var $oObjects[] Interfaces\TreeDataObject */
		if (!$aObjects || !is_array($aObjects))
			throw new Exceptions\LoggedException('Błąd usuwania danych!');
		/* @var $obj_info Models\TreeDataObjectInfo */
		$obj = reset($aObjects);
		$obj_info =$obj::GetTreeDataObjectInfo();
		foreach ($aObjects as $obj)
			$ids[] = $obj->GetObjectId();
		$ids = self::_ClearIds($ids);
		$table_name = ConfigWrapper::Get('DbPrefix').$obj_info->sTableName;
		$key_name = $obj_info->sIdFieldName;
		$sql = 'DELETE FROM `'.$table_name.'` WHERE `'.$key_name.'` IN ('.implode(', ', $ids).')';
		if (!self::GetConnection()->execute($sql))
			throw new Exceptions\LoggedException(self::GetConnection()->getNumberError().': '.self::GetConnection()->getMsgError());
		if ($obj_info->aDependedObjects)
		{
			foreach($obj_info->aDependedObjects as $sub_obj_name => $sub_obj_info)
			{
				$sub_objects = array();
				foreach ($aObjects as $obj)
					if ($obj->$sub_obj_name instanceof Interfaces\TreeDataObject)
						$sub_objects[] = $obj->$sub_obj_name;
				if ($sub_objects)
					self::_SubDelete($sub_objects);
			}
		}
		$ids = array();
	}
	public static function Exists(Models\TreeDataObjectInfo $oObjectInfo, $iId)
	{
		return self::ExistsMany($oObjectInfo, array((int)$iId));
	}
	public static function ExistsMany(Models\TreeDataObjectInfo $oObjectInfo, $aIds)
	{
		$table_name = ConfigWrapper::Get('DbPrefix').$oObjectInfo->sTableName;
		$key_name = $oObjectInfo->sIdFieldName;
		if (!$aIds || !is_array($aIds))
			throw new Exceptions\LoggedException("Błedne ID do sprawdzenia w tabeli '{$table_name}'!");
		$aIds = self::_ClearIds($aIds);
		if (!$aIds)
			return false;
		$sql = 'SELECT COUNT(`'.$key_name.'`) AS `qty` FROM `'.$table_name.'` WHERE `'.$key_name.'` IN ('.implode(', ', $aIds).')';
		$result = self::GetConnection()->executeS($sql, true, false);
		if (!$result)
			throw new Exceptions\LoggedException(self::GetConnection()->getNumberError().': '.self::GetConnection()->getMsgError());
		return  $result[0]['qty'] == count($aIds);
	}
	public static function LoadAll(Models\TreeDataObjectInfo $oObjectInfo)
	{
		/* @var $oObjectInfo Models\TreeDataObjectInfo */
		$oObjectInfo->sIdFieldName;
		$table_name = ConfigWrapper::Get('DbPrefix').$oObjectInfo->sTableName;
		$key_name = $oObjectInfo->sIdFieldName;
		$sql = 'SELECT `'.$key_name.'` as `Id` FROM `'.$table_name.'`';
		$sql_result = self::GetConnection()->executeS($sql, true, false);
		$result = array();
		if (!$sql_result)
			return $result;
		foreach ($sql_result as $row)
			$result[] = $row['Id'];
		return self::LoadMany($oObjectInfo, $result);
	}
	public static function Search(Models\TreeDataObjectInfo $oObjectInfo, $sWhereStatement = null, $aWhereValues = array(), $sOrderBy = null, $iLimit = null, $iPage = null, $bCountMode = false)
	{
		$table_name = ConfigWrapper::Get('DbPrefix').$oObjectInfo->sTableName;
		$key_name = $oObjectInfo->sIdFieldName;
		$select_string = $bCountMode ? 'COUNT(`'.$key_name.'`)' : '`'.$key_name.'`';
		$sql = 'SELECT '.$select_string.' as `Result` FROM `'.$table_name.'`';
		//where
		if ($sWhereStatement)
		{
			if ($aWhereValues)
			{
				$sanitized_values = array();
				foreach ($aWhereValues as $key => $value)
					$sanitized_values[$key] = self::_SanitizeValue($value);
				$sWhereStatement = strtr($sWhereStatement, $sanitized_values);
			}
			$sql .= ' WHERE '.$sWhereStatement;
		}
		if (!$bCountMode)
		{
			if ($sOrderBy !== null)
				$sql .= ' ORDER BY '.$sOrderBy;
			if ($iLimit !== null)
			{
				$sql .= ' LIMIT ';
				if ($iPage !== null)
					$sql .= ((int)$iPage * (int)$iLimit).',';
				$sql .= (int)$iLimit;
			}
		}
		$sql_result = self::GetConnection()->executeS($sql, true, false);
		if ($sql_result === false)
			throw new Exceptions\LoggedException(self::GetConnection()->getNumberError().': '.self::GetConnection()->getMsgError());
		$result = array();
 		if (!$bCountMode && !$sql_result)
 			return $result;
 		if ($bCountMode)
 			return $sql_result[0]['Result'];
		foreach ($sql_result as $row)
			$result[] = $row['Result'];
		return $result;
	}
	public static function SearchAndLoad(Models\TreeDataObjectInfo $oObjectInfo, $sWhereStatement = null, $aWhereValues = array(), $sOrderBy = null, $iLimit = null, $iPage = null)
	{
		$ids = self::Search($oObjectInfo, $sWhereStatement, $aWhereValues, $sOrderBy, $iLimit, $iPage);
		if (!$ids)
			return array();
		return self::LoadMany($oObjectInfo, $ids);
	}
	public static function Count(Models\TreeDataObjectInfo $oObjectInfo, $sWhereStatement = null, $aWhereValues = array())
	{
		return self::Search($oObjectInfo, $sWhereStatement, $aWhereValues, null, null, null, true);	
	}
	public static function IsTransactionInProgress()
	{
		return self::$S_bTransactionInProgress;
	}
	protected static function _StartTransaction()
	{
		if (self::$S_bTransactionInProgress)
			throw new \Exception ("Już istnieje rozpoczęta transakcja!");
		if (!self::GetConnection()->execute('BEGIN'))
			throw new \Exception ("Błąd przy rozpoczynaniu transakcji!");
		self::$S_bTransactionInProgress = true;
	}
	protected static function _CommitTransaction()
	{
		if (!self::$S_bTransactionInProgress)
			throw new \Exception ("Nie ma rozpoczętej transakcji!");
		if (!self::GetConnection()->execute('COMMIT'))
			throw new \Exception ("Błąd przy wysyłaniu transakcji!");
		self::$S_bTransactionInProgress = false;
	}
	protected static function _RollbackTransaction()
	{
		if (!self::$S_bTransactionInProgress)
			throw new \Exception ("Nie ma rozpoczętej transakcji!");
		if (!self::GetConnection()->execute('ROLLBACK'))
			throw new \Exception ("Błąd przy anulowaniu transakcji!");
		self::$S_bTransactionInProgress = false;
	}
	public static function LoadConfig()
	{
		$result = array();
		if (self::GetConnection() === null)
			return $result;
		$table_name = ConfigWrapper::Get('DbPrefix').'setting';
		if (!SourceWrapper::IsModuleActive())
			return $result;
		$sql = 'SELECT `Key`, `Value` FROM `'.$table_name.'`';
		$sql_result = self::GetConnection()->executeS($sql, true, false);
		if ($sql_result)
			foreach ($sql_result as $row)
				$result[$row['Key']] = $row['Value'];
		return $result;
	}
	public static function SaveConfigValue($sKeyName, $sValue)
	{
		$table_name = ConfigWrapper::Get('DbPrefix').'setting';
		$sql = 'SELECT `Id`, `Value` FROM `'.$table_name.'` WHERE `Key` = '.self::_SanitizeValue($sKeyName).' LIMIT 1';
		$sql_result = self::GetConnection()->executeS($sql, true, false);
		if (!$sql_result) //INSERT
			$sql = 'INSERT INTO `'.$table_name.'` (`Key`, `Value`) VALUES ('.self::_SanitizeValue($sKeyName).', '.self::_SanitizeValue($sValue).')';
		else //UPDATE
			$sql = 'UPDATE `'.$table_name.'` SET `Value` = '.self::_SanitizeValue($sValue).' WHERE `Key` = '.self::_SanitizeValue($sKeyName).' LIMIT 1';
		$sql_result = self::GetConnection()->execute($sql);
		if ($sql_result === false)
			throw new Exceptions\LoggedException(self::GetConnection()->getNumberError().': '.self::GetConnection()->getMsgError());
		return true;
	}
	protected static function _ClearIds($aIds)
	{
		$result = array();
		foreach ($aIds as $value)
			if ((int) $value)
				$result[] = (int)$value;
		return $result;
	}

	protected static function _SanitizeValue($mValue)
	{
		if ($mValue !== null)
			return "'".pSQL($mValue)."'";
		else 
			return 'NULL';
	}

    public static function GetDhl24Link()
    {
        $sql = 'SELECT Dhl24Link FROM '._DB_PREFIX_.'dhla_dhluser';
        $dhl24Link = \Db::getInstance()->getValue($sql);
        return strval($dhl24Link);
    }

    public static function GetDhlPsLink()
    {
        $sql = 'SELECT DhlPsLink FROM '._DB_PREFIX_.'dhla_dhluser';
        $dhlPsLink = \Db::getInstance()->getValue($sql);;
        return $dhlPsLink;
    }

    public static function GetCodeCarrier($carrierID)
    {
        $sql = 'SELECT carrier_code FROM ' . _DB_PREFIX_ . 'dhla_carrier_codes' . ' WHERE carrier_id = ' . $carrierID;
        $carrier_code = \Db::getInstance()->getValue($sql);

        return $carrier_code;
    }

    public static function UpdateCarrierId($oldId, $newId)
    {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'dhla_carrier_codes SET carrier_id = ' . (int)$newId . ' WHERE carrier_id = ' . (int)$oldId;
        return \Db::getInstance()->execute($sql);
    }

    public static function GetBillingAccountNumber()
    {
        $sql = 'SELECT BillingAccountNumber FROM '._DB_PREFIX_.'dhla_shipperpreset';
        $dhlSAPNumber = \Db::getInstance()->getValue($sql);;
        return $dhlSAPNumber;
    }

}
?>