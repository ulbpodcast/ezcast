<?php

/**
 * Count all bookmarks (personal and official)
 * and all access of an asset
 */

class Asset_infos extends Module {

    private $saved_data = array();


    function analyse_line($date, $timestamp, $session, $ip, $netid, $level, $action, $other_info = NULL) {
        // album | asset | timecode | target (personal|official) | type | title | descr | keywords | bookmark_lvl
        if($action == "asset_bookmark_add") {
            $album = trim($other_info[0]);
            $asset = trim($other_info[1]);
            $timecode = trim($other_info[2]);
            $target = trim($other_info[3]);
            $type = trim($other_info[4]);
            $title = trim($other_info[5]);
            $descr = trim($other_info[6]);
            $keywords = trim($other_info[7]);
            $bookmark_lvl = trim($other_info[8]);

            if(!array_key_exists($album, $this->saved_data)) {
                $this->saved_data[$album] = array();
            }

            if(!array_key_exists($asset, $this->saved_data[$album])) {
                $this->saved_data[$album][$asset] = array();
            }

            if(!array_key_exists('bookmark', $this->saved_data[$album][$asset])) {
                $this->saved_data[$album][$asset]['bookmark'] = array();
            }

            if(!array_key_exists($target, $this->saved_data[$album][$asset])) {
                if(in_array($target, array('personal', 'official'))) {
                    $this->saved_data[$album][$asset]['bookmark'][$target] = 0;
                } else {
                    $this->logger->warn("Target type unknow ! ('" . $target . "') For " . 
                        $album . ' - ' . $asset);
                }
            }

            $this->saved_data[$album][$asset]['bookmark'][$target]++;

        } else if($action == "view_asset_details") {
            // album, asset, record type (cam|slide|camslide), permissions (view official | add personal), origin (from ezplayer | from external link)

            $album = trim($other_info[0]);
            $asset = trim($other_info[1]);
            $record_type = trim($other_info[2]);
            $permissions = trim($other_info[3]);
            $origin = trim($other_info[4]);

            if(!array_key_exists($album, $this->saved_data)) {
                $this->saved_data[$album] = array();
            }

            if(!array_key_exists($asset, $this->saved_data[$album])) {
                $this->saved_data[$album][$asset] = array();
            }

            if(!array_key_exists('access', $this->saved_data[$album][$asset])) {
                $this->saved_data[$album][$asset]['access'] = 0;
            }

            $this->saved_data[$album][$asset]['access']++;

        }
    }

    function end_file() {
        foreach ($this->saved_data as $album => $album_data) {
            foreach ($album_data as $asset => $asset_data) {
                $nbr_bookmark_personal = 0;
                $nbr_bookmark_official = 0;
                $nbr_access = 0;

                if(isset($asset_data['bookmark'])) {
                    foreach ($asset_data['bookmark'] as $target => $value) {
                        if($target == 'personal') {
                            $nbr_bookmark_personal = $value;
                        } else if($target == 'official') {
                            $nbr_bookmark_official = $value;
                        }
                    }
                }
                if(isset($asset_data['access'])) {
                    $nbr_access = $asset_data['access'];
                }

                $this->save_to_sql($album, $asset, $nbr_access, $nbr_bookmark_personal, $nbr_bookmark_official);
            }
        }
    }

    function save_to_sql($album, $asset, $nbr_access, $nbr_bookmark_personal, $nbr_bookmark_official) {
        $this->logger->debug('[asset_infos] save sql: album:' . $album . ' | asset: ' . $asset . 
            ' | nbr_access: ' . $nbr_access . ' | nbr_bookmark_personal: ' . $nbr_bookmark_personal . 
            ' | nbr_bookmark_official: ' . $nbr_bookmark_official);

        $db = $this->database->get_database_object();
        $query = $db->prepare('INSERT INTO ' . $this->database->get_table('stats_video_infos') . ' ' .
                    '(asset, album, nbr_access, nbr_bookmark_personal, nbr_bookmark_official) ' .
                    'VALUES(:asset, :album, :nbr_access, :nbr_bookmark_personal, :nbr_bookmark_official) '.
                'ON DUPLICATE KEY UPDATE ' .
                    'nbr_access = :nbr_access, ' .
                    'nbr_bookmark_personal = :nbr_bookmark_personal, ' . 
                    'nbr_bookmark_official = :nbr_bookmark_official;');
        $query->execute(array(
                ':asset' => $asset,
                ':album' => $album,
                ':nbr_access' => $nbr_access,
                ':nbr_bookmark_personal' => $nbr_bookmark_personal,
                ':nbr_bookmark_official' => $nbr_bookmark_official
            ));
    }

}