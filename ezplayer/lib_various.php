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
 * Various useful functions
 * @package ezcast.ezplayer.lib.various
 */
require_once 'config.inc';
require_once 'lib_ezmam.php';
require_once dirname(__FILE__) . '/../commons/lib_template.php';

/**
 * Trims the '-priv' or '-pub' suffix from an album name
 * TODO: check that it's only the end of the name that's removed
 * @param string $album_name
 * @return string
 */
function suffix_remove($album_name)
{
    $res = $album_name;

    if (substr($album_name, -4) == "-pub") {
        $res = substr($album_name, 0, -4);
    } elseif (substr($album_name, -5) == '-priv') {
        $res = substr($album_name, 0, -5);
    }

    return $res;
}

/**
 * Changes "priv" to "pub" and controversely. If the album name has neither suffix, returns the same string.
 * @param type $album_name
 * @return string
 */
function suffix_replace($album_name)
{
    $res = $album_name;
    $res = suffix_remove($album_name);

    if (substr($album_name, -4) == "-pub") {
        $res .= '-priv';
    } elseif (substr($album_name, -5) == '-priv') {
        $res .= '-pub';
    }

    return $res;
}

/**
 * Returns an album suffix based on its name
 * @param type $album_name
 * @return string|false the suffix (with the initial hyphen) if all went well, false otherwise
 */
function suffix_get($album_name)
{
    if (substr($album_name, -4) == "-pub") {
        return '-pub';
    } elseif (substr($album_name, -5) == '-priv') {
        return '-priv';
    } else {
        return false;
    }
}

/**
 * Checks whether an album is private based on its suffix
 * @param type $album_name
 * @return bool true if album is private
 */
function album_is_private($album_name)
{
    return (suffix_get($album_name) == '-priv');
}

/**
 * Checks whether an album is public based on its suffix
 * @param type $album_name
 * @return bool true if album is public
 */
function album_is_public($album_name)
{
    return (suffix_get($album_name) == '-pub');
}

/**
 * Takes a ls-friendly date and translates it into human-readable
 * @param string $date The date in format YYYY_mm_dd_HHhii
 * @param string $space_char The delimiter to use between digits
 * @param bool $long_months_names(true) If set to "false", the month will be displayed as a number instead of a noun
 * @param string $lang Language the months are displayed in, in cast $long_months_names is set to true
 * @param bool $long_date if set to true, the date will be a "gramatically correct" date, instead of a "easily computable" one
 * @return string The date in format dd_mmmm_YYYY_HH:ii
 */
function get_user_friendly_date($date, $space_char = '_', $long_months_names = true, $lang = 'fr', $long_date = false)
{
    if (!isset($date) || empty($date)) {
        return null;
    }

    $matches = array();
    preg_match('!(\d{4})\_(\d{2})\_(\d{2})\_(\d{2})h(\d{2})!', $date, $matches);

    $new_date = $matches[3] . $space_char; // Day
    // If we want long month names (in letters, that is), we retrieve these names
    // from the translations file, and remove the non-ASCII characters if needed
    if ($long_months_names) {
        template_load_dictionnary('translations.xml');

        if ($lang == 'fr-ASCII') {
            $new_date .= str_replace(array('é', 'û'), array('e', 'u'), template_get_message('month_' . $matches[2], 'fr'));
        } else {
            $new_date .= template_get_message('month_' . $matches[2], $lang);
        }
    }
    // Otherwise, we just display the month as a number
    else {
        $new_date .= $matches[2];
    }

    $new_date .= $space_char . $matches[1]; // year
    if ($long_date) {
        $new_date .= $space_char . $at;
    } // Separator between date and hour

    $new_date .= $space_char . $matches[4] . 'h' . $matches[5]; // Hours and minutes

    return $new_date;
}

/**
 * Returns a date in RFC822 format from a date in "our" format
 * @param type $date Date in format YYYY_mm_dd_HHhii
 */
function get_RFC822_date($date)
{
    //$date_array = date_parse_from_format('Y_m_d_H:i', $date);
    list($year, $month, $day, $hourandminutes) = explode('_', $date);
    list($hours, $minutes) = explode('h', $hourandminutes);

    //$date_array = date_parse('Y_m_d_H:i', $date);
    return date(DATE_RFC822, mktime($hours, $minutes, '0', $month, $day, $year));
}

/**
 * Takes a duration in seconds, and returns a string with the duration using international units
 * @param float $duration A duration in seconds
 * @return string A duration in hours, minutes, seconds
 */
function get_user_friendly_duration($duration)
{
    if (!isset($duration) || empty($duration)) {
        return null;
    }

    $res = round($duration);
    if ($res < 60) {
        $res .= ' sec';
    } else {
        $res = round($res / 60) . ' min. ' . ($res % 60) . ' sec.';
    }
    return $res;
}

/**
 * Takes a date as a year and month, and returns a string representing the academic year thereof
 * @param string $year
 * @param string $month
 * @return A string of format currentYear-nextYear
 */
function get_anac($year, $month)
{
    $year_start = (int) $year;
    // Before July 2011, the academic year is 2010-2011, so the starting year is "one year before" current date
    if ((int) $month <= 6) {
        --$year_start;
    }

    $year_end = $year_start + 1;

    return $year_start . '-' . $year_end;
}

/**
 * Returns the asset full title from an asset name
 * @global type $repository_path
 * @param type $album
 * @param type $asset the original asset name
 * @return boolean|string the asset full title if the asset exists ; false otherwise
 */
function get_asset_title($album, $asset)
{
    global $repository_path;
    global $template_folder;

    ezmam_repository_path($repository_path);

    //
    // Usual sanity checks
    //
    if (!ezmam_album_exists($album)) {
        return false;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        return template_get_message('Inexistant', get_lang());
    }
    $asset_title = ezmam_asset_metadata_get($album, $asset);
    $asset_title = $asset_title['title'];

    return $asset_title;
}


/**
 * Returns a URL that allows the user to view the media
 * @global type $url
 * @param string $album
 * @param string $asset
 * @param string $media
 * @param bool $htmlentities If set to true, the result will be encoded with htmlentities (& replaced by &amp;)
 * @param bool $itunes_friendly If set to true, the link will include a fake .m4v file
 * @return string Media
 */
function get_link_to_media($album, $asset, $media, $htmlentities = true, $itunes_friendly = false)
{
    global $ezplayer_url;
    global $distribute_url;
    global $repository_path;

    ezmam_repository_path($repository_path);

    //
    // Usual sanity checks
    //
    if (!ezmam_album_exists($album)) {
        error_print_message('get_link_to_media: Album ' . $album . ' does not exist');
        return false;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        error_print_message('get_link_to_media: Asset ' . $asset . ' does not exist');
        return false;
    }

    // We take the asset's token if it exists.
    // If not, then we use the album's token instead.
    $token = ezmam_asset_token_get($album, $asset);
    if (!$token) {
        $token = ezmam_album_token_get($album);
    }

    if (!$token) {
        error_print_message('get_link_to_media: ' . ezmam_last_error());
        return false;
    }

    $media_infos = explode('_', $media); // 'media' is like high_cam, so we want to extract the "high" part (quality) and the "cam" part (type)
    $quality = $media_infos[0];
    $type = $media_infos[1];

    $resurl = $distribute_url;
    if ($itunes_friendly) {
        $resurl.= '/' . $type . '.m4v';
    }
    $resurl.= '?action=media&album=' . $album . '&asset=' . $asset . '&type=' . $type . '&quality=' . $quality . '&token=' . $token;
    if ($htmlentities) {
        return htmlentities($resurl);
    } else {
        return $resurl;
    }
}

/**
 * Returns a special code that contains information about the media
 * @global type $url
 * @param string $album
 * @param string $asset
 * @param string $media
 * @return string Media
 */
function get_code_to_media($album, $asset, $media)
{
    global $ezplayer_url;
    global $distribute_url;
    global $repository_path;

    ezmam_repository_path($repository_path);

    //
    // Usual sanity checks
    //
    if (!ezmam_album_exists($album)) {
        error_print_message('get_link_to_media: Album ' . $album . ' does not exist');
        return false;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        error_print_message('get_link_to_media: Asset ' . $asset . ' does not exist');
        return false;
    }

    // We take the asset's token if it exists.
    // If not, then we use the album's token instead.
    $token = ezmam_asset_token_get($album, $asset);
    if (!$token) {
        $token = ezmam_album_token_get($album);
    }

    if (!$token) {
        error_print_message('get_link_to_media: ' . ezmam_last_error());
        return false;
    }

    $media_infos = explode('_', $media); // 'media' is like high_cam, so we want to extract the "high" part (quality) and the "cam" part (type)
    $quality = $media_infos[0];
    $type = $media_infos[1];

    return $album . '/' . $asset . '/' . $type . '/' . $quality . '/' . $token;
}

/**
 * Checks if a string begins with some other string
 * @param string $string main string
 * @param string $beginning begins with
 * @return bool
 */
function str_begins_with($string, $beginning)
{
    $beglen = strlen($beginning);
    $stringbeg = substr($string, 0, $beglen);

    if ($stingbeg == $beginning) {
        return true;
    } else {
        return false;
    }
}

/**
 * scans a filename and extract 'name' and 'ext'(ension) parts return them in an assoc array
 * @param <type> $filename
 * @return false|assoc_array
 */
function file_get_extension($filename)
{
    //search last dot in filename
    $pos_dot = strrpos($filename, '.');
    if ($pos_dot === false) {
        return array('name' => $filename, 'ext' => "");
    }

    $ext_part = substr($filename, $pos_dot + 1);
    $name_part = substr($filename, 0, $pos_dot);
    $result_assoc['name'] = $name_part;
    $result_assoc['ext'] = $ext_part;
    return $result_assoc;
}

/**
 * Sets the current language to the one chosen in parameter
 * @param type $lang
 */
//function set_lang($lang) {
//    $_SESSION['lang'] = $lang;
//}

/**
 * Returns current chosen language
 * @return string(fr|en)
 */
//function get_lang() {
//    //if(isset($_SESSION['lang']) && in_array($_SESSION['lang'], $accepted_languages)) {
//    if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
//        return $_SESSION['lang'];
//    }
//    else
//        return 'en';
//}

/**
 * Isolates keywords from a string
 * Each keyword must be surrounded by a '#' tag (i.e: #keyword#)
 * @param type $string
 * @return string
 */
function get_keywords(&$string)
{
    $keywords = array();
    $string_length = strlen($string);
    // loop on the text
    for ($i = 0; $i < $string_length; $i++) {
        // if there is a keyword (starts by '#')
        if ($string[$i] == '#') {
            // saves the position of the pointer
            $j = $i + 1;
            $keyword = '';
            // saves the keyword
            while ($j <= $string_length && $string[$j] != "#" && $string[$j] != "@") {
                $keyword .= $string[$j];
                $j++;
            }
            // if it ends by '#', it is a keyword
            if ($string[$j] == "#") {
                // pushes the keyword in the array
                $keywords[] = trim($keyword);
                // removes the '#' tags from the text
                $string[$i] = ' ';
                $string[$j] = ' ';
                // if it ends by '@' it is not a keyword but a link (we keep it)
            } elseif ($string[$j] == "@") {
                while ($j <= $string_length && $string[$j] != " ") {
                    $j++;
                }
            }
            // moves the pointer at the end of the keyword (or link)
            $i = $j++;
        }
    }
    return $keywords;
}

/**
 * ...
 * @param string $string
 * @return string
 */
function surround_url($string)
{
    // checks for http url
    $pos = 0;
    // all prefixes we want to surround
    $patterns = array('http://', 'https://', 'www.', 'mailto:');
    $end_of_line = array(' ', PHP_EOL, '<', '>', '(', ')', '[', ']', '{', '}', '"', '\'');
    
    while ($pos >= 0) {
        // finds the first occurence of each pattern
        $pos_array = array();
        foreach ($patterns as $pattern) {
            // searches from the last known position
            $tmp_pos = stripos($string, $pattern, $pos);
            if (!($tmp_pos === false)) { // pattern found
                $pos_array[] = $tmp_pos;
            }
        }
        // saves the position of the first encountered pattern
        $pos = (empty($pos_array)) ? -1 : min($pos_array);
        if ($pos != -1) { // ends if there is no pattern found
            if ($pos == 0 || $string[$pos - 1] != '*') { // the url is not yet surrounded
                // adds a '*' tag before the url
                $string = substr($string, 0, $pos) . "**" . substr($string, $pos);
                // moves to the end of the url
                while ($pos < strlen($string) && !in_array($string[$pos], $end_of_line)) {
                    $pos++;
                }
                // if the url ends with a '.', excludes it from the surrounding
                if ($string[$pos - 1] == '.') {
                    $pos--;
                }
                // adds a '*' at the end of the url
                $string = substr($string, 0, $pos) . "**" . substr($string, $pos);
                
                $pos+=2;
            } else { // the url is already surrounded, just move to the next '*' tag
                $tmp_pos = stripos(substr($string, $pos), '**');
                if ($tmp_pos !== false) {
                    $pos += $tmp_pos - 1;
                } else {
                    $pos = -1;
                }
            }
        }
    }

    return $string;
}

/**
 * This function replaces or remove all threats such as
 * php code, javascript, sql injections, ...
 * @param type $string
 * @return type
 */
function safe_text($string)
{
    // remove php and javascript tags
    $string = preg_replace('/(<\?{1}[pP\s]{1}.+\?>)/Us', "", $string);
    $string = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $string);
    
    $string = str_replace('javascript', "-javascript-", $string);
    $string = str_replace('onmouseover', "-onmouseover-", $string);
    $string = str_replace('onclick', "-onclick-", $string);
    $string = str_replace('onmouseup', "-onmouseup-", $string);
    $string = str_replace('onmousedown', "-onmousedown-", $string);
    $string = str_replace('onmousemove', "-onmousemove-", $string);
    $string = str_replace('ondblclick', "-ondblclick-", $string);
    $string = str_replace('onkeydown', "-onkeydown-", $string);
    $string = str_replace('onkeypress', "-onkeypress-", $string);
    $string = str_replace('onkeyup', "-onkeyup-", $string);
    $string = str_replace('href', "-href-", $string);
    $string = preg_replace('/<div\b[^>]*>/is', '&lt;div&gt;', $string);
    $string = preg_replace('/<\/div>/is', '&lt;/div&gt;', $string);
    // prevent mysql injections
    $string = htmlspecialchars($string, ENT_QUOTES);
     
    return $string;
}

/**
 * Updates an asset metadata
 * @global type $repository_basedir
 * @param type $album
 * @param type $asset
 * @param type $key
 * @param type $value
 * @return type
 */
function asset_meta_update($album, $asset, $key, $value)
{
    global $repository_basedir;
    $path_to_metadata = $repository_basedir . "/repository/" . $album . "/" . $asset . "/_metadata.xml";
    $metadata = simplexml_load_file($path_to_metadata);
    if ($key == "display_download_link") {
        $metadata->display_download_link = $value;
    }

    return $metadata->asXML($path_to_metadata);
}

/**
 * Returns an asset metadata as array
 * @global type $repository_basedir
 * @param type $album
 * @param type $asset
 * @return type
 */
function asset_meta_get($album, $asset)
{
    global $repository_basedir;
    $path_to_metadata = $repository_basedir . "/repository/" . $album . "/" . $asset . "/_metadata.xml";
    $metadata = simplexml_load_file($path_to_metadata);

    return xml_file2assoc_array($metadata);
}

/**
 * converts a SimpleXMLElement in an associative array
 * @param SimpleXMLElement $xml
 * @anonymous_key the name of root tag we don't want to get for each item
 * @return type
 */
function xml_file2assoc_array($xml, $anonymous_key = 'anon')
{
    if (is_string($xml)) {
        $xml = new SimpleXMLElement($xml);
    }
    $children = $xml->children();
    if (!$children) {
        return (string) $xml;
    }
    $arr = array();
    foreach ($children as $key => $node) {
        $node = xml_file2assoc_array($node);
        // support for 'anon' non-associative arrays
        if ($key == $anonymous_key) {
            $key = count($arr);
        }

        // if the node is already set, put it into an array
        if (isset($arr[$key])) {
            if (!is_array($arr[$key]) || $arr[$key][0] == null) {
                $arr[$key] = array($arr[$key]);
            }
            $arr[$key][] = $node;
        } else {
            $arr[$key] = $node;
        }
    }
    return $arr;
}

function simple_assoc_array2xml_file($assoc_array, $file_path, $global)
{
    $xmlstr = "<?xml version='1.0' standalone='yes'?>\n<$global>\n</$global>\n";
    $xml = new SimpleXMLElement($xmlstr);
    foreach ($assoc_array as $key => $value) {
        $xml->addChild($key, $value);
    }
    $xml_txt = $xml->asXML();
    file_put_contents($file_path, $xml_txt);
    chmod($file_path, 0644);
}

/**
 * converts an array of associative array in xml file
 * @param type $array the array to convert
 * @param type $file_path the path for the xml file
 * @param type $global the root element of the xml file
 * @param type $each each item of the xml file
 * @return boolean
 */
function assoc_array2xml_file($array, $file_path, $global = 'bookmarks', $each = 'bookmark')
{
    $xmlstr = "<?xml version='1.0' standalone='yes'?>\n<$global>\n</$global>\n";
    $xml = new SimpleXMLElement($xmlstr);
    foreach ($array as $assoc_array) {
        $node = $xml->addChild($each);
        foreach ($assoc_array as $key => $value) {
            $node->addChild($key, htmlspecialchars($value));
        }
    }
    $xml_txt = $xml->asXML();
    $res = file_put_contents($file_path, $xml_txt, LOCK_EX);
    //did we write all the characters
    if ($res != strlen($xml_txt)) {
        return false;
    } //no

    return true;
}

/**
 * converts an associative array in xml string
 * @param type $array the list of album tokens
 * @return boolean
 */
function assoc_array2xml_string($array, $global = 'bookmarks', $each = 'bookmark')
{
    $xmlstr = "<?xml version='1.0' standalone='yes'?>\n<$global>\n</$global>\n";
    $xml = new SimpleXMLElement($xmlstr);
    foreach ($array as $assoc_array) {
        $node = $xml->addChild($each);
        foreach ($assoc_array as $key => $value) {
            $node->addChild($key, htmlspecialchars($value));
        }
    }

    $xml_txt = $xml->asXML();
    return trim($xml_txt);
}

/**
 * Searches a specific pattern in a bookmarks list
 * @param type $search the pattern to search (array containing a selection of words to find)
 * @param type $bookmarks the eligible bookmarks list
 * @param type $fields the bookmark fields where to search :
 * it can be the title, the description and/or the keywords
 * @return the matching bookmarks list
 */
function search_in_array($search, $bookmarks, $fields, $level)
{

    // set $relevancy to true if the result is aimed to be sorted by relevancy.
    // With $relevancy = false, as soon as a word is found in any of the fields,
    // we stop the search and check for the next bookmark.
    // With $relevancy = true, we search for every words in every fields and
    // give a score to each bookmark, according to certain rules.
    $relevancy = false;
    $score = 0;

    foreach ($bookmarks as $index => &$bookmark) {
        if ($level == 0 || $bookmark['level'] == $level) {
            foreach ($search as $word) {
                foreach ($fields as $field) {
                    // Does the field contain the word ?
                    $offset = stripos($bookmark[$field], $word);
                    if ($offset !== false) {
                        if ($relevancy) {
                            // the word has been found, we increment the score
                            $last_index = $offset + strlen($word);
                            $score++;

                            // there is nothing before and/or after the word, we increment the score
                            if ($offset == 0) {
                                $score++;
                            }
                            if ($last_index == strlen($bookmark[$field])) {
                                $score++;
                            }
                            if ($offset > 0 && $bookmark[$field][$offset - 1] == ' ') {
                                $score++;
                            }
                            if ($last_index < strlen($bookmark[$field]) && $bookmark[$field][$last_index] == ' ') {
                                $score++;
                            }

                            // There are multiple occurences of the word, we increment the score
                            $count = substr_count(strtoupper($bookmark[$field]), strtoupper($word));
                            if ($count > 1) {
                                $score += ($count - 1) * 2;
                            }
                        } else {
                            $score++;
                            break 2;
                        }
                    }
                }
            }
        }
        if ($score == 0) {
            unset($bookmarks[$index]);
        } else {
            $bookmark['score'] = $score;
        }
        $score = 0;
    }
    return (is_array($bookmarks)) ? array_values($bookmarks) : null;
}

//===== V A R I O U S - THREADS ================================================
//==============================================================================

/**
 * Returns the childens of a comment
 * @param type $fullList
 * @param type $parent
 * @return array
 */
function get_comment_childs($fullList, $parent)
{
    $childs = array();
    foreach ($fullList as $child) {
        if ($child['parent'] == $parent['id']) {
            $child['level'] = 'level-1';
            $childs[] = $child;
            if ($child['nbChilds'] > 0) {
                $sub_childs = get_comment_childs($fullList, $child);
                foreach ($sub_childs as $value) {
                    $value['level'] = 'level-2';
                    $childs[] = $value;
                }
            }
        }
    }

    return $childs;
}

/**
 * Returns the list of comments without parent
 * @param type $fullList
 * @return array
 */
function get_main_comments($fullList)
{
    $without_parents = array();
    foreach ($fullList as $comment) {
        if ($comment['parent'] == null) {
            $comment['level'] = 'level-0';
            $without_parents[] = $comment;
        }
    }

    return $without_parents;
}

/**
 * Returns an array with threds from the same asset
 * @param type $all
 * @param type $asset
 */
function threads_sort_get_by_asset($all, $asset)
{
    $ret = array();
    foreach ($all as $thread) {
        if ($thread['assetName'] == $asset) {
            $ret[] = $thread;
        }
    }
    return $ret;
}

function thread_is_archive($album, $asset)
{
    return !ezmam_asset_exists($album, $asset);
}

//=== END - V A R I O U S THREAD ===============================================

/**
 * Returns true if the haystack starts with the needle
 * @param string $haystack
 * @param string $needle
 * @return boolean
 */
function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

/**
 * Returns true if the haystack ends with the needle
 * @param string $haystack
 * @param string $needle
 * @return boolean
 */
function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
