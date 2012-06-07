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
	var $AppRoot;
	var $HtmlFile;
	var $RegisteredControls;
	var $PageControls;
	var $PageVariables;
	
	var $InputTree;
	var $OutputTree;
	
	var $AsyncOperation;
	
	var $IsAsyncPage;
	
	function __construct( $markupFile, $appRoot, $AsyncPage )
	{
		$this->IsAsyncPage = $AsyncPage;
	
		//Register Base Controls Here
		$this->RegisterControl(new phpcControl_Button());
		$this->RegisterControl(new phpcControl_Calendar());
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
		$this->AppRoot = $appRoot;
		$this->InputTree = $parser->ResultTree[0]->Children[0];
		$this->OutputTree = new phpcXml( "ROOT", null );
		
		//Check For Async Operation
		if(isset($_REQUEST['__PHPCLEAN_ASYNC_OPERATION__']))
			$this->AsyncOperation = true;
		else
			$this->AsyncOperation = false;
		
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
		$rValue = "\n\n";
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
		
		$rValue .= "function phpClean_CleanupControls( Form )";
		$rValue .= "{\n";
		for($i = 0; $i < count($this->PageControls); $i++)
		{
			$obj = $this->PageControls[$i]['Control'];
			$rValue .= "        phpClean_RemoveFieldByElement( Form, '" . $obj->id . "' );\n";
		}
		$rValue .= "}\n\n\n";
		
		for($i = 0; $i < count($this->PageControls); $i++)
		{
			$inScript = $this->PageControls[$i]['Control']->_AddJavaScript();
			if(trim($inScript) != "")
				$rValue .= $inScript . "\n\n";
		}
		
		$rValue .= "function phpClean_AsyncUpdate(Keys,Values,Count)\n";
		$rValue .= "{\n";
		for($i = 0; $i < count($this->PageControls); $i++)
		{
			$inScript = $this->PageControls[$i]['Control']->JsAjaxUpdate();
			if(trim($inScript) != "")
				$rValue .= $inScript . "\n";
		}
		$rValue .= "}\n\n";
		
			
		return $rValue;
	}
	
	function ParseNodes(&$InputObject)
	{
		for($i = 0; $i < count($this->RegisteredControls); $i++)
		{
			if(strtolower($this->RegisteredControls[$i]->RespondsToTag()) == strtolower($InputObject->Name))
			{
				$newControl = $this->RegisteredControls[$i]->GetInstance();
				$newControl->_SetPage($this);
				$newControl->_ParseObject($InputObject);
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
				$jqucNode = $newNode->addChild( "link" );
				$jqucNode->addAttribute( "href", $this->AppRoot . "css/jquery-ui-1.8.20.custom.css" );
				$jqucNode->addAttribute( "rel", "stylesheet" );
				$jqucNode->addAttribute( "type", "text/css" );
				
				$jqNode = $newNode->addChild( "script" );
				$jqNode->addAttribute( "src", $this->AppRoot . "js/jquery-1.7.2.min.js" );
				$jqNode->Text = "//jQuery";
				
				$jquNode = $newNode->addChild( "script" );
				$jquNode->addAttribute( "src", $this->AppRoot . "js/jquery-ui-1.8.20.custom.min.js" );
				$jquNode->Text = "//jQuery UI";
				
				if($this->IsAsyncPage)
				{ 
					$jfNode = $newNode->addChild( "script" );
					$jfNode->addAttribute( "src", $this->AppRoot . "js/jquery.form.js" );
					$jfNode->Text = "//jQuery Form";
				}
				
				$pcNode = $newNode->addChild( "script" );
				$pcNode->addAttribute( "src", $this->AppRoot . "js/phpClean.js" );
				$pcNode->Text = "//phpClean";
				
				$jsNode = $newNode->addChild( "script" );
				$jsNode->Text = $this->PrepareJavascript();
			}
			else if(strtolower($InputNode->Name) == "body")
			{
				$formNode = $newNode->addChild("form");
				$formNode->addAttribute("id", "phpCleanBaseForm");
				$formNode->addAttribute("name", "phpCleanBaseForm");
				$formNode->addAttribute("enctype", "multipart/form-data");
				$formNode->addAttribute("method", "POST");
				$formNode->addAttribute("action", phpcUtils::SelfUrl());
				
				$newNode = $formNode;
				
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
		if( isset($_REQUEST['PostBackViewState']) )
		{
			$this->PageVariables[] = array('Name' => "IsPostBack", 'Object' => true);
			$this->ViewState->RestoreViewState( $_REQUEST['PostBackViewState'] );
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
		
		if($this->AsyncOperation == false)
		{
			if(method_exists($this, "PreRender"))
				$this->PreRender();
		
			$this->DrawNodes( $this->InputTree, $this->OutputTree );
		
			if(method_exists($this, "PostRender"))
				$this->PostRender();
			
			$this->OutputTree->baseOutput();
		}
		else
		{
			$returnData = "";
			for($i = 0; $i < count($this->PageControls); $i++)
			{
				$obj = $this->PageControls[$i]['Control'];
				$returnData .= $obj->id . "@@#";
				for($e = 0; $e < count($obj->Properties); $e++)
					$returnData .= strtolower($obj->Properties[$e]['Name']) . "@@*" . $obj->Properties[$e]['Value'] . "@@$";
				$returnData .= "@@%";
			}
			$returnData .= "ViewState@@#Value@@*" . $this->ViewState->GetViewState() . "@@$@@%";
			$returnData .= "ReceivedData@@#Value@@*";
			
			echo $returnData;
			print_r($_REQUEST);
			echo "@@$@@%";
			
			//echo $returnData;
		}
	}
}

?>
