<?
	class Utilities extends IPSModule
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
		
		public function test()
		{
			echo "test";
			
		}
		/*
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
		*/

		
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
	}
?>
