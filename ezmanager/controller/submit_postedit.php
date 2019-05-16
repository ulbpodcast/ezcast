<?php
require_once __DIR__ . '/../config.inc';
require_once 'lib_ezmam.php';

Logger::$print_logs = true;
/**
 * Processes media submission
 * Used only by old web browsers (when xhr2 is not supported)
 * @global type $input
 */
function index($param = array())
{
    global $input;
    global $php_cli_cmd;
    global $submit_post_edit_pgm;
    global $dir_date_format;
    global $submit_postedit_dir;
    global $logger;

    // 1) Sanity checks
    // album=<?php echo $album; &asset=<?php echo $asset_name; &sesskey=<?php echo $_SESSION['sesskey']&cutArray='+myJson
    $album=$input['album'];
    $asset=$input['asset'];
    $jsonStr=$input['cutArray'];

    if (!isset($album) || empty($album) || !ezmam_album_exists($album)) {
        if (!ezmam_album_exists($album)) {
            error_print_message('No corresponding album in the repository');
            die;
        }
        error_print_message('Album not set');
        die;
    }

    if (!isset($asset) || empty($asset) || !ezmam_asset_exists($album, $asset)) {
        if (!ezmam_asset_exists($album, $asset)) {
            error_print_message('No corresponding album in the repository');
            die;
        }
        error_print_message('Album not set');
        die;
    }

    if (!acl_session_key_check($input['sesskey']) && !checkInitSubmitServiceVar($input)) {
        echo "Usage: Session key is not valid";
        die;
    }

    //test json validity
    if (!isset($jsonStr) || empty($jsonStr)) {
        error_print_message('cutarray not set');
        die;
    }

    try {
        $stdClass=json_decode($jsonStr);
    } catch (Exception $e) {
        error_print_message('cutarray not a well formatted JSON');
        die;
    }

    $metadata=ezmam_asset_get_processed_metadata($album, $asset);

    if(!$metadata)
    {
        error_print_message('No processed directory');
        die;
    }
    $duration=$metadata['duration'];
    $cutArray=get_object_vars($stdClass)['cutArray'];
    if ($cutArray[0][0]<0||$cutArray[0][1]>$duration||$cutArray[0][0]>$cutArray[0][1]) {
        error_print_message('cutarray first arg is false');
        die;
    }
    for ($i=1; $i < sizeof($cutArray); $i++) {
        if ($cutArray[$i-1][1]>$cutArray[$i][0]||$cutArray[$i][0]>$cutArray[$i][1]||$cutArray[$i][1]>$duration) {
            error_print_message('cutarray is false');
            die;
        }
    }
// 2) check if _cutlist.json exist create it if needed then put the cutarray content in it
    ezmam_set_cutlist($album,$asset,$jsonStr);


    // 3) Creating the folder in the queue, and the metadata for the media

    $tmp_name = $asset . '_' . $album;
    $path = $submit_postedit_dir . '/' . $tmp_name;



    $album_metadata=ezmam_album_metadata_get($album);
    $asset_metadata=ezmam_asset_metadata_get($album, $asset);
    $original_metadata=ezmam_media_metadata_get($album,$asset,'original_cam');

    $metadata = array(
        'course_name' => $album,
        'origin' => 'SUBMIT',
        'title' => $album_metadata['title'],
        'description' => $asset_metadata['description'],
        'record_type' => $asset_metadata['record_type'],
        'author' => $asset_metadata['author'],
        'record_date' => $asset_metadata['record_date'],
        'super_highres' => $asset_metadata['super_highres'],
        'intro' => $album_metadata['intro'],
        'credits' => $album_metadata['credits'],
        'add_title' => $album_metadata['add_title'],
        'downloadable' => $asset_metadata['downloadable'],
        'ratio' => $asset_metadata['ratio']
    );
    $res=ezmam_postedit_submit_create_metadata($tmp_name, $metadata);

// 4) Calling cli_submit_postedit.php so that it begin the postedit processing
    $cmd = "$php_cli_cmd $submit_post_edit_pgm $album $asset";

    system($cmd, $returncode);
    if ($returncode) {
        $logger->log(EventType::MANAGER_SUBMIT_POSTEDIT, LogLevel::CRITICAL, "Command $cmd failed with result $returncode", array("submit_postedit"), $asset);
        // set_asset_status_to_failure();
        exit(7);
    }

}
