<?php

/*
 * All sql updates must be added to this array
 * Format :
 * $update_list = [
 *    <source_version> => array(<new_version>, array(<sql query 1>, <sql_query 2>, ...)),
 *    (...)
 * ]
 */
$update_list = [
    "1.0.0" =>
        array("1.0.1",
            array(
                'ALTER TABLE !PREFIX!courses ADD COLUMN `course_code_public` varchar(50) NOT NULL AFTER `course_code`;',
              
                'CREATE TABLE IF NOT EXISTS `!PREFIX!stats_video_month_infos` (' .
                        '`id` int(11) NOT NULL AUTO_INCREMENT, ' .
                        '`visibility` tinyint(1) NOT NULL DEFAULT \'1\', ' .
                        '`asset` varchar(30) NOT NULL, ' .
                        '`asset_name` varchar(70) NOT NULL, ' .
                        '`album` varchar(30) NOT NULL, ' .
                        '`nbr_view_total` int(11) NOT NULL DEFAULT \'0\', ' .
                        '`nbr_view_unique` int(11) NOT NULL DEFAULT \'0\', ' .
                        '`month` varchar(7) NOT NULL, '.
                        'PRIMARY KEY (`id`), '.
                        'UNIQUE KEY(`visibility`, `asset`,`album`,`month`)'.
                    ') ENGINE=InnoDB DEFAULT CHARSET=utf8;',
                
                'CREATE TABLE IF NOT EXISTS `!PREFIX!stats_video_view` (' .
                        '`id` int(11) NOT NULL AUTO_INCREMENT, ' .
                        '`visibility` tinyint(1) NOT NULL DEFAULT \'1\', ' .
                        '`asset` varchar(30) NOT NULL, ' .
                        '`album` varchar(30) NOT NULL, ' .
                        '`type` ENUM(\'cam\', \'slide\') NOT NULL, ' .
                        '`nbr_view` int(11) NOT NULL, ' .
                        '`video_time` int(11) NOT NULL, ' .
                        'PRIMARY KEY (`id`), ' .
                        'UNIQUE KEY(`visibility`, `asset`, `album`, `type`, `video_time`)' .
                    ') ENGINE=InnoDB DEFAULT CHARSET=utf8;',
                
                'CREATE TABLE IF NOT EXISTS `!PREFIX!stats_video_infos` (' .
                        '`id` int(11) NOT NULL AUTO_INCREMENT, ' .
                        '`visibility` tinyint(1) NOT NULL DEFAULT \'1\', ' .
                        '`asset` varchar(30) NOT NULL, ' .
                        '`album` varchar(30) NOT NULL, ' .
                        '`nbr_bookmark_personal` int(11) NOT NULL, ' .
                        '`nbr_bookmark_official` int(11) NOT NULL, ' .
                        '`nbr_thread` int(11) NOT NULL, ' .
                        '`nbr_access` int(11) NOT NULL, ' .
                        'PRIMARY KEY (`id`), ' .
                        'UNIQUE KEY(`visibility`, `asset`,`album`)' .
                    ') ENGINE=InnoDB DEFAULT CHARSET=utf8;'
            )
        )
];
