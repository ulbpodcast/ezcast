<?php
require_once(__DIR__."/../commons/config.inc");

//user Logger?
class DBUpdater 
{
    private $db_object;
    private $db_version_complete_table_name;
    CONST DB_VERSION_TABLE_NAME = 'db_version';
    
    function __construct() 
    {
        global $db_type;
        global $db_host;
        global $db_login;
        global $db_passwd;
        global $db_name;
        global $db_prefix ;

        try {
            $this->db_object = new PDO("$db_type:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_passwd);
        } catch (PDOException $e) {
            throw new Exception("Could not connect to db $db_host");
        }
        
        $this->db_version_complete_table_name = $db_prefix . self::DB_VERSION_TABLE_NAME;
    }
    
    public function get_db_version() 
    {
        $res = $this->db_object->query('SELECT version FROM '.$this->db_version_complete_table_name);
        if($res === false || $res->rowCount() == 0) {
            return false;
        }
        $version = $res->fetch(PDO::FETCH_ASSOC)['version'];
        return $version;
    }

    private function update_db_version($to_version) 
    {
        $res = $this->db_object->query('UPDATE '.$this->db_version_complete_table_name.' SET version = "'. $to_version . '"');
        if($res === false || $res->rowCount() == 0) { //at least one row should be affected
            return false;
        }
        return true;
    }

    private function apply_update($query) 
    {
        global $db_prefix ;
        
        $query_prefixed = str_replace("!PREFIX!", $db_prefix, $query);

        $res = $this->db_object->query($query_prefixed);
        if($res === false) {
            $this->log("Failed to run query: ");
            $this->log($query_prefixed);
            $this->log("Last SQL error: " . var_export($this->db_object->errorInfo(), true));
            return false;
        }
        
        return true;
    }
    
    public function auto_update($print = true) 
    {
        require_once(__DIR__.'/update_list.php');
        
        $current_db_version = $this->get_db_version($this->db_object);
        if($current_db_version === false) {
            $this->log("auto_update: Could not get current db version", $print);
            return false;
        }
        $this->log("Updating from current version $current_db_version", $print);
        $count = 0;
        foreach($update_list as $from_version => $update) {
            $target_version = $update[0];
            $queries = $update[1];
            
            if($current_db_version > $from_version) {
                //$this->log("Update from $from_version is too old, skipping", $print);
                continue;
            }
            //at this point, only updates more recent than our version are left
            //first next we should find should be for our version
            if($current_db_version != $from_version) {
                $this->log("Could not find update for our current version $current_db_version", $print);
                return false;
            }
            
            foreach($queries as $query) {
                if($this->apply_update($query)) {
                    $this->log("Successfully updated from version $from_version to version $target_version", $print);
                } else {
                    $this->log("Failed to update from version $from_version to version $target_version", $print);
                    return false; //stop immediately
                }
            }
            //all okay, update current db version
            $this->update_db_version($target_version);
            $current_db_version = $target_version;
            $count++;
        }
        if($count != 0) 
            $this->log("Done. Successfully applied $count update(s). New version: $current_db_version", $print);
        else 
            $this->log("Your database is up to date. Current version: $current_db_version", $print);
        
        return true;
    }
    
    private function log($message, $print = true)
    {
        if($print)
            echo $message . PHP_EOL;
    }
}
