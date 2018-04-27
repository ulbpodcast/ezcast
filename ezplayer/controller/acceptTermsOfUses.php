<?php

require_once(__DIR__ . '/../../commons/lib_sql_management.php');

/**
 * All the business logic related to the album creation: this function effectively creates the album and displays a confirmation message to the user
 */
 function index($param = array())
 {
//     
//     print_r($_SESSION);
//     die();
     
    if(db_termsOfUseUpdate($_SESSION['user_real_login'],1))
        $_SESSION['termsOfUses']=1 ;
    
    $redirect= json_decode($_SESSION['redirect']);
    $path='?';
    
    foreach ($redirect as $k => $v) {
        $path.=$k."=".$v."&";
    }

     header('Location: index.php'.$path); 
     
 }