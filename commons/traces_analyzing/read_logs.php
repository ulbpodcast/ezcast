<?php

//////////////////// CONFIG ////////////////////
date_default_timezone_set('Europe/Brussels');

// Load config
require_once __DIR__.'/../config.inc';

// Default param
$all_logs = false;
$modules_folder = __DIR__.'/modules';
$trace_folder = $ezplayer_trace_path;
$repos_folder = $repository_basedir . "/repository/";

class Logs
{
    public function info($message)
    {
        echo '[INFO] ' . $message . PHP_EOL;
    }

    public function warn($message)
    {
        echo '[WARN] ' . $message . PHP_EOL;
    }

    public function err($message)
    {
        echo '[ERROR] ' . $message . PHP_EOL;
    }
       
    public function debug($message)
    {
        echo '[DEBUG] ' . $message . PHP_EOL;
    }
}

class Database
{
    public function __construct($host, $dbname,$port, $login, $passwd, $prefix = "")
    {
        $this->db = new PDO("mysql:host=$host;dbname=$dbname;port=$port;charset=utf8", $login, $passwd);
        $this->prefix = $prefix;
    }

    public function get_database_object()
    {
        return $this->db;
    }

    public function get_table($name)
    {
        return $this->prefix . $name;
    }
}


class Read_Logs
{
    const FILE_ALREADY_CHECK_PATH = 'checked_file.txt';

    public function __construct($database, $repos_folder, $folder_traces = '.', $folder_module = 'modules', $all_logs = false)
    {
        $this->logger = new Logs();
        $this->database = $database;

        $this->folder_traces = $folder_traces;
        $this->repos_folder = $repos_folder;
        $this->folder_module = $folder_module;

        $this->all_modules = array();
        $this->module_load();

        $this->file_already_check = $this->get_file_already_check();
        if ($all_logs) {
            $this->traces_read_all();
        } else {
            $this->traces_read_yeasterday();
        }
    }

    public function module_require_file()
    {
        require_once $this->folder_module . DIRECTORY_SEPARATOR . 'Module.php';
    }

    public function module_load()
    {
        $this->module_require_file();
        $modules = glob($this->folder_module . DIRECTORY_SEPARATOR . '*_module.php');
        foreach ($modules as $module) {
            include_once $module;
            $module_name = preg_replace("/_module.php/", "", basename($module));

            try {
                $this->all_modules[] = new $module_name($this->database, $this->repos_folder);
            } catch (Exception $e) {
                $this->logger->warn('Module ' . $module_name . ' could not be load !');
                var_dump($e->getMessage());
            }
        }
    }

    public function traces_read_all()
    {
        $traces_path = $this->folder_traces . DIRECTORY_SEPARATOR . '*.trace';
        $traces = glob($traces_path);
        foreach ($traces as $file) {
            $this->traces_read($file);
        }
    }

    public function traces_read_yeasterday()
    {
        $date = date('Y-m-d', strtotime("yesterday"));
        $file_path = $this->folder_traces . DIRECTORY_SEPARATOR . $date . '.trace';
        $this->traces_read($file_path);
    }

    public function traces_read($filename)
    {
        if (in_array(basename($filename), $this->file_already_check)) {
            $this->logger->info('File ' . $filename . ' already analysed');
            return;
        }

        try {
            $file = fopen($filename, 'r');
        } catch (Exception $e) {
            $this->logger->err('Error with file ' . $filename);
            var_dump($e->getMessage());
        }

        if (!$file) {
            $this->logger->err('Could not read file ' . $filename);
            return;
        }
        $this->logger->info('=== Read file: ' . $filename . ' ===');

        $line_error = 0;
        $line_count = 0;
        while (($line = fgets($file)) !== false) {
            ++$line_count;

            $infos = explode('|', $line);
            if (count($infos) < 6) {
                $this->logger->warn('Could not read line ' . $line_count . PHP_EOL . "\t " . $line);
                ++$line_error;
                continue;
            }

            $date = trim($infos[0]);
            $date = preg_replace("/([0-9]{4}-[0-9]{2}-[0-9]{2})-([0-9]{2}:[0-9]{2}:[0-9]{2})/", "$1 $2", $date);
            $timestamp = strtotime($date);
            $session = trim($infos[1]);
            $ip = trim($infos[2]);
            $netid = trim($infos[3]);
            $level = trim($infos[4]);
            $action = trim($infos[5]);
            $other_info = array_slice($infos, 6);

            foreach ($this->all_modules as $module) {
                $module->analyse_line($date, $timestamp, $session, $ip, $netid, $level, $action, $other_info);
            }
        }

        foreach ($this->all_modules as $module) {
            $module->end_file();
        }

        $this->logger->info($line_count . ' lines in ' . $filename);
        if ($line_error > 0) {
            $this->logger->warn($line_error . ' line' . ($line_error > 1 ? 's' : '') . ' with an error !');
        }

        $this->add_file_already_check($filename);
    }

    private function get_file_already_check()
    {
        if (file_exists(self::FILE_ALREADY_CHECK_PATH)) {
            return explode("\n", file_get_contents(self::FILE_ALREADY_CHECK_PATH));
        }
        return array();
    }

    private function add_file_already_check($filepath)
    {
        file_put_contents(self::FILE_ALREADY_CHECK_PATH, basename($filepath) . "\n", FILE_APPEND);
    }
}


/////////////////////////////////////

function help_view()
{
    echo '-=- Read Logs -=-' . PHP_EOL;
    echo 'Usage: php read_logs.php [param]' . PHP_EOL;
    echo "\t-help,--h\t\tView this help" . PHP_EOL;
    echo "\t-all\t\t\tRead all logs" . PHP_EOL;
    echo "\t-module <folder>\tDefine module folder" . PHP_EOL;
    echo "\t-trace <folder>\t\tDefine trace folder" . PHP_EOL;
}

for ($i=1; $i < count($argv); ++$i) {
    switch ($argv[$i]) {
        case '--h':
        case '-h':
        case '--help':
        case '-help':
            help_view();
            return;
            break;

        case '-all':
            $all_logs = true;
            break;

        case '-module':
            if (isset($argv[$i+1])) {
                $modules_folder = $argv[++$i];
            } else {
                echo 'Module folder is not define !' . PHP_EOL;
                help_view();
                return;
            }
            break;

        case '-trace':
            if (isset($argv[$i+1])) {
                $trace_folder = $argv[++$i];
            } else {
                echo 'Trace folder is not define !' . PHP_EOL;
                help_view();
                return;
            }
            break;
        
        default:
            echo 'Command: ' . $argv[$i] . ' is not valide !' . PHP_EOL;
            help_view();
            return;
    }
}


try {
    $database = new Database($db_host, $db_name,$db_port, $db_login, $db_passwd, $db_prefix);
} catch (PDOException $e) {
    echo PHP_EOL . PHP_EOL . "ERROR DATABASE" . PHP_EOL;
    print_r($e->getMessage());
    echo PHP_EOL;
    return;
}
echo "Connect to database\n";

new Read_Logs($database, $repos_folder, $trace_folder, $modules_folder, $all_logs);
