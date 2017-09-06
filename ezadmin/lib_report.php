<?php

class Report
{

    // Constant
    const EZCAST_FOLDER = "/var/lib/ezcast/";
    const REPOSITOR_FOLDER = 'repository';
    const TRACE_FOLDER = 'ezplayer/ezplayer_traces';
    
    private static $IGNORED_COURSES = array('TEST', 'TEST-AW', 'APR-POD', 'DEMO');
    private static $IGNORED_FILES = array('.', '..', '.gitignore');

    const META_FILE_NAME = '_metadata.xml';
    
    // Cache
    private $allRepositoryXML;
    private $allTrace;
    
    // Param
    private $param_start_date;
    private $param_end_date;
    
    private $param_general = true;
    private $param_ezplayer = true;
    
    
    // Result
    private $list_all_author = array();
    private $list_all_submit_author = array();
    private $list_all_record_author = array();
    
    private $list_all_cours = array();
    private $list_all_cours_submit = array();
    private $list_all_cours_record = array();
    
    private $count_total_asset = 0;
    private $count_submit_asset = 0;
    private $count_record_asset = 0;
    
    private $date_list_author = array();
    private $date_list_submit_author = array();
    private $date_list_record_author = array();
    
    private $date_list_cours = array();
    private $date_list_cours_submit = array();
    private $date_list_cours_record = array();
    
    private $date_count_asset = 0;
    private $date_count_submit_asset = 0;
    private $date_count_record_asset = 0;
    
    private $date_classroom_record_time;
    
    private $nbr_total_user = -1;
    
    // Result EZPlayer
    private $ezplayer_total_thread = 0;
    private $ezplayer_list_cours_thread = array();
    private $ezplayer_total_comment = 0;
    private $ezplayer_list_cours_comment = array();
    private $ezplayer_total_bookmark = 0;
    private $ezplayer_total_offi_bookmark = 0;
    private $ezplayer_total_pers_bookmark = 0;
    private $ezplayer_list_user_offi_bookmark = array();
    private $ezplayer_list_user_pers_bookmark = array();
    
    private $ezplayer_date_total_thread = 0;
    
    private $ezplayer_date_list_user_login = array();
    private $ezplayer_date_list_ip_login = array();
    private $ezplayer_date_list_user_system = array();
    private $ezplayer_date_list_user_os = array();
    private $ezplayer_date_list_user_browser = array();
    private $ezplayer_date_list_album = array();
    private $ezplayer_date_list_album_click = array();
    private $ezplayer_date_asset = array();
    private $ezplayer_date_unique_asset = array();
    private $ezplayer_asset_view_date = array();
    
    private $ezplayer_date_cours_thread = array();
    private $ezplayer_date_nbr_comment = 0;
    private $ezplayer_date_cours_comment = array();
    private $ezplayer_date_total_bookmark = 0;
    private $ezplayer_date_pers_bookmark = 0;
    private $ezplayer_date_user_pers_bookmark = array();
    private $ezplayer_date_cours_pers_bookmark = array();
    private $ezplayer_date_offi_bookmark = 0;
    private $ezplayer_date_user_offi_bookmark = array();
    
    
    public function __construct($start_date, $end_date, $general = true, $ezplayer = true)
    {
        set_time_limit(0);
        
        $this->allRepositoryXML = array();
        $this->allTrace = array();
        
        $this->param_start_date = $start_date;
        $this->param_end_date = $end_date;
        
        $this->param_general = $general;
        $this->param_ezplayer = $ezplayer;
        
        $this->date_classroom_record_time = array(
            'SUBMIT' => array(
                    'nbr' => 0,
                    'time' => 0),
            'CLASSROOM' => array(
                    'nbr' => 0,
                    'time' => 0)
            );
        
       
        $this->repository_get_all();
        if ($this->param_ezplayer) {
            $this->ezplayer_trace_get_all();
            $this->ezplayer_calcul_user_nbr();
        }
        
        $this->utils_sortAllList();
    }
    
    
    private function repository_get_all()
    {
        $listFile = array();
        $this->repository_get_xml(self::EZCAST_FOLDER.self::REPOSITOR_FOLDER, $listFile);

        foreach ($listFile as $filePath) {
            $xml = simplexml_load_file($filePath);

            $output_array = array();
            preg_match(
                "/".preg_quote(self::EZCAST_FOLDER.self::REPOSITOR_FOLDER, '/')."\/(([\w-_]*)\-[^\-^\/]*)\/(\w*)\/_metadata.xml/",
                    $filePath,
                $output_array
            );
            $xml->cours = $output_array[2];
            $xml->album = $output_array[1];
            $xml->asset = $output_array[3].'_'.$xml->cours;
            
            if ($this->param_general) {
                $this->calcul_general_infos(
                    $xml,
                    $this->count_total_asset,
                    $this->count_submit_asset,
                    $this->count_record_asset,
                    $this->list_all_author,
                    $this->list_all_submit_author,
                    $this->list_all_record_author,
                    $this->list_all_cours,
                    $this->list_all_cours_submit,
                    $this->list_all_cours_record
                );
            }

            $nbrDate = $this->utils_date_to_number($xml->record_date);
            if ($this->param_start_date <= $nbrDate && $nbrDate <= $this->param_end_date) {
                $this->allRepositoryXML[] = $xml;
                $this->calcul_general_infos(
                    $xml,
                    $this->date_count_asset,
                    $this->date_count_submit_asset,
                    $this->date_count_record_asset,
                    $this->date_list_author,
                    $this->date_list_submit_author,
                    $this->date_list_record_author,
                    $this->date_list_cours,
                    $this->date_list_cours_submit,
                    $this->date_list_cours_record,
                    $this->date_classroom_record_time
                );
            }
        }
    }

    private function calcul_general_infos(
        &$asset,
        &$countAsset,
        &$countSubmitAsset,
        &$countClassroomAsset,
        &$listAuthor,
        &$submitAuthor,
        &$recordAuthor,
        &$listCours,
        &$listCoursSubmit,
        &$listCoursRecord,
        &$classroomRecordTime = -1
    ) {
        $author = (String) $asset->author;
        $origin = (String) $asset->origin;

        $cours = (String) $asset->cours;
        if (in_array($cours, self::$IGNORED_COURSES)) {
            return;
        }
        
        // ASSET
        ++$countAsset;
        if ($origin == 'SUBMIT') {
            ++$countSubmitAsset;
        } else {
            ++$countClassroomAsset;
        }

        // Total Author
        array_increment_or_init($listAuthor, $author);

        // Submit author
        if ($origin == 'SUBMIT') {
            array_increment_or_init($submitAuthor, $author);
        } else {
            array_increment_or_init($recordAuthor, $author);
        }

        // Cours
        array_increment_or_init($listCours, $cours);

        if ($origin == 'SUBMIT') {
            array_increment_or_init($listCoursSubmit, $cours);
        } else {
            array_increment_or_init($listCoursRecord, $cours);
        }

        if ($classroomRecordTime != -1 && $asset->duration > 0) {
            $origin = (String) $origin;
            
            if (!array_key_exists($origin, $classroomRecordTime)) {
                $classroomRecordTime[$origin] = array('nbr' => 0, 'time' => 0);
            }
            ++$classroomRecordTime[$origin]['nbr'];
            $classroomRecordTime[$origin]['time'] += $asset->duration;


            if ($origin != "SUBMIT") {
                if (!array_key_exists('CLASSROOM', $classroomRecordTime)) {
                    $classroomRecordTime['CLASSROOM'] = array('nbr' => 0, 'time' => 0);
                }
                ++$classroomRecordTime['CLASSROOM']['nbr'];
                $classroomRecordTime['CLASSROOM']['time'] += $asset->duration;
            }
        }
    }

    
    private function repository_get_xml($folder, &$res = array(), $rec = 0)
    {
        if ($rec > 2) {
            return;
        }
        foreach (scandir($folder) as $file) {
            if (!in_array($file, self::$IGNORED_FILES)) {
                if (is_dir($folder.'/'.$file)) {
                    ++$rec;
                    $this->repository_get_xml($folder.'/'.$file, $res, $rec);
                    --$rec;
                } elseif ($file == self::META_FILE_NAME && $rec == 2) {
                    $res[] = $folder.'/'.$file;
                }
            }
        }
    }
    

    /**
     * Calcul the number of user
     */
    private function ezplayer_calcul_user_nbr()
    {
        $res = scandir(self::EZCAST_FOLDER.'ezplayer/users');
        $this->nbr_total_user = count(array_diff($res, self::$IGNORED_FILES));
    }


    private function ezplayer_trace_get_all()
    {
        // $listAction = array();
        $allFile = array();
        $this->ezplayer_trace_get_file(self::EZCAST_FOLDER.'/'.self::TRACE_FOLDER, $allFile);
        foreach ($allFile as $file) {
            $traceFile = fopen($file, "r") or die("Unable to open file!");

            // Output one line until end-of-file
            while (!feof($traceFile)) {
                $this->ezplayer_read_one_trace($traceFile);
                //$allTrace[] = $newEntryTrace;
            }
            fclose($traceFile);
        }
    }

         
    private function ezplayer_trace_get_file($folder, &$res = array())
    {
        $vide = null;

        foreach (scandir($folder) as $file) {
            if (!is_dir($folder.'/'.$file) && !in_array($file, self::$IGNORED_FILES) &&
                    preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}.trace/", $file, $vide) == 1) {
                $res[] = $folder.'/'.$file;
            }
        }
    }
    
    private function ezplayer_read_one_trace(&$traceFile)
    {
        // Split line
        $traceInfo = explode('|', fgets($traceFile));
        if (empty($traceInfo) || (count($traceInfo) == 1 && $traceInfo[0] == null) ||
                count($traceInfo) < 5) {
            return;
        }
        
        // Default data
        $newEntryTrace = array(
                'date' => trim($traceInfo[0]),
                'session_id' => trim($traceInfo[1]),
                'ip' => trim($traceInfo[2]),
                'user' => trim($traceInfo[3]),
                'lvl' => trim($traceInfo[4])
            );

        $newEntryTrace['infos'] = $traceInfo; // DEBUG
        
        $isInDate = false;
        $nbrDate = $this->utils_date_to_number_alt($newEntryTrace['date']);
        if ($this->param_start_date <= $nbrDate && $nbrDate <= $this->param_end_date) {
            $isInDate = true;
        
            // If no general data
        } elseif (!$this->param_general) {
            return;
        }

        if (count($traceInfo) >= 6) {
            $newEntryTrace['action'] = trim($traceInfo[5]);
            $this->ezplayer_treat_trace_action($traceInfo, $newEntryTrace, $isInDate);
        }
    }
    
    private function ezplayer_treat_trace_action(&$traceInfo, &$newEntryTrace, $isInDate)
    {
        switch ($newEntryTrace['action']) {
            case 'asset_bookmark_add':
                $this->trace_info_bookmark_add($traceInfo, $newEntryTrace, $isInDate);
                break;


            case 'thread_add':
                $this->trace_info_thread_add($traceInfo, $newEntryTrace, $isInDate);
                break;

            case 'comment_add':
                $this->trace_info_comment_add($traceInfo, $newEntryTrace, $isInDate);
                break;

            case 'login_as_anonymous':
            case 'login':
                if ($isInDate) {
                    $this->trace_info_login($traceInfo, $newEntryTrace);
                }
                break;
                
            case 'view_album_assets':
                if ($isInDate) {
                    $this->trace_info_view_album_assets($traceInfo, $newEntryTrace);
                }
                break;
                
            case 'view_asset_details':
                if ($isInDate) {
                    $this->trace_info_view_asset_details($traceInfo, $newEntryTrace);
                }
                break;
                
            default:
                return;

        }
    }

    private function trace_info_bookmark_add(&$traceInfo, &$newEntryTrace, $isInDate)
    {
        $output_array = array();
        preg_match("/^([\w-_]*)-(priv|pub)$/", trim($traceInfo[6]), $output_array);

        $newEntryTrace['album'] = trim($traceInfo[6]);
        $newEntryTrace = array_merge(
            $newEntryTrace,
                $this->utils_decode_album_date($newEntryTrace['album'], trim($traceInfo[7]))
        );
        $newEntryTrace['timecode'] = trim($traceInfo[8]);
        $newEntryTrace['target'] = trim($traceInfo[9]); // (personal|official)
        //    $newEntryTrace['type'] = trim($traceInfo[10]); // (cam|slide)
        //    $newEntryTrace['title'] = trim($traceInfo[11]);
        //    $newEntryTrace['description'] = trim($traceInfo[12]);
        //    $newEntryTrace['keywords'] = trim($traceInfo[13]);
        //    $newEntryTrace['bookmark_lvl'] = trim($traceInfo[14]);

        ++$this->ezplayer_total_bookmark;
        if ($isInDate) {
            ++$this->ezplayer_date_total_bookmark;
        }
        $user = $newEntryTrace['user'];

        if ($newEntryTrace['target'] == 'official') {
            ++$this->ezplayer_total_offi_bookmark;
            array_increment_or_init($this->ezplayer_list_user_offi_bookmark, $user);
            if ($isInDate) {
                ++$this->ezplayer_date_offi_bookmark;
                array_increment_or_init($this->ezplayer_date_user_offi_bookmark, $user);
            }
        } else {
            ++$this->ezplayer_total_pers_bookmark;
            array_increment_or_init($this->ezplayer_list_user_pers_bookmark, $user);
            
            if ($isInDate) {
                ++$this->ezplayer_date_pers_bookmark;
                array_increment_or_init($this->ezplayer_date_user_pers_bookmark, $user);
                array_increment_or_init($this->ezplayer_date_cours_pers_bookmark, $newEntryTrace['cours']);
            }
        }
    }

    private function trace_info_thread_add(&$traceInfo, &$newEntryTrace, &$isInDate)
    {
        ++$this->ezplayer_total_thread;
        if ($isInDate) {
            ++$this->ezplayer_date_total_thread;
        }
        

        // '3', 'thread_add', $thread_album, $thread_asset, $thread_timecode, $thread_title, $thread_visibility
        $newEntryTrace['album'] = trim($traceInfo[6]);
        $newEntryTrace = array_merge(
            $newEntryTrace,
                $this->utils_decode_album_date($newEntryTrace['album'], trim($traceInfo[7]))
        );
        $newEntryTrace['timecode'] = trim($traceInfo[8]);
        $newEntryTrace['title'] = trim($traceInfo[9]);
        $newEntryTrace['visibility'] = trim($traceInfo[10]);

        $cours = $newEntryTrace['cours'];
        array_increment_or_init($this->ezplayer_list_cours_thread, $cours);
        if ($isInDate) {
            array_increment_or_init($this->ezplayer_date_cours_thread, $cours);
        }
    }

    private function trace_info_comment_add(&$traceInfo, &$newEntryTrace, &$isInDate)
    {
        ++$this->ezplayer_total_comment;

        // '3', 'comment_add', $album, $asset, $comment_thread)
        $newEntryTrace['album'] = trim($traceInfo[6]);
        $newEntryTrace = array_merge(
            $newEntryTrace,
                $this->utils_decode_album_date($newEntryTrace['album'], trim($traceInfo[7]))
        );
        $newEntryTrace['thread'] = trim($traceInfo[8]);

        $cours = $newEntryTrace['cours'];
        array_increment_or_init($this->ezplayer_list_cours_comment, $cours);
        if ($isInDate) {
            array_increment_or_init($this->ezplayer_date_cours_comment, $cours);
            ++$this->ezplayer_date_nbr_comment;
        }
    }

    private function trace_info_login(&$traceInfo, &$newEntryTrace)
    {
        $user = $newEntryTrace['user'];

        $newEntryTrace['browser_name'] = $traceInfo[6];
        $newEntryTrace['browser_version'] = $traceInfo[7];
        $newEntryTrace['user_os'] = $traceInfo[8];
        
        array_increment_or_init(
        
            $this->ezplayer_date_list_user_system,
                $newEntryTrace['browser_name']
        
        );
        
        array_increment_or_init(
        
            $this->ezplayer_date_list_user_os,
                $newEntryTrace['user_os']
        
        );

        $browse = $newEntryTrace['browser_name'].' | '.$newEntryTrace['user_os'];
        array_increment_or_init(
            $this->ezplayer_date_list_user_browser,
                $browse
        );
        
        if ($user != 'nologin') {
            array_increment_or_init($this->ezplayer_date_list_user_login, $user);
        } else {
            array_increment_or_init($this->ezplayer_date_list_ip_login, $newEntryTrace['ip']);
        }
    }
    
    private function trace_info_view_album_assets(&$traceInfo, &$newEntryTrace)
    {
        $newEntryTrace['album'] = $traceInfo[6];
        array_increment_or_init($this->ezplayer_date_list_album, $newEntryTrace['album']);
    }
    
    private function trace_info_view_asset_details($traceInfo, $newEntryTrace)
    {
        //  6       7         8
        // album, asset, record type (cam|slide|camslide), permissions (view official | add personal), origin
        $newEntryTrace['album'] = $traceInfo[6];
        $newEntryTrace = array_merge(
            $newEntryTrace,
                $this->utils_decode_album_date($newEntryTrace['album'], trim($traceInfo[7]))
        );
        $newEntryTrace['camslide'] = $traceInfo[8];
        
        array_increment_or_init($this->ezplayer_date_list_album_click, $newEntryTrace['album']);
        $this->ezplayer_date_asset[] = $newEntryTrace['asset'];
        array_increment_or_init_static(
            $this->ezplayer_asset_view_date,
                strtotime(substr($newEntryTrace['date'], 0, 10)).'000'
        );
    }
    
    /////////////////// UTILITIES ///////////////////

    /**
     * Transform an album name with the date to an asset and a cours
     *
     * @param String $album name
     * @param String $date of the asset
     */
    private function utils_decode_album_date($album, $date)
    {
        $output_array = array();
        preg_match("/^([\w-_]*)-(priv|pub)$/", trim($album), $output_array);

        $cours = $output_array[1];
        $asset = trim($date).'_'.$cours;
        return array('cours' => $cours, 'asset' => $asset);
    }


    /**
     * Convert date to a number (remove space and - _ / ...)
     *
     * @param String $date who must be convert
     * @return number of this date
     */
    private function utils_date_to_number($date)
    {
        $nbrDate = preg_replace(
            "/([0-9]{4})_([0-9]{2})_([0-9]{2})_[0-9]{2}h[0-9]{2}/",
                    "$1$2$3",
            $date
        );

        return str_replace(' ', '', $nbrDate);
    }
    
    private function utils_date_to_number_alt($date)
    {
        $nbrDate = preg_replace(
            "/([0-9]{4})-([0-9]{2})-([0-9]{2})-[0-9]{2}:[0-9]{2}:[0-9]{2}/",
                    "$1$2$3",
            $date
        );
        
        return str_replace(' ', '', $nbrDate);
    }
    
    private function utils_sortAllList()
    {
        arsort($this->list_all_author);
        arsort($this->list_all_submit_author);
        arsort($this->list_all_record_author);

        arsort($this->list_all_cours);
        arsort($this->list_all_cours_submit);
        arsort($this->list_all_cours_record);

        arsort($this->date_list_author);
        arsort($this->date_list_submit_author);
        arsort($this->date_list_record_author);

        arsort($this->date_list_cours);
        arsort($this->date_list_cours_submit);
        arsort($this->date_list_cours_record);
        arsort($this->date_classroom_record_time);

        arsort($this->ezplayer_list_cours_thread);

        arsort($this->ezplayer_list_cours_comment);
        arsort($this->ezplayer_list_user_offi_bookmark);
        arsort($this->ezplayer_list_user_pers_bookmark);

        arsort($this->ezplayer_date_list_user_login);
        arsort($this->ezplayer_date_list_user_system);
        arsort($this->ezplayer_date_list_user_os);
        arsort($this->ezplayer_date_list_user_browser);
        arsort($this->ezplayer_date_list_album);
        arsort($this->ezplayer_date_list_album_click);
        
        $this->ezplayer_date_unique_asset = array_count_values($this->ezplayer_date_asset);
        arsort($this->ezplayer_date_unique_asset);
        
        arsort($this->ezplayer_date_cours_comment);
        arsort($this->ezplayer_date_cours_pers_bookmark);
        arsort($this->ezplayer_date_user_offi_bookmark);
    }
    
    
    //////////////// GETTER ////////////////////
    
    public function get_list_all_author()
    {
        return $this->list_all_author;
    }
    
    public function get_nbr_list_all_author()
    {
        return count($this->get_list_all_author());
    }
    
    public function get_list_all_submit_author()
    {
        return $this->list_all_submit_author;
    }
    
    public function get_nbr_list_all_submit_author()
    {
        return count($this->get_list_all_submit_author());
    }
    
    public function get_list_all_record_author()
    {
        return $this->list_all_record_author;
    }
    
    public function get_nbr_list_all_record_author()
    {
        return count($this->get_list_all_record_author());
    }
    
    public function get_list_all_cours()
    {
        return $this->list_all_cours;
    }
    
    public function get_nbr_list_all_cours()
    {
        return count($this->list_all_cours);
    }
    
    public function get_list_all_cours_submit()
    {
        return $this->list_all_cours_submit;
    }
    
    public function get_nbr_list_all_cours_submit()
    {
        return count($this->list_all_cours_submit);
    }
    
    public function get_list_all_cours_record()
    {
        return $this->list_all_cours_record;
    }
    
    public function get_nbr_list_all_cours_record()
    {
        return count($this->list_all_cours_record);
    }

    public function get_count_total_asset()
    {
        return $this->count_total_asset;
    }

    public function get_count_submit_asset()
    {
        return $this->count_submit_asset;
    }

    public function get_count_record_asset()
    {
        return $this->count_record_asset;
    }

    public function get_date_list_author()
    {
        return $this->date_list_author;
    }
    
    public function get_nbr_date_list_author()
    {
        return count($this->date_list_author);
    }

    public function get_date_list_submit_author()
    {
        return $this->date_list_submit_author;
    }
    
    public function get_nbr_date_list_submit_author()
    {
        return count($this->date_list_submit_author);
    }

    public function get_date_list_record_author()
    {
        return $this->date_list_record_author;
    }
    
    public function get_nbr_date_list_record_author()
    {
        return count($this->date_list_record_author);
    }

    public function get_date_list_cours()
    {
        return $this->date_list_cours;
    }
    
    public function get_nbr_date_list_cours()
    {
        return count($this->date_list_cours);
    }

    public function get_date_list_cours_submit()
    {
        return $this->date_list_cours_submit;
    }
    
    public function get_nbr_date_list_cours_submit()
    {
        return count($this->date_list_cours_submit);
    }

    public function get_date_list_cours_record()
    {
        return $this->date_list_cours_record;
    }
    
    public function get_nbr_date_list_cours_record()
    {
        return count($this->date_list_cours_record);
    }

    public function get_date_count_asset()
    {
        return $this->date_count_asset;
    }

    public function get_date_count_submit_asset()
    {
        return $this->date_count_submit_asset;
    }

    public function get_date_count_record_asset()
    {
        return $this->date_count_record_asset;
    }

    public function get_date_classroom_record_time()
    {
        return $this->date_classroom_record_time;
    }

    public function get_nbr_total_user()
    {
        return $this->nbr_total_user;
    }

    public function get_ezplayer_total_thread()
    {
        return $this->ezplayer_total_thread;
    }

    public function get_ezplayer_list_cours_thread()
    {
        return $this->ezplayer_list_cours_thread;
    }
    
    public function get_ezplayer_nbr_list_cours_thread()
    {
        return count($this->ezplayer_list_cours_thread);
    }

    public function get_ezplayer_total_comment()
    {
        return $this->ezplayer_total_comment;
    }

    public function get_ezplayer_list_cours_comment()
    {
        return $this->ezplayer_list_cours_comment;
    }
    
    public function get_ezplayer_nbr_list_cours_comment()
    {
        return count($this->ezplayer_list_cours_comment);
    }

    public function get_ezplayer_total_bookmark()
    {
        return $this->ezplayer_total_bookmark;
    }

    public function get_ezplayer_total_offi_bookmark()
    {
        return $this->ezplayer_total_offi_bookmark;
    }

    public function get_ezplayer_total_pers_bookmark()
    {
        return $this->ezplayer_total_pers_bookmark;
    }

    public function get_ezplayer_list_user_offi_bookmark()
    {
        return $this->ezplayer_list_user_offi_bookmark;
    }
    
    public function get_ezplayer_nbr_list_user_offi_bookmark()
    {
        return count($this->ezplayer_list_user_offi_bookmark);
    }

    public function get_ezplayer_list_user_pers_bookmark()
    {
        return $this->ezplayer_list_user_pers_bookmark;
    }
    
    public function get_ezplayer_nbr_list_user_pers_bookmark()
    {
        return count($this->ezplayer_list_user_pers_bookmark);
    }

    public function get_ezplayer_date_total_thread()
    {
        return $this->ezplayer_date_total_thread;
    }
    
    public function get_ezplayer_date_list_user_login()
    {
        return $this->ezplayer_date_list_user_login;
    }
    
    public function get_ezplayer_nbr_date_list_user_login()
    {
        return count($this->ezplayer_date_list_user_login);
    }
    
    public function get_ezplayer_date_list_ip_login()
    {
        return $this->ezplayer_date_list_ip_login;
    }
    
    public function get_ezplayer_nbr_date_list_ip_login()
    {
        return count($this->ezplayer_date_list_ip_login);
    }

    public function get_ezplayer_date_list_user_system()
    {
        return $this->ezplayer_date_list_user_system;
    }

    public function get_ezplayer_date_list_user_os()
    {
        return $this->ezplayer_date_list_user_os;
    }

    public function get_ezplayer_date_list_user_browser()
    {
        return $this->ezplayer_date_list_user_browser;
    }
    
    public function get_ezplayer_date_list_album()
    {
        return $this->ezplayer_date_list_album;
    }
    
    public function get_ezplayer_nbr_date_list_album()
    {
        return count($this->ezplayer_date_list_album);
    }
    
    public function get_ezplayer_date_list_album_click()
    {
        return $this->ezplayer_date_list_album_click;
    }
    
    public function get_ezplayer_nbr_date_list_album_click()
    {
        return count($this->ezplayer_date_list_album_click);
    }
    
    public function get_ezplayer_asset_view_date()
    {
        return $this->ezplayer_asset_view_date;
    }
    
    public function get_ezplayer_date_unique_asset()
    {
        return $this->ezplayer_date_unique_asset;
    }
    
    public function get_ezplayer_nbr_date_unique_asset()
    {
        return count($this->ezplayer_date_unique_asset);
    }
    
    public function get_ezplayer_nbr_date_asset()
    {
        return count($this->ezplayer_date_asset);
    }
    
    public function get_ezplayer_date_cours_thread()
    {
        return $this->ezplayer_date_cours_thread;
    }
    
    public function get_ezplayer_nbr_date_cours_thread()
    {
        return count($this->ezplayer_date_cours_thread);
    }
    
    public function get_ezplayer_date_nbr_comment()
    {
        return $this->ezplayer_date_nbr_comment;
    }
    
    public function get_ezplayer_date_cours_comment()
    {
        return $this->ezplayer_date_cours_comment;
    }
    
    public function get_ezplayer_nbr_date_cours_comment()
    {
        return count($this->ezplayer_date_cours_comment);
    }
    
    public function get_ezplayer_date_total_bookmark()
    {
        return $this->ezplayer_date_total_bookmark;
    }
    
    public function get_ezplayer_date_pers_bookmark()
    {
        return $this->ezplayer_date_pers_bookmark;
    }
    
    public function get_ezplayer_nbr_date_user_pers_bookmark()
    {
        return count($this->ezplayer_date_user_pers_bookmark);
    }
    
    public function get_ezplayer_date_cours_pers_bookmark()
    {
        return $this->ezplayer_date_cours_pers_bookmark;
    }
    
    public function get_ezplayer_date_offi_bookmark()
    {
        return $this->ezplayer_date_offi_bookmark;
    }
    
    public function get_ezplayer_nbr_date_user_offi_bookmark()
    {
        return count($this->ezplayer_date_user_offi_bookmark);
    }
    
    public function get_ezplayer_date_user_offi_bookmark()
    {
        return $this->ezplayer_date_user_offi_bookmark;
    }
}
