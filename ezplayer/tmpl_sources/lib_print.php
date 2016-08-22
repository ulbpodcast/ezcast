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

function bookmark_sort($bookmarks, $default_order = "chron"){
    $order = acl_value_get("bookmarks_order");
    if (isset($order) && $order != '' && $order != $default_order){
        array_reverse($bookmarks);
    }
    return $bookmarks;
}
/**
 * Helper function, used for pretty print.
 * @param type $info 
 */
function print_info($info, $suffix = '', $htmlspecialchars = true) {
    if (isset($info) && !empty($info))
        echo replace_links(($htmlspecialchars ? htmlspecialchars($info) : $info) . $suffix);
    else
        echo '®Not_available®';
}


function print_search($keywords){
    
    if (!isset($keywords) || empty($keywords)) {
        echo '®Not_available®';
        return;
    }
    
    // split the string
    $keywords_array = explode(",", $keywords);
    $keywords = '';
    // transforms each keyword in a search link
    foreach($keywords_array as $keyword){
    	$comma = ($keywords != '') ? ',' : '';
    	$tmp = "$comma<a href=\"#\" onclick=\"keyword_search('$keyword');\">$keyword</a>";
    	$keywords .= $tmp;
    }
    echo $keywords;
}

function print_time($timecode) {
    $time = str_pad((int) ($timecode / 3600), 1, "0", STR_PAD_LEFT);
    $time .= ':' . str_pad((int) (($timecode % 3600) / 60), 2, "0", STR_PAD_LEFT);
    $time .= ':' . str_pad((int) (($timecode % 3600) % 60), 2, "0", STR_PAD_LEFT);
    echo $time;
}

function print_bookmark_title($bookmark_title) {
    if (isset($bookmark_title) && !empty($bookmark_title)) {
        echo htmlspecialchars($bookmark_title);
    } else {
        echo '®No_title®';
    }
}

function print_new_video($count) {

    if ($count < 0)
        $count = 0;
    if ($count <= 1) {
        echo $count . ' ®New_video®';
    } else {
        echo $count . ' ®New_videos®';
    }
}

// Adds a new syntax for supporting usage of external links in some text
// The string is parsed following these rules:
// - @url ==> adds a simple html tag <a href="url">url</a>
// - #alias@url ==> adds an html tag using the alias <a href="url">alias</a>
// - something@url ==> nothing happens
function replace_links_old($string) {
    // saves the text length
    $str_length = strlen($string);
    // loop on the text
    for ($i = 0; $i < $str_length; $i++) {
        // init var
        $j = 0;
        $alias = '';
        $link = '';
        // if there is an alias (starts by '#')
        if ($string[$i] == '#') {
            // saves the alias (ends by '@')
            $j = $i + 1;
            while ($j <= $str_length && $string[$j] != "@") {
                $alias .= $string[$j];
                $j++;
            }
            // saves the position of the pointer 
            $i = $j;
        }
        // if there is a link (starts by '@')
        if ($string[$i] == "@") {
            $j = $i + 1;
            while ($j < $str_length && $string[$j] != " ") {
                // saves the link (everything that follows the '@' (till the next space) is considered as a link)
                $link .= $string[$j];
                $j++;
            }
        }
        // if we've found a link and it is not an email address (something@url)
        if ($link != '' && ($alias != '' || $i == 0 || $string[$i - 1] == ' ')) {
            // makes sure the url starts by 'http://' or 'mailto:'
            $http = (substr($link, 0, 7) != 'http://' && substr($link, 0, 8) != 'https://' && substr($link, 0, 7) != 'mailto:') ? 'http://' : '';
            // uses the alias if it exists
            if ($alias != '') {
                $full_link = "<a href=\"$http$link\" target=\"_blank\">$alias</a>";
                $token_length = strlen($alias) + strlen($link) + 2;
            } else { // uses the link otherwise
                $full_link = "<a href=\"$http$link\" target=\"_blank\">$link</a>";
                $token_length = strlen($link) + 1;
            }
            // replaces the previous syntax with the adequate html tag (#alias@url ==> <a href="url">alias</a>)
            // in the original text
            $string = substr_replace($string, $full_link, $j - $token_length, $token_length);
            $replace_length = strlen($full_link);
            // moves the pointer at the end of the html tag
            $i = $j - $token_length + $replace_length;
            // saves the new text length
            $str_length = strlen($string);
        }
    }
    return $string;
}

/**
 * Adds a new syntax for supporting usage of external links in some text
 * the string is parsed following this rule:
 * -  *url* --> adds an html tag (<a href="url">url</a>)
 * -  $url alias* --> adds an html tag using the alias (<a href="url">alias</a>)
 * @param type $string
 * @return type
 */
function replace_links($string) {
    $str_length = strlen($string);
    // loop on the text
    for ($i = 0; $i < $str_length; $i++) {
        // reinit variables
        $link = '';
        $alias = '';
        // if there is a link (starts by '*')
        if ($string[$i] == "*" && $string[$i+1] == "*") {
            // saves the position
            $j = $i + 2;
            // saves the url
            while ($j < $str_length && $string[$j] != " " && ($string[$j] != "*" || ($string[$j] == "*" && $string[$j+1] != "*" ))) {
                $link .= $string[$j];
                $j++;
            }
            // if the next char is not '*' it means there is an alias
            while ($j < $str_length && ($string[$j] != "*" || ($string[$j] == "*" && $string[$j+1] != "*" ))) {
                $j++;
                if ($string[$j] != "*" || ($string[$j] == "*" && $string[$j+1] != "*" ))
                    $alias .= $string[$j];
            }
            // there is a url
            if ($link != '') {
                // makes sure the url starts by 'http://' or 'mailto:'
                $http = (substr($link, 0, 7) != 'http://' && substr($link, 0, 8) != 'https://' && substr($link, 0, 7) != 'mailto:') ? 'http://' : '';
                // uses the alias in the html tag
                if ($alias != '') {
                    $full_link = "<a href=\"$http$link\" onclick=\"server_trace(new Array('3', 'description_link', current_album, current_asset, current_tab));\" target=\"_blank\">$alias</a>";
                } else {
                    $full_link = "<a href=\"$http$link\" onclick=\"server_trace(new Array('3', 'description_link', current_album, current_asset, current_tab));\" target=\"_blank\">$link</a>";
                }

                // replaces the previous syntax with the adequate html tag (*url alias* ==> <a href="url">alias</a>)
                // in the original text
                $string = substr_replace($string, $full_link, $i, $j - $i + 2);
                $replace_length = strlen($full_link);
                // moves the pointer at the end of the html tag
                $i = $i + $replace_length - 1;
                // saves the new text length
                $str_length = strlen($string);
            }
        }
    }
    return $string;
}

/**
 * Replaces a hashtag by a clickable link
 * @param type $string the string containing the hashtag
 * @param type $hashtag the hashtag to make clickable (i.e '#hashtag')
 * @param type $href link for the hashtag
 * @param type $index index of the first occurence of hashtag
 * @return type
 */
function replace_hashtag($string, $hashtag, $href, $index = false){
    if ($index === false){
        $index = strpos($string, $hashtag);
    }
    if ($index === false) return $string;
    $hash_length = strlen($hashtag);
    $hashtag = "<a href=\"$href\" onclick=\"server_trace(new Array('3', 'hashtag_click', current_album, current_asset, '$hashtag'));\">$hashtag</a>";
    $string = substr_replace($string, $hashtag, $index, $hash_length);
    return $string;
}
?>