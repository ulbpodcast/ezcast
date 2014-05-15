/*

Project:	Input Placeholder Text
Title:		Automatic population of form fields with contents of title attributes
Author      Jon Gibbins (aka dotjay)
Created:	13 Aug 2005
Modified:	26 Oct 2009



Notes:

Add the following classes to text inputs or textareas to get the 
desired behaviour:

auto-select
	Will pre-populate the input with the title attribute and select the
	text when it receives focus.

auto-clear
	Will pre-populate the input with the title attribute and clear the 
	text when it receives focus. Note: if auto-select and auto-clear 
	are set, auto-select takes precedence.

populate
	Will just populate the input with the title attribute.

NB: A class name of "placeholder" is set on form inputs that have 
placeholder text in them. This allows you to style the inputs 
differently when there is user input versus placeholder text, e.g.
input.placeholder,
textarea.placeholder{
color: #777;
}



Potential additions:

Add in handling of default text if an initial value is detected (line 
60, line 93, etc) using something like:
if (!el.defaultValue) continue;
el.onfocus = function() {
	if (this.value == this.defaultValue) this.value = "";
}
el.onblur = function() {
	if (this.value == "") this.value = this.defaultValue;
}


*/

function hasClass(el,cls) {
	return el.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
}
function addClass(el,cls) {
    if (!this.hasClass(el,cls)) el.className += " "+cls;
}
function removeClass(el,cls) {
    if (hasClass(el,cls)) {
        var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
        el.className = el.className.replace(reg,' ');
    }
}
function addLoadEvent(func) {
    var oldonload = window.onload;
    if (typeof window.onload != 'function') {
        window.onload = func;
    } else {
        window.onload = function() {
            if (oldonload) {
                oldonload();
            }
            func();
        }
    }
}

function initFormPlaceholders() {

	if (!document.getElementsByTagName) return true;

	ourForms = document.getElementsByTagName('form');

	// go through each form
	var numForms = ourForms.length;
	for (var i=0;i<numForms;i++) {

		// go through each form element
		var numFormElements = ourForms[i].elements.length;
		for (var j=0;j<numFormElements;j++) {

			var el = ourForms[i].elements[j];

			// ignore submit buttons
			if (el.type == "submit") continue;

			// if we got a text type input or textarea
			if ((el.type == "text") || (el.type == "textarea")) {
				// only populate if we want it to
				// note: might want title attribute but no pre-population of inputs
				var ourClassName = el.className;
				if (ourClassName.match('auto-select') || ourClassName.match('auto-clear') || ourClassName.match('populate')) {
					// only populate if empty
					if (el.value == '') {
					    addClass(el, "placeholder");
					    el.value = el.title;
					}
				}

				// add auto select if class contains auto-select
				// note: else if below so auto-select takes precedence (assuming select is better than clear)
				if (el.className.match('auto-select')) {
					el.onfocus = function () {
						if (this.value == this.title) {
						    removeClass(this, "placeholder");
						    this.select();
						}
					}
					if (el.captureEvents) el.captureEvents(Event.FOCUS);

					el.onblur = function () {
						if (this.value == '') {
						    this.value = this.title;
						}
						if (this.value == this.title) {
						    addClass(this, "placeholder");
						}
					}
					if (el.captureEvents) el.captureEvents(Event.BLUR);
				}

				// add auto clear if class contains auto-clear
				else if (el.className.match('auto-clear')) {
					el.onfocus = function () {
						if (this.value == this.title) {
						    removeClass(this, "placeholder");
						    this.value = '';
						}
					}
					if (el.captureEvents) el.captureEvents(Event.FOCUS);

					el.onblur = function () {
						if (this.value == '') {
						    this.value = this.title;
						}
						if (this.value == this.title) {
						    addClass(this, "placeholder");
						}
					}
					if (el.captureEvents) el.captureEvents(Event.BLUR);
				}
			}

		}

	}

}

addLoadEvent(initFormPlaceholders);
