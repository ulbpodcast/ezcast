<?php
/*---------------------------------
Create table for sso user
---------------------------------*/

require_once __DIR__.'/config.inc';

require_once __DIR__.'/lib_database.php';
global $db_object;

try{

  db_prepare();
  
  $query='CREATE TABLE IF NOT EXISTS `ezcast_sso_users` (
    `user_ID` varchar(50) NOT NULL,
    `surname` varchar(255) DEFAULT NULL,
    `forename` varchar(255) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `first_time` datetime NOT NULL,
    `last_time` datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
  $db_object->exec($query);
  
  $query='ALTER TABLE `ezcast_sso_users`
    ADD PRIMARY KEY (`user_ID`);';
  $db_object->exec($query);
  
}
catch (Exception $e)
{
  die('Error in add/update sso_user:'.$e->getMessage());
}


