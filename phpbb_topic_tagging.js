/*
	This is the JavaScript file for the AJAX Suggest Tutorial

	You may use this code in your own projects as long as this 
	copyright is left	in place.  All code is provided AS-IS.
	This code is distributed in the hope that it will be useful,
 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
	
	For the rest of the code visit http://www.DynamicAJAX.com
	
	Copyright 2006 Ryan Smith / 345 Technical / 345 Group.	

*/
var input;
var output;
var insert_suggest 	= true;

function trim(string){
    return string.replace(/^\s*|\s*$/g,'');
}

//Gets the browser specific XmlHttpRequest Object
function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		alert("Your Browser Sucks!\nIt's about time to upgrade don't you think?");
	}
}

//Our XmlHttpRequest object to get the auto suggest
var searchReq = getXmlHttpRequestObject();

//Called from keyup on the search textbox.
//Starts the AJAX request.
function searchSuggest(inp, outp) {
	input 	= inp;
	output 	= outp;
	if (searchReq.readyState == 4 || searchReq.readyState == 0) {
		var allTags 	= document.getElementById(input).value;
		var lastComma	= allTags.lastIndexOf(",");
		 
		var lastTag		= allTags.substr(lastComma + 1, allTags.length);
		if(trim(lastTag) != ""){
			var ss = document.getElementById(output);
			
			if(ss.className.indexOf("suggest_posting") > -1){
				ss.className = 'suggestions_loading suggest_posting';
			}else{
				ss.className = 'suggestions_loading';
			}
			searchReq.open("GET", 'phpbb_topic_tagging.php?mode=get_suggestions&tag=' + trim(lastTag), true);
			searchReq.onreadystatechange = handleSearchSuggest; 
			searchReq.send(null);
		}
	}		
}

function replaceHtml(el, html) {

	var oldEl = (typeof el === "string" ? document.getElementById(el) : el);
	var newEl = document.createElement(oldEl.nodeName);
	// Preserve the element's id and class (other properties are lost)
	newEl.id = oldEl.id;
	newEl.className = oldEl.className;
	// Replace the old with the new
	newEl.innerHTML = html;
	oldEl.parentNode.replaceChild(newEl, oldEl);
	/* Since we just removed the old element from the DOM, return a reference
	to the new element, which can be used to restore variable references. */
	return newEl;

};

//Called when the AJAX response is returned.
function handleSearchSuggest() {
	if (searchReq.readyState == 4) {
		var ss = document.getElementById(output)
		ss.innerHTML = '';
		var newHTML = '';
		var str = searchReq.responseText.split("\n");
		//alert(str.length);
		for(i=0; i < str.length; i++) {
			//Build our element string.  This is cleaner using the DOM, but
			//IE doesn't support dynamically added attributes.
			var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
			suggest += 'onmouseout="javascript:suggestOut(this);" ';
			suggest += 'onclick="javascript:setSearch(this.innerHTML);" ';
			suggest += 'class="suggest_link">' + str[i] + '</div>';
			newHTML += suggest;
			//ss.innerHTML += suggest;
		}
		//document.getElementById(output).class = "suggestions";
		if(ss.className.indexOf("suggest_posting") > -1){
			ss.className = "suggestions suggest_posting";
		}else{
			ss.className = "suggestions";
		}
		replaceHtml(ss, newHTML)		
	}
}

//Mouse over function
function suggestOver(div_value) {
	div_value.className = 'suggest_link_over';
}
//Mouse out function
function suggestOut(div_value) {
	div_value.className = 'suggest_link';
}
//Click function
function setSearch(value) {
	var allTags = document.getElementById(input).value;
	var lastComma	 = allTags.lastIndexOf(",");
	var current_text = allTags.substr(0, lastComma+1);
	document.getElementById(input).value = current_text + value + ", ";
	document.getElementById(input).focus();
	document.getElementById(output).innerHTML = '';
}
function get_object(id) {
	var object = null;
	if( document.layers ) {   
		object = document.layers[id];
	} else if( document.all ) {
		object = document.all[id];
	} else if( document.getElementById ) {
		object = document.getElementById(id);
	}
	return object;
}

function activate(id){
	if(typeof(id) == 'string'){
		object = get_object(id);
	}else{
		object = id;	
	}
	if(object.disabled){
		object.disabled = false;
		object.value 	= ''; 
	}
}

function hide(id){
	if(typeof(id) == 'string'){
		object = get_object(id);
	}else{
		object = id;	
	}
	
	alert(id + '\n' + object);
	
	if(object.style.visibility != 'none'){
		object.style.visibility = 'none'; 
	}	
}