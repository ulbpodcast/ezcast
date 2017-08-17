<?php

$update_list = [
    "1.0.0" => 
        array("1.0.1", 
            array(
                'ALTER TABLE !PREFIX!_courses` MODIFY COLUMN `course_code_public` varchar(50) NOT NULL;',
                'CREATE TABLE IF NOT EXISTS `!PREFIX!stats_video_infos` (' .
                        '`id` int(11) NOT NULL AUTO_INCREMENT, ' .
                        '`asset` varchar(30) NOT NULL, ' .
                        '`album` varchar(30) NOT NULL, ' .
                        '`nbr_view_total` int(11) NOT NULL DEFAULT \'0\', ' .
                        '`nbr_view_unique` int(11) NOT NULL DEFAULT \'0\', ' .
                        '`month` varchar(7) NOT NULL, '.
                        'PRIMARY KEY (`id`), '.
                        'UNIQUE KEY(`asset`,`album`)'.
                    ') ENGINE=InnoDB DEFAULT CHARSET=utf8;',
                'CREATE TABLE IF NOT EXISTS `!PREFIX!stats_video_view` (' .
                        '`id` int(11) NOT NULL AUTO_INCREMENT, ' .
                        '`asset` varchar(30) NOT NULL, ' .
                        '`album` varchar(30) NOT NULL, ' .
                        '`nbr_view` int(11) NOT NULL, ' .
                        '`video_time` int(11) NOT NULL, ' .
                        '`month` varchar(7) NOT NULL, ' .
                        'PRIMARY KEY (`id`), ' .
                        'UNIQUE KEY(`asset`,`album`,`video_time`)' . 
                    ') ENGINE=InnoDB DEFAULT CHARSET=utf8;'
            )
        )
];