<?php
function db_sync()
{
    global $db_object;
    
    $origin = $include_external && $include_internal ? '%' : ($include_external ? 'external' : 'internal');
    
    $join = 'LEFT';
    if ($with_teacher == 1) {
        $join = 'INNER';
    }
    
    $query = "SELECT * from  pcast_digger_VW_LISTE_ENSEIGNANTS_tmp WHERE TERM='201415'";
    $list_users_digger = $db_object->query($query);
    
    $query;
        
    return $res;
}
