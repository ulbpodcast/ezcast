<?php

/**
 * === View ===
 * A user have "view" a video if he have watch minimum 1 minute.
 *
 * === Total ===
 * We use "session" to count total view. So if a user watch a video
 * go to menu and watch agin this video, there will be only one view.
 *
 * === Unique ===
 * A user can only have (maximum) one view per video (per month).
 * If a user watch a video, and the next day watch the same video
 * (if it's the same month), there will be only one view.
 * ANONYM
 * For anonym user, we use "session".
 *
 */

class Infos_per_month extends Module
{
    private static $VIEW_MIN_TIME = 60;

    private $sql_data = array();
    private $saved_view_data = array();
    private $month;

    private $cache_asset_name = array();

    public function analyse_line($date, $timestamp, $session, $ip, $netid, $level, $action, $other_info = null)
    {
        $this->month = date('m-Y', $timestamp);

        if ($action == "video_play_time") {
            // other_info: current_album, current_asset, current_asset_name, type, last_play_start, play_time
            $album = trim($other_info[0]);
            $asset = trim($other_info[1]);
            $asset_name = trim($other_info[2]);
            $type = trim($other_info[3]);
            $start = trim($other_info[4]);
            $play_time = trim($other_info[5]);

            if (!array_key_exists($album, $this->saved_view_data)) {
                $this->saved_view_data[$album] = array();
                $this->cache_asset_name[$album] = array();
            }
            $this->cache_asset_name[$album][$asset] = $asset_name;

            if (!array_key_exists($asset, $this->saved_view_data[$album])) {
                $this->saved_view_data[$album][$asset] = array(
                            'total' => array(),
                            'unique' => array(),
                            'unique_session' => array()
                        );
            }

            $key_unique_netid = 'unique';
            if ($netid == 'nologin') {
                $netid = $session;
                $key_unique_netid = 'unique_session';
            }

            if (!array_key_exists($netid, $this->saved_view_data[$album][$asset][$key_unique_netid])) {
                $this->saved_view_data[$album][$asset][$key_unique_netid][$netid] = $play_time;
            } elseif ($this->saved_view_data[$album][$asset][$key_unique_netid][$netid] < self::$VIEW_MIN_TIME) {
                $this->saved_view_data[$album][$asset][$key_unique_netid][$netid] += $play_time;
            }

            if (!array_key_exists($session, $this->saved_view_data[$album][$asset]['total'])) {
                $this->saved_view_data[$album][$asset]['total'][$session] = $play_time;
            } elseif ($this->saved_view_data[$album][$asset]['total'][$session] < self::$VIEW_MIN_TIME) {
                $this->saved_view_data[$album][$asset]['total'][$session] += $play_time;
            }
        }
    }

    public function end_file()
    {
        foreach ($this->saved_view_data as $album => $album_data) {
            foreach ($album_data as $asset => $asset_data) {
                $this->add_album_asset_sql_data($album, $asset);

                if (!array_key_exists('nbr_view_unique', $this->sql_data[$album][$asset])) {
                    $this->sql_data[$album][$asset]['nbr_view_unique'] = 0;
                    $this->sql_data[$album][$asset]['nbr_view_total'] = 0;
                }

                foreach ($asset_data['total'] as $session => $value) {
                    if ($value >= self::$VIEW_MIN_TIME) {
                        $this->sql_data[$album][$asset]['nbr_view_total']++;
                    }
                }

                $user_file = $this->get_user_view_file($album, $asset);
                foreach ($asset_data['unique'] as $netid => $value) {
                    if ($value >= self::$VIEW_MIN_TIME && !in_array($netid, $user_file)) {
                        $this->sql_data[$album][$asset]['nbr_view_unique']++;
                        $this->add_user_view_file($album, $asset, $netid);
                    }
                }

                foreach ($asset_data['unique_session'] as $netid => $value) {
                    if ($value >= self::$VIEW_MIN_TIME) {
                        $this->sql_data[$album][$asset]['nbr_view_unique']++;
                    }
                }
            }
        }

        // SAVE_TO_SQL
        foreach ($this->sql_data as $album => $album_data) {
            foreach ($album_data as $asset => $asset_data) {
                $nbr_view_total = 0;
                $nbr_view_unique = 0;

                if (array_key_exists('nbr_view_total', $asset_data)) {
                    $nbr_view_total = $asset_data['nbr_view_total'];
                    $nbr_view_unique = $asset_data['nbr_view_unique'];
                }

                if ($nbr_view_total > 0 || $nbr_view_unique > 0) {
                    $asset_name = "";
                    if (isset($this->cache_asset_name[$album][$asset])) {
                        $asset_name = $this->cache_asset_name[$album][$asset];
                    }
                    $this->save_to_sql($album, $asset, $asset_name, $nbr_view_total, $nbr_view_unique);
                }
            }
        }

        // Reset saved_view_data
        $this->cache_asset_name = array();
        $this->saved_view_data = array();
    }

    public function save_to_sql($album, $asset, $asset_name, $nbr_view_total, $nbr_view_unique)
    {
        $this->logger->debug('[infos_per_month] save sql: album: ' . $album . ' | asset: ' . $asset .
            ' | asset_name: ' . $asset_name . ' | nbr_view_total: ' . $nbr_view_total .
            ' | nbr_view_unique: ' . $nbr_view_unique . ' | month: ' . $this->month);

        $db = $this->database->get_database_object();
        $query = $db->prepare('INSERT INTO ' . $this->database->get_table('stats_video_month_infos') . ' ' .
                    '(visibility, asset, asset_name, album, nbr_view_total, nbr_view_unique, month) ' .
                    'VALUES(:visibility, :asset, :asset_name, :album, :nbr_view_total, :nbr_view_unique, :month) '.
                'ON DUPLICATE KEY UPDATE ' .
                    'nbr_view_total = nbr_view_total + :nbr_view_total, ' .
                    'nbr_view_unique =  nbr_view_unique + :nbr_view_unique;');
        $query->execute(array(
                ':visibility' => 1,
                ':asset' => $asset,
                ':asset_name' => $asset_name,
                ':album' => $album,
                ':nbr_view_total' => $nbr_view_total,
                ':nbr_view_unique' => $nbr_view_unique,
                ':month' => $this->month
            ));
        $query->execute(array(
                ':visibility' => 0,
                ':asset' => $asset,
                ':asset_name' => $asset_name,
                ':album' => $album,
                ':nbr_view_total' => $nbr_view_total,
                ':nbr_view_unique' => $nbr_view_unique,
                ':month' => $this->month
            ));
    }

    private function add_album_asset_sql_data($album, $asset)
    {
        if (!array_key_exists($album, $this->sql_data)) {
            $this->sql_data[$album] = array($asset => array());
        }
        if (!array_key_exists($asset, $this->sql_data[$album])) {
            $this->sql_data[$album][$asset] = array();
        }
    }

    private function get_user_view_file($album, $asset)
    {
        $path_file = $path_file = $this->get_user_view_file_path($album, $asset);
        if (file_exists($path_file)) {
            return explode("\n", file_get_contents($path_file));
        }
        return array();
    }

    private function add_user_view_file($album, $asset, $netid)
    {
        $path_file = $this->get_user_view_file_path($album, $asset);
        if (file_exists($path_file)) {
            $data = "\n" . $netid;
        } else {
            $data = $netid;
        }
        $dir_path = dirname($path_file);
        if (!file_exists($path_file)) {
            $allPath = array();
            $currentPath = $dir_path;
            while ($currentPath != "/") {
                $currentPath = dirname($currentPath);
                $allPath[] = $currentPath;
            }
            $allPath = array_reverse($allPath);

            foreach ($allPath as $interPath) {
                if (!is_dir($interPath)) {
                    mkdir($interPath, 0777, true);
                }
            }
        }
        file_put_contents($path_file, $data, FILE_APPEND);
    }

    private function get_user_view_file_path($album, $asset)
    {
        return $this->repos_folder . '/' . $album . '/' . $asset . '/' . 'user_view.txt';
    }
}
