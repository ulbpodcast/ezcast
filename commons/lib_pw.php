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
 * @package ezcast.commons.lib.pw
 */

/**
 * This function enrypts/create hash of a cleartext pw to be stored for authentication purpose
 * @param string $login 
 * @param string $clearpw clear-text password
 */
function pw_encrypt($login,$clearpw){
    $des_seed = chr(rand(33, 126)) . chr(rand(33, 126));
    $encrypted_passwd = crypt($clearpw, $des_seed);
    //should use password_hash in the near future, crypt() is deprecated
    //$encrypted_passwd = password_hash($clearpw, PASSWORD_DEFAULT);
    return $encrypted_passwd;
}

/**
 * check password given the encrypted(/hashed) pw and login 
 * @param string $login
 * @param string $clearpw
 * @param string $encpw
 * @return boolean  true if clearpw matches encpw
 */
function pw_check($login,$clearpw,$encpw){
  $salt = substr($encpw, 0, 2);
  $cpasswd = crypt($clearpw, $salt);
  $fpasswd = rtrim($encpw);       
  if ($fpasswd == $cpasswd)
    return true;//good pw
   else
    return false;  //bad pw 
}