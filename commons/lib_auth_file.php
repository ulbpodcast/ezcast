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

/**
 * check if user credentials are ok and return an assoc array containing ['full_name'] and ['email'] ['login']
 * (['real_login']) of the user. failure returns false. Error message can be received via checkauth_last_error()
 * @param string $login can be tdupont or jsmith/tdupont (auth as jsmith and become tdupont)
 * @param string $passwd
 * @return assoc_array|false
 */
function file_checkauth($login, $passwd)
{
    require "pwfile.inc"; //file containing passwords and info

    $login = trim($login);

    if(!preg_match('/^[a-zA-Z0-9_]+$/',$login)){
        return false;
    } //sanity check
    if (isset($users[$login])) {
        $fpasswd = $users[$login]['password']; // password from pwfile.inc
        $salt = substr($fpasswd, 0, 2);
        $cpasswd = crypt($passwd, $salt);
        $fpasswd = rtrim($fpasswd);
    
        if ($fpasswd == $cpasswd) {
            //user exists and password matches
            $userinfo = $users[$login];
            unset($userinfo['password']); //removes password info
                $userinfo['login'] = $login; //return login as normal login
                $userinfo['real_login'] = $login; //return login as normal login
                return $userinfo;
            // user does not exist or password is incorrect
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * Returns information about the user without requesting any password
 * (Used for runas)
 * @param type $login the user we need information about
 * @return user's info if user has been found; false otherwise
 */
function file_getinfo($login)
{
    include "pwfile.inc"; //file containing passwords and info

    if (isset($users[$login])) { // from pwfile.inc
        $userinfo = $users[$login];
        unset($userinfo['password']); //removes password info
        $userinfo['login'] = $login; //return login as normal login
        $userinfo['real_login'] = $login; //return login as normal login
        return $userinfo;
    } else {
        return false;
    }
}
