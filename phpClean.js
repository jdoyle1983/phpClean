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

function phpClean_AddFieldByElement( form, itemid, itemname, elementid )
{
	var Val = '';
	try
	{
		Val = document.getElementById( elementid ).value;
	}
	catch(err){}
        phpClean_AddFieldByValue( form, itemid, itemname, Val );
}

function phpClean_AddFieldByValue( form, itemid, itemname, itemvalue )
{
	var phpClean_ObjectValue = document.createElement( 'input' );
	phpClean_ObjectValue.setAttribute( 'type', 'hidden' );
	phpClean_ObjectValue.setAttribute( 'id', itemid );
	phpClean_ObjectValue.setAttribute( 'name', itemname );
	phpClean_ObjectValue.setAttribute( 'value', itemvalue );
	form.appendChild( phpClean_ObjectValue );
}

function phpClean_FireEvent( item, event )
{
	var phpClean_Form = document.createElement('form');
	phpClean_Form.setAttribute('id', 'postBackForm');
	phpClean_Form.setAttribute('method', 'POST');
	phpClean_Form.setAttribute('OnSubmit', 'return false;');
	phpClean_Form.setAttribute('action', document.URL);
	phpClean_Form.setAttribute('enctype', 'multipart/form-data');
	document.getElementsByTagName('body').item(0).appendChild(phpClean_Form);
    
	phpClean_PrepareControls( phpClean_Form );
	phpClean_AddFieldByElement( phpClean_Form, 'ViewState', 'ViewState', 'ViewState' );
	phpClean_AddFieldByValue( phpClean_Form, 'EventItem', 'EventItem', item );
	phpClean_AddFieldByValue( phpClean_Form, 'Event', 'Event', event );
	phpClean_Form.submit();
}