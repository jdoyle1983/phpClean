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



class phpcControl_FileUpload extends phpcControl
{
	public function RespondsToTag()
	{
		return "phpc:fileupload";
	}
	
	public function GetInstance()
	{
		return new phpcControl_FileUpload();
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
		$this->HasFile = false;
		$this->TempPath = "";
		$this->Error = "";
		$this->Name = "";
		$this->Size = 0;
		$this->Type = "";
		
		if($_FILES[$this->JsStatePassElement()]["error"] > 0)
			$this->Error = $_FILES[$this->JsStatePassElement()]["error"];
		else
		{
			$this->HasFile = true;
			$this->TempPath = $_FILES[$this->JsStatePassElement()]["tmp_name"];
			$this->Name = $_FILES[$this->JsStatePassElement()]["name"];
			$this->Size = $_FILES[$this->JsStatePassElement()]["size"];
			$this->Type = $_FILES[$this->JsStatePassElement()]["type"];
			
		}
	}
	
	public function DrawControl(&$OutputParent, &$InputNode)
	{
		$input = $OutputParent->addChild("input");
		$input->addAttribute("type", "file");
		$input->addAttribute("id", $this->JsStatePassElement());
		$input->addAttribute("name", $this->JsStatePassElement());
	}
	
	public function PrepareStates()
	{
		
	}
	
	public function RetrieveStates()
	{
		
	}
}

?>