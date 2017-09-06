<?php
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

/**
* @package ezcast.ezplayer.lib.toc
*/
     
require_once 'config.inc';
require_once __DIR__.'/../commons/lib_error.php';
require_once 'lib_various.php';
require_once 'lib_ezmam.php';


/**
 * Searches a specific pattern in one or more albums / assets / fields
 * @param type $search the pattern to search (array containing a selection of words to find)
 * @param type $fields the bookmark fields where to search :
 * it can be the title, the description and/or the keywords
 * @param type $level the level where to search
 * @param type $albums an array containing the albums where to search
 * @param type $asset a string containing a specific asset
 * @return the list of matching bookmarks; false if an error occurs; null
 * no bookmark matches the pattern
 */
function toc_bookmarks_search($search, $fields, $level, $albums, $asset = '')
{
    if (!isset($level) || $level < 0 || $level > 4) {
        $level = 0;
    }

    $bookmarks = array();

    if (isset($asset) && $asset != '') {
        if (isset($albums[0]) && $albums[0] != '') {
            $bookmarks = toc_asset_bookmark_list_get($albums[0], $asset);
        }
    } else {
        if (isset($albums) && count($albums) > 0) {
            foreach ($albums as $album) {
                $bookmarks = array_merge($bookmarks, toc_album_bookmarks_list_get($album));
            }
        }
    }

    if ((!isset($search) || count($search) == 0) && $level == 0) {
        return $bookmarks;
    }

    return search_in_array($search, $bookmarks, $fields, $level);
}

/**
 * Returns the list of (official) bookmarks for a given album
 * @param type $album the name of the album
 * @return the list of bookmarks for a given album; false if an error occurs
 */
function toc_album_bookmarks_list_get($album)
{
    // Sanity check

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set repository path
    $toc_path = ezmam_repository_path();
    if ($toc_path === false) {
        return false;
    }
    // 2) set user's file path
    $toc_path = $toc_path . "/" . $album;

    $assoc_album_bookmarks = array();
    // 3) if the xml file exists, it is converted in associative array
    if (file_exists($toc_path . "/_bookmarks.xml")) {
        $xml = simplexml_load_file($toc_path . "/_bookmarks.xml");
        if (!$xml) {
            return false;
        }
        $assoc_album_bookmarks = xml_file2assoc_array($xml, 'bookmark');
    }

    return $assoc_album_bookmarks;
}

/**
 * Returns the list of (official) bookmarks for a specific asset in a given album
 * @param type $album the name of the album
 * @param type $asset the name of the asset
 * @return boolean|array the list of bookmarks related to the given asset,
 * false if an error occurs
 */
function toc_asset_bookmark_list_get($album, $asset)
{
    $assoc_album_bookmarks = toc_album_bookmarks_list_get($album);
    if (!isset($assoc_album_bookmarks) || $assoc_album_bookmarks === false ||
            empty($assoc_album_bookmarks)) {
        //echo '(3)';
        return false;
    }

    $assoc_asset_bookmarks = array();
    $index = 0;
    $ref_asset = $assoc_album_bookmarks[$index]['asset'];
    $count = count($assoc_album_bookmarks);
    while ($index < $count && $asset >= $ref_asset) {
        if ($asset == $ref_asset) {
            array_push($assoc_asset_bookmarks, $assoc_album_bookmarks[$index]);
        }
        ++$index;
        //update ref_asset for next loop
        if ($index < $count) {
            $ref_asset = $assoc_album_bookmarks[$index]['asset'];
        }
    }

    return $assoc_asset_bookmarks;
}

/**
 * Returns a selection of bookmarks for a specific asset in a given album
 * @param type $album the album containing the bookmarks
 * @param type $asset the asset (not mandatory)
 * @param type $selection a selection of indexes in the bookmark list
 * @return boolean|array the list of selected bookmarks; false if an error occurs
 */
function toc_asset_bookmarks_selection_get($album, $asset, $selection)
{
    $bookmarks_list;
    $selected_bookmarks = array();
    
    if (!isset($album) || $album == '') {
        //echo "(1)";
        return false;
    }

    if (!isset($asset) || $asset == "") {
        $bookmarks_list = toc_album_bookmarks_list_get($album);
    } else {
        $bookmarks_list = toc_asset_bookmark_list_get($album, $asset);
    }
    
    if ($bookmarks_list == false) {
        //echo "(2)";
        return false;
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
        //echo "(3)";
        return false;
    }
}

/**
 * Determines if an official bookmark exists
 * @param type $album the album where to search the bookmark
 * @param type $asset the asset where to search the bookmark
 * @param type $timecode the timecode of the bookmark
 * @return boolean true if the bookmark exists; false otherwise
 */
function toc_asset_bookmark_exists($album, $asset, $timecode)
{
    $assoc_asset_bookmarks = toc_album_bookmarks_list_get($album);
    foreach ($assoc_asset_bookmarks as $bookmark) {
        if ($bookmark['asset'] == $asset
                && $bookmark['timecode'] == $timecode) {
            return true;
        }
    }
    return false;
}

/**
 * Gets a specific bookmark
 * @param type $album the album where to search the bookmark
 * @param type $asset the asset where to search the bookmark
 * @param type $timecode the timecode of the bookmark
 * @return the bookmark if it exists; false otherwise
 */
function toc_asset_bookmark_get($album, $asset, $timecode)
{
    $assoc_asset_bookmarks = toc_album_bookmarks_list_get($album);
    foreach ($assoc_asset_bookmarks as $bookmark) {
        if ($bookmark['asset'] == $asset
                && $bookmark['timecode'] == $timecode) {
            return $bookmark;
        }
    }
    return false;
}

/**
 * Moves all bookmarks of an asset from the public to the private album and reverse.
 * @param type $album
 * @param type $asset
 */
function toc_album_bookmarks_swap($album, $asset)
{
    $bookmarks = toc_asset_bookmark_list_get($album, $asset);
    toc_asset_bookmarks_delete_all($album, $asset);
    $album = suffix_replace($album);
    $count = count($bookmarks);
    for ($index = 0; $index < $count; $index++) {
        $bookmarks[$index]['album'] = $album;
    }
    toc_album_bookmarks_add($bookmarks);
}

/**
 * Adds a bookmark in the bookmarks file (table of contents)
 * @param type $album the album the bookmark is intended to
 * @param type $asset the asset the bookmark is intended to
 * @param type $timecode the specific time code of the bookmark
 * @param type $title the title of the bookmark
 * @param type $description the description of the bookmark
 * @param type $keywords the keywords of the bookmark
 * @param type $level the level of the bookmark
 * @return boolean true if the bookmark has been added to the table of contents;
 * false otherwise
 */
function toc_asset_bookmark_add($album, $asset, $timecode, $title = '', $description = '', $keywords = '', $level = '1', $type = '')
{
    // Sanity check

    if (!ezmam_album_exists($album)) {
        return false;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        return false;
    }

    if (!isset($timecode) || $timecode == '' || $timecode < 0) {
        return false;
    }

    // 1) set the repository path
    $toc_path = ezmam_repository_path();
    if ($toc_path === false) {
        return false;
    }

    // set user's file path
    $toc_path = $toc_path . '/' . $album;
    // remove the previous same bookmark if it existed yet
    toc_asset_bookmark_delete($album, $asset, $timecode);

    // Get the bookmarks list
    $bookmarks_list = toc_album_bookmarks_list_get($album);
    $count = count($bookmarks_list);
    $index = 0;

    if ($count > 0) {
        $index = -1;
        
        // loop while the asset is older than the reference asset
        do {
            ++$index;
            $asset_ref = $bookmarks_list[$index]['asset'];
            $timecode_ref = $bookmarks_list[$index]['timecode'];
        } while ($index < ($count-1) && $asset > $asset_ref);
        
        // if the asset already contains bookmarks, loop while
        // timecode is bigger than reference timecode
        while ($index < ($count-1) && $asset == $asset_ref && $timecode > $timecode_ref) {
            ++$index;
            $timecode_ref = $bookmarks_list[$index]['timecode'];
            $asset_ref = $bookmarks_list[$index]['asset'];
        }

        if ($index < 0) { // no bookmarks yet
            $index = 0;
        }
        if ($index > $count) { // bookmark is in last position in the table of contents
            --$index;
        }
    }
    
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

    return assoc_array2xml_file($bookmarks_list, $toc_path . "/_bookmarks.xml", "bookmarks", "bookmark");
}

/**
 * Adds a list of bookmarks in the bookmarks file.
 * @param type $bookmarks an array of bookmarks
 * @return boolean true if the bookmarks have been added; false otherwise
 */
function toc_album_bookmarks_add($bookmarks)
{
    // Sanity check
    if (!isset($bookmarks) || count($bookmarks) == 0) {
        return false;
    }

    $album = $bookmarks[0]['album'];

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $toc_path = ezmam_repository_path();
    if ($toc_path === false) {
        return false;
    }

    $toc_path = $toc_path . '/' . $album;

    // Get the bookmarks list
    $bookmarks_list = toc_album_bookmarks_list_get($album);

    if (!isset($bookmarks_list) || $bookmarks_list == false) {
        $bookmarks_list = array();
    }

    foreach ($bookmarks as $bookmark) {
        if ($bookmark['album'] == $album
                && ezmam_asset_exists($album, $bookmark['asset'])
                && $bookmark['timecode'] >= 0) {
            $count = count($bookmarks_list);
            $index = 0;

            if ($count > 0) {
                $index = -1;
                $asset_ref = $bookmarks_list[0]['asset'];
                $timecode_ref = $bookmarks_list[0]['timecode'];
                // loop while the asset is older than the reference asset
                while ($index < $count && $bookmark['asset'] > $asset_ref) {
                    ++$index;
                    $asset_ref = $bookmarks_list[$index]['asset'];
                    $timecode_ref = $bookmarks_list[$index]['timecode'];
                }
                // if the asset already contains bookmarks, loop while
                // timecode is bigger than reference timecode
                while ($index < $count
                && $bookmark['asset'] == $asset_ref
                && $bookmark['timecode'] >= $timecode_ref) {
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
                if ($bookmark['asset'] == $bookmarks_list[$index - 1]['asset']
                        && $bookmark['timecode'] == $bookmarks_list[$index - 1]['timecode']) {
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

    return assoc_array2xml_file($bookmarks_list, $toc_path . "/_bookmarks.xml", "bookmarks", "bookmark");
}

/**
 * Removes a specific bookmark from the bookmarks file (table of contents).
 * If it is the last bookmark contained in the file, the file is deleted.
 * @param type $album the album of the bookmark
 * @param type $asset the asset of the bookmark
 * @param type $timecode the timecode of the bookmark
 * @return an array of bookmarks if the bookmark has been deleted; false otherwise
 */
function toc_asset_bookmark_delete($album, $asset, $timecode)
{
    // Sanity check
    if (!ezmam_album_exists($album)) {
        return false;
    }

    if (!isset($timecode) || $timecode == '' || $timecode < 0) {
        return false;
    }

    // 1) set the repository path
    $toc_path = ezmam_repository_path();
    if ($toc_path === false) {
        return false;
    }

    // set user's file path
    $toc_path = $toc_path . '/' . $album;

    if (toc_asset_bookmark_exists($album, $asset, $timecode)) {
        $bookmarks_list = toc_album_bookmarks_list_get($album);

        // if it is the last bookmark in the file, the file is deleted
        if (count($bookmarks_list) == 1) {
            return toc_album_bookmarks_delete_all($album);
        }
        foreach ($bookmarks_list as $index => $bookmark) {
            if ($bookmark['asset'] == $asset
                    && $bookmark['timecode'] == $timecode) {
                unset($bookmarks_list[$index]);
            }
        }
        // rewrites the bookmarks file
        return assoc_array2xml_file($bookmarks_list, $toc_path . "/_bookmarks.xml", "bookmarks", "bookmark");
    }
}

/**
 * Deletes a selection of bookmarks in the official bookmarks file
 * @param type $bookmarks
 * @return boolean
 */
function toc_album_bookmarks_delete($bookmarks)
{
    // Sanity check
    if (!isset($bookmarks) || count($bookmarks) == 0) {
        return false;
    }

    $album = $bookmarks[0]['album'];

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $toc_path = ezmam_repository_path();
    if ($toc_path === false) {
        return false;
    }

    // set user's file path
    $toc_path = $toc_path . '/' . $album;

    $bookmarks_list = toc_album_bookmarks_list_get($album);

    foreach ($bookmarks as $bookmark) {
        if ($bookmark['album'] == $album
                && $bookmark['timecode'] >= 0) {
            foreach ($bookmarks_list as $index => $ref_bookmark) {
                if ($bookmark['album'] == $ref_bookmark['album']
                        && $bookmark['asset'] == $ref_bookmark['asset']
                        && $bookmark['timecode'] == $ref_bookmark['timecode']) {
                    unset($bookmarks_list[$index]);
                    break;
                }
            }
        }
    }

    if (count($bookmarks_list) == 0) {
        return toc_album_bookmarks_delete_all($album);
    } else {
        return assoc_array2xml_file($bookmarks_list, $toc_path . "/_bookmarks.xml", "bookmarks", "bookmark");
    }
}

/**
 * Removes all bookmarks of a given asset
 * @param type $album the album containing bookmarks
 * @param type $asset the asset we want to remove bookmarks from
 * @return an array of bookmarks if the bookmarks have been deleted; false otherwise
 */
function toc_asset_bookmarks_delete_all($album, $asset)
{
    // Sanity check

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $toc_path = ezmam_repository_path();
    if ($toc_path === false) {
        return false;
    }

    // set user's file path
    $toc_path = $toc_path . '/' . $album;

    $bookmarks_list = toc_album_bookmarks_list_get($album);
    foreach ($bookmarks_list as $index => $bookmark) {
        if ($bookmark['asset'] == $asset) {
            unset($bookmarks_list[$index]);
        }
    }
    // if there is no bookmark anymore, the file is deleted
    if (count($bookmarks_list) == 0) {
        return toc_album_bookmarks_delete_all($album);
    }
    return assoc_array2xml_file($bookmarks_list, $toc_path . "/_bookmarks.xml", "bookmarks", "bookmark");
}

/**
 * Deletes the file that contains the bookmarks
 * @param type $album the album containing bookmarks
 * @return boolean true if the file has been deleted; false otherwise
 */
function toc_album_bookmarks_delete_all($album)
{
    // Sanity check

    if (!ezmam_album_exists($album)) {
        return false;
    }

    // 1) set the repository path
    $toc_path = ezmam_repository_path();
    if ($toc_path === false) {
        return false;
    }

    // set user's file path
    $toc_path = $toc_path . '/' . $album;
    unlink($toc_path . "/_bookmarks.xml");
    return true;
}
