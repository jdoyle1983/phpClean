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
class phpcControl_Panel extends phpcControl
{
	public function RespondsToTag()
	{
		return "phpc:panel";
	}
	
	public function GetInstance()
	{
		return new phpcControl_Panel();
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
	
	
	public function JsSupportsAjax()
	{
		return false;
	}
	
	public function JsAjaxUpdate()
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
		$input->addAttribute("type","hidden");
		$input->addAttribute("id", $this->JsStatePassElement());
		$input->addAttribute("name", $this->JsStatePassElement());
		$input->addAttribute("value", $this->text == null ? "" : $this->text);
		
		//Send drawing operations back to the system for children of this control
		$span = $OutputParent->addChild("span");
		for($i = 0; $i < count($InputNode->Children); $i++)
			$this->phpcPage->DrawNodes($InputNode->Children[$i], $span);
	}
	
	public function PrepareStates()
	{
		
	}
	
	public function RetrieveStates()
	{
		
	}
}
//#END_EXPORT

?>