<?php

/**
 * General popup dispatcher. Depending on the value of $input['popup'], it will show the right popup.
 * @global type $input
 */
function index($param = array())
{
    global $input;

    if (!isset($input['popup'])) {
        echo 'Usage: web_index.php?action=show_popup&amp;popup=POPUP';
        die;
    }

    switch ($input['popup']) {
        case 'media_url':
            popup_media_url();
            break;

        case 'embed_code':
            popup_embed_code();
            break;

        case 'ezplayer_link':
            popup_ezplayer_link();
            break;

        case 'ulb_code':
            popup_ulb_code();
            break;
        
        case 'new_album':
            popup_new_album();
            break;

        case 'ezrecorder':
            popup_ezrecorder();
            break;
        
        case 'delete_album':
            popup_delete_album();
            break;
        
        case 'reset_rss_feed':
            reset_rss_feed();
            break;

        case 'unpublish_asset':
            unpublish_asset();
            break;

        case 'publish_asset':
            publish_asset();
            break;

        case 'delete_asset':
            delete_asset();
            break;

        case 'popup_not_available':
            popup_not_available();
            break;

        case 'move_asset':
            move_asset();
            break;

        case 'schedule_asset':
            schedule_asset();
            break;
        
        case 'regen_title':
            regen_title();
            break;
        
        case 'moderator_delete':
            moderator_delete();
            break;
        
        case 'asset_stats':
            asset_stats();
            break;
        
        case 'album_stats_reset':
            album_stats_reset();
            break;

        case 'copy_asset':
            copy_asset();
            break;
        
        default:
            error_print_message('view_popup: content of popup ' . $input['popup'] . ' not found');
            die;
    }
}


/**
 * Displays the popup with a link to the media
 */
function popup_media_url()
{
    global $input;
    global $repository_path;
    global $ezmanager_url;

    ezmam_repository_path($repository_path);
    $media_url = get_link_to_media($input['album'], $input['asset'], $input['media']) . "&origin=link";
    $media_url_web = get_link_to_media($input['album'], $input['asset'], $input['media'], false) . "&origin=link";


    require_once template_getpath('popup_media_url.php');

    $url = $ezmanager_url;
    // TODO: why include 2 times ?
    require_once template_getpath('popup_media_url.php');
}


/**
 * DIsplays the popup with the embed code to copypaste
 * @global type $input
 * @global type $repository_path
 * @global type $url
 */
function popup_embed_code()
{
    global $input;
    global $repository_path;
    global $ezmanager_url;
    global $distribute_url;
    global $embedIframed;

    ezmam_repository_path($repository_path);
    template_load_dictionnary('translations.xml');

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['media'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=embed_code&amp;album=ALBUM&amp;asset=ASSET&amp;media=MEDIA';
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }

    // Retrieving the info needed for the embed code and target link
    $metadata = ezmam_media_metadata_get($input['album'], $input['asset'], $input['media']);
    $token = ezmam_asset_token_get($input['album'], $input['asset']);
    if (!$token) {
        $token = ezmam_album_token_get($input['album']);
    }

    $media_infos = explode('_', $input['media']);
    $type = $media_infos[1];
    $quality = $media_infos[0];
    //compute iframe size according to media size
//    $iframe_height = $metadata['height'] + 40;
//    $iframe_width = $metadata['width'] + 30;
    //force width & heigth in youtube standart
    $iframe_width = 560;
    $iframe_height = 315;
    // Embed code
    $link_target = $distribute_url . '?action=embed&amp;album=' . $input['album'] . '&amp;asset=' . $input['asset'] .
            '&amp;type=' . $type . '&amp;quality=' . $quality . '&amp;token=' . $token;
    $embed_code_web = '<iframe width="' . $iframe_width . '" height="' . $iframe_height .
            '" style="padding: 0;" frameborder="0" scrolling="no" src="' . $distribute_url . '?action=embed&album=' .
            $input['album'] . '&asset=' . $input['asset'] . '&type=' . $type . '&quality=' . $quality . '&token=' .
            $token . '&width=' . $metadata['width'] . '&height=' . $metadata['height'] . '&lang=' . get_lang() .
            '"><a href="' . $link_target . '">' . template_get_message('view_video', get_lang()) . '</a></iframe>';
    $embed_code = htmlentities($embed_code_web, ENT_COMPAT, 'UTF-8');

    // Displaying the popup
    require_once template_getpath('popup_embed_code.php');
}


/**
 * Displays the popup with the EZplayer link to copypaste
 * @global type $input
 * @global type $repository_path
 * @global type $url
 */
function popup_ezplayer_link()
{
    global $input;
    global $ezplayer_url;
    global $repository_path;

    $album = $input['album'];
    $asset = $input['asset'];

    ezmam_repository_path($repository_path);

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset'])) {
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }
    $asset_meta = ezmam_asset_metadata_get($input['album'], $input['asset']);
    $action = (isset($asset_meta['origin']) && strtolower($asset_meta['origin']) === "streaming") ?
            'view_asset_streaming' :
            'view_asset_details';
    $token = ezmam_asset_token_get($input['album'], $input['asset']);
    $ezplayer_link = $ezplayer_url . '/index.php?'
            . 'action=' . $action
            . '&album=' . $album
            . '&asset=' . $asset
            . '&asset_token=' . $token;

    // Displaying the popup
    require_once template_getpath('popup_ezplayer_link.php');
}


/**
 * Displays the popup with the ulb code to copypaste
 * @global type $input
 * @global type $repository_path
 * @global type $url
 */
function popup_ulb_code()
{
    global $input;
    global $repository_path;

    $asset_name = $input['asset'];

    ezmam_repository_path($repository_path);
    template_load_dictionnary('translations.xml');

    //
    // Sanity checks
    //
    if (!isset($input['album']) || !isset($input['asset']) || !isset($input['media'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=ulb_code&amp;album=ALBUM&amp;asset=ASSET&amp;media=MEDIA';
        die;
    }

    if (!ezmam_album_exists($input['album']) || !ezmam_asset_exists($input['album'], $input['asset'])) {
        error_print_message(ezmam_last_error());
        die;
    }

    $ulb_code = get_code_to_media($input['album'], $input['asset'], $input['media']);

    // Displaying the popup
    require_once template_getpath('popup_ulb_code.php');
}

function popup_new_album()
{
    $not_created_albums_with_descriptions = acl_authorized_albums_list_not_created(true); // Used to display the popup_new_album
    require_once template_getpath('popup_new_album.php');
}

function popup_ezrecorder()
{
    //$not_created_albums_with_descriptions = acl_authorized_albums_list_not_created(true); // Used to display the popup_new_album
    $album= acl_authorized_albums_list_created($assoc = false);
    $album_view=array();
    foreach ($album as $album_name) { 
      $course_info=db_course_read($album_name);
      if (isset($course_info['course_code_public']) && $course_info['course_code_public']!='') {
            $course_code_public = $course_info['course_code_public'];
        } else {
            $course_code_public = $album_name;
        }
      $in_recorders=isset($course_info['in_recorders']) && $course_info['in_recorders']!=false;
      array_push($album_view, array('course_code'=>$album_name,'course_code_public'=>$course_code_public,'in_recorders'=>$in_recorders));
    }
    
    //check if the user already has an ezrecorder pw
    $user_info=db_user_read($_SESSION['user_login']);
    $user_has_ezrecorder_pw = $user_info!=false && isset($user_info) && $user_info['passNotSet']!=true;
    require_once template_getpath('popup_ezrecorder.php');
}

function popup_delete_album()
{
    global $input;
    if (!isset($input['album']) || !isset($input['album_id'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=delete_album&amp;album=ALBUM&amp;album_id=ALBUM-ID';
        die;
    }
    $album_name = $input['album'];
    $album_id = $input['album_id'];
    require_once template_getpath('popup_delete_album.php');
}

function reset_rss_feed()
{
    global $input;
    if (!isset($input['album'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=reset_rss_feed&amp;album=ALBUM';
        die;
    }
    $album = $input['album'];
    require_once template_getpath('popup_reset_rss_feed.php');
}

function unpublish_asset()
{
    global $input;
    if (!isset($input['asset']) || !isset($input['album']) || !isset($input['title'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=unpublish_asset&amp;title=TITLE&amp;album=ALBUM&amp;asset=ASSET';
        die;
    }
    $title = $input['title'];
    $album = $input['album'];
    $asset_name = $input['asset'];
    require template_getpath('popup_unpublish_asset.php');
}

function publish_asset()
{
    global $input;
    if (!isset($input['asset']) || !isset($input['album']) || !isset($input['title'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=publish_asset&amp;title=TITLE&amp;album=ALBUM&amp;asset=ASSET';
        die;
    }
    $title = $input['title'];
    $album = $input['album'];
    $asset_name = $input['asset'];
    require template_getpath('popup_publish_asset.php');
}

function delete_asset()
{
    global $input;
    if (!isset($input['asset']) || !isset($input['album']) || !isset($input['title'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=delete_asset&amp;title=TITLE&amp;album=ALBUM&amp;asset=ASSET';
        die;
    }
    $title = $input['title'];
    $album = $input['album'];
    $asset_name = $input['asset'];
    require template_getpath('popup_delete_asset.php');
}

function popup_not_available()
{
    require template_getpath('popup_not_available_while_processing.php');
}

function move_asset()
{
    global $input;
    if (!isset($input['asset']) || !isset($input['album'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=move_asset&amp;album=ALBUM&amp;asset=ASSET';
        die;
    }
    $created_albums_list_with_descriptions = acl_authorized_albums_list_created(true);
    $asset_name = $input['asset'];
    $album = $input['album'];

    require template_getpath('popup_move_asset.php');
}

function schedule_asset()
{
    global $input;
    if (!isset($input['asset']) || !isset($input['album'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=schedule_asset&amp;album=ALBUM&amp;asset=ASSET';
        die;
    }
    $asset = $input['asset'];
    $asset_name = $asset;
    $album = $input['album'];

    $DTZ = new DateTimeZone('Europe/Paris');
    global $repository_path;
    ezmam_repository_path($repository_path);
    $asset_metadata = ezmam_asset_metadata_get($album, $asset);

    $title = $asset_metadata['title']; // "user-friendly" asset name (title)
    $asset_scheduled = isset($asset_metadata['scheduled']) ? $asset_metadata['scheduled'] : false;
    $asset_sched_date = isset($asset_metadata['schedule_date']) ? $asset_metadata['schedule_date'] : false;
    $asset_sched_id = isset($asset_metadata['schedule_id']) ? $asset_metadata['schedule_id'] : false;


    require template_getpath('popup_schedule.php');
}

function regen_title()
{
    global $input;
    if (!isset($input['asset']) || !isset($input['album']) || !isset($input['title'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=regen_title&amp;album=ALBUM&amp;asset=ASSET&amp;title=TITLE';
        die;
    }
    $asset_name = $input['asset'];
    $album = $input['album'];
    $title = $input['title'];
    
    require template_getpath('popup_regen_title.php');
}

function moderator_delete()
{
    global $input;
    if (!isset($input['id_user']) || !isset($input['album'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=moderator_delete&amp;album=ALBUM&amp;id_user=ID_USER';
        die;
    }
    $album = $input['album'];
    $id_user = $input['id_user'];
    
    require template_getpath('popup_moderator_delete.php');
}

function asset_stats()
{
    global $input;
    global $video_split_time;
    
    if (!isset($input['asset']) || !isset($input['album'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=asset_stats&amp;album=ALBUM&amp;asset=ASSET';
        die;
    }
    require_once dirname(__FILE__) . '/../lib_sql_stats.php';
    
    $asset = $input['asset'];
    $album = $input['album'];
    $asset_metadata = ezmam_asset_metadata_get($album, $asset);
    $has_cam = (strpos($asset_metadata['record_type'], 'cam') !== false); // Whether or not the asset has a "live-action" video
    $has_slides = (strpos($asset_metadata['record_type'], 'slide') !== false); // Whether or not the asset has slides
    $asset_token = ezmam_asset_token_get($album, $asset); // Asset token, used for embedded media player (preview)
    $all_view_time = db_stats_video_get_view_time($album, $asset);
    
    $stats = array();
    if (count($all_view_time) > 0) {
        $data_view_time = array('cam' => array(), 'slide' => array());
        $last_time = 0;
        foreach ($all_view_time as $view_time) {
            if (!array_key_exists($view_time['type'], $data_view_time)) {
                $data_view_time[$view_time['type']] = array();
            }
            while ($view_time['video_time'] > $last_time) {
                $data_view_time[$view_time['type']][] = 0;
                ++$last_time;
            }
            $data_view_time[$view_time['type']][] = intval($view_time['nbr_view']);
            ++$last_time;
        }
        $stats['display'] = true;
        $stats['str_view_time_cam'] = json_encode($data_view_time['cam']);
        $stats['str_view_time_slide'] = json_encode($data_view_time['slide']);
    } else {
        $stats['display'] = false;
    }
    
    require template_getpath('popup_asset_stats.php');
}

function album_stats_reset()
{
    global $input;
    
    if (!isset($input['album'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=album_stats_reset&amp;album=ALBUM';
        die;
    }
    $album = $input['album'];
    
    require template_getpath('popup_album_stats_reset.php');
}

function copy_asset()
{
    global $input;
    if (!isset($input['asset']) || !isset($input['album'])) {
        echo 'Usage: index.php?action=show_popup&amp;popup=copy_asset&amp;album=ALBUM&amp;asset=ASSET';
        die;
    }
    $created_albums_list_with_descriptions = acl_authorized_albums_list_created(true);
    $asset_name = $input['asset'];
    $album = $input['album'];

    require template_getpath('popup_copy_asset.php');
}
