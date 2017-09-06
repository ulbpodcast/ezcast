<?php

/*
 * EZCAST EZmanager
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	   Arnaud Wijns <awijns@ulb.ac.be>
 *         Antoine Dewilde
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
 * @package ezcast.ezmanager.lib.acl
 */

/**
 * This file contains all methods related to access control.
 * NOTE: This library needs session vars, so please call session_start() first
 */

require_once dirname(__FILE__) . '/config.inc';
require_once dirname(__FILE__) . '/../commons/lib_courses.php';
require_once dirname(__FILE__) . '/lib_ezmam.php';

/**
 * First method to call when using the lib_acl
 * @param string $netid the user's netID
 * @return bool error status
 */
function acl_init($netid)
{
    // initializing ezmam (we'll need it later)
    global $repository_path;
    ezmam_repository_path($repository_path);
    
    // Retrieving the permissions
    acl_update_permissions_list();
    
    // All is set, we're good to go
    return true;
}

/**
 * Upon calling this function, the user will no longer be identified, and hence won't have access to any of the other ACL functions
 */
function acl_exit()
{
    unset($_SESSION['acl_created_albums']); // Array of all created albums, by album name
    unset($_SESSION['acl_created_albums_descriptions']); // Array of all created albums, by album name
    unset($_SESSION['acl_not_created_albums']); // Array of all albums the user could create bus has not, by album name
    unset($_SESSION['acl_not_created_albums_descriptions']); // Array of all albums the user could create bus has not, by album name
    unset($_SESSION['acl_permitted_albums']);
    unset($_SESSION['acl_permitted_albums_descriptions']);
}

/**
 * Updates the (locally stored) list of albums created/not created
 */
function acl_update_permissions_list()
{
    // Checking which courses the user can manage, which have been created, which have not
    $courses_id_list_for_author = courses_list($_SESSION['user_login']);
    $existing_albums = ezmam_album_list();
    //albums are actually courses_id with a suffix, get ids by removing those (not very robust to changes...)
    $existing_courses_id = array();
    foreach ($existing_albums as $album) {
        array_push($existing_courses_id, suffix_remove($album));
    }
    
    /*print_r($courses_id_list_for_author);
    echo '<br/>';
    print_r($existing_courses_id);*/
    
    // By comparing $courses_list_for_autor and $existing_albums, we're able
    // to infer which album has already been created and which hasn't
    // So we store this information in various arrays
    $albums_created_array = array(); // Albums already created (name only)
    $albums_created_array_descriptions = array(); // Album created (name as a key and description as a value)
    $albums_not_created_array = array();
    $albums_not_created_array_descriptions = array();
    
    // For each course an author can manage, we check whether or not albums exist
    foreach ($courses_id_list_for_author as $course) {
        $course_infos = explode('|', $course, 2); // courses_list gives us the information as name|description, so we need to change that
        $course_id = $course_infos[0];
        $course_description = $course_infos[1];
        
        if (in_array($course_id, $existing_courses_id)) {
            $albums_created_array[] = $course_id;
            $albums_created_array_descriptions[$course_id] = $course_description;
        } else {
            $albums_not_created_array[] = $course_id;
            $albums_not_created_array_descriptions[$course_id] = $course_description;
        }
    }
    
    // Finally, we save the information in session vars
    $_SESSION['acl_created_albums'] = $albums_created_array; // Array of all created albums, by album name
    $_SESSION['acl_created_albums_descriptions'] = $albums_created_array_descriptions; // Associative array listing all album names (key) and description (value)
    $_SESSION['acl_not_created_albums'] = $albums_not_created_array; // Array of all albums the user could create bus has not, by album name
    $_SESSION['acl_not_created_albums_descriptions'] = $albums_not_created_array_descriptions; // Associative array listing all album names (key) and description (value)
    $_SESSION['acl_permitted_albums' ] = array_merge($albums_created_array, $albums_not_created_array);
    $_SESSION['acl_permitted_albums_descriptions'] = array_merge($albums_created_array_descriptions, $albums_not_created_array_descriptions);
}

/**
 * Checks whether a user has access to an album
 * @param string $album Name of the album we want to access
 * @return bool true if user can access $album, false otherwise
 */
function acl_has_album_permissions($album)
{
    if (!acl_user_is_logged()) {
        error_print_message('Error: acl_has_album_permissions: You are not logged in');
        return false;
    }
    
    return (in_array(suffix_remove($album), $_SESSION['acl_permitted_albums']));
}

/**
 * @param bool $assoc(false) If set to true, the function returns an associative array listing every album by its name (key) and its description (value). If set to false, only the album name (value) is returned
 * @return array|false An array with all album names the user can manage, or false if error occurred
 */
function acl_authorized_albums_list($assoc = false)
{
    if (!acl_user_is_logged()) {
        error_print_message('Error: acl_authorized_albums_list: You are not logged in');
        return false;
    }
    
    if ($assoc) {
        return $_SESSION['acl_permitted_albums_descriptions'];
    } else {
        return $_SESSION['acl_permitted_albums'];
    }
}

/**
 * @param bool $assoc(false) If set to true, the function returns an associative array listing every album by its name (key) and its description (value). If set to false, only the album name (value) is returned
 * @return array|false An array with all the albums the user can create
 */
function acl_authorized_albums_list_created($assoc = false)
{
    if (!acl_user_is_logged()) {
        error_print_message('Error: acl_authorized_albums_list_created: You are not logged in');
        return false;
    }
    
    if ($assoc) {
        return $_SESSION['acl_created_albums_descriptions'];
    } else {
        return $_SESSION['acl_created_albums'];
    }
}

/**
 * @param bool $assoc(false) If set to true, the function returns an associative array listing every album by its name (key) and its description (value). If set to false, only the album name (value) is returned
 * @return array|false An array with all the album names the user can use but still hasn't
 */
function acl_authorized_albums_list_not_created($assoc = false)
{
    if (!acl_user_is_logged()) {
        error_print_message('Error: acl_authorized_albums_list_not_created: You are not logged in');
        return false;
    }
    
    if ($assoc) {
        return $_SESSION['acl_not_created_albums_descriptions'];
    } else {
        return $_SESSION['acl_not_created_albums'];
    }
}

function acl_user_is_logged()
{
    return (isset($_SESSION['podman_logged']) && !empty($_SESSION['podman_logged']));
}
