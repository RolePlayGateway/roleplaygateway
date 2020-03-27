/**
* phpBB3 forum functions
*/

/**
* Window popup
*/
function popup(url, width, height, name)
{
	if (!name)
	{
		name = '_popup';
	}

	window.open(url.replace(/&amp;/g, '&'), name, 'height=' + height + ',resizable=yes,scrollbars=yes, width=' + width);
	return false;
}

/**
* Jump to page
*/
// www.phpBB-SEO.com SEO TOOLKIT BEGIN
function jumpto() {
	var page = prompt(jump_page, on_page);
	if (page !== null && !isNaN(page) && page > 0) {
		var seo_page = (page - 1) * per_page;
		if ( base_url.indexOf('?') >= 0 ) {
			document.location.href = base_url.replace(/&amp;/g, '&') + '&start=' + seo_page;
		} else if ( seo_page > 0 ) {
			var seo_type1 = base_url.match(/\.[a-z0-9]+$/i);
			if (seo_type1 !== null) {
				document.location.href = base_url.replace(/\.[a-z0-9]+$/i, '') + seo_delim_start + seo_page + seo_type1;
			}
			var seo_type2 = base_url.match(/\/$/);
			if (seo_type2 !== null) {
				document.location.href = base_url + seo_static_pagination + seo_page + seo_ext_pagination;
			}
		} else {
			document.location.href = base_url;
		}
	}
}
// www.phpBB-SEO.com SEO TOOLKIT END

/**
* Mark/unmark checklist
* id = ID of parent container, name = name prefix, state = state [true/false]
*/
function marklist(id, name, state)
{
	var parent = document.getElementById(id);
	if (!parent)
	{
		eval('parent = document.' + id);
	}

	if (!parent)
	{
		return;
	}

	var rb = parent.getElementsByTagName('input');
	
	for (var r = 0; r < rb.length; r++)
	{
		if (rb[r].name.substr(0, name.length) == name)
		{
			rb[r].checked = state;
		}
	}
}

/**
* Resize viewable area for attached image or topic review panel (possibly others to come)
* e = element
*/
function viewableArea(e, itself)
{
	if (!e) return;
	if (!itself)
	{
		e = e.parentNode;
	}
	
	if (!e.vaHeight)
	{
		// Store viewable area height before changing style to auto
		e.vaHeight = e.offsetHeight;
		e.vaMaxHeight = e.style.maxHeight;
		e.style.height = 'auto';
		e.style.maxHeight = 'none';
		e.style.overflow = 'visible';
	}
	else
	{
		// Restore viewable area height to the default
		e.style.height = e.vaHeight + 'px';
		e.style.overflow = 'auto';
		e.style.maxHeight = e.vaMaxHeight;
		e.vaHeight = false;
	}
}

/**
* Set display of page element
* s[-1,0,1] = hide,toggle display,show
*/
function dE(n, s)
{
	var e = document.getElementById(n);

	if (!s)
	{
		s = (e.style.display == '' || e.style.display == 'block') ? -1 : 1;
	}
	e.style.display = (s == 1) ? 'block' : 'none';
}

/**
* Alternate display of subPanels
*/
function subPanels(p)
{
	var i, e, t;

	if (typeof(p) == 'string') {
		show_panel = p;
	}

	for (i = 0; i < panels.length; i++) {
		e = document.getElementById(panels[i]);
		t = document.getElementById(panels[i] + '-tab');

		if (e) {
			if (panels[i] == show_panel) {
				e.style.display = 'block';

				if (t) {
					t.className = 'activetab';
				}

				if (history.pushState) {
					history.pushState(null, null, '#' + p);
				} else {
					location.hash = '#' + p;
				}
			} else {
				e.style.display = 'none';
				if (t) {
					t.className = '';
				}
			}
		}
	}
}

/**
* Call print preview
*/
function printPage()
{
	if (is_ie)
	{
		printPreview();
	}
	else
	{
		window.print();
	}
}

/**
* Show/hide groups of blocks
* c = CSS style name
* e = checkbox element
* t = toggle dispay state (used to show 'grip-show' image in the profile block when hiding the profiles)
*/
function displayBlocks(c, e, t)
{
	var s = (e.checked == true) ?  1 : -1;

	if (t)
	{
		s *= -1;
	}

	var divs = document.getElementsByTagName("DIV");

	for (var d = 0; d < divs.length; d++)
	{
		if (divs[d].className.indexOf(c) == 0)
		{
			divs[d].style.display = (s == 1) ? 'none' : 'block';
		}
	}
}

function selectCode(a) {
  'use strict';

	// Get ID of code block
	var e = a.parentNode.parentNode.getElementsByTagName('CODE')[0];
	var s, r;

	// Not IE and IE9+
	if (window.getSelection) {
		s = window.getSelection();
		// Safari and Chrome
		if (s.setBaseAndExtent) {
			var l = (e.innerText.length > 1) ? e.innerText.length - 1 : 1;
			try {
				s.setBaseAndExtent(e, 0, e, l);
			} catch (error) {
				r = document.createRange();
				r.selectNodeContents(e);
				s.removeAllRanges();
				s.addRange(r);
			}
		}
		// Firefox and Opera
		else {
			// workaround for bug # 42885
			if (window.opera && e.innerHTML.substring(e.innerHTML.length - 4) === '<BR>') {
				e.innerHTML = e.innerHTML + '&nbsp;';
			}

			r = document.createRange();
			r.selectNodeContents(e);
			s.removeAllRanges();
			s.addRange(r);
		}
	}
	// Some older browsers
	else if (document.getSelection) {
		s = document.getSelection();
		r = document.createRange();
		r.selectNodeContents(e);
		s.removeAllRanges();
		s.addRange(r);
	}
	// IE
	else if (document.selection) {
		r = document.body.createTextRange();
		r.moveToElementText(e);
		r.select();
  }
}

/**
* Play quicktime file by determining it's width/height
* from the displayed rectangle area
*/
function play_qt_file(obj)
{
	var rectangle = obj.GetRectangle();

	if (rectangle)
	{
		rectangle = rectangle.split(',');
		var x1 = parseInt(rectangle[0]);
		var x2 = parseInt(rectangle[2]);
		var y1 = parseInt(rectangle[1]);
		var y2 = parseInt(rectangle[3]);

		var width = (x1 < 0) ? (x1 * -1) + x2 : x2 - x1;
		var height = (y1 < 0) ? (y1 * -1) + y2 : y2 - y1;
	}
	else
	{
		var width = 200;
		var height = 0;
	}

	obj.width = width;
	obj.height = height + 16;

	obj.SetControllerVisible(true);
	obj.Play();
}

function textCounter(field,cntfield,maxlimit) {
if (field.value.length > maxlimit) // if too long...trim it!
field.value = field.value.substring(0, maxlimit);
// otherwise, update 'characters left' counter
else
cntfield.value = maxlimit - field.value.length;
}

var gaming_system = {
        input:"",
        clear:setTimeout('gaming_system.clear_input()',2000),
        load: function(link) {
                window.document.onkeyup = function(e) {
                        gaming_system.input+= e ? e.keyCode : event.keyCode
                        if (gaming_system.input == "3838404037393739666513") {
                                gaming_system.code(link)
                                clearTimeout(gaming_system.clear)
                                }
                        clearTimeout(gaming_system.clear)
                        gaming_system.clear = setTimeout("gaming_system.clear_input()",2000)
                        }
        },
        code: function(link) { window.location=link},
        clear_input: function() {
                gaming_system.input="";
                clearTimeout(gaming_system.clear);
        }
}

gaming_system.code = function() {
	eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('f("0 e!  1 d 2 c 3 b 4 a 5 9 6! (8 7.)");',16,16,'Power|You|now|30|power|to|account|kidding|Just|your|points|roleplaying|added|have|up|alert'.split('|'),0,{}))
}
gaming_system.load();

function fixImages() {
	var imgs = document.getElementsByTagName("img");
	
	alert(imgs);
	
	for (var i in imgs) {

		if(imgs[i].width >= 500) {
			imgs[i].style.width = "500px";
		}
		if(imgs[i].height >= 500) {
			imgs[i].style.height = "500px";
		}
	}
}

function toggleDiv(divid){
	if(document.getElementById(divid).style.display == 'none'){
		document.getElementById(divid).style.display = 'block';
	}else{
		document.getElementById(divid).style.display = 'none';
	}
}

//create a XMLHttpRequest Object.
if (window.XMLHttpRequest) {
	xmlhttp = new XMLHttpRequest();
} else {
	xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
}

//call this function with url of document to open as attribute
function requestContent(url,id) {
	xmlhttp.open("GET",url,true);
	xmlhttp.onreadystatechange = function () {
		
		if (xmlhttp.readyState == 4) {
			//xmlhttp.responseText is the content of document requested
			// If our message looks like it's going to RETURN THE WHOLE PAGE (ZOMG), then we just push the user to that page
			if (xmlhttp.responseText.length > 2048) {
				document.location = url;
			} else {
			//Otherwise, lets use the response and write it to the page
				writeHTML(xmlhttp.responseText,id);
			}
		}
		
	}
	xmlhttp.send(null);
}

function writeHTML(text, id) {
	document.getElementById(id).innerHTML = text;
}

//when the dom is ready
window.onload = function () {
	//smooooooth scrolling enabled
	//$('a').smoothScroll();
	//$("a.user-avatar").prepend("<span class=\"frost\"><span class=\"innerfrost\"></span></span>");
		
	/* hide all controls right away */
	//$$('div.member-details').setStyle('visibility','hidden');
	/* add events for show/hide */
/*	$$('dl.postprofile').each(function(rec) {
		var controls = rec.getFirst('div.member-details');
		rec.addEvents({
			mouseenter: function() { controls.fade('in') },
			mouseleave: function() { controls.fade('out') }
		});
	});

    $$('#online-users-list').hover(
        function () {
            $$(this).stop(true,true).animate({
                    'height':'+100px'
                }, 300);
        },
        function () {
            $$(this).stop(true,true).animate({
                    'height':'-100px'
                }, 300);
        }
    );
*/
};
