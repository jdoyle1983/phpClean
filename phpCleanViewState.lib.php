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



class phpcViewState
{

	//Three parts comprimise a view state value, the Object Id of the object that owns it, the key, and the value.

	var $ObjectIds = array();
	var $ObjectKeys = array();
	var $ObjectValues = array();
	
	function GetViewState()
	{
		return base64_encode(gzcompress($this -> GetViewStatePlain(), 9));
	}
	
	function GetViewStatePlain()
	{
		//Set return value to empty string 
		$out = "";
		//Iterate through each object in the view state
		for( $i = 0; $i < count( $this -> ObjectIds ); $i++ )
		{ 
			//Seperators for each section of key. Perhaps a more elegant solution
			//can be found to ensure the seperators are never found in the id, key
			//or value?  Should be fine for now.
			
			$out .= $this -> ObjectIds[$i] . "~!%";
			$out .= $this -> ObjectKeys[$i] . "(@~";
			$out .= $this -> ObjectValues[$i] . "=`^";
		}
		return $out;
	}
	
	function RestoreViewState( $State )
	{
		$uncompressed = gzuncompress(base64_decode($State));

		//Break out individual objects
		$tObjects = explode( "=`^", $uncompressed );
		//Iterate through each object
		for( $i = 0; $i < count( $tObjects ); $i++ )
		{

			//Pull values from string
			//	
			//----------------------------
			//|tSet1[0]|    tSet1[1]     |
			//----------------------------
			//|        |tSet2[0]|tSet2[1]|
			//----------------------------
			//|   ID   |   KEY  | VALUE  |
			//----------------------------

			$val1 = "";
			$val2 = "";
			$val3 = "";

			$tSet1 = explode( "~!%", $tObjects[ $i ] );
			if(count($tSet1) > 1)
			{
				$val1 = $tSet1[0];
				$tSet2 = explode( "(@~", $tSet1[ 1 ] );
				if(count($tSet2) > 1)
				{
					$val2 = $tSet2[0];
					$val3 = $tSet2[1];
				}
			}
			//Save values to view state
			$this -> ObjectIds[] = $val1;
			$this -> ObjectKeys[] = $val2;
			$this -> ObjectValues[] = $val3;
		}
	}
	
	function AddObject( $ObjectId, $KeyName, $KeyValue )
	{
		//Does key already exists?
		if( $this -> KeyExists( $ObjectId, $KeyName ) )
			//Remove it
			$this -> RemoveObject( $ObjectId, $KeyName );
		//Add new key
		$this -> ObjectIds[] = $ObjectId;
		$this -> ObjectKeys[] = $KeyName;
		$this -> ObjectValues[] = $KeyValue;
	}
	
	function KeyExists( $ObjectId, $KeyName )
	{
		//Iterate through keys
		for( $i = 0; $i < count( $this -> ObjectIds ); $i++ )
		{
			//Have match?
			if( $this -> ObjectIds[ $i ] == $ObjectId && $this -> ObjectKeys[ $i ] == $KeyName )
				//Match found 
				return true;
		}
		//No match 
		return false;
	}
	
	function KeyValue( $ObjectId, $KeyName )
	{
		//Iterate through keys 
		for( $i = 0; $i < count( $this -> ObjectIds ); $i++ )
		{
			//Have match?
			if( $this -> ObjectIds[ $i ] == $ObjectId && $this -> ObjectKeys[ $i ] == $KeyName )
				//Return key value
				return $this -> ObjectValues[ $i ];
		}
		//No match, return empty string 
		return "";
	}
	
	function KeysForObject( $ObjectId )
	{
		$rValue = array();
		for($i = 0; $i < count($this->ObjectIds); $i++)
			if($this->ObjectIds[$i] == $ObjectId)
				$rValue[] = $this->ObjectKeys[$i];
		return $rValue;
	}
	
	function RemoveObject( $ObjectId, $KeyName )
	{
		//Iterate through keys 
		for( $i = 0; $i < count( $this -> ObjectIds ); $i++ )
		{
			//Have match?
			if( $this -> ObjectIds[ $i ] == $ObjectId && $this -> ObjectKeys[ $i ] == $KeyName )
			{
				//Remove from arrays 
				unset( $this -> ObjectIds[ $i ] );
				unset( $this -> ObjectKeys[ $i ] );
				unset( $this -> ObjectValues[ $i ] );
			}
		}
		
		//Re-align arrays 
		array_unshift( $this -> ObjectIds, "");
		array_shift( $this -> ObjectIds );
		
		array_unshift( $this -> ObjectKeys, "" );
		array_shift( $this -> ObjectKeys );
		
		array_unshift( $this -> ObjectValues, "" );
		array_shift( $this -> ObjectValues );
	}
}


?>
