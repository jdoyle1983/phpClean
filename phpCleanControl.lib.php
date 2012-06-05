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



abstract class phpcControl
{
	//The Page This Control Belongs To
	var $phpcPage;
	
	//Array of Properties
	var $Properties;
	

	function _ParseObject(&$InObject)
	{	
		for($i = 0; $i < count($InObject->Attributes); $i++)
			$this->SetStateProperty($InObject->Attributes[$i]['Name'], $InObject->Attributes[$i]['Value']);
		
		if($this->visible == null)
			$this->SetStateProperty("visible", true);
		else
		{
			if(phpcUtils::IsFalseVal($this->visible))
				$this->SetStateProperty("visible", false);
			else
				$this->SetStateProperty("visible", true);
		}
			
		$this->ParseObject($InObject);
	}
	
	function __get($name)
	{
		return $this->GetStateProperty($name);
	}
	
	function __set($name, $value)
	{
		$this->SetStateProperty($name, $value);
	}
	
	function _SetPage($parentPage)
	{
		$this->phpcPage = $parentPage;
	}
	
	function DoCallBack()
	{
		$allargs = func_get_args();
		$callback = $allargs[0];
		$passargs = array();
		for($i = 1; $i < count($allargs); $i++)
			$passargs[] = $allargs[$i];
		call_user_func_array(array($this->phpcPage, $callback), $passargs);
	}
	
	function GenEventCall($event)
	{
		return "phpClean_FireEvent('" . $this->id . "','" . $event . "');";
	}
	
	function SetStateProperty($name, $value)
	{
		$wasFound = false;
		for($i = 0; $i < count($this->Properties); $i++)
		{
			if(strtolower($this->Properties[$i]['Name']) == strtolower($name))
			{
				$this->Properties[$i]['Value'] = $value;
				$wasFound = true;
			}
		}
		
		if(!$wasFound)
			$this->Properties[] = array( 'Name' => strtolower($name), 'Value' => $value );
	}
	
	function GetStateProperty($name)
	{
		for($i = 0; $i < count($this->Properties); $i++)
			if(strtolower($this->Properties[$i]['Name']) == strtolower($name))
				return $this->Properties[$i]['Value'];
		return null;
	}
	
	function _SaveToViewState( $viewState )
	{	
		$this->PrepareStates();	
		for($i = 0; $i < count($this->Properties); $i++)
			$viewState->AddObject( $this->id, $this->Properties[$i]['Name'], $this->Properties[$i]['Value']);
	}
	
	function _LoadFromViewState( $viewState )
	{
		$keys = $viewState->KeysForObject( $this->id );
		for($i = 0; $i < count($keys); $i++)
			$this->SetStateProperty( $keys[$i], $viewState->KeyValue( $this->id, $keys[$i] ) );
		$this->RetrieveStates();
	}
	
	function _AddJavaScript()
	{
		if(phpcUtils::IsTrueVal($this->visible))
			return $this->AddJavaScript();
		return "";
	}
	
	function _DrawControl( &$OutputParent, &$InputNode )
	{
		if(phpcUtils::IsTrueVal($this->visible))
			$this->DrawControl( $OutputParent, $InputNode );
	}
	
	function _ParseQueryString()
	{
		if( isset( $_REQUEST[ $this->id ]) )
			$this->SetStateProperty("text", $_REQUEST[ $this->id ]);
		$this->ParseQueryString();
	}

	//Return
	abstract public function RespondsToTag();	
	abstract public function ParseObject(&$InObject);
	abstract public function ParseQueryString();
	abstract public function AddJavaScript();
	abstract public function DrawControl(&$OutputParent, &$InputNode);
	abstract public function JsStatePassValByElement();
	abstract public function JsStatePassElement();
	abstract public function JsStatePassValByValue();
	abstract public function JsStatePassValue();
	abstract public function JsStateCustom();
	abstract public function JsStateCustomScript();
	abstract public function GetInstance();
	abstract public function PrepareStates();
	abstract public function RetrieveStates();
}

?>
