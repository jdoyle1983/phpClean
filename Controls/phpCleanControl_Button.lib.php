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



class phpcControl_Button extends phpcControl
{
	public function RespondsToTag()
	{
		return "phpc:button";
	}
	
	public function GetInstance()
	{
		return new phpcControl_Button();
	}
	
	
	public function JsStatePassValByElement()
	{
		return true;
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
	
	
	
	
	public function ParseObject(&$InObject)
	{
		
	}
	
	public function ParseQueryString()
	{
		
	}
	
	public function DrawControl(&$OutputParent, &$InputNode)
	{
		$newNode = $OutputParent->addChild("input");
		$newNode->addAttribute("id", $this->JsStatePassElement());
		$newNode->addAttribute("name", $this->JsStatePassElement());
		$newNode->addAttribute("type", "submit");
		$newNode->addAttribute("value", $this->text == null ? "" : $this->text);
		if($this->onclick != null && $this->onclick != "")
			$newNode->addAttribute("OnClick", $this->GenEventCall("OnClick"));
	}
	
	public function PrepareStates()
	{
		
	}
	
	public function RetrieveStates()
	{
	}
}

?>
