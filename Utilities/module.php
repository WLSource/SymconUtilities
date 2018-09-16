<?
	class SymconUtilities extends IPSModule
	{ 
		public function Create() 
		{
			//Never delete this line!
			parent::Create();
		}
    
		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
		}
		
		
		public function GetVariableIdByName($spezName)
		{
		   $alleVariablen = IPS_GetVariableList();
		   foreach($alleVariablen as $varid)
			{
				$obj = IPS_GetObject($varid);
				if ($spezName == $obj['ObjectName'])
				{
					return $varid;
				}
			}
			return -1;
		}

		public function GetVariableIdByNameByParent($spezName, $spezParentName)
		{
		   $alleVariablen = IPS_GetVariableList();

		   foreach($alleVariablen as $varid)
			{
				$obj = IPS_GetObject($varid);
				if ($spezName == $obj['ObjectName'])
				{
					return $varid;
				}
			}
			return -1;
		}

		public function GetVariableValueByName($spezName)
		{
		   $alleVariablen = IPS_GetVariableList();
		   foreach($alleVariablen as $varid)
			{
				$obj = IPS_GetObject($varid);
				if ($spezName == $obj['ObjectName'])
				{
					return GetValue($varid);
				}
			}
			return -1;
		}

		public function GetVariableByName($spezName)
		{
		   $alleVariablen = IPS_GetVariableList();
		   foreach($alleVariablen as $varid)
			{
				$var = IPS_GetVariable($varid);
				$obj = IPS_GetObject($varid);
				if ($spezName == $obj['ObjectName'])
				{
					$result[] = Array
					(
						"name" => $obj['ObjectName'],
						"id" => $varid,
						"value" => GetValue($varid),
					);
					return $result;
				}
			}
			return false;
		}

		public function GetObjectByName($spezName)
		{
		   $alleObjekte = IPS_GetObjectList();

		   foreach($alleObjekte as $objectId)
			{
				$obj = IPS_GetObject($objectId);
				if ($spezName == $obj['ObjectName'])
				{
					$result[] = Array
					(
						"name" => $obj['ObjectName'],
						"id" => $objectId,
					);
					return $result;
				}
			}
			return false;
		}

		public function GetObjectIdByName($spezName)
		{
		   $alleObjekte = IPS_GetObjectList();

		   foreach($alleObjekte as $objectId)
			{
				$obj = IPS_GetObject($objectId);
				if ($spezName == $obj['ObjectName'])
				{
					return $obj['ObjectID'];
				}
			}
			return false;
		}

		public function GetObjectIdByNameAndParent($spezName, $spezParentName)
		{
		   $alleObjekte = IPS_GetObjectList();
			$parentId = 0;

		   foreach($alleObjekte as $objectId)
			{
				$obj = IPS_GetObject($objectId);

				if (($spezName == $obj['ObjectName']) && ($obj['ParentID'] != 0))
				{
					$parentId = $obj['ParentID'];
					while ($parentId != 0)
					{
					   $higherObj = IPS_GetObject($parentId);
					   if ($higherObj['ObjectName'] == $spezParentName)
					   {
						  echo $objectId;
						  echo " ";
					   }
					   $parentId = $higherObj['ParentID'];
					}
				}
			}
		}
		
		private function CalcProcessValues($spezData)
		{
			// temperature = tempValue / 250 * 40 °C
			$temperature = floatval($spezData->{'DataByte1'}); 
			$temperature = $temperature / 250 * 40;
			
			// humidity = humValue / 250 * 100 %
			$humidity = floatval($spezData->{'DataByte2'}); 
			$humidity = $humidity / 250 * 100;
			
			// goldCapVoltage = voltageValue / 255 * 1,8V * 4 - usually DataByte3 is not used in enocean standard!
			$goldCapVoltage = floatval($spezData->{'DataByte3'});
			$goldCapVoltage = $goldCapVoltage / 255 * 1.8 * 4;
			
			// Calc dewpoint and abs. humidity with Magnus coefficients
			$c1 = 6.1078; 							// hPa
			$c2 = 17.08085;                  // °C
			$c3 = 234.175;                   // °C
			$mw = 18.016;                    // g/mol
			$uniGasConstant = 8.3144598;    	// J/(mol*K)
			$tempInK = $temperature + 273.15;
			// Calculate saturationVaporPressure in hPa
			$saturationVaporPressure = $c1 * exp(($c2 * $temperature) / ($c3 + $temperature));
			// Calculate vaporPressure in hPa
			$vaporPressure = $saturationVaporPressure *  $humidity / 100;
			// Calculate dewpoint in °C
			$dewpoint = (log($vaporPressure / $c1) * $c3) / ($c2 - log($saturationVaporPressure / $c1));
			// Calculate absolute humidity in g/m³
			$absHum = $mw / $uniGasConstant * $vaporPressure / $tempInK * 100;
			
			// Write calculated values to registered variables
			$this->SetValueFloat("TMP", $temperature);
			$this->SetValueFloat("HUM", $humidity);
			$this->SetValueFloat("VLT", $goldCapVoltage);
			$this->SetValueFloat("AHUM", $absHum);
			$this->SetValueFloat("DEW", $dewpoint);
		}		
		
		public function CalcDewpoint($spezTemperatur, $spezRelHumidity)
		{
			// Calc dewpoint and abs. humidity with Magnus coefficients
			$c1 = 6.1078; 							// hPa
			$c2 = 17.08085;                  // °C
			$c3 = 234.175;                   // °C
			$mw = 18.016;                    // g/mol
			$uniGasConstant = 8.3144598;    	// J/(mol*K)
			$tempInK = $spezTemperatur + 273.15;
			// Calculate saturationVaporPressure in hPa
			$saturationVaporPressure = $c1 * exp(($c2 * $spezTemperatur) / ($c3 + $spezTemperatur));
			// Calculate vaporPressure in hPa
			$vaporPressure = $saturationVaporPressure *  $spezRelHumidity / 100;
			// Calculate dewpoint in °C
			$dewpoint = (log($vaporPressure / $c1) * $c3) / ($c2 - log($saturationVaporPressure / $c1));
			
			return $dewpoint;
		}
		
		public function CalcAbsHumidity($spezTemperatur, $spezRelHumidity)
		{
			// Calc dewpoint and abs. humidity with Magnus coefficients
			$c1 = 6.1078; 							// hPa
			$c2 = 17.08085;                  // °C
			$c3 = 234.175;                   // °C
			$mw = 18.016;                    // g/mol
			$uniGasConstant = 8.3144598;    	// J/(mol*K)
			$tempInK = $spezTemperatur + 273.15;
			// Calculate saturationVaporPressure in hPa
			$saturationVaporPressure = $c1 * exp(($c2 * $spezTemperatur) / ($c3 + $spezTemperatur));
			// Calculate vaporPressure in hPa
			$vaporPressure = $saturationVaporPressure *  $spezRelHumidity / 100;
			// Calculate dewpoint in °C
			$dewpoint = (log($vaporPressure / $c1) * $c3) / ($c2 - log($saturationVaporPressure / $c1));
			// Calculate absolute humidity in g/m³
			$absHum = $mw / $uniGasConstant * $vaporPressure / $tempInK * 100;
			
			return $absHum;
		}

		
		protected function SendDebug($Message, $Data, $Format)
		{
			if (is_array($Data))
			{
			    foreach ($Data as $Key => $DebugData)
			    {
						$this->SendDebug($Message . ":" . $Key, $DebugData, 0);
			    }
			}
			else if (is_object($Data))
			{
			    foreach ($Data as $Key => $DebugData)
			    {
						$this->SendDebug($Message . "." . $Key, $DebugData, 0);
			    }
			}
			else
			{
			    parent::SendDebug($Message, $Data, $Format);
			}
		} 
		
		// Removes all Links from under spezParentId
		public function DeleteAllLinks($spezParentId)
		{
			$catChildIds = IPS_GetChildrenIDs($spezParentId);
			for($i = 0; $i < count($catChildIds); $i++)
			{
			   // Check if the id belongs to a link
			   if (IPS_LinkExists($catChildIds[$i]))
			   {
				  IPS_DeleteLink($catChildIds[$i]);
			   }
			}
		}
		
		public function DeleteAllVariablesInInstance($spezInstanceId)
		{
			// First, delete all varialbes
			$instanceChildIds = IPS_GetChildrenIDs($spezInstanceId);
			for($i = 0; $i < count($instanceChildIds); $i++)
			{
			   // Check if the id belongs to a variable
			   if (IPS_VariableExists($instanceChildIds[$i]))
			   {
				  IPS_DeleteVariable($instanceChildIds[$i]);
			   }
			}
		}
		
		public function AddTextToParent($spezText, $spezName, $spezInstanceId)
		{
			$StatusTextId = IPS_CreateVariable(3);
			IPS_SetParent($StatusTextId, $spezInstanceId);
			IPS_SetName($StatusTextId, $spezName);
			SetValueString($StatusTextId, $spezText);
		}
		
		public function AddLinkToParent($spezText, $spezParentId, $spezTargetId)
		{
			// Anlegen eines neuen Links
			$linkID = IPS_CreateLink();             // Link anlegen
			IPS_SetName($linkID, $spezText); 		// Link benennen
			IPS_SetParent($linkID, $spezParentId); // Link einsortieren unter dem Objekt mit der ID "12345"
			IPS_SetLinkTargetID($linkID, $spezTargetId);    // Link verknüpfen
		
		}
		
		public function AddDummyModuleInstance($spezText, $spezParentId)
		{
			// Create Status Instance als DummyModul
			$InstanceId = IPS_GetInstanceIDByName($spezText, $spezParentId);
			if ($InstanceId == 0)
			{
				$InstanceId = IPS_CreateInstance("{485D0419-BE97-4548-AA9C-C083EB82E61E}");
				IPS_SetName($InstanceId, $spezText);
				IPS_SetParent($InstanceId, $spezParentId);
			}
			return $InstanceId;
		}
		


		public function CreateEventTrigger($EventName, $VarID, $ParentID, $EventTyp) 
		{
			$eid = @IPS_GetEventIDByName($EventName, $ParentID);
			if (is_numeric($eid) == false) {
				$eid = IPS_CreateEvent(0);
				IPS_SetEventTrigger($eid, $EventTyp, $VarID);
				IPS_SetParent($eid, $ParentID);
				IPS_SetName($eid, $EventName);
				IPS_SetEventActive($eid, true);
			}
		}
	
	}
?>
