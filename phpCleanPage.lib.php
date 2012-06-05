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




class phpcPage
{
	var $HtmlFile;
	var $RegisteredControls;
	var $PageControls;
	var $PageVariables;
	
	var $InputTree;
	var $OutputTree;
	
	function __construct( $markupFile )
	{
		//Register Base Controls Here
		$this->RegisterControl(new phpcControl_Button());
		$this->RegisterControl(new phpcControl_CheckBox());
		$this->RegisterControl(new phpcControl_DataResult());
		$this->RegisterControl(new phpcControl_DropDownList());
		$this->RegisterControl(new phpcControl_FileUpload());
		$this->RegisterControl(new phpcControl_Hidden());
		$this->RegisterControl(new phpcControl_Image());
		$this->RegisterControl(new phpcControl_ImageButton());
		$this->RegisterControl(new phpcControl_Label());
		$this->RegisterControl(new phpcControl_ListView());
		$this->RegisterControl(new phpcControl_Panel());
		$this->RegisterControl(new phpcControl_RadioButton());
		$this->RegisterControl(new phpcControl_TextBox());
		
		$parser = new phpcXmlParser($markupFile);
		$this->InputTree = $parser->ResultTree[0]->Children[0];
		$this->OutputTree = new phpcXml( "ROOT", null );
		
		$this->Parse();
		
		//$this->ParseNodes($this->InputTree, $this->OutputTree);
		//$this->OutputTree->baseOutput();
	}
	
	function _GetControlByName($name)
	{
		for($i = 0; $i < count($this->PageControls); $i++)
		{
			if(strtolower($this->PageControls[$i]['Control']->GetStateProperty("id")) == strtolower($name))
				return $this->PageControls[$i]['Control'];
		}
		return null;
	}
	
	function __get($name)
	{
		for($i = 0; $i < count($this->PageControls); $i++)
			if(strtolower($this->PageControls[$i]['Control']->GetStateProperty("id")) == strtolower($name))
				return $this->PageControls[$i]['Control'];
		for($i = 0; $i < count($this->PageVariables); $i++)
			if(strtolower($this->PageVariables[$i]['Name']) == strtolower($name))
				return $this->PageVariables[$i]['Object'];
		return null;
	}
	
	function RegisterControl($newControl)
	{
		$this->RegisteredControls[] = $newControl;
	}
	
	function PrepareJavascript()
	{
		$rValue = "";
		$sourcepath = dirname(__FILE__) . "/phpClean.js";
		$handle = @fopen($sourcepath, "r");
		if($handle)
		{
			while(($buffer = fgets($handle)) !== false)
				$rValue .= $buffer;
			fclose($handle);
		}
		$rValue .= "\n\n";
		$rValue .= "function phpClean_PrepareControls( Form )";
		$rValue .= "{\n";
		for($i = 0; $i < count($this->PageControls); $i++)
		{
			$obj = $this->PageControls[$i]['Control'];
			if($obj->JsStatePassValByElement())
				$rValue .= "        phpClean_AddFieldByElement( Form, '" . $obj->id . "','" . $obj->id . "','" . $obj->JsStatePassElement() . "');\n";
			else if($obj->JsStatePassValByValue())
			{
				$valToPass = $obj->JsStatePassValue();
				$rValue .= "        phpClean_AddFieldByValue( Form, '" . $obj->id . "','" . $obj->id . "','" . $valToPass . "');\n";
			}
			else if($obj->JsStateCustom())
			{
				$rValue .= $obj->JsStateCustomScript();
			}
		}
		$ArKeys = array_keys( $_GET );
		for($i = 0; $i < count( $_GET ); $i++)
			$rValue .= "        phpClean_AddFieldByValue( Form, '" . $ArKeys[$i] . "','" . $ArKeys[$i] . "','" . $_GET[$ArKeys[$i]] . "');\n";
		$rValue .= "}\n\n\n";
		
		for($i = 0; $i < count($this->PageControls); $i++)
		{
			$inScript = $this->PageControls[$i]['Control']->_AddJavaScript();
			if(trim($inScript) != "")
				$rValue .= $inScript . "\n\n";
		}
			
		return $rValue;
	}
	
	function ParseNodes(&$InputObject)
	{
		for($i = 0; $i < count($this->RegisteredControls); $i++)
		{
			if(strtolower($this->RegisteredControls[$i]->RespondsToTag()) == strtolower($InputObject->Name))
			{
				$newControl = $this->RegisteredControls[$i]->GetInstance();
				$newControl->_ParseObject($InputObject);
				$newControl->_SetPage($this);
				$this->PageControls[] = array( 'Name' => $newControl->id, 'Control' => $newControl );
			}
		}
		
		for($i = 0; $i < count($InputObject->Children); $i++)
			$this->ParseNodes($InputObject->Children[$i]);
	}
	
	function DrawNodes( &$InputNode, &$OutputNode )
	{
		$parseObject = false;
		for($i = 0; $i < count($this->RegisteredControls); $i++)
		{
			if(strtolower($this->RegisteredControls[$i]->RespondsToTag()) == strtolower($InputNode->Name))
			{
				$parseObject = true;
				$ControlId = $InputNode->getAttribute("id");
				$Control = $this->_GetControlByName($ControlId);
				if($Control != null)
					$Control->_DrawControl( $OutputNode, $InputNode );
			}
		}
		
		if(!$parseObject)
		{
			$newNode = $OutputNode->addChildObject( $InputNode->copyNode( $OutputNode ) );
			
			if(strtolower($InputNode->Name) == "head")
			{
				$jsNode = $newNode->addChild( "script" );
				$jsNode->Text = $this->PrepareJavascript();
				$jsNode->addAttribute( "language", "JavaScript" );
			}
			else if(strtolower($InputNode->Name) == "body")
			{
				$vsNode = $newNode->addChild( "input" );
				$vsNode->addAttribute( "type", "hidden" );
				$vsNode->addAttribute( "id", "ViewState" );
				$vsNode->addAttribute( "value", $this->ViewState->GetViewState() );
			}
			
			for($i = 0; $i < count($InputNode->Children); $i++)
				$this->DrawNodes( $InputNode->Children[$i], $newNode );
		}
	}
	
	function Parse()
	{
		$this->ParseNodes($this->InputTree);
		
		$this->PageVariables[] = array('Name' => "ViewState", 'Object' => new phpcViewState());
		if( isset($_REQUEST['ViewState']) )
		{
			$this->PageVariables[] = array('Name' => "IsPostBack", 'Object' => true);
			$this->ViewState->RestoreViewState( $_REQUEST['ViewState'] );
			for( $i = 0; $i < count($this->PageControls); $i++ )
			{
				$this->PageControls[$i]['Control']->_LoadFromViewState( $this->ViewState );
				$this->PageControls[$i]['Control']->_ParseQueryString();
			}
		}
		else
			$this->PageVariables[] = array('Name' => "IsPostBack", 'Object' => false);
			
		if(method_exists($this, "Page_Load"))
			$this->Page_Load();
			
		if( isset($_REQUEST['Event']) && isset($_REQUEST['EventItem']) )
		{
			$Event = $_REQUEST['Event'];
			$EventItem = $_REQUEST['EventItem'];
			$Control = $this->_GetControlByName($EventItem);
			if($Control != null)
			{
				if( $Event == "OnClick" )
					call_user_func(array($this, $Control->OnClick), $Control);
				else if( $Event == "OnSelectedIndexChanged" )
					call_user_func(array($this, $Control->OnSelectedIndexChanged), $Control);
				else if( $Event == "OnCheckChanged" )
					call_user_func(array($this, $Control->OnCheckChanged), $Control);
			}
		}
		
		for($i = 0; $i < count($this->PageControls); $i++)
			$this->PageControls[$i]['Control']->_SaveToViewState( $this->ViewState );
			
		if(method_exists($this, "PreRender"))
			$this->PreRender();
		
		$this->DrawNodes( $this->InputTree, $this->OutputTree );
		
		if(method_exists($this, "PostRender"))
			$this->PostRender();
			
		$this->OutputTree->baseOutput();
	}
}

?>
