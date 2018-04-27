<?php

include_once dirname(__FILE__).'/config.inc';
require_once __DIR__.'/../commons/lib_database.php';
require_once __DIR__.'/../commons/lib_sql_management.php';


/**
 * check if user credentials are ok and return an assoc array containing ['full_name'] and ['email'] ['login'] (['real_login']) of the user. failure returns false. Error message can be received via checkauth_last_error()
 * @global string $ldap_servers_auth_json_file path to the json file containing list of ldap servers for authentication
 * @param string $login
 * @param string $passwd
 * @return assoc_array|false
 */
function sso_checkauth($login, $password)
{
    global $sso_ssp_lib;
    global $sso_ssp_sp;
    global $sso_ssp_att_name;
    global $sso_ssp_att_firstname;
    global $sso_ssp_att_email;
    global $sso_ssp_att_login;

    if (!isset($_GET['sso'])) {
        return false;
    }

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
        if (!file_exists("{$sso_ssp_lib}/_autoload.php")) {
            throw(new Exception("simpleSAMLphp lib loader file does not exist: ".
            "{$sso_ssp_lib}/_autoload.php"));
        }
       
        include_once("{$sso_ssp_lib}/_autoload.php");

        $sso_ssp_auth = new SimpleSAML_Auth_Simple($sso_ssp_sp);
     
        // Take the user to IdP and authenticate.
        $sso_ssp_auth->requireAuth();
        //$valid_saml_session = $sso_ssp_auth->isAuthenticated();
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


             
    // restore previous session
    session_write_close();
    session_id($sesId);
    session_name($sesNam);
    session_start();

    $_SESSION['sso_ssp_auth'] = $sso_ssp_auth;

     
    // Do something with assertion data.
    /* initialization of userinfo based on sso attributes */
    $userinfo['full_name'] = $attributes[$sso_ssp_att_name][0]." ".$attributes[$sso_ssp_att_firstname][0];
    $userinfo['email'] = $attributes[$sso_ssp_att_email][0];
    $userinfo['login'] = $attributes[$sso_ssp_att_login][0];
    $userinfo['group'] = $attributes['eduPersonAffiliation'];

    $userinfo['real_login'] = $userinfo['login'];


    //IF idp error and everithing is null  ==> return "error"
    if (!isset($attributes[$sso_ssp_att_name][0])  || !isset($attributes['eduPersonAffiliation']) || !isset($attributes[$sso_ssp_att_login][0]) || !isset($attributes[$sso_ssp_att_email][0])
       || $attributes[$sso_ssp_att_name][0] ==''  || $attributes['eduPersonAffiliation'] == '' || $attributes[$sso_ssp_att_login][0] == '' || $attributes[$sso_ssp_att_email][0] =='') {

        return 'error';
    }






    /*------------UCL METHOD TO CHECK RIGHTS-------------*/

    //Check if the User have the right to enter ezmanager, Even if he don't have any courses
    if (in_array("staff", $userinfo['group'])) {        
        $userinfo['ismanager'] = 'true';        
    }
    global $db_object;
    // add user in table Users
    $user = db_user_read($attributes[$sso_ssp_att_login][0]);
    if (!$user) {
        db_user_create($attributes[$sso_ssp_att_login][0], $attributes[$sso_ssp_att_name][0] ,$attributes[$sso_ssp_att_firstname][0] , "", "0","SSO");
        $userinfo['termsOfUses'] = 0;
    }
    else
        $userinfo['termsOfUses'] = $user['termsOfUse'];     
    


      
    /*----------------END UCL METHOD------------------*/   
      
      
      
      
    // Create user_sso information, if doesn't exist, or update last login date
    //
    try {
        require_once __DIR__.'/../commons/lib_database.php';
        global $db_object;        

        db_prepare();

        $reqSQL = $db_object->prepare('SELECT * FROM ezcast_sso_users WHERE user_ID=?');
        $reqSQL->bindParam(1, $attributes[$sso_ssp_att_login][0], PDO::PARAM_STR);
        $reqSQL->execute();

        if ($reqSQL->rowCount() == 0) {
            $reqSQL = $db_object->prepare('INSERT INTO ezcast_sso_users (user_ID,surname,forename,email,first_time,last_time) VALUES (?,?,?,?,NOW(),NOW())');
            $reqSQL->bindParam(1, $attributes[$sso_ssp_att_login][0], PDO::PARAM_STR);
            $reqSQL->bindParam(2, $attributes[$sso_ssp_att_name][0], PDO::PARAM_STR);
            $reqSQL->bindParam(3, $attributes[$sso_ssp_att_firstname][0], PDO::PARAM_STR);
            $reqSQL->bindParam(4, $attributes[$sso_ssp_att_email][0], PDO::PARAM_STR);
            
        } else {
            $reqSQL = $db_object->prepare('UPDATE  ezcast_sso_users SET last_time=NOW() WHERE  user_ID=?');
            $reqSQL->bindParam(1, $attributes[$sso_ssp_att_login][0], PDO::PARAM_STR);
        }

        $reqSQL->execute();
    } catch (Exception $e) {
        throw(new Exception("Error in add/update sso_user: ". $e->getMessage()));
    }

    return $userinfo;
}

function sso_logout()
{
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
function sso_getinfo($login)
{

    try {
        require_once __DIR__.'/../commons/lib_database.php';
        global $db_object;
        db_prepare();

        $reqSQL = $db_object->prepare('SELECT * FROM ezcast_sso_users WHERE user_ID=:login');
        $reqSQL->bindParam(":login", $login, PDO::PARAM_STR);
        $reqSQL->execute();
        
        if ($reqSQL->rowCount() == 1) {
            $row = $reqSQL->fetch(PDO::FETCH_ASSOC);
            $userinfo['full_name'] = $row['surname']." ".$row['forename'];
            $userinfo['email'] = $row['email'];
            $userinfo['login'] = $row['user_ID'];
            $userinfo['real_login'] = $userinfo['login'];

        } else {
            checkauth_last_error("wrong search result count:" . $reqSQL->rowCount());
            return false;
        }
    } catch (Exception $e) {
        throw(new Exception("Error in add/update sso_user: ". $e->getMessage()));
    }

    return $userinfo;
}
//end function
