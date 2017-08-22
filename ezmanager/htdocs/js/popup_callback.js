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

function popup_album_new_callback(album) {
    show_popup_from_outer_div('index.php?action=create_album&amp;album='+album);
    show_album_details(album);
    $.colorbox.close();
}

/**
 * Called when the user chooses to delete an asset: this method calls the controller, then reloads the view
 */
function popup_asset_delete_callback(album, asset) {
    show_popup_from_outer_div('index.php?action=delete_asset&album='+album+'&asset='+asset);
    show_album_details(album);
    $.colorbox.close();
}

function popup_asset_move_callback(from, to, asset) {
    show_popup_from_outer_div('index.php?action=move_asset&from='+from+'&to='+to+'&&asset='+asset);
    show_album_details(from);
    $.colorbox.close();
}

function popup_asset_copy_callback(from, to, asset) {
    show_popup_from_outer_div('index.php?action=copy_asset&from='+from+'&to='+to+'&&asset='+asset);
    show_album_details(from);
    $.colorbox.close();
}

function popup_asset_publish_callback(album, asset) {
    show_popup_from_outer_div('index.php?action=publish_asset&album='+album+'&asset='+asset);
    show_album_details(album);
    $.colorbox.close();
}

function popup_asset_unpublish_callback(album, asset) {
    show_popup_from_outer_div('index.php?action=unpublish_asset&album='+album+'&asset='+asset);
    show_album_details(album);
    $.colorbox.close();
}

function popup_regenerate_rss_callback(album) {
    show_popup_from_outer_div('index.php?action=reset_rss&album='+album);
    show_album_details(album);
    //$.colorbox.close();
}

function popup_asset_regentitle_callback(album, asset) {
    show_popup_from_outer_div('index.php?action=regen_title&album='+album+'&asset='+asset);
    show_album_details(album);
    $.colorbox.close();
}