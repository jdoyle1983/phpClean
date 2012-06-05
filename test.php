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

include_once('phpCleanDebug.php');

class Page extends phpcPage
{
	function __construct()
	{
		parent::__construct("test.phpc.html");
	}
	
	function Page_Load()
	{
		if(!$this->IsPostBack)
		{
			$dataconn = new phpcDataConnMySql("127.0.0.1", "testuser", "test", "test");
			$dataconn->SetQuery("select * from testtable");
			$this->drTest->DataSource( $dataconn );
			$this->drTest->DataBind();
			$this->lvTest->DataSource( $dataconn );
			$this->lvTest->DataBind();
		}
	}
	
	function btnTest_OnClick( $sender )
	{
		$this->lblTest->Text = "Item Was Clicked!";
		$this->lblTest4->Text = $this->txtTest->Text;
	}
	
	function ddlTest_OnSelectedIndexChanged( $sender )
	{
		$this->lblTest2->Text = $this->ddlTest->SelectedValue();
	}
	
	function chkTest_OnCheckChanged( $sender )
	{
		if($this->chkTest->checked)
		{
			$this->lblTest3->Text = "Is Checked";
			$this->pnlTest->Visible = true;
		}
		else
		{
			$this->lblTest3->Text = "Is Not Checked";
			$this->pnlTest->Visible = false;
		}
	}
	
	function setGroupLabel()
	{
		$sVal = "";
		if($this->rb1->checked)
			$sVal .= $this->rb1->value;
		else if($this->rb2->checked)
			$sVal .= $this->rb2->value;
		else if($this->rb3->checked)
			$sVal .= $this->rb3->value;
			
		$sVal .= " : ";
		
		if($this->rb4->checked)
			$sVal .= $this->rb4->value;
		else if($this->rb5->checked)
			$sVal .= $this->rb5->value;
		else if($this->rb6->checked)
			$sVal .= $this->rb6->value;
			
		$this->lblTest5->Text = $sVal;
	}
	
	function rbGroup1_CheckChanged( $sender )
	{
		$this->setGroupLabel();
	}
	
	function rbGroup2_CheckChanged( $sender )
	{
		$this->setGroupLabel();
	}
	
	function btnUploadFile_Click( $sender )
	{
		if($this->fuFile->HasFile)
		{
			$this->lblResult->Text = "Error: " . $this->fuFile->Error . "  Has File: " . $this->fuFile->HasFile . "  Temp File: " . $this->fuFile->TempPath . "  Size: " . $this->fuFile->Size . "  Name: " . $this->fuFile->Name . "  Type: " . $this->fuFile->Type;
			$this->lblResult2->Text = $this->fuFile->Contents;
		}
		else
		{
			$this->lblResult->Text = "No File Uploaded";
			$this->lblResult2->Text = "";
		}
		
		$this->hHiddenData->Text = time();
	}
}

new Page();

?>
