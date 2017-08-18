<?php

class View_per_time extends Module {

    private static $SPLIT_TIME = 30;

    private $saved_data = array();
    private $month;


    function analyse_line($date, $timestamp, $session, $ip, $netid, $level, $action, $other_info = NULL) {
        if($action == "video_play_time") {
            // other_info: current_album, current_asset, type, last_play_start, play_time
            $album = trim($other_info[0]);
            $asset = trim($other_info[1]);
            $type = trim($other_info[2]);
            $start = trim($other_info[3]);
            $play_time = trim($other_info[4]);
            $this->month = date('m-Y', $timestamp);

            if(!array_key_exists($album, $this->saved_data)) {
                $this->saved_data[$album] = array($asset => array());
            }

            if(!array_key_exists($asset, $this->saved_data[$album])) {
                $this->saved_data[$album][$asset] = array();
            }

            for($i = 0; $i < $play_time; ++$i) {
                $time_until_start = $start+$i;

                $video_time = $time_until_start / self::$SPLIT_TIME;
                $str_video_time = strval(floor($video_time));

                if(!array_key_exists($str_video_time, $this->saved_data[$album][$asset])) {
                    $this->saved_data[$album][$asset][$str_video_time] = (1 / self::$SPLIT_TIME);
                } else {
                    $this->saved_data[$album][$asset][$str_video_time] += (1 / self::$SPLIT_TIME);
                }
            }

        }
    }

    function end_file() {
        foreach ($this->saved_data as $album => $album_data) {
            foreach ($album_data as $asset => $asset_data) {
                foreach ($asset_data as $video_time => $value) {
                    $nbr_view = round($value);
                    if($nbr_view > 0) {
                        $this->save_to_sql($album, $asset, $nbr_view, $video_time);
                    }
                }
            }
        }
        // Reset saved_data
        $this->saved_data = array();
    }

    function save_to_sql($album, $asset, $nbr_view, $video_time) {
        $this->logger->debug('[view_per_time] save sql: album:' . $album . ' | asset: ' . $asset . 
            ' | nbr_view: ' . $nbr_view . ' | video_time: ' . $video_time . ' | month: ' . $this->month);

        $db = $this->database->get_database_object();
        $query = $db->prepare('INSERT INTO ' . $this->database->get_table('stats_video_view') . ' ' .
                    '(asset, album, nbr_view, video_time, month) ' .
                    'VALUES(:asset, :album, :nbr_view, :video_time, :month) '.
                'ON DUPLICATE KEY UPDATE ' .
                    'nbr_view =  nbr_view + :nbr_view;');
        $query->execute(array(
                ':asset' => $asset,
                ':album' => $album,
                ':nbr_view' => $nbr_view,
                ':video_time' => $video_time,
                ':month' => $this->month
            ));
    }

}

