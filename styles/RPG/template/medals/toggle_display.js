function hdr_ref(object)
{
	if (document.getElementById)
	{
		return document.getElementById(object);
	}
	else if (document.all)
	{
		return eval('document.all.' + object);
	}
	else
	{
		return false;
	}
}

function hdr_expand(object)
{
	var object = hdr_ref(object);

	if( !object.style )
	{
		return false;
	}
	else
	{
		object.style.display = '';
	}

	if (window.event)
	{
		window.event.cancelBubble = true;
	}
}

function hdr_contract(object)
{
	var object = hdr_ref(object);

	if( !object.style )
	{
		return false;
	}
	else
	{
		object.style.display = 'none';
	}

	if (window.event)
	{
		window.event.cancelBubble = true;
	}
}

function hdr_toggle(object, open_close)
{
	var object = hdr_ref(object);
	var icon = hdr_ref(open_close);

	if( !object.style )
	{
		return false;
	}

	if( object.style.display == 'none' )
	{
		object.style.display = '';
	}
	else
	{
		object.style.display = 'none';
	}
}