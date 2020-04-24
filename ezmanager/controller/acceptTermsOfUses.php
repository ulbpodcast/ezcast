<?php

require_once(__DIR__ . '/../../commons/lib_sql_management.php');

/**
 * All the business logic related to the album creation: this function effectively creates the album and displays a confirmation message to the user
 */
 function index($param = array())
 {
    if(db_termsOfUseUpdate($_SESSION['user_real_login'],1))
        $_SESSION['termsOfUses']=1 ;
    
//    print_r($_SESSION);die();
    $redirect= json_decode($_SESSION['redirect']);
    $path='?';
    
    foreach ($redirect as $k => $v) {
        $path.=$k."=".$v."&";
    }
//    die($path);
     header('Location: index.php'.$path); 
     
 }