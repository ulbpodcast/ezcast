<?php

// /!\ Check WHAT STILL NEEDS TO BE DONE below, this is not ready for production usage.

require_once __DIR__."/lib_various.php";
require_once __DIR__."/config.inc";

// Start ExternalStreamDaemon if not already running
function ensure_external_stream_daemon_is_running($video_root_dir, $asset_token)
{
    if (ExternalStreamDaemon::is_running($asset_token)) {
        return;
    }
   
    global $logger;
    $logger->log(EventType::MANAGER_EXTERNAL_STREAM, LogLevel::NOTICE, "Started external stream deamon for asset token $asset_token with video_root_dir $video_root_dir", array(__FUNCTION__));
    
    // Start daemon in background
    $log_folder = ExternalStreamDaemon::get_temp_folder($asset_token);
    $command = PHP_BINARY . ' ' .__DIR__."/cli_external_stream_daemon.php $video_root_dir $asset_token &> $log_folder/sync.log &";
    file_put_contents("$log_folder/sync_command", $command . PHP_EOL);
    system($command);
}

/**
 *
 * *** Description
 * This class handle the syncing of stream video files from the ezmanager server to a remote distribution server.
 * This system is divided in two parts :
 *  - The actual files syncing, handled here and triggered in ezmanager - streaming_content_add()
 *  - The actual redirection of the users, handled in ezplayer - asset_streaming_player_update
 *
 * *** Usage
 * - All local configuration is done in config.inc
 * - The remote server must have crossdomain enabled. With apache, this may be done by adding a crossdomain.xml file in the web space root folder (Google for more details)
 * - The local server must have an auto login with ssh access to the remote server
 * - You may use cli_external_stream_daemon to start the file syncing process manually for debugging/testing purpose
 * -
 *
 * *** Additional usage notes
 * This class in meant to be run in a separate process by starting cli_external_stream_daemon in background. For now, only one instance is meant to be run at a time.
 * You can check the state or interact with this object by calling the static functions such as is_running(), is_ready(), stop().
 * The daemon stops automatically after TIMEOUT_LENGHT
 * Requires ssh2 extension to run
 *
 * *** WHAT STILL NEEDS TO BE DONE
 * - the redirect currently only support "high" stream quality. See create_m3u8_external() function.
 * - vm call stubs (ip, location de la clé, user -> fourni par le cloud_vm_start)
 * - "stop" is not called for now and process will stop after 12 hours (hard timeout)
 * - When activating redirect, EZPlayer in browser currently wont ask for a new master live.m3u8 and the switch is not automatical for client already connected
 *
 * You can already use this class by using the cli_external_stream_daemon to sync files, and enable redirect only in config.inc.
 *
 */

class ExternalStreamDaemon
{
    
   //lock file to be placed at stream root dir to prevent the daemon to copy files from it (content does not matter, only file existence is checked)
   const TIMEOUT_LENGHT = 43200; // in seconds, 12H
 
   public $ssh_user;
    public $ssh_address;
    public $ssh_port;
    public $ssh_remote_root_path;
    public $local_root_path;
    public $asset_token;
    public $stop_file;
    public $start_time;
    public $ssh_connection;
    public $ssh_sftp_connection;
    
    /* Constructor. Multiple instances currently not supported.
     * @param string $video_root_dir Source files directory
     */
    public function __construct($video_root_dir, $asset_token)
    {
        global $streaming_video_alternate_server_user;
        global $streaming_video_alternate_server_address;
        global $streaming_video_alternate_server_document_root;
       
        $this->local_root_path = $video_root_dir . '/';
        $this->asset_token = $asset_token;
        $this->start_time = time();
               
        $this->ssh_user = $streaming_video_alternate_server_user;
        $this->ssh_address = $streaming_video_alternate_server_address;
        $this->ssh_port = 22;
        $this->ssh_remote_root_path = $streaming_video_alternate_server_document_root . '/' . $asset_token;
        $this->ssh_connection = null;
        $this->ssh_sftp_connection = null;
       
        // extra sanity checks
        $temp_folder = $this->get_temp_folder($this->asset_token);
        if (!is_writable(dirname($temp_folder))) {
            trigger_error('ExternalStreamDaemon:: temp folder is not writable: ' . $temp_folder, E_USER_ERROR);
        }
        if ($this->asset_token == "") {
            trigger_error('ExternalStreamDaemon:: no token given', E_USER_ERROR);
        }
        if ($this->local_root_path == "") {
            trigger_error('ExternalStreamDaemon:: no video path given', E_USER_ERROR);
        }
    }

    public static function get_temp_folder($asset_token)
    {
        global $repository_basedir;
        $temp_folder = $repository_basedir . '/external_stream/' . $asset_token . '/';
        return $temp_folder;
    }

    public static function get_pid_file_path($asset_token)
    {
        return self::get_temp_folder($asset_token) . '/pid';
    }

    private static function get_lock_file_path($asset_token)
    {
        return self::get_temp_folder($asset_token) . '/lock';
    }

    private static function get_ready_file_path($asset_token)
    {
        return self::get_temp_folder($asset_token) . '/ready';
    }

    private static function get_stop_file_path($asset_token)
    {
        return self::get_temp_folder($asset_token) . '/stop';
    }

    // return true if ExternalStreamDaemon is currently running
    public static function is_running($asset_token)
    {
        return is_process_running(get_pid_from_file(self::get_pid_file_path($asset_token)));
    }
 
    // return true if we've done at least one complete sync and redirect can be enabled
    public static function is_ready($asset_token)
    {
        $ready_file = self::get_ready_file_path($asset_token);
        return file_exists($ready_file) && self::is_running($asset_token);
    }
   
    // create stop marker. Daemon will stop at the end of the current sync operation.
    public static function stop($asset_token)
    {
        $stop_file = self::get_stop_file_path($asset_token);
        if (!file_exists(dirname($stop_file))) {
            mkdir(dirname($stop_file), 0777, true);
        }
        file_put_contents($stop_file, "1"); //content does not matter
    }
   
    // write the pid of the hosting process (this is used by is_running).
    private function write_PID()
    {
        $pid_file = self::get_pid_file_path($this->asset_token);
        if (!file_exists(dirname($pid_file))) {
            mkdir(dirname($pid_file), 0777, true);
        }
        file_put_contents($pid_file, getmypid());
    }
   
    // pause syncing
    public static function lock($asset_token)
    {
        $lock_file = self::get_lock_file_path($asset_token);
        if (!file_exists(dirname($lock_file))) {
            mkdir(dirname($lock_file), 0777, true);
        }
        file_put_contents($lock_file, "1"); //content does not matter
    }
   
    // resume syncing if paused
    public static function unlock($asset_token)
    {
        $lock_file = self::get_lock_file_path($asset_token);
        if (file_exists($lock_file)) {
            unlink($lock_file);
        }
    }
   
    // is syncing currently paused
    public static function is_locked($asset_token)
    {
        $lock_file = self::get_lock_file_path($asset_token);
        return file_exists($lock_file);
    }
   
    /* See is_ready()
     * @param bool $ready
     * */
    private function set_ready($ready)
    {
        $ready_file = self::get_ready_file_path($this->asset_token);
        if ($ready == true) {
            file_put_contents($ready_file, "1");
        } //content does not matter
        elseif (file_exists($ready_file)) {
            unlink($ready_file);
        }
    }
   
    // true if stop marker is present
    private function must_stop()
    {
        $stop_file = self::get_stop_file_path($this->asset_token);
        return file_exists($stop_file);
    }
   
    // delete stop marker
    private function delete_stop_file()
    {
        $stop_file = self::get_stop_file_path($this->asset_token);
        if (file_exists($stop_file)) {
            unlink($stop_file);
        }
    }
   
    // true if daemon was started more than TIMEOUT_LENGHT seconds ago
    private function is_in_timeout()
    {
        return $this->start_time + self::TIMEOUT_LENGHT < time();
    }
   
    private function sync_files()
    {
        $ssh_result = self::refresh_ssh_connection();
        if ($ssh_result === false) {
            trigger_error('Failed to establish ssh connection', E_USER_WARNING);
            return false;
        }

        $prepare = self::sync_file_prepare_m3u8();
        if (!$prepare) {
            trigger_error('Failed to prepare m3u8 files', E_USER_WARNING);
            return false;
        }

        $local_files = self::get_local_files($this->local_root_path, ".ts");
        if ($local_files === false) {
            trigger_error('Failed to get local files', E_USER_WARNING);
            return false;
        }

        $remote_files = self::get_remote_files($this->ssh_remote_root_path, ".ts");
        if ($remote_files === false) {
            trigger_error('Failed to get remote files', E_USER_WARNING);
            return false;
        }

        $new_files = self::list_new_files($local_files, $remote_files);
        if (empty($new_files)) {
            return true;
        }
       
        $ok = self::send_video_files($new_files);
        $ok = self::sync_file_send_m3u8() && $ok;
        if (!$ok) {
            trigger_error('sync_files failed', E_USER_WARNING);
        }
           
        return $ok;
    }
    
    //return recursive file list with relative paths, from given root
    //return false on failure
    private function get_local_files($path, $contain = "")
    {
        $files = array();
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $root_path_len = strlen($path);
        foreach ($objects as $name) {
            //echo "$name\n";
            if ($contain == "" || strpos($name, $contain) !== false) { //if $contain is set, only select files containing $contain
                //hacky way to make this path relative
                $name = substr($name, $root_path_len);
                array_push($files, $name);
            }
        }
        /*
        echo PHP_EOL."get_local_files dir $path: " . PHP_EOL;
        var_dump($files);
        echo PHP_EOL;*/
        return $files;
    }
    
    // return recursive file list with relative paths, from given remote root
    // return false on failure
    private function get_remote_files($root_remote_path, $contain = "")
    {
        //create folder if needed
        ssh2_exec($this->ssh_connection, "mkdir -p $root_remote_path");
        $ssh2_remote_path = 'ssh2.sftp://' . intval($this->ssh_sftp_connection) . $root_remote_path;
        $files = scandir($ssh2_remote_path);
        if ($files === false) {
            trigger_error("get_remote_files: failed to scandir $ssh2_remote_path", E_USER_WARNING);
            return array();
        }
        $results = array();
        foreach ($files as $key => $path) {
            if (!is_dir($ssh2_remote_path.DIRECTORY_SEPARATOR.$path)) {
                if ($contain == "" || strpos($path, $contain) !== false) { //if $contain is set, only select files containing $contain
                    $results[] = $path;
                }
            } elseif ($path != "." && $path != "..") {
                $more_results = self::get_remote_files($root_remote_path.'/'.$path, $contain);
                foreach ($more_results as &$more_result) {
                    $more_result = $path .'/'. $more_result;
                }
                $results = array_merge($results, $more_results);
            }
        }
        return $results;
    }
    
    //list files present in local_files but not in remote_files
    private function list_new_files($local_files, $remote_files)
    {
        $new_files = array();
        foreach ($local_files as $local_file) {
            if (in_array($local_file, $remote_files)) {
                continue;
            }
            array_push($new_files, $local_file);
        }
        return $new_files;
    }
    
    //send given files with path relative to local root path to remote ssh_remote_root_path
    private function send_video_files($local_files_relatives)
    {
        $success = true;
        foreach ($local_files_relatives as $relative_path) {
            $absolute_file_path = realpath($this->local_root_path .'/'. $relative_path);
            if ($absolute_file_path === false) {
                trigger_error("send_video_files: Failed to find file $relative_path in ".$this->local_root_path, E_USER_WARNING);
                $success = false;
            } else {
                $target_path = $this->ssh_remote_root_path . '/' . $relative_path;
                $dir_path = dirname($target_path);
                $mkdir_success = ssh2_exec($this->ssh_connection, "mkdir -p $dir_path");
                if (!$mkdir_success) {
                    trigger_error("send_video_files: Failed to create remote folder $dir_path", E_USER_WARNING);
                    $success = false;
                }
                echo "Sending $relative_path...".PHP_EOL;
                $success = $mkdir_success && ssh2_scp_send($this->ssh_connection, $absolute_file_path, $this->ssh_remote_root_path . '/' . $relative_path) && $success;
            }
        }
        
        return $success;
    }
    
    private function sync_file_send_m3u8()
    {
        $temp_folder = self::get_temp_folder($this->asset_token) . '/m3u8/';
        $files = self::get_local_files($temp_folder, ".m3u8");
        if ($files === false) {
            return false;
        }

        $success = true;
        foreach ($files as $relative_path) {
            $absolute_file_path = realpath($temp_folder . $relative_path);
            if ($absolute_file_path === false) {
                trigger_error("sync_file_send_m3u8: Failed to find file $relative_path in ".$this->local_root_path, E_USER_WARNING);
                $success = false;
            } else {
                echo "Sending $relative_path...".PHP_EOL;
                $success = ssh2_scp_send($this->ssh_connection, $absolute_file_path, $this->ssh_remote_root_path . '/' . $relative_path) && $success;
            }
        }
        
        return $success;
    }
    
    //prepare m3u8 files. We need to copy them first so that we're sure they havent been modified with a new .ts while we were copying them.
    private function sync_file_prepare_m3u8()
    {
        $temp_folder = self::get_temp_folder($this->asset_token) . '/m3u8/';
        
        //remove temporary files from last sync
        if (is_dir($temp_folder)) {
            $files = glob($temp_folder); // get all file names
            foreach ($files as $file) { // iterate files
              if (is_file($file)) {
                  unlink($file);
              } // delete file
            }
        }

        //get m3u8 files
        $files = array();
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->local_root_path));
        $root_path_len = strlen($this->local_root_path);
        foreach ($objects as $name) {
            //echo "$name\n";
            if (strpos($name, ".m3u8") !== false) {
                //hacky way to make this path relative
                $name = substr($name, $root_path_len);
                array_push($files, $name);
            }
        }
        
        //copy them to temp folder
        $ok = true;
        foreach ($files as $file) {
            $target_file = $temp_folder.$file;
            //create target dir if needed
            $result_val = 0;
            $target_dir = dirname($target_file);
            system("mkdir -p $target_dir", $result_val); //todo: prefer using php function instead of system function calls
            if ($result_val !== 0) {
                trigger_error("sync_file_prepare_m3u8: Failed to create directory $target_dir", E_USER_WARNING);
                return false;
            }
            $ok = copy($this->local_root_path.DIRECTORY_SEPARATOR.$file, $temp_folder.$file) && $ok;
            //echo "Copy ".$this->local_root_path.DIRECTORY_SEPARATOR.$file .' to '.$temp_folder.$file . PHP_EOL;
        }
        return $ok;
    }
   
    //create or recreate ssh connection if needed
    //return result
    private function refresh_ssh_connection()
    {
        global $streaming_video_alternate_server_keyfile_private;
        global $streaming_video_alternate_server_keyfile_pub;
        global $streaming_video_alternate_server_keyfile_password;

        //test connexion if already existing
        if ($this->ssh_connection !== null) {
            $result = ssh2_exec($this->ssh_connection, "pwd"); //dummy command
            if ($result !== false) {
                return true;
            } //all okay
        }

        $ok = $this->ssh_connection = ssh2_connect($this->ssh_address, $this->ssh_port);
        if (!$ok) {
            trigger_error("refresh_ssh_connection: Failed to connect to server ".$this->ssh_address.':'.$this->ssh_port, E_USER_WARNING);
            return false;
        }
        $ok = ssh2_auth_pubkey_file($this->ssh_connection, $this->ssh_user, $streaming_video_alternate_server_keyfile_pub, $streaming_video_alternate_server_keyfile_private, $streaming_video_alternate_server_keyfile_password);
        if (!$ok) {
            trigger_error("refresh_ssh_connection: Failed to auth user ". $this->ssh_user .". using keys: pub $streaming_video_alternate_server_keyfile_pub / priv $streaming_video_alternate_server_keyfile_private", E_USER_WARNING);
            return false;
        }
        
        $this->ssh_sftp_connection = ssh2_sftp($this->ssh_connection);
        
        echo "Opened ssh connection with handle :" . $this->ssh_connection . PHP_EOL;
    }
   
    // main loop, sync files until told to stop (with stop() function)
    public function run()
    {
        $this->write_PID();
        // make sure stop and ready files from previous run do not exist anymore
        $this->delete_stop_file();
        $this->set_ready(false);
       
        // first sync
        $ok = $this->sync_files();
        if ($ok) {
            echo "First sync: Okay";
        } else {
            echo "First sync: Failure!";
        }
        echo PHP_EOL;
       
        $this->set_ready(true);
       
        while (true) {
            if (self::is_locked($this->asset_token)) { // do nothing and wait if daemon is paused
                sleep(1);
                continue;
            }
           
            $this->sync_files();
            sleep(1);
          
            // stop conditions
            if ($this->must_stop() || $this->is_in_timeout()) {
                break;
            }
        }
       
        // not necessary but let's clean after ourselves
        $this->delete_stop_file();
        $this->set_ready(false);
        unset($this->ssh_connection);
    }
}
