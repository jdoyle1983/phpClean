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



class phpcControl_ListView extends phpcControl
{
	var $EmptyDataTemplate;
	var $LayoutTemplate;
	var $ItemTemplate;
	var $AltItemTemplate;
	
	var $DataConnection;
	var $ResultData;
	var $CurrentRowIndex;
	
	public function RespondsToTag()
	{
		return "phpc:listview";
	}
	
	public function GetInstance()
	{
		return new phpcControl_ListView();
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
	
	
	function DataSource( $Source )
	{
		$this->DataConnection = $Source;
	}
	
	function DataBind()
	{
		if( isset( $this->DataConnection ) )
		{
			$this->DataConnection->Execute();
			$this->ResultData['RowCount'] = $this->DataConnection->NumRows();
			$this->ResultData['FieldCount'] = $this->DataConnection->NumFields();
			for($i = 0; $i < $this->ResultData['FieldCount']; $i++)
				$this->ResultData['FieldName'][$i] = $this->DataConnection->GetFieldName($i);
			for($i = 0; $i < $this->ResultData['RowCount']; $i++)
				for($e = 0; $e < $this->ResultData['FieldCount']; $e++)
					$this->ResultData['Data'][$i][$e] = $this->DataConnection->GetRowFieldValue($i, $this->DataConnection->GetFieldName($e));
		}
	}
	
	
	public function ParseObject(&$InObject)
	{
		$this->EmptyDataTemplate = null;
		$this->LayoutTemplate = null;
		$this->ItemTemplate = null;
		$this->AltItemTemplate = null;
		
		for($i = 0; $i < count($InObject->Children); $i++)
		{
			$tObject = $InObject->Children[$i];
			if(strtolower($tObject->Name) == "emptydatatemplate")
				$this->EmptyDataTemplate = $tObject;
			else if(strtolower($tObject->Name) == "layouttemplate")
				$this->LayoutTemplate = $tObject;
			else if(strtolower($tObject->Name) == "itemtemplate")
				$this->ItemTemplate = $tObject;
			else if(strtolower($tObject->Name) == "altitemtemplate")
				$this->AltItemTemplate = $tObject;
		}
	}
	
	public function ParseQueryString()
	{
		
	}
	
	function DrawItemTemplate(&$OutputParent, &$SourceNode)
	{
		if(count($SourceNode->Children) > 0)
		{
			for($i = 0; $i < count($SourceNode->Children); $i++)
			{
				$cNode = $SourceNode->Children[$i];
				if(strtolower($cNode->Name) == "eval")
				{
					$column = $cNode->getAttribute("source");
					for($e = 0; $e < count($this->ResultData['FieldName']); $e++)
					{
						if($this->ResultData['FieldName'][$e] == $column)
						{
							$span = $OutputParent->addChild("span");
							$span->Text = $this->ResultData['Data'][$this->CurrentRowIndex][$e];
						}
					}
				}
				else
				{
					$newNode = $OutputParent->addChild($cNode->Name);
					$newNode->Text = $cNode->Text;
					for($e = 0; $e < count($cNode->Attributes); $e++)
						$newNode->addAttribute( $cNode->Attributes[$e]['Name'], $cNode->Attributes[$e]['Value'] );
					$this->DrawItemTemplate( $newNode, $cNode );
				}
			}
		}
	}
	
	function DrawLayoutTemplate(&$OutputParent, &$SourceNode)
	{
		if(count($SourceNode->Children) > 0)
		{
			for($i = 0; $i < count($SourceNode->Children); $i++)
			{
				$tNode = $SourceNode->Children[$i];
				if(strtolower($tNode->Name) == "itemplaceholder")
				{
					for( $this->CurrentRowIndex = 0; $this->CurrentRowIndex < $this->ResultData['RowCount']; $this->CurrentRowIndex++ )
					{
						if($this->AltItemTemplate == null || $this->CurrentRowIndex == 0 || $this->CurrentRowIndex % 2 == 0)
							$this->DrawItemTemplate($OutputParent, $this->ItemTemplate);
						else
							$this->DrawItemTemplate($OutputParent, $this->AltItemTemplate);
					}
				}
				else
				{
					$newNode = $OutputParent->addChild($tNode->Name);
					$newNode->Text = $tNode->Text;
					for($e = 0; $e < count($tNode->Attributes); $e++)
						$newNode->addAttributes( $tNode->Attributes[$e]['Name'], $tNode->Attributes[$e]['Value']);
					$this->DrawLayoutTemplate( $newNode, $tNode );
				}
			}
		}
	}
	
	public function DrawControl(&$OutputParent, &$InputNode)
	{
		$input = $OutputParent->addChild("input");
		$input->addAttribute("type", "hidden");
		$input->addAttribute("id", $this->JsStatePassElement());
		$input->addAttribute("name", $this->JsStatePassElement());
		$input->addAttribute("value", $this->text == null ? "" : $this->text);
		
		if( isset( $this->ResultData['RowCount'] ) && $this->ResultData['RowCount'] > 0 )
		{
			if($this->LayoutTemplate != null && $this->ItemTemplate != null )
				$this->DrawLayoutTemplate($OutputParent, $this->LayoutTemplate);
		}
		else
			if($this->EmptyDataTemplate != null)
				if(count($this->EmptyDataTemplate->Children) > 0)
					$this->phpcPage->DrawNodes($this->EmptyDataTemplate, $OutputParent);
	}
	
	public function RetrieveStates()
	{
		//Get db results from teh view state
		if( $this->GetStateProperty("RowCount") != null )
		{
			$this->ResultData['RowCount'] = (int)$this->GetStateProperty("RowCount");
			$this->ResultData['FieldCount'] = (int)$this->GetStateProperty("FieldCount");
			for($i = 0; $i < $this->ResultData['FieldCount']; $i++)
				$this->ResultData['FieldName'][$i] = $this->GetStateProperty("FieldName" . $i);
			for($i = 0; $i < $this->ResultData['RowCount']; $i++)
			{
				for($e = 0; $e < $this->ResultData['FieldCount']; $e++)
					$this->ResultData['Data'][$i][$e] = $this->GetStateProperty("RowData-" . $i . "-" . $e);
			}
		}
	}
	
	public function PrepareStates()
	{ 
		//Push the db results into the view state (might not be the base idea, but works for now)
		$this->SetStateProperty("RowCount", $this->ResultData['RowCount']);
		$this->SetStateProperty("FieldCount", $this->ResultData['FieldCount']);
		for($i = 0; $i < $this->ResultData['FieldCount']; $i++)
			$this->SetStateProperty("FieldName" . $i, $this->ResultData['FieldName'][$i]);
		for($i = 0; $i < $this->ResultData['RowCount']; $i++)
		{
			for($e = 0; $e < $this->ResultData['FieldCount']; $e++)
				$this->SetStateProperty("RowData-" . $i . "-" . $e, $this->ResultData['Data'][$i][$e]);
		}
	}
}

?>