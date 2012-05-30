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



class phpcDataConnMySql extends phpcDataConnection
{
	//The query the user will call
	var $Query;
	//Once the query has been made, all data will be stored here
	var $StoredResults;
	//Number of fields in the result set
	var $FieldCount;
	//Field names from the result set
	var $FieldName;
	
	//MySQL host name
	var $sqlHost;
	//MySQL user name
	var $sqlUserName;
	//MySQL password
	var $sqlPassword;
	//MySQL database
	var $sqlDatabase;
	
	function __construct( $Host, $UserName, $Password, $Database )
	{
		//Set MySQL information
		$this -> sqlHost = $Host;
		$this -> sqlUserName = $UserName;
		$this -> sqlPassword = $Password;
		$this -> sqlDatabase = $Database;
	}
	
	function SetQuery( $Sql )
	{
		//Set the query string
		$this -> Query = $Sql;
	}
	
	function ExecuteNonQuery()
	{
		//Default return value;
		$rValue = true;
		//Make the MySQL connection
		$conn = mysql_connect( $this -> sqlHost, $this -> sqlUserName, $this -> sqlPassword ) or $rValue = false;
		//If we have a valid connection
		if( $rValue )
			//Select the database
			mysql_select_db( $this -> sqlDatabase, $conn ) or $rValue = false;
		//If everthing is okay
		if( $rValue )
		{
			//Query the database
			$result = mysql_query( $this -> Query, $conn );
			
			//Query Okay?
			if( !$result )
				$rValue = false;
		}
		//Return our success state
		return $rValue;
	}
	
	function Execute()
	{
		//Default return value;
		$rValue = true;
		//Make the MySQL connection
		$conn = mysql_connect( $this -> sqlHost, $this -> sqlUserName, $this -> sqlPassword ) or $rValue = false;
		//If we have a valid connection
		if( $rValue )
			//Select the database
			mysql_select_db( $this -> sqlDatabase, $conn ) or $rValue = false;
		//If everthing is okay
		if( $rValue )
		{
			//Query the database
			$result = mysql_query( $this -> Query, $conn );
			//Do we have any results?
			if( mysql_num_rows( $result ) > 0 )
			{
				//Get number of fields
				$this -> FieldCount = mysql_num_fields( $result );
				//Iterate through each field
				for( $i = 0; $i < $this -> FieldCount; $i++ )
					//Save the field name
					$this -> FieldName[ $i ] = mysql_field_name( $result, $i );
				//Row indexer
				$RowIndex = 0;
				//While we get a valid row
				while( $tRow = mysql_fetch_array( $result ) )
				{
					//Iterate through each field
					for( $i = 0; $i < $this -> FieldCount; $i++ )
						//Save the row field data to the main storage
						$this -> StoredResults[ $RowIndex ][ $this -> FieldName[ $i ] ] = $tRow[ $this -> FieldName[ $i ] ];
					//Increment row index
					$RowIndex++;
				}
			}
			else
			{
				//Set our field count to 0;
				$this -> FieldCount = 0;
			}
		}
		//Return our success state
		return $rValue;
	}
	
	function NumFields()
	{
		//Return number of fields
		return $this -> FieldCount;
	}
	
	function GetFieldName( $FieldId )
	{
		//Return field name
		return $this -> FieldName[ $FieldId ];
	}
	
	function NumRows()
	{
		//Return number of rows
		return count( $this -> StoredResults );
	}
	
	function GetRowFieldValue( $RowId, $FieldName )
	{
		//Get the field value from a row
		return $this -> StoredResults[ $RowId ][ $FieldName ];
	}
}

?>
