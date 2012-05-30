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



class phpcUtils
{
	public static function SelfUrl()
	{
		//Picked up this code on the net somewhere, not sure who the author is,
		//if you are, or you know who is, contact Jason Doyle, and credit
		//will be given to the original author.
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
	}
	
	public static function IsTrueVal( $val )
	{
		if($val != null)
		{
			if($val == true)
				return true;
			$tval = trim(strtolower($val));
			if($tval == "yes" || $tval == "true" || $tval == "1")
				return true;
		}
		return false;
	}
	
	public static function IsFalseVal( $val )
	{
		if($val != null)
		{
			if($val == false)
				return true;
			$tval = trim(strtolower($val));
			if($tval == "no" || $tval == "false" || $tval == "0")
				return true;
		}
		return false;
	}
}

?>
