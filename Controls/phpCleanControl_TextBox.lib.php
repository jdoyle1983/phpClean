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



class phpcControl_TextBox extends phpcControl
{
	public function RespondsToTag()
	{
		return "phpc:textbox";
	}
	
	public function GetInstance()
	{
		return new phpcControl_TextBox();
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
		$input = $OutputParent->addChild("input");
		//If password mode, set native input type to password instead of text
		if($this->textmode != null && strtolower($this->textmode) == "password")
			$input->addAttribute("type", "password");
		else
			$input->addAttribute("type", "text");
		$input->addAttribute("id", $this->JsStatePassElement());
		$input->addAttribute("name", $this->JsStatePassElement());
		$input->addAttribute("value", $this->text == null ? "" : $this->text);
		//If a width was supplied, emit it to the native input
		if($this->width != null)
			$input->addAttribute("size", $this->width);
	}
	
	public function PrepareStates()
	{
		
	}
	
	public function RetrieveStates()
	{
		
	}
}

?>