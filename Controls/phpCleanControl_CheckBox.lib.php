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



class phpcControl_CheckBox extends phpcControl
{
	public function RespondsToTag()
	{
		return "phpc:checkbox";
	}
	
	public function GetInstance()
	{
		return new phpcControl_CheckBox();
	}
	
	
	
	public function JsStatePassValByElement()
	{
		return false;
	}
	
	public function JsStatePassElement()
	{
		return "phpClean_Control_". $this->id;
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
		return true;
	}
	
	public function JsStateCustomScript()
	{
		$fCall1 = "        phpClean_AddFieldByValue( Form, '" . $this->id . "','" . $this->id . "','checked');";
		$fCall2 = "        phpClean_AddFieldByValue( Form, '" . $this->id . "','" . $this->id . "','');";
		$rValue = "        if(" . $this->JsStatePassElement() . ".checked == 1)\n";
		$rValue .= "                " . $fCall1 . "\n";
		$rValue .= "        else\n";
		$rValue .= "                " . $fCall2 . "\n";
		return $rValue;
	}
	
	
	
	
	public function AddJavaScript()
	{
		return "";
	}
	
	
	
	
	public function ParseObject(&$InObject)
	{
		if($this->checked == null)
			$this->SetStateProperty("checked", false);
		else
		{
			if(phpcUtils::IsTruVal($this->checked))
				$this->SetStateProperty("checked", true);
			else
				$this->SetStateProperty("checked", false);
		}
	}
	
	public function ParseQueryString()
	{
		if( $this->Text != null && $this->Text != "" )
			$this->SetStateProperty("checked", true);
		else
			$this->SetStateProperty("checked", false);
	}
	
	public function DrawControl(&$OutputParent, &$InputNode)
	{
		$newNode = $OutputParent->addChild("input");
		$newNode->addAttribute("type", "checkbox");
		$newNode->addAttribute("id", $this->JsStatePassElement());
		$newNode->addAttribute("name", $this->JsStatePassElement());
		$newNode->addAttribute("value", "checked");
		if($this->checked)
			$newNode->addAttribute("checked", "checked");
		if($this->oncheckchanged != null && $this->oncheckchanged != "")
			$newNode->addAttribute("OnClick", $this->GenEventCall("OnCheckChanged"));
	}
	
	public function PrepareStates()
	{
		
	}
	
	public function RetrieveStates()
	{
	}
}

?>
