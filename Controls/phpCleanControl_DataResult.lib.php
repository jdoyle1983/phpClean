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
class phpcControl_DataResult extends phpcControl
{
	var $DataConnection;
	var $ResultData;
	
	public function RespondsToTag()
	{
		return "phpc:dataresult";
	}
	
	public function GetInstance()
	{
		return new phpcControl_DataResult();
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
		
	}
	
	public function ParseQueryString()
	{
	}
	
	public function DrawControl(&$OutputParent, &$InputNode)
	{
		if( isset($this->ResultData['RowCount']) && $this->ResultData['RowCount'] > 0 )
		{
			$table = $OutputParent->addChild("table");
			$uheaderrow = $table->addChild("tr");
			$uheadercolumn = $uheaderrow->addChild("td");
			$uheadercolumn->addAttribute("colspan", $this->ResultData["FieldCount"]);
			$ucenter = $uheadercolumn->addChild("center");
			$ubold = $ucenter->addChild("b");
			$ubold->Text = ($this->Text == null ? '' : $this->Text);
			
			if($this->ShowHeaders == null || phpcUtils::IsTrueVal($this->ShowHeaders))
			{
				$headerrow = $table->addChild("tr");
				for($i = 0; $i < $this->ResultData['FieldCount']; $i++)
				{
					$fcol = $headerrow->addChild("th");
					$fcol->Text = $this->ResultData['FieldName'][$i];
				}
			}
			
			for($i = 0; $i < $this->ResultData['RowCount']; $i++)
			{
				$trow = $table->addChild("tr");
				for($e = 0; $e < $this->ResultData['FieldCount']; $e++)
				{
					$tcol = $trow->addChild("td");
					$tempData = $this->ResultData['Data'][$i][$e];
					if($this->onfielddatabind != null && $this->onfielddatabind != "")
						$this->DoCallBack($this->onfielddatabind, $tcol, $this->ResultData['FieldName'][$e], $tempData);
					if(count($tcol->Children) <= 0)
						$tcol->Text = $tempData;
				}
			}
		}
	}
	
	public function PrepareStates()
	{
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
	
	public function RetrieveStates()
	{
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
}
//#END_EXPORT

?>