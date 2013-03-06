/**
* gym_rss_links.js Dom scrolling
* Based on
* DOMnews 1.0 
* homepage: http://www.onlinetools.org/tools/domnews/
* released 11.07.05
*/
var dn_interval = 0;
/* Initialise scroller when window loads */
// check for DOM
if(document.getElementById && document.createTextNode) {
	onload_functions.push('initDOMnews()');
	onunload_functions.push('clearInterval(dn_interval)');
}
var dn_scrollpos=dn_startpos;
var dn_paused=false;
var dn_els = '';
var dn_inels = '';
var dn_interval = '';
/* Initialise scroller */
function initDOMnews(reinit) {
	if(!dn_els) {
		dn_els = document.getElementById(dn_newsID);
		if(!dn_els){
			return;
		}
		dn_inels = document.getElementById(dn_newsID+'scrld');
		if(!dn_inels){
			return;
		}
		// Auto height for Gecko browsers
		if (document.defaultView) {
			dn_els.style.cssText = '';
			dn_inels.style.cssText = '';
			var real_endpos = document.defaultView.getComputedStyle(dn_els,"").getPropertyValue("height");
			real_endpos = parseInt(real_endpos.replace(/px/ig, ""));
			if (real_endpos) {
				dn_endpos = - real_endpos;
			}
		}	
	}
	if (reinit) {
		dn_els.parentNode.removeChild(dn_els.nextSibling);
		clearInterval(dn_interval);
	}
	dn_els.style.cssText = 'width:100%;height:'+dn_startpos+'px;overflow:hidden;position:relative;';
	dn_inels.style.cssText = 'width:100%; position:relative; top:'+dn_startpos+'px;';
	dn_interval=setInterval('scrollDOMnews()',dn_speed);
	var newa=document.createElement('a');
	var newp=document.createElement('p');
	newp.setAttribute('id',dn_paraID);
	newa.href='#' + dn_newsID;
	newa.appendChild(document.createTextNode(dn_stopMessage));
	newa.onclick=stopDOMnews;
	newp.appendChild(newa);
	dn_els.parentNode.insertBefore(newp,dn_els.nextSibling);
	if (!dn_paused) {
		dn_inels.style.cssText = 'width:100%;position:absolute; top:'+dn_startpos+'px;';
		dn_els.style.cssText = 'width:100%;height:'+dn_startpos+'px;overflow:hidden;position:relative;';
	}
	dn_els.onmouseover=function() {		
		clearInterval(dn_interval);
	}
	dn_els.onmouseout=function() {
		if (!dn_paused) {
			dn_interval=setInterval('scrollDOMnews()',dn_speed);
		}
	}
}
function ReinitDOMnews() {
	dn_paused= dn_paused ? false : true;
	initDOMnews(true);
}
function stopDOMnews() {
	dn_paused = true;
	clearInterval(dn_interval);
	dn_inels.style.cssText = 'width:100%;position:absolute;';
	dn_els.style.cssText = 'width:100%;height:'+dn_startpos+'px;overflow-y:auto;overflow-x:hidden;position:relative;';
	dn_els.parentNode.removeChild(dn_els.nextSibling);
	var newa=document.createElement('a');
	var newp=document.createElement('p');
	newp.setAttribute('id',dn_paraID);
	newa.href='#' + dn_newsID;
	newa.appendChild(document.createTextNode(dn_startMessage));
	newa.onclick=ReinitDOMnews;
	newp.appendChild(newa);
	dn_els.parentNode.insertBefore(newp,dn_els.nextSibling);
	return false;
}
function scrollDOMnews() {
	dn_inels.style.top=dn_scrollpos+'px';	
	if(dn_scrollpos==dn_endpos) {
		dn_scrollpos=dn_startpos;
	}
	dn_scrollpos--;	
}
