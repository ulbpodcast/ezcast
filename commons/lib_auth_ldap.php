<?php

/*
* EZCAST Commons
* Copyright (C) 2016 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*
* This library is free software; you can redistribute it and/or
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
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * @package ezcast.commons.lib.auth
 */

require_once __DIR__.'/lib_various.php';
require_once __DIR__.'/config.inc';
/**
 * check if user credentials are ok and return an assoc array containing ['full_name'] and ['email'] ['login'] (['real_login']) of the user. failure returns false. Error message can be received via checkauth_last_error()
 * @global string $ldap_servers_auth_json_file path to the json file containing list of ldap servers for authentication
 * @param string $login
 * @param string $passwd
 * @return assoc_array|false
 */
function ldap_checkauth($login, $password)
{
    global $ldap_servers_auth_json_file;
    global $ldap_institution;
    
    $ldap_servers_auth = json_to_array($ldap_servers_auth_json_file);

    if (count($ldap_servers_auth) == 0) {
        return false;
    }
    $login = trim($login);

    if (!ctype_alnum($login)) {
        return false;
    } //sanity check

    if ($ldap_institution=="ucl") {
        $link_identifier = private_ldap_connect($ldap_servers_auth, $index, $login, "");
    } else {
        $link_identifier = private_ldap_connect($ldap_servers_auth, $index, $login, $password);
    }
    
 
 
    // bind to ldap failed
    if ($link_identifier === false) {
        return false;
    }
    //bind succeeded, so try to get more info about the user
    $treepath = $ldap_servers_auth[$index]["base_dn"];
    $filter = str_replace("!LOGIN", $login, $ldap_servers_auth[$index]["filter"]);
    $search_res = ldap_search($link_identifier, $treepath, $filter);
    if (!$search_res) {
        //bind failed
        $errno = ldap_errno($link_identifier);
        $errstring = ldap_error($link_identifier);
        checkauth_last_error("$errno:$errstring:search into ldap failed");
        return false;
    }
    //retrieve the result of the search
    $info = ldap_get_entries($link_identifier, $search_res);
    if ($ldap_institution=="ucl") {
        $employeeNumber=$info[0]['employeenumber'][0];
        $ldap_servers_auth[$index]["rdn"]="employeenumber=".$employeeNumber.",ou=personne,o=universite catholique de louvain,c=be";
        $link_identifier = private_ldap_connect($ldap_servers_auth, $index, $login, $password);
        $search_res = ldap_search($link_identifier, $treepath, $filter);
        $info = ldap_get_entries($link_identifier, $search_res);
    }
    
    if ($info['count'] != 1) {
        checkauth_last_error("wrong search result count:" . $info['count']);
        return false;
    }
    $userinfo = array();
    if (isset($info[0]['cn'][0])) {
        $userinfo['full_name'] = $info[0]['cn'][0];
    }
    
        
    // AJOUT UCL
    if (isset($info[0]['uclressource'])) {
        for ($i=0;$i<count($info[0]['uclressource']);$i++) {
            if (isset($info[0]['uclressource'][$i]) && $info[0]['uclressource'][$i] =='podcast.role.manager') {
                $userinfo['ismanager'] = 'true';
            }
        }
    }
    
    
    if (isset($info[0]['mail'][0])) {
        $userinfo['email'] = $info[0]['mail'][0];
    }
    if ($userinfo) {
        $userinfo['login'] = $login; //return login as normal login
        $userinfo['real_login'] = $login; //return login as real login
        return $userinfo;
    } else {
        return false;
    }
}

/**
 * Gets information about the givin user, searching results in the ldap server.
 * @global string $ldap_servers_cred_json_file path to the json file containing list of ldap servers for credentials
 * @param string $login the user we search info about
 * @return assoc_array|false
 */
function ldap_getinfo($login)
{
    global $ldap_servers_cred_json_file;
    
    $ldap_servers_cred = json_to_array($ldap_servers_cred_json_file);
    if (count($ldap_servers_cred) == 0) {
        return false;
    }

    //try go get user's full name
    $index = 0;
    $result = false;
    do {
        $link_identifier = private_ldap_connect($ldap_servers_cred, $index);
        // bind to ldap failed
        if ($link_identifier === false) {
            return false;
        }
        checkauth_last_error("");

        //bind succeeded, so try to get more info about the user
        $treepath = $ldap_servers_cred[$index]["base_dn"];
        $filter = str_replace("!LOGIN", $login, $ldap_servers_cred[$index]["filter"]);
        $search_res = ldap_search($link_identifier, $treepath, $filter);
        if (!$search_res) {
            //bind failed
            $errno = ldap_errno($link_identifier);
            $errstring = ldap_error($link_identifier);
            checkauth_last_error("$errno:$errstring:search into ldap failed");
        } else {
            //retrieve the result of the search
            $info = ldap_get_entries($link_identifier, $search_res);
            if ($info['count'] != 1) {
                checkauth_last_error("wrong search result count:" . $info['count']);
            }
            $userinfo = array();
            $userinfo['login'] = $login; //return login as normal login
            if (isset($info[0]['cn'][0])) {
                $userinfo['full_name'] = $info[0]['cn'][0];
                $result = true;
            }
            if (isset($info[0]['mail'][0])) {
                $userinfo['email'] = $info[0]['mail'][0];
            }
        }
        $index++;
    } while (!$result);
    
    return $userinfo;
}

/**
 * Tries to establish a connection to ldap server. Loops on all available servers while the
 * connection has not been established
 * @param type $ldap_servers array containing the available servers
 * @param int $index position in the array where the search starts
 * @param type $login
 * @param type $password
 * @return boolean
 */
function private_ldap_connect($ldap_servers, &$index = 0, $login = "", $password = "")
{
    $ldap_servers_count = count($ldap_servers);
    if (!isset($index)) {
        $index = 0;
    }
    
    $link_identifier = false;
    while ($index < $ldap_servers_count) {
        $rdn = str_replace("!LOGIN", $login, $ldap_servers[$index]["rdn"]);
        if (!isset($password) || $password == "") {
            $password = $ldap_servers[$index]["password"];
        }

        //try to connect to ldap server
        if (isset($ldap_servers[$index]["port"]) && trim($ldap_servers[$index]["port"]) != "") {
            $link_identifier = ldap_connect($ldap_servers[$index]["hostname"], $ldap_servers[$index]["port"]);
        } else {
            $link_identifier = ldap_connect($ldap_servers[$index]["hostname"]);
        }
        ldap_set_option($link_identifier, LDAP_OPT_PROTOCOL_VERSION, 3);
        //try to bind with login and password
        @ $res = ldap_bind($link_identifier, $rdn, $password); //check ldap branch
        if ($res) {
            return $link_identifier;
        }
        $index++;
    }
    //if not sucessfull show reason:
    $errno = ldap_errno($link_identifier);
    $errstring = ldap_error($link_identifier);
    checkauth_last_error("$errno:$errstring:Bind to ldap failed");
    if ($link_identifier) {
        ldap_close($link_identifier);
    }
        
    return false;
}

//end function
