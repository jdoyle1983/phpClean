phpClean
========

PHP Based Object Oriented Pages

PHP is great, but it reminds me of the old ASP days.  I always really liked the
concept of code behind pages like in ASP.NET.  I always thought PHP should 
implement something like that, so I created this library to do just that.

Currently there are only a few base objects defined, but it is completely extensible:
	* Button
	* CheckBox
	* DataResult (A Data Grid)
	* DropDownList
	* Hidden
	* Image
	* ImageButton
	* Label
	* ListView
	* Panel
	* RadioButton
	* TextBox

It handles object state management through post backs.  Events can be defined and
driven through controls (button click event, check box state change...etc).  A
custom view state object is used to pass data back and forth through post backs.

It does require STRICT XML formatting on the design pages.

You can see an example of the library in use in the source tree.