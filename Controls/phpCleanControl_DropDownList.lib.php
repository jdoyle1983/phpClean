<?php

//	phpClean
//		Original Author: Jason Doyle
//
//    	This library is free software; you can redistribute it and/or
//    	modify it under the terms of the GNU Lesser General Public
//    	License as published by the Free Software Foundation; either
//    	version 2.1 of the License, or (at your option) any later version.
//
//    	This library is distributed in the hope that it will be useful,
//    	but WITHOUT ANY WARRANTY; without even the implied warranty of
//    	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
//    	Lesser General Public License for more details.
//
//    	You should have received a copy of the GNU Lesser General Public
//    	License along with this library; if not, write to the Free Software
//    	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
//    
//    	Project Maintained By Jason Doyle
//    	Contact: 
//    				email: 			jdoyle1983@gmail.com


//#BEGIN_EXPORT
class phpcControl_DropDownList extends phpcControl
{
	var $Items;
	var $ItemCount;
	
	public function RespondsToTag()
	{
		return "phpc:dropdownlist";
	}
	
	public function GetInstance()
	{
		return new phpcControl_DropDownList();
	}
	
	
	public function JsStatePassValByElement()
	{
		return true;
	}
	
	public function JsStatePassElement()
	{
		return "phpClean_Control_" . $this->id;
	}
	
	public function JsStatePassValByValue()
	{
		return false;
	}
	
	public function JsStatePassValue()
	{
		return "";
	}
	
	public function JsStateCustom()
	{
		return false;
	}
	
	public function JsStateCustomScript()
	{
		return "";
	}
	
	
	
	
	public function AddJavaScript()
	{
		return "";
	}
	
	
	public function JsSupportsAjax()
	{
		return false;
	}
	
	public function JsAjaxUpdate()
	{
		return "";
	}
	
	
	function SelectedName()
	{
		if($this->SelectedIndex != -1)
			return $this->Items[$this->SelectedIndex]['Name'];
		else
			return "";
	}
	
	function SelectedValue()
	{
		if($this->SelectedIndex != -1)
			return $this->Items[$this->SelectedIndex]['Value'];
		else
			return "";
	}
	
	function SetSelectedValue( $val )
	{
		for($i = 0; $i < $this->ItemCount; $i++)
		{
			if($this->Items[$i]['Value'] == $val)
			{
				$this->SetStateProperty("SelectedIndex", $i);
			}
		}
	}
	
	function SetSelectedIndex( $idx )
	{
		if($idx > -1 && $idx < $this->ItemCount)
			$this->SetStateProperty("SelectedIndex", $idx);
	}
	
	function AddListItem( $ItemName, $ItemValue )
	{
		$this->Items[] = array('Name' => $ItemName, 'Value' => $ItemValue);
		$this->ItemCount++;
	}
	
	function ClearItems()
	{
		$this->ItemCount = 0;
	}
	
	function GetItemName( $idx )
	{
		return $this->Items[$idx]['Name'];
	}
	
	function GetItemValue( $idx )
	{
		return $this->Items[$idx]['Value'];
	}
	
	public function ParseObject(&$InObject)
	{
		$this->SetStateProperty("SelectedIndex", -1);
		
		$ICounter = 0;
		for($i = 0; $i < count($InObject->Children); $i++)
		{
			$tNode = $InObject->Children[$i];
			if(strtolower($tNode->Name) == "listitem")
			{
				$iName = $tNode->getAttribute("Name");
				$iValue = $tNode->getAttribute("Value");
				if(phpcUtils::IsTrueVal($tNode->getAttribute("Selected")))
					$this->SetStateProperty("SelectedIndex", $ICounter);
				$this->AddListItem( $iName, $iValue );
				$ICounter++;
			}
		}
	}
	
	public function ParseQueryString()
	{
		if($this->text != null && $this->text != "")
			$this->SetSelectedValue($this->text);
	}
	
	public function DrawControl(&$OutputParent, &$InputNode)
	{
		$select = $OutputParent->addChild("select");
		$select->addAttribute( "id", $this->JsStatePassElement() );
		$select->addAttribute( "name", $this->JsStatePassElement() );
		if( $this->onselectedindexchanged != null && $this->onselectedindexchanged != "" )
			$select->addAttribute( "OnChange", $this->GenEventCall("OnSelectedIndexChanged") );
		for($i = 0; $i < $this->ItemCount; $i++)
		{
			$optiontext = $select->addChild("option");
			$optiontext->Text = $this->Items[$i]['Name'];
			$optiontext->addAttribute("value", $this->Items[$i]['Value']);
			if($i == $this->SelectedIndex)
				$optiontext->addAttribute("selected", "selected");
		}
	}
	
	public function PrepareStates()
	{
		$this->SetStateProperty("ItemsCount", $this->ItemCount);
		for($i = 0; $i < $this->ItemCount; $i++)
		{
			$this->SetStateProperty("N" . $i, $this->Items[$i]['Name']);
			$this->SetStateProperty("V" . $i, $this->Items[$i]['Value']);
		}
	}
	
	public function RetrieveStates()
	{
		if($this->GetStateProperty("ItemsCount") != null)
		{
			$this->ItemCount = (int)$this->GetStateProperty("ItemsCount");
			for($i = 0; $i < $this->ItemCount; $i++)
				$this->Items[$i] = array('Name' => $this->GetStateProperty("N" . $i), 'Value' => $this->GetStateProperty("V" . $i));
		}
	}
}
//#END_EXPORT

?>