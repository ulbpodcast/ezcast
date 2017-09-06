<?php


/**
 * Get the current URL and replace (or add if not exist) the value of a
 * specific param
 *
 * @param String $param who muste change the value
 * @param String $value new value
 * @param String $initURL the current URL
 * @return string The new URL
 */
function url_post_replace($param, $value, $initURL = null)
{
    if ($initURL == null) {
        $initURL = $_SERVER["REQUEST_URI"];
        
        if (!isset($_GET)) {
            $initURL .= '?';
        }
    }
    $regex = "/([&|?]".$param."=)[\w-+.%]*/";
    $res = "";

    if (preg_match($regex, $initURL)) {
        $res = preg_replace($regex, '${1}'.$value, $initURL);
    } else {
        $res = $initURL."&".$param."=".$value;
    }

    return $res;
}

/**
 * Get the current URL and replace (or add if not exist) all the value of
 * specifics params
 *
 * @param Array $replaceInfos with param in key and new value in value
 * @return string The new URL
 */
function url_post_replace_multiple($replaceInfos)
{
    $res = $_SERVER["REQUEST_URI"];
    if (!isset($_GET)) {
        $res .= '?';
    }
    
    foreach ($replaceInfos as $param => $value) {
        $res = url_post_replace($param, $value, $res);
    }

    return $res;
}
