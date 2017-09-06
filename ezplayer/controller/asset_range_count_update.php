<?php

/**
 * Increments the view count for a specific range of the video
 */
function index($param = array())
{
    global $input;
    global $repository_path;

    $time = $input['time'];
    $type = $input['type'];

    $album = (isset($input['album']) && $input['album'] != '') ? $input['album'] : $_SESSION['album'];
    $asset = (isset($input['asset']) && $input['asset'] != '') ? $input['asset'] : $_SESSION['asset'];


    if (!isset($time) || is_nan($time) || $time == '') {
        return;
    }
    if (!isset($type) || $type == '') {
        return;
    }
    if (!isset($album) || $album == '') {
        return;
    }
    if (!isset($asset) || $asset == '') {
        return;
    }

    ezmam_repository_path($repository_path);
    if (!ezmam_asset_exists($album, $asset)) {
        return;
    }

    $range_count_path = $repository_path . '/' . $album . '/' . $asset . '/range_count';
    mkdir($range_count_path, 0755);
    $date = date('Ymd');
    $array = array();
    if (file_exists($range_count_path . '/' . $date . '_' . $type . '.php')) {
        $array = include_once $range_count_path . '/' . $date . '_' . $type . '.php';
    }
    $index = ((int) ($time / 3)) - 1;
    if ($index >= 0) {
        if (isset($array[$index]) && $array[$index] != '') {
            $array[$index] ++;
        } else {
            $array[$index] = 1;
        }
    }
    ksort($array);
    $range_count_str = "<?php return ";
    $range_count_str .= var_export($array, true);
    $range_count_str .= "; ?>";

    $random = rand();
    file_put_contents($range_count_path . '/' . $date . '_' . $type . '_' . $random . '.php', $range_count_str);
    rename($range_count_path . '/' . $date . '_' . $type . '_' . $random . '.php', $range_count_path . '/' . $date . '_' . $type . '.php');
}
