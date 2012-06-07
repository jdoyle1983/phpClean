
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

var AsyncAssigned = false;

function phpClean_AddFieldByElement( form, itemid, itemname, elementid )
{
	var Val = '';
	try
	{
		Val = $('#' + elementid).val();
	}
	catch(err){}
    phpClean_AddFieldByValue( form, itemid, itemname, Val );
}

function phpClean_RemoveFieldByElement( form, itemid )
{
	$(itemid).remove();
	//document.getElementById(itemid).remove();
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
	var phpClean_Form = document.getElementById( 'phpCleanBaseForm' );
	//document.getElementsByTagName('body').item(0).appendChild(phpClean_Form);
	phpClean_PrepareControls( phpClean_Form );
	phpClean_AddFieldByValue( phpClean_Form, 'PostBackViewState', 'PostBackViewState', $('#ViewState').val() );
	phpClean_AddFieldByValue( phpClean_Form, 'EventItem', 'EventItem', item );
	phpClean_AddFieldByValue( phpClean_Form, 'Event', 'Event', event );
	phpClean_SubmitForm(false);
	phpClean_Cleanup();
}

function phpClean_FireAsyncEvent( item, event )
{
	phpClean_PrepareAsync(item, event);
	phpClean_SubmitForm(true);
}

function phpClean_PrepareAsync(item, event)
{
	var phpClean_Form = document.getElementById('phpCleanBaseForm');
	phpClean_PrepareControls( phpClean_Form );
	phpClean_AddFieldByValue( phpClean_Form, 'PostBackViewState', 'PostBackViewState', $('#ViewState').val() );
	phpClean_AddFieldByValue( phpClean_Form, 'EventItem', 'EventItem', item );
	phpClean_AddFieldByValue( phpClean_Form, 'Event', 'Event', event );
	phpClean_AddFieldByValue( phpClean_Form, '__PHPCLEAN_ASYNC_OPERATION__', '__PHPCLEAN_ASYNC_OPERATION__', '1' );
}

function phpClean_SubmitForm(isAsync)
{
	if(isAsync == true && AsyncAssigned == false)
	{
		$('#phpCleanBaseForm').submit(function() {
			var asyncOptions = { success: handleResponse };
			$(this).ajaxSubmit(asyncOptions);
			return false;
		});
		AsyncAssigned = true;
	}
	$('#phpCleanBaseForm').submit();
}

function phpClean_Cleanup()
{
	var phpClean_Form = document.getElementById('phpCleanBaseForm');
	phpClean_CleanupControls(phpClean_Form);
	phpClean_RemoveFieldByElement( phpClean_Form, 'PostBackViewState' );
	phpClean_RemoveFieldByElement( phpClean_Form, 'EventItem' );
	phpClean_RemoveFieldByElement( phpClean_Form, 'Event' );
	phpClean_RemoveFieldByElement( phpClean_Form, '__PHPCLEAN_ASYNC_OPERATION__' );
}

function GetAsyncValue(Keys, Values, Count, ControlId, Field)
{
	for(i = 0; i < Count; i++)
		if(Keys[i] == ControlId + "__" + Field)
			return Values[i];
	return "";
}


function handleResponse(responseData)
{
	phpClean_Cleanup();
	
	var ResponseControlKey = new Array();
	var ResponseControlValue = new Array();
	var ResponseControlCount = 0;
	
	var Controls = responseData.split('@@%');
	for(i = 0; i < Controls.length; i++)
	{
		if(Controls[i] != "")
		{
			var CtlDtls = Controls[i].split('@@#');
			if(CtlDtls.length == 2)
			{
				var ControlId = CtlDtls[0];
				var Properties = CtlDtls[1].split('@@$');
				for(p = 0; p < Properties.length; p++)
				{
					var PropDtl = Properties[p].split('@@*');
					if(PropDtl.length == 2)
					{
						var PropName = PropDtl[0];
						var PropValue = PropDtl[1];
						ResponseControlKey[ResponseControlCount] = ControlId + "__" + PropName;
						ResponseControlValue[ResponseControlCount] = PropValue;
						ResponseControlCount++;
					}
				}
			}
		}
	}
	
	phpClean_AsyncUpdate(ResponseControlKey, ResponseControlValue, ResponseControlCount);
	$('#ViewState').val(GetAsyncValue(ResponseControlKey, ResponseControlValue, ResponseControlCount, "ViewState", "Value"));
	alert(GetAsyncValue(ResponseControlKey, ResponseControlValue, ResponseControlCount, "ReceivedData", "Value"));
}