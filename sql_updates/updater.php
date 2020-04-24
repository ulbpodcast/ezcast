<?php

require_once(__DIR__ . '/sql_update.php');

/* Base table :
 *
 CREATE TABLE ezcast_db_version (
      `version` varchar(30) NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO ezcast_db_version VALUES ("1.0.0");
 */
$updater = new DBUpdater();
$updater->auto_update();
