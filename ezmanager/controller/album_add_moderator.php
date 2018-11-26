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

    if ($input['action'] == 'add_moderator' && isset($input['album']) && isset($input['tokenmanager']))
    {
        $file_token = json_decode(file_get_contents($repository_basedir. '/repository/' . $input['album'].'/_test1'), true);
        $is_exist = false;

        foreach ($file_token as $album => $tokens)
        {
            if($input['album'] == $album)
            {
                if(in_array($input['tokenmanager'], $tokens))
                {
                    $is_exist = true;
                }
            }
        }

        if ($is_exist)
        {
            $tbcours = db_user_get_courses($iduser);
            $exist = 0;
            for ($i = 0;$i<count($tbcours);$i++)
            {
                if (suffix_remove($input['album']) == $tbcours[$i]['course_code'])
                {
                    $exist = 1;
                }
            }
            if ($exist == 0)
            {
                $res = db_users_courses_create(suffix_remove($input['album']), $iduser);
                $_SESSION['modoAdded'] = get_album_title(($input['album']));
            }

            $index_token = array_search($input['tokenmanager'], $file_token[$input['album']]);
            unset($file_token[$input['album']][$index_token]);
            file_put_contents($repository_basedir. '/repository/' . $input['album'].'/_test1', json_encode($file_token));
        }
    }
    acl_update_permissions_list();
    header('Location: index.php');
}
