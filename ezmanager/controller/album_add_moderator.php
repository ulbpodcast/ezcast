<?php
function index($param = array())
{
    global $input;
    global $basedir;
    global $repository_basedir;

    require_once $basedir.'/commons/lib_sql_management.php';
    require_once $basedir.'/commons/lib_various.php';
    include_once $basedir.'/ezmanager/lib_ezmam.php';

    $iduser = $_SESSION['user_login'];

    if ($input['action'] == 'add_moderator' && isset($input['album']) && isset($input['tokenmanager'])) {
        if (strcmp($input['tokenmanager'], file_get_contents($repository_basedir. '/repository/' . $input['album'].'/_tokenmanager')) == 0) {
            $tbcours = db_user_get_courses($iduser);
            $exist = 0;
            for ($i = 0;$i<count($tbcours);$i++) {
                if (suffix_remove($input['album']) == $tbcours[$i]['course_code']) {
                    $exist = 1;
                }
            }
            if ($exist == 0) {
                $res = db_users_courses_create(suffix_remove($input['album']), $iduser);
                file_put_contents($repository_basedir. '/repository/' . $input['album'].'/_tokenmanager', '');
                $_SESSION['modoAdded'] = get_album_title(($input['album']));
            }
        }
    }
    acl_update_permissions_list();
    header('Location: index.php');
}
