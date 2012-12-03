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
class phpcXmlParser
{
	var $ResultTree;
	var $CurNode;
	
	function __construct($XmlFile)
	{
		$this->CurNode = new phpcXml( "ROOT", null );
		$this->ResultTree[] = $this->CurNode;
		
		$data = null;
		$xml_parser = xml_parser_create();
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0); 
		xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 1); 
		xml_set_default_handler($xml_parser, array($this, "xmlpDefaultHandler")); 
		xml_set_element_handler($xml_parser, array($this,"xmlpStartElementHandler"), array($this,"xmlpEndElementHandler")); 
		xml_set_character_data_handler($xml_parser, array($this,"xmlpDefaultHandler"));
		
		if (!($fp = fopen($XmlFile, "r"))) 
		{ 
    		if (!xml_parse($xml_parser, $data, feof($fp))) 
    		{ 
       			die( sprintf("XML error: %s at line %d", 
                            xml_error_string(xml_get_error_code($xml_parser)), 
                            xml_get_current_line_number($xml_parser))); 
    		} 
		} 
		while ($data = fread($fp, 4096)) 
		{ 
    		if (!xml_parse($xml_parser, $data, feof($fp))) 
    		{ 
       			die( sprintf("XML error: %s at line %d", 
                            xml_error_string(xml_get_error_code($xml_parser)), 
                            xml_get_current_line_number($xml_parser))); 
    		} 
		} 
		xml_parser_free($xml_parser);
	}
	
	function xmlpDefaultHandler($parser, $data)
	{
		$this->CurNode->Text = $data;
	}
	
	function xmlpStartElementHandler($parser, $name, $attrs)
	{
		$this->CurNode = $this->CurNode->addChild( $name );
		foreach($attrs as $key => $value)
			$this->CurNode->addAttribute( $key, $value );
	}
	
	function xmlpEndElementHandler($parser, $name)
	{
		$this->CurNode = $this->CurNode->Parent;
	}
}

class phpcXml
{
	var $Name;
	var $Text;
	var $Attributes;
	var $Children;
	var $Parent;
	
	function __construct( $nName, $nParent )
	{
		$this->Name = $nName;
		$this->Parent = $nParent;
		$this->Text = "";
		$this->Attributes = array();
		$this->Children = array();
	}
	
	function copyNode($nParent, $recurse = false)
	{
		$newNode = new phpcXml( $this->Name, $nParent );
		$newNode->Text = $this->Text;
		for($i = 0; $i < count($this->Attributes); $i++)
			$newNode->addAttribute($this->Attributes[$i]['Name'], $this->Attributes[$i]['Value']);
		if($recurse)
			for($i = 0; $i < count($this->Children); $i++)
				$newNode->Children[] = $this->Children[$i]->copyNode($newNode, true);
		return $newNode;
	}
	
	function addAttribute($attName, $attValue)
	{
		$wasFound = false;
		for($i = 0; $i < count($this->Attributes); $i++)
		{
			if($this->Attributes[$i]['Name'] == $attName)
			{
				$this->Attributes[$i]['Value'] = $attValue;
				$wasFound = true;
			}
		}
		
		if(!$wasFound)
			$this->Attributes[] = array('Name' => $attName, 'Value' => $attValue);
	}
	
	function getAttribute($attName)
	{
		for($i = 0; $i < count($this->Attributes); $i++)
			if(strtolower($this->Attributes[$i]['Name']) == strtolower($attName))
				return $this->Attributes[$i]['Value'];
		return "";
	}
	
	function addChild($childName)
	{
		$newChild = new phpcXml( $childName, $this );
		$this->Children[] = $newChild;
		return $newChild;
	}
	
	function addChildObject($childObject)
	{
		$this->Children[] = $childObject;
		$childObject->Parent = $this;
		return $childObject;
	}
	
	function baseOutput()
	{
		$output = new XmlWriter();
		$output->openMemory();
		$output->setIndent(true);
		$output->setIndentString(" ");
		$this->recurseOutput($output, true);
		echo $output->outputMemory();
	}
	
	function recurseOutput(&$output, $ignoreMe)
	{
		if(!$ignoreMe)
		{
			$output->startElement($this->Name);
			for($i = 0; $i < count($this->Attributes); $i++)
			{
				$output->startAttribute($this->Attributes[$i]['Name']);
				$output->text($this->Attributes[$i]['Value']);
				$output->endAttribute();
			}
		}
		
		if(trim($this->Text) != "")
			$output->text($this->Text);
		for($i = 0; $i < count($this->Children); $i++)
			$this->Children[$i]->recurseOutput($output, false);
		
		if(!$ignoreMe)
			$output->endElement();
	}
}
//#END_EXPORT

?>
