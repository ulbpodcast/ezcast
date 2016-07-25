/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Carlos Avidmadjessi
 * UI Design by Julien Di Pietrantonio
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

var http_request = false;
function makeRequest(url, parameters,div_id) {
    http_request = false;
    if (window.XMLHttpRequest) { // Mozilla, Safari,...
        http_request = new XMLHttpRequest();
        if (http_request.overrideMimeType) {
            http_request.overrideMimeType('text/xml');
        }
    } else if (window.ActiveXObject) { // IE
        try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
        }
    }
    if (!http_request) {
        alert('Cannot create XMLHTTP instance');
        return false;
    }
    http_request.onreadystatechange=function(){
        if (http_request.readyState==4 && http_request.status==200){
            //alert('response from server:'+http_request.responseText);
            var div_element = document.getElementById(div_id);
            div_element.innerHTML=http_request.responseText;
     
            //makes sure the scripts contained in the page are executed after being 
            //loaded by ajax
            var scripts = div_element.getElementsByTagName('script');
            for(var i=0; i < scripts.length;i++)
            {			
                // if IE, we have to use execScript to define functions as global
                if (window.execScript)
                {
                    //Replaces the HTML comments because IE doesn't handle them well
                    window.execScript(scripts[i].text.replace('<!--',''));
                }
                // if any other web browser, we use a simple window.eval()
                else
                {
                    window.eval(scripts[i].text);
                }
            }
        }
    }
    //http_request.onreadystatechange = alertContents;
    http_request.open('GET', url + parameters, true);
    http_request.send(null);
}

function ajaxUpload(fileName, fileId, url, parameters,div_id) {
    http_request = false;
    
    var form = document.getElementById('submit_import_form');
    var fd = new FormData(form);
    fd.append(fileName, document.getElementById(fileId).files[0]);
    if (window.XMLHttpRequest) { // Mozilla, Safari,...
        http_request = new XMLHttpRequest();
        if (http_request.overrideMimeType) {
            http_request.overrideMimeType('text/xml');
        }
    } else if (window.ActiveXObject) { // IE
        try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
        }
    }
    if (!http_request) {
        alert('Cannot create XMLHTTP instance');
        return false;
    }
    http_request.onreadystatechange=function(){
        if (http_request.readyState==4 && http_request.status==200){
            //alert('response from server:'+http_request.responseText);
            var div_element = document.getElementById(div_id);
            div_element.innerHTML=http_request.responseText;
     
            //makes sure the scripts contained in the page are executed after being 
            //loaded by ajax
            var scripts = div_element.getElementsByTagName('script');
            for(var i=0; i < scripts.length;i++)
            {			
                // if IE, we have to use execScript to define functions as global
                if (window.execScript)
                {
                    //Replaces the HTML comments because IE doesn't handle them well
                    window.execScript(scripts[i].text.replace('<!--',''));
                }
                // if any other web browser, we use a simple window.eval()
                else
                {
                    window.eval(scripts[i].text);
                }
            }
        }
    }
    //http_request.onreadystatechange = alertContents;
    http_request.open('POST', url + parameters, true);
    http_request.send(fd);
}



function ajaxSubmitForm(formId, url, parameters,div_id) {
    http_request = false;
    
    var form = document.getElementById(formId);
    var fd = new FormData(form);
    if (window.XMLHttpRequest) { // Mozilla, Safari,...
        http_request = new XMLHttpRequest();
        if (http_request.overrideMimeType) {
            http_request.overrideMimeType('text/xml');
        }
    } else if (window.ActiveXObject) { // IE
        try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
        }
    }
    if (!http_request) {
        alert('Cannot create XMLHTTP instance');
        return false;
    }
    http_request.onreadystatechange=function(){
        if (http_request.readyState==4 && http_request.status==200){
            //alert('response from server:'+http_request.responseText);
            var div_element = document.getElementById(div_id);
            div_element.innerHTML=http_request.responseText;
     
            //makes sure the scripts contained in the page are executed after being 
            //loaded by ajax
            var scripts = div_element.getElementsByTagName('script');
            for(var i=0; i < scripts.length;i++)
            {			
                // if IE, we have to use execScript to define functions as global
                if (window.execScript)
                {
                    //Replaces the HTML comments because IE doesn't handle them well
                    window.execScript(scripts[i].text.replace('<!--',''));
                }
                // if any other web browser, we use a simple window.eval()
                else
                {
                    window.eval(scripts[i].text);
                }
            }
        }
    }
    //http_request.onreadystatechange = alertContents;
    http_request.open('POST', url + parameters, true);
    http_request.send(fd);
}