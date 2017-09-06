<?php

/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 *            Arnaud Wijns <awijns@ulb.ac.be>
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

require_once 'config.inc';
require_once __DIR__.'/../commons/lib_error.php';
require_once 'lib_various.php';
require_once 'lib_ezmam.php';
/**
 * This library contains functions that allow to get user's preferences
 * @package ezcast.ezplayer.lib.userPreferences
 */

/**
 *
 * @param [path $path of the user's files (optional)]
 * @return false|path
 * @desc tells the library where the user's files are. CALL THIS FUNCTION BEFORE ANYTHING ELSE!!!!
 * @desc if called without parameter, returns current user's files
 */
function user_prefs_repository_path($path = "")
{
    static $user_files_path = false;

    if ($path == "") {
        if ($user_files_path === false) {
            user_prefs_last_error("1 Error: user's files path not defined");
            return false;
        } else {
            return $user_files_path;
        }//if $user_files_path
    }//if $path
    //if path exists then store it
    $res = is_dir($path);
    if ($res) {
        $user_files_path = $path;
    } else {
        user_prefs_last_error("2 Error: user's files path not found: $path");
    }
    return $res;
}

/**
 * returns a list that contains all albums the user has already consulted
 * @param type $user the user_login
 * @return assoc_album_tokens || false : the list if user and album exist,
 * false otherwise
 */
function user_prefs_tokens_get($user)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    $assoc_album_tokens = array();

    // 1) set repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // 2) set user's file path
    $user_path = $user_files_path . "/" . $user;

    // 3) if the xml file exists, it is converted in associative array
    if (file_exists($user_path . "/_album_tokens.xml")) {
        $xml = simplexml_load_file($user_path . "/_album_tokens.xml");
        $assoc_album_tokens = xml_file2assoc_array($xml, 'album_token');
    }

    return $assoc_album_tokens;
}

/**
 * Deletes the file that contains the album tokens
 * @param type $user the owner of the file
 * @return boolean true if the file has been deleted; false otherwise
 */
function user_prefs_tokens_delete($user)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // set user's file path
    $user_path = $user_files_path . '/' . $user;
    unlink($user_path . "/_album_tokens.xml");
    return true;
}

/**
 * returns the album token if it exists, false otherwise
 * @param type $user the user_login
 * @param type $album the searched album
 * @return album_token || false : album_token if it exists, false otherwise
 */
function user_prefs_token_get($user, $album)
{

    // Get the list that contains all album tokens
    $token_list = acl_album_tokens_get();

    // if no result, the user and/or album are not correct
    if (!isset($token_list) || $token_list == false) {
        return false;
    }

    foreach ($token_list as $album_token) {
        if ($album_token['album'] == $album) {
            return $album_token;
        }
    }

    // no match found
    return false;
}

/**
 * checks if an album token exists in the list
 * @param type $user the user's login
 * @param type $album the searched album
 * @return index the position of the token in the list, -1 if it doesn't exist
 */
function user_prefs_token_index($user, $album)
{

    // Get the list that contains all album tokens
    $token_list = acl_album_tokens_get();

    // if no result, the user and/or album are not correct
    if (!isset($token_list) || $token_list == false) {
        return false;
    }

    foreach ($token_list as $index => $album_token) {
        if ($album_token['album'] == $album) {
            return $index;
        }
    }

    // no match found
    return -1;
}

/**
 * Adds album tokens in the album tokens file
 * @param type $user the owner of the file
 * @param type $tokens_array the list of tokens to be added
 * @return true if all tokens of the array have been added to the file; false otherwise
 */
function user_prefs_tokens_add($user, $tokens_array)
{

    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    if (count($tokens_array) == 0) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }
    // set user's file path
    $user_path = $user_files_path . '/' . $user;

    // if the user's directory doesn't exist yet, we create it
    if (!file_exists($user_path)) {
        mkdir($user_path, 0755, true);
    }

    // Get the albums list
    $token_list = acl_album_tokens_get();
    
    foreach ($tokens_array as &$token) {
        if (ezmam_album_exists($token['album']) && !token_array_contains($token_list, $token)) {
            array_unshift($token_list, $token);
        }
    }

    if (count($token_list) == 0) {
        return false;
    }
    // converts the array in xml file
    return assoc_array2xml_file($token_list, $user_path . "/_album_tokens.xml", "album_tokens", "album_token");
}

// checks if the array contains the token
function token_array_contains(&$array, $token)
{
    if (count($array) == 0) {
        return false;
    }
    
    foreach ($array as $index => $array_token) {
        if ($array_token['album'] == $token['album']) {
            if ($array_token['token'] != $token['token'] ||
                $array_token['title'] != $token['title']) {
                $array[$index]['token'] = $token['token'];
                $array[$index]['title'] = $token['title'];
            }
            return true;
        }
    }
    return false;
}

/**
 * adds the album token to the token list
 * @param type $user the user_login
 * @param type $album the album to add
 * @param type $title the album title
 * @param type $token the album token
 * @index type $index the position where the album token should be added
 * @return boolean true if album has been added to the list, false otherwise
 */
function user_prefs_token_add($user, $album, $title, $token, $index = 0)
{
    // Sanity check
    if (!isset($user) || $user == '' ||
            !ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // set user's file path
    $user_path = $user_files_path . '/' . $user;

    // if the user's directory doesn't exist yet, we create it
    if (!file_exists($user_path)) {
        mkdir($user_path, 0755, true);
    }
    // Get the albums list
    $token_list = acl_album_tokens_get();
    $album_token = array('title' => $title, 'album' => $album, 'token' => $token);
    if (!in_array($album_token, $token_list)) {
        // add a token at the specified index in the albums list
        array_splice($token_list, $index, 0, array(null));
        $token_list[$index] = $album_token;
        // converts the array in xml file
        return assoc_array2xml_file($token_list, $user_path . "/_album_tokens.xml", "album_tokens", "album_token");
    }
    return false;
}

/**
 * Updates the 'new assets' count
 * @param type $user the user
 * @param type $album the album to update
 * @return boolean
 */
function user_prefs_token_update_count($user, $album)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    $index = user_prefs_token_index($user, $album);
    if ($index >= 0) {
        // set user's file path
        $user_path = $user_files_path . '/' . $user;
        $token_list = user_prefs_tokens_get($user);
        // updates the count
        $count = count(ezmam_asset_list($album));
        $token_list[$index]['count'] = $count;
        return assoc_array2xml_file($token_list, $user_path . "/_album_tokens.xml", "album_tokens", "album_token");
    }

    return false;
}

/**
 * removes the album token from the user preferences
 * @param type $user the user_login
 * @param type $album the album to remove
 */
function user_prefs_token_remove($user, $album)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }
    if (!isset($album) || $album == '') {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // 2) loop on the albums list and removes the token if it exists
    $token_list = acl_album_tokens_get();
    $removed = false;
    foreach ($token_list as $index => $album_token) {
        if ($album_token['album'] == $album) {
            // if it is the only token contained in the file
            if (count($token_list) == 1) {
                // the file is deleted
                return user_prefs_tokens_delete($user);
            }
            // else
            unset($token_list[$index]);
            $removed = true;
        }
    }
    if ($removed) {
        $user_path = $user_files_path . '/' . $user;
        return assoc_array2xml_file($token_list, $user_path . "/_album_tokens.xml", "album_tokens", "album_token");
    }
    return false;
}

/**
 * removes the album token from the user preferences
 * @param type $user the user_login
 * @param type $index the index of the token to remove
 */
function user_prefs_token_remove_at($user, $index)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }
    if (!isset($index) || $index < 0) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // 2) loop on the albums list and removes the token if it exists
    $token_list = acl_album_tokens_get();
    if ($index > count($token_list)) {
        return false;
    }

    if (count($token_list) == 1) {
        return user_prefs_tokens_delete($user);
    }
    unset($token_list[$index]);
    $user_path = $user_files_path . '/' . $user;
    return assoc_array2xml_file($token_list, $user_path . "/_album_tokens.xml", "album_tokens", "album_token");
}

/**
 * Moves an album token from a position to an other position in the list
 * @param type $user the user
 * @param type $index the index of the token to move
 * @param type $new_index the new index for the token to move
 * @return the new list; false if an error occurs
 */
function user_prefs_token_swap($user, $index, $new_index)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }
    if (!isset($index) || $index < 0) {
        return false;
    }
    if (!isset($new_index) || $new_index < 0) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    $token_list = acl_album_tokens_get();
    $max_index = count($token_list);
    if ($index >= $max_index || $new_index >= $max_index) {
        return false;
    }

    $token = $token_list[$index];
    $token_list[$index] = $token_list[$new_index];
    $token_list[$new_index] = $token;

    $user_path = $user_files_path . '/' . $user;
    return assoc_array2xml_file($token_list, $user_path . "/_album_tokens.xml", "album_tokens", "album_token");
}

/**
 * Adds an asset to the list of all watched assets.
 * @param type $user
 * @param type $album
 * @param type $asset
 * @return boolean
 */
function user_prefs_watched_add($user, $album, $asset)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // 2) if the album is not in the list yet
    // set user's file path
    $user_path = $user_files_path . '/' . $user;

    // if the user's directory doesn't exist yet, we create it
    if (!file_exists($user_path)) {
        mkdir($user_path, 0755, true);
    }
    // Get the albums list
    $exist = false;
    $watched_list = user_prefs_watchedlist_get($user);
    foreach ($watched_list as $index => $watched) {
        if ($watched['album'] == $album) {
            $exist = true;
            if (!in_array($asset, $watched['assets'])) {
                array_push($watched['assets'], $asset);
            } else {
                return false;
            }
        }
        $watched_list[$index]['assets'] = serialize($watched['assets']);
    }
    if (!$exist) {
        $serialized_asset = serialize(array($asset));
        $watched_list[] = array('album' => $album, 'assets' => $serialized_asset);
    }
    // converts the array in xml file
    return assoc_array2xml_file($watched_list, $user_path . "/_watched_assets.xml", "watched_assets", "watched");
}

/**
 * Returns the list of all watched assets.
 * @param type $user
 * @return boolean false if an error occured; the list of watched assets otherwise.
 */
function user_prefs_watchedlist_get($user, $from_acl = true)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    $assoc_watched_assets = array();

    // 1) set repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }
    
    if ($from_acl && isset($_SESSION['acl_watched_assets']) && !empty($_SESSION['acl_watched_assets'])) {
        return $_SESSION['acl_watched_assets'];
    }

    // 2) set user's file path
    $user_path = $user_files_path . "/" . $user;
    // 3) if the xml file exists, it is converted in associative array
    if (file_exists($user_path . "/_watched_assets.xml")) {
        $xml = simplexml_load_file($user_path . "/_watched_assets.xml");
        $assoc_watched_assets = xml_file2assoc_array($xml, 'watched');
        foreach ($assoc_watched_assets as $index => $watched) {
            // the list of assets is serialized in the xml file. We need it as array
            $assoc_watched_assets[$index]['assets'] = unserialize($watched['assets']);
        }
    }

    return $assoc_watched_assets;
}

/**
 * Searches a specific pattern in one or more albums / assets / fields
 * @param type $user the user who owns the personal bookmarks
 * @param type $search the pattern to search (array containing a selection of words to find)
 * @param type $fields the bookmark fields where to search :
 * it can be the title, the description and/or the keywords
 * @param type $level the level where to search
 * @param type $albums an array containing the albums where to search
 * @param type $asset a string containing a specific asset
 * @return the list of matching bookmarks; false if an error occurs; null
 * no bookmark matches the pattern
 */
function user_prefs_bookmarks_search($user, $search, $fields, $level, $albums, $asset = '')
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return null;
    }

    if (!isset($level) || $level < 0 || $level > 4) {
        $level = 0;
    }

    $bookmarks = array();

    if (isset($asset) && $asset != '') {
        if (isset($albums[0]) && $albums[0] != '') {
            $bookmarks = user_prefs_asset_bookmarks_list_get($user, $albums[0], $asset);
        }
    } else {
        if (isset($albums) && count($albums) > 0) {
            foreach ($albums as $album) {
                $bookmarks = array_merge($bookmarks, user_prefs_album_bookmarks_list_get($user, $album));
            }
        }
    }

    // we have now the list of all eligible bookmarks to search in
    if ((!isset($search) || count($search) == 0) && $level == 0) {
        return $bookmarks;
    }

    return search_in_array($search, $bookmarks, $fields, $level);
}


/**
 * Returns all bookmarks of the given album
 * @param type $user the user
 * @param type $album the album
 * @return the bookmarks list ; false if an error occurs
 */
function user_prefs_album_bookmarks_list_get($user, $album)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    // 1) set repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }
    // 2) set user's file path
    $user_path = $user_files_path . "/" . $user;

    $assoc_album_bookmarks = array();
    // 3) if the xml file exists, it is converted in associative array
    if (file_exists($user_path . "/bookmarks_$album.xml")) {
        $xml = simplexml_load_file($user_path . "/bookmarks_$album.xml");
        if (!$xml) {
            return false;
        }
        $assoc_album_bookmarks = xml_file2assoc_array($xml, 'bookmark');
    }

    return $assoc_album_bookmarks;
}

/**
 * Returns all bookmarks of a specific asset in a given album
 * @param type $user the user
 * @param type $album the album
 * @param type $asset the asset
 * @return boolean|array the list of bookmarks ; false if an error occurs
 */
function user_prefs_asset_bookmarks_list_get($user, $album, $asset)
{
    $assoc_album_bookmarks = user_prefs_album_bookmarks_list_get($user, $album);
    if (!isset($assoc_album_bookmarks) || $assoc_album_bookmarks === false ||
            empty($assoc_album_bookmarks)) {
        return false;
    }

    $assoc_asset_bookmarks = array();
    $index = 0;
    $ref_asset = $assoc_album_bookmarks[$index]['asset'];
    $count = count($assoc_album_bookmarks);
    while ($index < $count && $asset <= $ref_asset) {
        if ($asset == $ref_asset) {
            array_push($assoc_asset_bookmarks, $assoc_album_bookmarks[$index]);
        }
        ++$index;
        if ($index < $count) {
            $ref_asset = $assoc_album_bookmarks[$index]['asset'];
        }
    }

    return $assoc_asset_bookmarks;
}

/**
 * Returns a selection of bookmarks in a specific asset of a given album
 * @param type $user the user
 * @param type $album the album
 * @param type $asset the asset or empty if the selection is just on the album
 * @param type $selection the list of positions for the bookmarks we want to keep
 * @return boolean|array the selection of bookmarks
 */
function user_prefs_asset_bookmarks_selection_get($user, $album, $asset, $selection)
{
    $bookmarks_list;
    $selected_bookmarks = array();

    if (!isset($album) || $album == '') {
        return false;
    }

    if (!isset($asset) || $asset == '') {
        // selection is on the whole album
        $bookmarks_list = user_prefs_album_bookmarks_list_get($user, $album);
    } else {
        // selection is on the specific asset
        $bookmarks_list = user_prefs_asset_bookmarks_list_get($user, $album, $asset);
    }

    if (count($bookmarks_list) > 0) {
        $min_index = 0;
        $max_index = count($bookmarks_list) - 1;
        foreach ($selection as $index) {
            if ($index >= $min_index && $index <= $max_index) {
                array_push($selected_bookmarks, $bookmarks_list[$index]);
            }
        }
        return $selected_bookmarks;
    } else {
        return false;
    }
}

/**
 * Determines if a specific bookmark exists
 * @param type $user the user
 * @param type $album the album
 * @param type $asset the asset
 * @param type $timecode the timecode of the bookmark
 * @return boolean true if the bookmark exists ; false otherwise
 */
function user_prefs_asset_bookmark_exists($user, $album, $asset, $timecode)
{
    $assoc_asset_bookmarks = user_prefs_asset_bookmarks_list_get($user, $album, $asset);
    if ($assoc_asset_bookmarks == false || !is_array($assoc_asset_bookmarks)) {
        return false;
    }
    
    foreach ($assoc_asset_bookmarks as $bookmark) {
        if ($bookmark['timecode'] == $timecode) {
            return true;
        }
    }
    return false;
}

/**
 * Gets a specific bookmark from the list
 * @param type $user the user
 * @param type $album the album
 * @param type $asset the asset
 * @param type $timecode the timecode of the bookmark
 * @return the bookmark if it exists; false otherwise
 */
function user_prefs_asset_bookmark_get($user, $album, $asset, $timecode)
{
    $assoc_asset_bookmarks = user_prefs_asset_bookmarks_list_get(trim($user), trim($album), trim($asset));
    if ($assoc_asset_bookmarks == false || !is_array($assoc_asset_bookmarks)) {
        return false;
    }
    foreach ($assoc_asset_bookmarks as $bookmark) {
        if ($bookmark['timecode'] == $timecode) {
            return $bookmark;
        }
    }
    return false;
}

/**
 * Adds a bookmark in the album bookmarks file
 * @param type $user the user
 * @param type $album the album
 * @param type $asset the asset
 * @param type $timecode the timecode of the bookmark
 * @param type $title the title of the bookmark
 * @param type $description the description of the bookmark
 * @param type $keywords the keywords of the bookmark
 * @param type $level the level of the bookmark
 * @return boolean
 */
function user_prefs_asset_bookmark_add(
    $user,
    $album,
    $asset,
    $timecode,
    $title = '',
        $description = '',
    $keywords = '',
    $level = '1',
    $type = ''
) {
    global $logger;
    
    // Sanity check
    if (!isset($user) || $user == '' ||
            !ezmam_album_exists($album) ||
            !ezmam_asset_exists($album, $asset) ||
            !isset($timecode) || $timecode == '' || $timecode < 0) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // set user's file path
    $user_path = $user_files_path . '/' . $user;
    // remove the previous same bookmark if it existed yet
    $delete_ok = user_prefs_asset_bookmark_delete($user, $album, $asset, $timecode);
    if (!$delete_ok) {
        $logger->log(
            EventType::MANAGER_BOOKMARKS,
            LogLevel::ERROR,
            "Could not delete last bookmark (user: "
                . "$user, album: $album, asset: $asset, timecode: $timecode). New bookmark cannot be added.",
                array(__FUNCTION__)
        );
        return false;
    }
    
    // if the user's directory doesn't exist yet, we create it
    if (!file_exists($user_path)) {
        mkdir($user_path, 0755, true);
    }

    // Get the bookmarks list
    $bookmarks_list = user_prefs_album_bookmarks_list_get($user, $album);
    $count = $bookmarks_list === false ? 0 : count($bookmarks_list);
    $index = 0;

    if ($count > 0) {
        $index = -1;
        $asset_ref = $bookmarks_list[0]['asset'];
        $timecode_ref = $bookmarks_list[0]['timecode'];
        // loop while the asset is older than the reference asset
        while ($index < ($count-1) && $asset < $asset_ref) {
            ++$index;
            $asset_ref = $bookmarks_list[$index]['asset'];
            $timecode_ref = $bookmarks_list[$index]['timecode'];
        }
        // if the asset already contains bookmarks, loop while
        // timecode is bigger than reference timecode
        while ($index < $count && $asset == $asset_ref && $timecode > $timecode_ref) {
            ++$index;
            $timecode_ref = $bookmarks_list[$index]['timecode'];
            $asset_ref = $bookmarks_list[$index]['asset'];
        }

        if ($index < 0) { // no bookmark yet
            $index = 0;
        }
        if ($index > $count) { // add in last index
            --$index;
        }
    }
    
    $logger->log(EventType::MANAGER_BOOKMARKS, LogLevel::DEBUG, "BOOKMARK bookmarks_list: " .
            print_r($bookmarks_list, true) .", count: $count, index: $index", array(__FUNCTION__));

    // extract keywords from the description
    $keywords_array = get_keywords($description);
    // and save them as keywords
    foreach ($keywords_array as $keyword) {
        if (strlen($keywords) > 0) {
            $keywords .= ', ';
        }
        $keywords .= $keyword;
    }
    // surround every url by '*' for url recognition in EZplayer
    $description = surround_url($description);

    // add a bookmark at the specified index in the albums list
    array_splice($bookmarks_list, $index, 0, array(null));
    $bookmarks_list[$index] = array('album' => $album, 'asset' => $asset, 'timecode' => $timecode,
        'title' => $title, 'description' => $description, 'keywords' => $keywords, 'level' => $level, 'type' => $type);

    return assoc_array2xml_file($bookmarks_list, $user_path . "/bookmarks_$album.xml", "bookmarks", "bookmark");
}

/**
 * Adds a list of bookmarks in the bookmarks file.
 * @param type $user the user
 * @param type $bookmarks an array of bookmarks
 * @return boolean true if the bookmarks have been added; false otherwise
 */
function user_prefs_album_bookmarks_add($user, $bookmarks)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    if (!isset($bookmarks) || count($bookmarks) == 0) {
        return false;
    }

    $album = $bookmarks[0]['album'];

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // if the user's directory doesn't exist yet, we create it
    if (!file_exists($user_path)) {
        mkdir($user_path, 0755, true);
    }

    // set user's file path
    $user_path = $user_files_path . '/' . $user;

    // Get the bookmarks list
    $bookmarks_list = user_prefs_album_bookmarks_list_get($user, $album);

    if (!isset($bookmarks_list) || $bookmarks_list == false) {
        $bookmarks_list = array();
    }

    foreach ($bookmarks as $bookmark) {
        if ($bookmark['album'] == $album && ezmam_asset_exists($album, $bookmark['asset']) && $bookmark['timecode'] >= 0) {
            $count = count($bookmarks_list);
            $index = 0;

            if ($count > 0) {
                $index = -1;
                $asset_ref = $bookmarks_list[0]['asset'];
                $timecode_ref = $bookmarks_list[0]['timecode'];
                // loop while the asset is older than the reference asset
                while ($index < $count && $bookmark['asset'] < $asset_ref) {
                    ++$index;
                    $asset_ref = $bookmarks_list[$index]['asset'];
                    $timecode_ref = $bookmarks_list[$index]['timecode'];
                }
                // if the asset already contains bookmarks, loop while
                // timecode is bigger than reference timecode
                while ($index < $count && $bookmark['asset'] == $asset_ref && $bookmark['timecode'] >= $timecode_ref) {
                    ++$index;
                    $timecode_ref = $bookmarks_list[$index]['timecode'];
                    $asset_ref = $bookmarks_list[$index]['asset'];
                }

                if ($index < 0) { // no bookmark yet
                    $index = 0;
                }
                if ($index > $count) { // add in last index
                    --$index;
                }
                if ($bookmark['asset'] == $bookmarks_list[$index - 1]['asset'] &&
                        $bookmark['timecode'] == $bookmarks_list[$index - 1]['timecode']) {
                    $bookmarks_list[$index - 1] = $bookmark;
                } else {
                    // add a bookmark at the specified index in the albums list
                    array_splice($bookmarks_list, $index, 0, array(null));
                    $bookmarks_list[$index] = $bookmark;
                }
            } else {
                $bookmarks_list[0] = $bookmark;
            }
        }
    }

    return assoc_array2xml_file($bookmarks_list, $user_path . "/bookmarks_$album.xml", "bookmarks", "bookmark");
}

/**
 * Removes a bookmark from the bookmarks file. If it is the last bookmark of
 * the file, the file is deleted
 * @param type $user the user
 * @param type $album the album
 * @param type $asset the asset
 * @param type $timecode the timecode of the bookmark
 * @return boolean true if the bookmark has been deleted or does not exists; false if failure
 */
function user_prefs_asset_bookmark_delete($user, $album, $asset, $timecode)
{
    global $logger;
    
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    if (!ezmam_album_exists($album)) {
        return false;
    }

    if (!isset($timecode) || $timecode == '' || $timecode < 0) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // set user's file path
    $user_path = $user_files_path . '/' . $user;

    if (user_prefs_asset_bookmark_exists($user, $album, $asset, $timecode)) {
        $logger->log(EventType::MANAGER_BOOKMARKS, LogLevel::DEBUG, "Bookmark already exists (user: $user, album: "
                . "$album, asset: $asset, timecode: $timecode)", array(__FUNCTION__));

        $bookmarks_list = user_prefs_album_bookmarks_list_get($user, $album);

        // if there is no bookmark anymore, the file is deleted
        if (count($bookmarks_list) == 1) {
            return user_prefs_album_bookmarks_delete_all($user, $album);
        }
        foreach ($bookmarks_list as $index => $bookmark) {
            if ($bookmark['asset'] == $asset && $bookmark['timecode'] == $timecode) {
                unset($bookmarks_list[$index]);
                break;
            }
        }
        return assoc_array2xml_file($bookmarks_list, $user_path . "/bookmarks_$album.xml", "bookmarks", "bookmark");
    }
    return true;
}

/**
 * Removes a list of bookmarks from the bookmarks file
 * @param type $user the user
 * @param type $bookmarks the array of bookmarks to be deleted
 * @return boolean true if the bookmarks have been removed; false otherwise
 */
function user_prefs_album_bookmarks_delete($user, $bookmarks)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }


    if (!isset($bookmarks) || count($bookmarks) == 0) {
        return false;
    }

    $album = $bookmarks[0]['album'];

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // set user's file path
    $user_path = $user_files_path . '/' . $user;

    $bookmarks_list = user_prefs_album_bookmarks_list_get($user, $album);

    foreach ($bookmarks as $bookmark) {
        if ($bookmark['album'] == $album && $bookmark['timecode'] >= 0) {
            foreach ($bookmarks_list as $index => $ref_bookmark) {
                if ($bookmark['album'] == $ref_bookmark['album'] && $bookmark['asset'] == $ref_bookmark['asset'] &&
                        $bookmark['timecode'] == $ref_bookmark['timecode']) {
                    unset($bookmarks_list[$index]);
                    break;
                }
            }
        }
    }

    if (count($bookmarks_list) == 0) {
        return user_prefs_album_bookmarks_delete_all($user, $album);
    } else {
        return assoc_array2xml_file($bookmarks_list, $user_path . "/bookmarks_$album.xml", "bookmarks", "bookmark");
    }
}

/**
 * Removes all bookmarks of a specific asset from the bookmarks file
 * @param type $user the user
 * @param type $album the album
 * @param type $asset the asset we want to remove bookmarks from
 * @return boolean true if the bookmarks have been deleted; false otherwise
 */
function user_prefs_asset_bookmarks_delete($user, $album, $asset)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // set user's file path
    $user_path = $user_files_path . '/' . $user;

    $bookmarks_list = user_prefs_album_bookmarks_list_get($user, $album);
    foreach ($bookmarks_list as $index => $bookmark) {
        if ($bookmark['asset'] == $asset) {
            unset($bookmarks_list[$index]);
        }
    }
    // if there is no bookmark anymore, the file is deleted
    if (count($bookmarks_list) == 0) {
        return user_prefs_album_bookmarks_delete_all($user, $album);
    }
    return assoc_array2xml_file($bookmarks_list, $user_path . "/bookmarks_$album.xml", "bookmarks", "bookmark");
}

/**
 * Deletes the file containing all bookmarks of the given album
 * @param type $user the user
 * @param type $album the album
 * @return boolean true if the file has been deleted; false otherwise
 */
function user_prefs_album_bookmarks_delete_all($user, $album)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // set user's file path
    $user_path = $user_files_path . '/' . $user;
    unlink($user_path . "/bookmarks_$album.xml");
    return true;
}

/**
 * Saves user's settings such as order sorting for bookmarks, ...
 * @param type $user
 * @param type $key
 * @param type $value
 * @return boolean
 */
function user_prefs_settings_edit($user, $key, $value)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    // 1) set the repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }

    // set user's file path
    $user_path = $user_files_path . '/' . $user;

    // if the user's directory doesn't exist yet, we create it
    if (!file_exists($user_path)) {
        mkdir($user_path, 0755, true);
    }

    $settings = user_prefs_settings_get($user);
    $settings[$key] = $value;

    // converts the array in xml file
    return simple_assoc_array2xml_file($settings, $user_path . "/_settings.xml", "settings");
}

function user_prefs_settings_get($user)
{
    // Sanity check
    if (!isset($user) || $user == '') {
        return false;
    }

    // 1) set repository path
    $user_files_path = user_prefs_repository_path();
    if ($user_files_path === false) {
        return false;
    }
    $settings = array();

    // 2) set user's file path
    $user_path = $user_files_path . "/" . $user;

    // 3) if the xml file exists, it is converted in associative array
    if (file_exists($user_path . "/_settings.xml")) {
        $xml = simplexml_load_file($user_path . "/_settings.xml");
        $settings = xml_file2assoc_array($xml);
    }

    return $settings;
}

/**
 *
 * @param [string $msg error meesage (optional)]
 * @return string error message
 * @desc Store and return last error message in ezmam library
 */
function user_prefs_last_error($msg = "")
{
    static $last_error = "";

    if ($msg == "") {
        return $last_error;
    } else {
        $last_error = $msg;
        return true;
    }
}

/**
 * Change the value of a user preference
 * @global type $user_files_path
 * @param type $user
 * @param type $key
 * @param type $value
 * @return boolean
 */
function user_prefs_settings_update($user, $key, $value)
{
    global $user_files_path;
    if (!isset($user) || $user == '') {
        return false;
    }

    if ($user_files_path === false) {
        return false;
    }

    $user_path = $user_files_path . '/' . $user;
    $setting_path = $user_path . "/_settings.xml";

    if (!file_exists($user_path)) {
        mkdir($user_path, 0755, true);
    }
    if (!file_exists($setting_path)) {
        file_put_contents($setting_path, '<settings></settings>');
    }

    $settings = simplexml_load_file($setting_path);
    switch ($key) {
        case 'bookmarks_order':
            $settings->bookmarks_order = $value;
            break;
        case 'display_new_video_notification':
            $settings->display_new_video_notification = $value;
            break;
        case 'display_threads':
            $settings->display_threads = $value;
            break;
        case 'display_thread_notification':
            $settings->display_thread_notification = $value;
            break;
    }

    return $settings->asXML($setting_path);
}
