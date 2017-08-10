<?php

/*
* EZCAST Commons 
* Copyright (C) 2014 Université libre de Bruxelles
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

//include_once dirname(__FILE__).'/lib_various.php';
include_once dirname(__FILE__).'/config.inc';


/**
 * check if user credentials are ok and return an assoc array containing ['full_name'] and ['email'] ['login'] (['real_login']) of the user. failure returns false. Error message can be received via checkauth_last_error()
 * @global string $ldap_servers_auth_json_file path to the json file containing list of ldap servers for authentication
 * @param string $login 
 * @param string $passwd
 * @return assoc_array|false
 */
function sso_checkauth($login, $password) {
 global $sso_ssp_lib;
 global $sso_ssp_sp;
 global $sso_ssp_att_name;
 global $sso_ssp_att_firstname;
 global $sso_ssp_att_email;
 global $sso_ssp_att_login;

if (isset($_SESSION)) {  // if there is already a session running
    session_write_close(); // save and close it
}

// save previon session
$sesId=session_id();
$sesNam=session_name();

//setup sesssion for sso authentification
session_name("PHPSESSID");  //needed to solve problem between session name (take tke ezName session)
session_start();
session_regenerate_id(); //Regenerating SID

 
try {
    // Autoload simplesamlphp classes.
    if(!file_exists("{$sso_ssp_lib}/_autoload.php")) {
        throw(new Exception("simpleSAMLphp lib loader file does not exist: ".
        "{$sso_ssp_lib}/_autoload.php"));
    }
 
   
   
   include_once("{$sso_ssp_lib}/_autoload.php");

   $sso_ssp_auth = new SimpleSAML_Auth_Simple($sso_ssp_sp);
 
    // Take the user to IdP and authenticate.
    $sso_ssp_auth->requireAuth();
//    $valid_saml_session = $sso_ssp_auth->isAuthenticated();
 
} catch (Exception $e) {
    // SimpleSAMLphp is not configured correctly.
    throw(new Exception("SSO authentication failed: ". $e->getMessage()));
    return false;
}
 
if (!$valid_saml_session) {
    // Not valid session. Redirect a user to Identity Provider
    try {
        $sso_ssp_auth = new SimpleSAML_Auth_Simple($sso_ssp_sp);
        $sso_ssp_auth->requireAuth();
    } catch (Exception $e) {
        // SimpleSAMLphp is not configured correctly.
        throw(new Exception("SSO authentication failed: ". $e->getMessage()));
        return false;
    }
}
 
// At this point, the user is authenticated by the Identity Provider, and has access
// to the attributes received with SAML assertion.
$attributes = $sso_ssp_auth->getAttributes();
 
// The print_r response of $sso_ssp_auth->getAttributes() look something like this:
//Array (
//      [first_name] => Array ( [0] => John )
//      [last_name] => Array ( [0] => Doe )
//      [email] => Array ( [0] => john.doe@webtrafficexchange.com )
//)
         
// restore previous session
session_write_close();
session_id($sesId);
session_name($sesNam);
session_start();

$_SESSION['sso_ssp_auth'] = $sso_ssp_auth;

 
// Do something with assertion data.
/* initialization of userinfo based on sso attributes */
$userinfo['full_name']=$attributes[$sso_ssp_att_name][0]." ".$attributes[$sso_ssp_att_firstname][0];
$userinfo['email']=$attributes[$sso_ssp_att_email][0];
$userinfo['login']=$attributes[$sso_ssp_att_login][0];
$userinfo['real_login']=$userinfo['login'];

//	echo "INSERT INTO ezcast_sso_users (user_ID,surname,forename,email,first_time) VALUES (".$attributes[$sso_ssp_att_login][0].",".$attributes[$sso_ssp_att_firstname][0].",".$attributes[$sso_ssp_att_name][0].",".$attributes[$sso_ssp_att_email][0].",NOW())";


// Create user_sso information, if doesn't exist, or update last login date
//
try{
  require_once __DIR__.'/../commons/lib_database.php';
  global $db_object;
  
  db_prepare();

  //$db_object->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $reqSQL = $db_object->prepare('SELECT * FROM ezcast_sso_users WHERE user_ID=?');
  $reqSQL->bindParam(1, $attributes[$sso_ssp_att_login][0], PDO::PARAM_STR);
  $reqSQL->execute();
//  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if($reqSQL->rowCount() == 0)
  {
    $reqSQL = $db_object->prepare('INSERT INTO ezcast_sso_users (user_ID,surname,forename,email,first_time,last_time) VALUES (?,?,?,?,NOW(),NOW())');
    $reqSQL->bindParam(1,$attributes[$sso_ssp_att_login][0],PDO::PARAM_STR);
    $reqSQL->bindParam(2,$attributes[$sso_ssp_att_firstname][0],PDO::PARAM_STR);
    $reqSQL->bindParam(3,$attributes[$sso_ssp_att_name][0],PDO::PARAM_STR);
    $reqSQL->bindParam(4,$attributes[$sso_ssp_att_email][0],PDO::PARAM_STR);  
  }else{ 
    $reqSQL = $db_object->prepare('UPDATE  ezcast_sso_users SET last_time=NOW() WHERE  user_ID=?');
    $reqSQL->bindParam(1, $attributes[$sso_ssp_att_login][0], PDO::PARAM_STR);
  }
  
  $reqSQL->execute();

}
catch (Exception $e)
{
  
  throw(new Exception("Error in add/update sso_user: ". $e->getMessage()));
  
}

return $userinfo;

}   

function sso_logout(){
    global $sso_ssp_lib;
    global $sso_ssp_sp;
	global $sso_ssp_auth;
	
	include_once("{$sso_ssp_lib}/_autoload.php");
	
	session_write_close();
	session_name("PHPSESSID");  //needed to solve problem between session name (take tke ezName session)
	session_start();
	session_regenerate_id();
	
    $sso_ssp_auth = new SimpleSAML_Auth_Simple($sso_ssp_sp);
 
    $sso_ssp_auth->requireAuth();

	$sso_ssp_auth->logout();  
}


/**
 * Gets information about the givin user, searching results in db for sso user.
 * @return assoc_array|false
 */
function sso_getinfo($login) {
	try{
	  require_once __DIR__.'/../commons/lib_database.php';
	  global $db_object;
	  
	  db_prepare();

	  //$db_object->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	  $reqSQL = $db_object->prepare('SELECT * FROM ezcast_sso_users WHERE user_ID=?');
	  $reqSQL->bindParam(1, $attributes[$sso_ssp_att_login][0], PDO::PARAM_STR);
	  $reqSQL->execute();

	  if($reqSQL->rowCount() != 1)
	  { 
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
  $userinfo['full_name']=$row['surname']." ".$row['forename'];
		$userinfo['email']=$row['email'];
		$userinfo['login']=$row['user_ID'];
		$userinfo['real_login']=$userinfo['login'];
	  }else{
		checkauth_last_error("wrong search result count:" . $reqSQL->rowCount());
		return false;
	  }
	  
	}
	catch (Exception $e)
	{
	  
	  throw(new Exception("Error in add/update sso_user: ". $e->getMessage()));
	  
	}

	return $userinfo;
}


//end function
?>
