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
include "config.inc";
foreach ($auth_methods as $method) {
    include dirname(__FILE__)."/lib_auth_$method.php";
}

/*
 * This library uses various libraries to authenticate the user
 * or get information about the user
 */

/**
 * Determines whether the user to authenticate is a simple user
 * or a 'runas' (admin).
 * Tries to authenticate the user and returns user's information
 * in case of success.
 * @global type $auth_methods various methods used for authentication (may be file / ldap / ...)
 * @param type $login user's login (can be user or admin/user with admin authenticated as user)
 * @param type $passwd user's password
 * @return user's information if the user has been authenticated; false otherwise
 */
function checkauth($login, $passwd)
{
    global $auth_methods;

    $auth_methods_length = count($auth_methods);
    $login = trim($login);
    
    //check if runas admin login
    $login_parts = explode("/", $login);

    //simple login
    if (count($login_parts) == 1) {
        $index = 0;
        $auth_user = false;
        // authenticates user (fallback on every available methods)
        while ($index < $auth_methods_length && $auth_user === false) {
            $check_auth = $auth_methods[$index] . "_checkauth";
            $auth_user = $check_auth($login, $passwd);
            $index++;
        }
        // user has not been authenticated using all available methods
        if ($auth_user === false) {
            checkauth_last_error("Authentication failure");
        }
        // returns user info or false if user has not been found
        return $auth_user;
        // admin run as login
    } else {
        //runas_login identification where user <login> wants to act as another one
        $real_login = $login_parts[0];
        $runas_login = $login_parts[1];
        
        $index = 0;
        $auth_admin = false;
        // loops on every available methods to authenticate the admin
        while ($index < $auth_methods_length && $auth_admin === false) {
            $check_auth = $auth_methods[$index] . "_checkauth";
            $auth_admin = $check_auth($real_login, $passwd);
            $index++;
        }
        // admin has not been authenticated
        if ($auth_admin === false) {
            checkauth_last_error("Authentication failure");
            return false;
            // admin has been authenticated
        } else {
            $index = 0;
            $auth_user = false;
            // loops on every available methods to get user info
            while ($index < $auth_methods_length && $auth_user === false) {
                $getinfo = $auth_methods[$index] . "_getinfo";
                $auth_user = $getinfo($runas_login);
                $index++;
            }
            // user does not exit
            if ($auth_user === false) {
                checkauth_last_error("Authentication failure");
            } else {
                $auth_user["real_login"] = $real_login;
            }
            // returns user info or false if user has not been found
            return $auth_user;
        }
    }
}

/**
 * set/get last error for checkauth
 * @staticvar string $last_error
 * @param string $msg
 * @return string
 */
function checkauth_last_error($msg = "")
{
    static $last_error = "";

    if ($msg == "") {
        return $last_error;
    } else {
        $last_error = $msg;
        return true;
    }
}
