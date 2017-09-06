<?php

require_once 'lib_sql_event.php';
include_once '../commons/event_status.php';
/// Define Helper ///
include_once '../commons/view_helpers/helper_pagination.php';
include_once '../commons/view_helpers/helper_sort_col.php';

global $MAX_TIME_NO_CRON;
$MAX_TIME_NO_CRON = 3 * 24 * 60 * 60; //3 days


function index($param = array())
{
    global $input;
    global $MAX_TIME_NO_CRON;
    global $ezmanager_basedir;
    global $logger;
    
    /// Make action from the modal ///
    if (isset($input) && array_key_exists('modal_action', $input) &&
            array_key_exists('current_asset', $input)) {
        $errorActionMsg = actionFromModal($input);
    }
    
    $date_last_insert = db_event_status_last_insert();
    if (time() - strtotime($date_last_insert[0]) > $MAX_TIME_NO_CRON) {
        $cronWarningMsg = true;
    }
    
    if (array_key_exists('page', $input)) {
        $pagination = new Pagination($input['page'], 50);
    } else {
        $pagination = new Pagination(1, 50);
    }
    if (array_key_exists('col', $input) && array_key_exists('order', $input)) {
        $colOrder = new Sort_colonne($input['col'], $input['order']);
    } else {
        $colOrder = new Sort_colonne('status_time');
    }
    
    // Get Status
    if (isset($input['post'])) {
        $listStatus = db_event_status_get(
            empty_str_if_not_def('startDate', $input),
                empty_str_if_not_def('endDate', $input),
                empty_str_if_not_def('status', $input),
                empty_str_if_not_def('asset', $input),
                $colOrder->getCurrentSortCol(),
            $colOrder->getOrderSort(),
                $pagination->getStartElem(),
            $pagination->getElemPerPage(),
                get_courses_excluded_from_stats()
        );
        $pagination->setTotalItem(db_found_rows());
        
        $view_all = array_key_exists('view_all', $input) && $input['view_all'] == 'on';
        
        // Get children and parent
        $allParent = db_event_get_asset_parent(empty_str_if_not_def('asset', $input));
        
        // List of the children (key = an asset and in value his children)
        $listChildren = array();
        $listAssetWithParent = array(); // List of asset who have an parent
        foreach ($allParent as $parentInfo) {
            $parentAsset = $parentInfo['parent_asset'];
            $asset = $parentInfo['asset'];
            
            array_push($listAssetWithParent, $asset);
            if (!array_key_exists($parentAsset, $listChildren)) {
                $listChildren[$parentAsset] = array();
            }
            array_push($listChildren[$parentAsset], $asset);
        }
        
        // List of status who must be viewed
        // If view_all is define, this is just an array list
        // If view_all is turn off, it's a dictionnary
        $resStatus = array();
        
        
        foreach ($listStatus as $status) {
            // If not in array or if we will see all
            if (!in_array($status['asset'], $listAssetWithParent) || $view_all) {
                // Just adapt var
                $status['status_time'] = date("d/m/y H:i:s", strtotime($status['status_time']));
                if (strlen($status['description']) > 50) {
                    $status['min_description'] = substr($status['description'], 0, 50);
                    $status['min_description'] .= "...";
                }
                $resStatus = status_listStatus_add($resStatus, $status, $view_all);
            }
        }
    }
    
    // Display page
    include template_getpath('div_main_header.php');
    include template_getpath('div_monit_search_status.php');
    if (isset($resStatus)) {
        include template_getpath('div_monit_list_status.php');
    }
    include template_getpath('div_main_footer.php');
}


function status_listStatus_add($listToAdd, $status, $view_all)
{
    if ($view_all) {
        array_push($listToAdd, $status);
    } else {
        
        // If not in array or if newer than the old saved
        if (!array_key_exists($status['asset'], $listToAdd) ||
                strtotime($listToAdd[$status['asset']]['status_time']) < strtotime($status['status_time'])) {
            $listToAdd[$status['asset']] = $status;
        }
    }
    return $listToAdd;
}

/**
 * Make action who was send by Modal "popup"
 *
 * @param Array $input All informations about this action
 * @return int Error code (NULL if all is ok)
 */
function actionFromModal($input)
{
    global $logger;
    
    $error = null;
    
    if (!isset($input['current_asset']) || $input['current_asset'] == "") {
        //invalid input asset
        $error = 1;
        return $error;
    }
    $current_asset = $input['current_asset'];
            
    if (!isset($input['modal_action']) || $input['modal_action'] == "") {
        //invalid input action
        $error = 2;
        return $error;
    }
    
    switch ($input['modal_action']) {
        case "new_parent":
            if (array_key_exists('parent_asset', $input)) {
                $parent_asset = $input['parent_asset'];
                if (db_event_asset_status_exist($parent_asset)) {
                    db_event_status_add(
                        $current_asset,
                        EventStatus::MANUAL_IGNORE,
                            "Define a new parent: ".$parent_asset,
                        $_SESSION['user_login']
                    );
                    db_event_asset_parent_add($current_asset, $parent_asset);
                } else {
                    $error = 3;
                }
            } else {
                $error = 4;
            }
            break;
        case  "new_status":

            if (array_key_exists('new_status', $input) && array_key_exists('new_description', $input)) {
                $new_status = $input['new_status'];
                $description = $input['new_description'];

                db_event_status_add(
                    $current_asset,
                    $new_status,
                    $description,
                        $_SESSION['user_login']
                );
            } else {
                $error = 10;
            }
            break;
        case "remove_parent":
            db_event_asset_parent_remove($current_asset);
            db_event_status_add(
                $current_asset,
                EventStatus::MANUAL_IGNORE,
                "Remove parent",
                $_SESSION['user_login']
            );
            break;
        default:
            return "Invalid input action " . $input['modal_action'];
    }
   
    return $error;
}
