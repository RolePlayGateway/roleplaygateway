/*****************************************/
// Name: Javascript Textarea BBCode Markup Editor
// Version: 1.3
// Author: Balakrishnan
// Last Modified Date: 25/jan/2009
// License: Free
// URL: http://www.corpocrat.com
/******************************************/

var textarea;
var content;
document.write("<link href=\"http://www.roleplaygateway.com/includes/bbeditor/styles.css\" rel=\"stylesheet\" type=\"text/css\">");

function edToolbar(obj) {
    document.write("<div class=\"toolbar\">");
	document.write("<img class=\"button\" src=\"http://www.roleplaygateway.com/includes/bbeditor/images/bold.gif\" name=\"btnBold\" title=\"Bold\" onClick=\"doAddTags('[b]','[/b]','" + obj + "')\">");
    document.write("<img class=\"button\" src=\"http://www.roleplaygateway.com/includes/bbeditor/images/italic.gif\" name=\"btnItalic\" title=\"Italic\" onClick=\"doAddTags('[i]','[/i]','" + obj + "')\">");
	document.write("<img class=\"button\" src=\"http://www.roleplaygateway.com/includes/bbeditor/images/underline.gif\" name=\"btnUnderline\" title=\"Underline\" onClick=\"doAddTags('[u]','[/u]','" + obj + "')\">");
	document.write("<img class=\"button\" src=\"http://www.roleplaygateway.com/includes/bbeditor/images/link.gif\" name=\"btnLink\" title=\"Insert URL Link\" onClick=\"doURL('" + obj + "')\">");
	document.write("<img class=\"button\" src=\"http://www.roleplaygateway.com/includes/bbeditor/images/picture.gif\" name=\"btnPicture\" title=\"Insert Image\" onClick=\"doImage('" + obj + "')\">");
	document.write("<img class=\"button\" src=\"http://www.roleplaygateway.com/includes/bbeditor/images/ordered.gif\" name=\"btnList\" title=\"Ordered List\" onClick=\"doList('[list=1]','[/list]','" + obj + "')\">");
	document.write("<img class=\"button\" src=\"http://www.roleplaygateway.com/includes/bbeditor/images/unordered.gif\" name=\"btnList\" title=\"Unordered List\" onClick=\"doList('[list]','[/list]','" + obj + "')\">");
	document.write("<img class=\"button\" src=\"http://www.roleplaygateway.com/includes/bbeditor/images/quote.gif\" name=\"btnQuote\" title=\"Quote\" onClick=\"doAddTags('[quote]','[/quote]','" + obj + "')\">"); 
  	document.write("<img class=\"button\" src=\"http://www.roleplaygateway.com/includes/bbeditor/images/code.gif\" name=\"btnCode\" title=\"Code\" onClick=\"doAddTags('[code]','[/code]','" + obj + "')\">");
    document.write("</div>");
	//document.write("<textarea id=\""+ obj +"\" name = \"" + obj + "\" cols=\"" + width + "\" rows=\"" + height + "\"></textarea>");
				}

function doImage(obj)
{
textarea = document.getElementById(obj);
var url = prompt('Enter the Image URL:','http://');
var scrollTop = textarea.scrollTop;
var scrollLeft = textarea.scrollLeft;

if (url != '' && url != null) {

	if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				sel.text = '[img]' + url + '[/img]';
			}
   else 
    {
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		var rep = '[img]' + url + '[/img]';
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
			
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
	}
}

}

function doURL(obj)
{
textarea = document.getElementById(obj);
var url = prompt('Enter the URL:','http://');
var scrollTop = textarea.scrollTop;
var scrollLeft = textarea.scrollLeft;

if (url != '' && url != null) {

	if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				
			if(sel.text==""){
					sel.text = '[url]'  + url + '[/url]';
					} else {
					sel.text = '[url=' + url + ']' + sel.text + '[/url]';
					}			

				//alert(sel.text);
				
			}
   else 
    {
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
        var sel = textarea.value.substring(start, end);
		
		if(sel==""){
				var rep = '[url]' + url + '[/url]';
				} else
				{
				var rep = '[url=' + url + ']' + sel + '[/url]';
				}
	    //alert(sel);
		
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
			
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
	}
 }
}

function doAddTags(tag1,tag2,obj)
{
textarea = document.getElementById(obj);
	// Code for IE
		if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				//alert(sel.text);
				sel.text = tag1 + sel.text + tag2;
			}
   else 
    {  // Code for Mozilla Firefox
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
		
		var scrollTop = textarea.scrollTop;
		var scrollLeft = textarea.scrollLeft;

		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		var rep = tag1 + sel + tag2;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
		
		
	}
}

function doList(tag1,tag2,obj){
textarea = document.getElementById(obj);
// Code for IE
		if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				var list = sel.text.split('\n');
		
				for(i=0;i<list.length;i++) 
				{
				list[i] = '[*]' + list[i];
				}
				//alert(list.join("\n"));
				sel.text = tag1 + '\n' + list.join("\n") + '\n' + tag2;
			} else
			// Code for Firefox
			{

		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var i;
		
		var scrollTop = textarea.scrollTop;
		var scrollLeft = textarea.scrollLeft;

		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		
		var list = sel.split('\n');
		
		for(i=0;i<list.length;i++) 
		{
		list[i] = '[*]' + list[i];
		}
		//alert(list.join("<br>"));
        
		
		var rep = tag1 + '\n' + list.join("\n") + '\n' +tag2;
		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
 }
}