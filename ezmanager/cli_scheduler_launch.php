<?php 

/**
 * @package ezcast.ezmanager.cli
 * This CLI is intended for manual use and is not used by the automatic ezcast process
 */

require_once __DIR__.'/../commons/lib_scheduling.php';
scheduler_schedule();
