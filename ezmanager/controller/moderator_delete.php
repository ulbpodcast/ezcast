<?php

function index($param = array())
{
    global $intros;
    global $credits;
    global $titlings;
    global $downloadable;
    global $anon_access;
    global $repository_path;
    global $default_add_title;
    global $default_downloadable;
    global $default_anon_access;
    global $basedir;
    global $input;
    global $logger;
       
    require_once $basedir.'/commons/lib_sql_management.php';

    $album = $input['album'];
    $iduser = $input['iduser'];
    
    if (!acl_has_album_permissions($album)) {
        error_print_message(template_get_message('Unauthorized', get_lang()));
        $logger->log(
             EventType::MANAGER_MODO_DELETE,
             LogLevel::WARNING,
             'User tried to delete modo '.$iduser.' from album  '. $album. ' but is not authorized',
                 array(basename(__FILE__)),
             $album
         );
        die;
    }
    
    db_users_courses_delete_row($album, $iduser);
    
    // $album = suffix_remove($_SESSION['podman_album']);
    $tbusercourse = users_courses_get_users($album);
    
    header('Location: index.php?action=view_list_moderator&album='.$album);
     
    die;
}
