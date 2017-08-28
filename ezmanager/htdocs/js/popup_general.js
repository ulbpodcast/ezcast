/*
* EZCAST EZmanager 
*
* Copyright (C) 2016 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
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

/**
 * Takes the content of a div INCLUDED INSIDE THE PAGE (in a hidden div for instance), and displays it in a new popup on top of the page
 */
function show_popup_from_inner_div(destination) {
    $.colorbox({opacity: 0.50, inline: true, overlayClose: false, href: destination});
}

/**
 * Calls the destination page given as a parameter, and displays its result in a new popupon top of the page.
 */
function show_popup_from_outer_div(destination) {
    console.log('show_popup_from_outer_div Dest: ' + destination);
    // $.colorbox({opacity: 0.50, overlayClose: false, href: destination});
}

function close_popup() {
    $.colorbox.close();
}