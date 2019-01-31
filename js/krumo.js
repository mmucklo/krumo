// Command to generate minified JS:
// curl -X POST -s --data-urlencode 'input@krumo.js' https://javascript-minifier.com/raw

/**
* JavaScript routines for Krumo
*
* @version $Id: krumo.js 22 2007-12-02 07:38:18Z Mrasnika $
*/

/////////////////////////////////////////////////////////////////////////////

/**
* Krumo JS Class
*/
function krumo() {
	}

// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

/**
* Add a CSS class to an HTML element
*
* @param HtmlElement el
* @param string className
* @return void
*/
krumo.reclass = function(el, className) {
	if (el.className.indexOf(className) < 0) {
		el.className += (' ' + className);
		}
	}

// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

/**
* Remove a CSS class to an HTML element
*
* @param HtmlElement el
* @param string className
* @return void
*/
krumo.unclass = function(el, className) {
	if (el.className.indexOf(className) > -1) {
		el.className = el.className.replace(className, '');
		}
	}

// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

/**
* Toggle the nodes connected to an HTML element
*
* @param HtmlElement el
* @return void
*/
krumo.toggle = function(event) {
	console.log(event);

	var el   = event.target.parentNode;
	var ctrl = event.ctrlKey;

	// Adding a control to you click does an expand/collapse all
	if (ctrl) {
		var is_expand = !el.classList.contains("krumo-opened");

		if (is_expand) {
			krumo.expand_all(el);
		} else {
			krumo.collapse_all(el);
		}
	} else {
		var ul = el.parentNode.getElementsByTagName('ul');
		for (var i=0; i<ul.length; i++) {
			if (ul[i].parentNode.parentNode == el.parentNode) {
				ul[i].parentNode.style.display = (ul[i].parentNode.style.display == 'none')
					? 'block'
					: 'none';
			}
		}

		// toggle class
		if (ul[0].parentNode.style.display == 'block') {
			krumo.reclass(el, 'krumo-opened');
		} else {
			krumo.unclass(el, 'krumo-opened');
		}
	}
}

krumo.expand = function(el) {
	paren = el.parentNode;
	nest  = paren.querySelector(".krumo-nest");
	exp   = paren.querySelector(".krumo-expand");

	nest.style.display = 'block';
	krumo.reclass(exp,"krumo-opened");
}

krumo.expand_all = function(el) {
	paren = el.parentNode;
	nest  = paren.querySelectorAll(".krumo-nest");
	exp   = paren.querySelectorAll(".krumo-expand");

	nest.forEach(function(item, i) {
		item.style.display = 'block';
	});

	exp.forEach(function(item, i) {
		krumo.reclass(item,"krumo-opened");
	});
}

krumo.collapse_all = function(el) {
	paren = el.parentNode;
	nest  = paren.querySelectorAll(".krumo-nest");
	exp   = paren.querySelectorAll(".krumo-expand");

	nest.forEach(function(item, i) {
		item.style.display = 'none';
	});

	exp.forEach(function(item, i) {
		krumo.unclass(item,"krumo-opened");
	});
}

// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

/**
* Hover over an HTML element
*
* @param HtmlElement el
* @return void
*/
krumo.over = function(el) {
	krumo.reclass(el, 'krumo-hover');
	}

// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

/**
* Hover out an HTML element
*
* @param HtmlElement el
* @return void
*/

krumo.out = function(el) {
	krumo.unclass(el, 'krumo-hover');
	}

/////////////////////////////////////////////////////////////////////////////

// Get element by ID
krumo.get_id = function(str) {
	var ret = document.getElementById(str);

	return ret;
}

// Get element by Class
krumo.get_class = function(str) {
	var elems = document.getElementsByClassName(str);
	var ret = new Array();

	// Just get the objects (not the extra stuff)
	for (var i in elems) {
		var elem = elems[i];
		if (typeof(elem) === 'object') {
			ret.push(elem);
		}
	}

	return ret;
}

// This is a poor mans querySelectorAll().
// querySelectorAll() isn't supported 100% until
// IE9, so we have to use this work around until
// we can stop supporting IE8
krumo.find = function(str) {
	if (!str) { return false; }

	var first  = str.substr(0,1);
	var remain = str.substr(1);

	if (first === ".") {
		return krumo.get_class(remain);
	} else if (first === "#") {
		return krumo.get_id(remain);
	} else {
		return false;
	}
}

function toggle_expand_all() {
	// Find all the expandable items
	var elems = krumo.find('.krumo-expand');
	if (elems.length === 0) { return false; }

	// Find the first expandable element and see what state it is in currently
	var action = 'expand';
	if (elems[0].nextSibling.style.display === 'block' || elems[0].nextSibling.style.display === '') {
		action = 'collapse';
	}

	// Expand each item
	for (var i in elems) {
		var item = elems[i];

		// The sibling is the hidden object
		var sib = item.nextSibling;

		if (action === 'expand') {
			sib.style.display = 'block';
			// Give the clicked item the krumo-opened class
			krumo.reclass(item, 'krumo-opened');
		} else {
			sib.style.display = 'none';
			// Remove the krumo-opened class
			krumo.unclass(item, 'krumo-opened');
		}
	}
}

function init_click() {
	var elems = krumo.find('.krumo-expand');

	// Add a click item to everything that's expandable
	elems.forEach(function(el) {
		el.addEventListener("click", krumo.toggle);
	});
}

init_click();
