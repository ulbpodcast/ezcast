<?php

abstract class Module
{
    public function __construct($database, $repos_folder)
    {
        $this->logger = new Logs();
        $this->database = $database;
        $this->repos_folder = $repos_folder;

        $this->logger->info('Module: ' . get_class($this) . ' LOAD !');
    }

    abstract public function analyse_line($date, $timestamp, $session, $ip, $netid, $level, $action, $other_info = null);

    /**
     * Call when a file have finish to be read
     */
    public function end_file()
    {
    }
}
