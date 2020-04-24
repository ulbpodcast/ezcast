<?php

class View_per_time extends Module
{
    private $saved_data = array();


    public function analyse_line($date, $timestamp, $session, $ip, $netid, $level, $action, $other_info = null)
    {
        if ($action == "video_play_time") {
            global $video_split_time;
            
            // other_info: current_album, current_asset, current_asset_name, type, last_play_start, play_time
            $album = trim($other_info[0]);
            $asset = trim($other_info[1]);
            $asset_name = trim($other_info[2]);
            $type = trim($other_info[3]);
            $start = trim($other_info[4]);
            $play_time = trim($other_info[5]);

            if (!array_key_exists($album, $this->saved_data)) {
                $this->saved_data[$album] = array($asset => array());
            }

            if (!array_key_exists($asset, $this->saved_data[$album])) {
                $this->saved_data[$album][$asset] = array();
            }

            if (!array_key_exists($type, $this->saved_data[$album][$asset])) {
                $this->saved_data[$album][$asset][$type] = array();
            }

            // For each second of the playtime
            for ($i = 0; $i < $play_time; ++$i) {
                // Calcul the real video time
                $time_until_start = $start+$i;
                // Find the "index" of the video (named video_time)
                $video_time = $time_until_start / $video_split_time;
                $str_video_time = strval(floor($video_time)); // bottom round and convert to string

                if (!array_key_exists($str_video_time, $this->saved_data[$album][$asset][$type])) {
                    $this->saved_data[$album][$asset][$type][$str_video_time] = (1 / $video_split_time);
                } else {
                    $this->saved_data[$album][$asset][$type][$str_video_time] += (1 / $video_split_time);
                }
            }
        }
    }

    public function end_file()
    {
        foreach ($this->saved_data as $album => $album_data) {
            foreach ($album_data as $asset => $asset_data) {
                foreach ($asset_data as $type => $type_data) {
                    foreach ($type_data as $video_time => $value) {
                        $nbr_view = round($value);
                        if ($nbr_view > 0) {
                            $this->save_to_sql($album, $asset, $type, $nbr_view, $video_time);
                        }
                    }
                }
            }
        }
        // Reset saved_data
        $this->saved_data = array();
    }

    public function save_to_sql($album, $asset, $type, $nbr_view, $video_time)
    {
        $this->logger->debug('[view_per_time] save sql: album:' . $album . ' | asset: ' . $asset .
            ' | type: ' . $type . ' | nbr_view: ' . $nbr_view . ' | video_time: ' . $video_time);

        $db = $this->database->get_database_object();
        $query = $db->prepare('INSERT INTO ' . $this->database->get_table('stats_video_view') . ' ' .
                    '(visibility, asset, album, type, nbr_view, video_time) ' .
                    'VALUES(:visibility, :asset, :album, :type, :nbr_view, :video_time) '.
                'ON DUPLICATE KEY UPDATE ' .
                    'nbr_view =  nbr_view + :nbr_view;');
        $query->execute(array(
                ':visibility' => 0,
                ':asset' => $asset,
                ':album' => $album,
                ':type' => $type,
                ':nbr_view' => $nbr_view,
                ':video_time' => $video_time
            ));
        $query->execute(array(
                ':visibility' => 1,
                ':asset' => $asset,
                ':album' => $album,
                ':type' => $type,
                ':nbr_view' => $nbr_view,
                ':video_time' => $video_time
            ));
    }
}
