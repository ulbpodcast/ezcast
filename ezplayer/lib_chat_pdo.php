<?php

/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 *            Arnaud Wijns <awijns@ulb.ac.be>
 *            Carlos Avidmadjessi
 * UI Design by Julien Di Pietrantonio
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Library used to send and retreive threads (and comments) informations between the database and the server
 * @package ezcast.ezplayer.lib.thread
 */
require_once '../commons/config.inc';
require_once '../commons/lib_database.php';

$stmts = chat_statements_get();
db_prepare($stmts);

function chat_statements_get()
{
    return array(
        'message_insert' =>
        'INSERT INTO ' . db_gettable('messages') .
        ' (message, timecode, authorId, authorFullName, creationDate, albumName, assetName) ' .
        'VALUES (:message, :timecode, :authorId, :authorFullName, :creationDate, :albumName, :assetName)',
        'messages_select_all' =>
        'SELECT * FROM ' . db_gettable('messages')
        . 'ORDER BY creationDate ASC',
        'messages_select_by_album' =>
        'SELECT * FROM ' . db_gettable('messages')
        . ' WHERE albumName like :albumName '
        . 'ORDER BY creationDate ASC',
        'messages_select_by_asset' =>
        'SELECT * FROM ' . db_gettable('messages')
        . ' WHERE albumName like :albumName AND assetName like :assetName '
        . 'ORDER BY creationDate ASC',
        'messages_select_by_date' =>
        'SELECT * FROM ' . db_gettable('messages')
        . ' WHERE albumName like :albumName'
        . ' AND assetName like :assetName'
        . ' AND creationDate >= :lastDate '
        . ' AND id > :id '
        . 'ORDER BY creationDate ASC',
    );
}

/**
 * Inserts a message into the database
 * @global type $db
 * @param type $values
 * @return boolean errorflag
 */
function message_insert($values)
{
    global $statements;
    global $db_object;
    //    foreach ($values as $row => $value) {
    //        $statements['thread_insert']->bindParam(':'.$row, $value);
    //    }
    
    $statements['message_insert']->bindParam(':message', $values['message']);
    $statements['message_insert']->bindParam(':timecode', $values['timecode']);
    $statements['message_insert']->bindParam(':authorId', $values['authorId']);
    $statements['message_insert']->bindParam(':authorFullName', $values['authorFullName']);
    $statements['message_insert']->bindParam(':creationDate', $values['creationDate']);
    $statements['message_insert']->bindParam(':albumName', $values['albumName']);
    $statements['message_insert']->bindParam(':assetName', $values['assetName']);


    $res = $statements['message_insert']->execute();
    cache_asset_chat_unset($values['albumName'], $values['assetName']);
    log_append('Create message: ', 'Create message = ' . $values['message']);

    return $res;
}

/**
 * Returns an array with all chat messages
 * @global null $db
 * @return Array
 */
function messages_select_all()
{
    global $statements;

    $statements['messages_select_all']->execute();
    return $statements['messages_select_all']->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Returns an array with all chat messages published on the asset
 * @global type $db
 * @param string $album
 * @param string $asset
 * @return array
 */
function messages_select_by_asset($album, $asset, $streaming_asset = '')
{
    global $statements;

    if ($streaming_asset == '') {
        $streaming_asset = $asset;
    }
    
    if (cache_asset_chat_isset($album, $streaming_asset)) {
        return cache_asset_chat_get($album, $streaming_asset);
    }

    $statements['messages_select_by_asset']->bindParam(':assetName', $asset);
    $statements['messages_select_by_asset']->bindParam(':albumName', $album);
    $statements['messages_select_by_asset']->execute();

    $res = $statements['messages_select_by_asset']->fetchAll(PDO::FETCH_ASSOC);

    cache_asset_chat_set($album, $streaming_asset, $res);

    return $res;
}

/**
 * Returns an array with all chat messages published on the asset since the given date
 * @global type $db
 * @param string $album
 * @param string $asset
 * @return array
 */
function messages_select_by_date($album, $asset, $date, $id = 0)
{
    global $statements;

    $statements['messages_select_by_date']->bindParam(':assetName', $asset);
    $statements['messages_select_by_date']->bindParam(':albumName', $album);
    $statements['messages_select_by_date']->bindParam(':lastDate', $date);
    $statements['messages_select_by_date']->bindParam(':id', $id);
    $statements['messages_select_by_date']->execute();

    $res = $statements['messages_select_by_date']->fetchAll(PDO::FETCH_ASSOC);

    return $res;
}

/**
 * Return x chat messages as an array
 * @global type $db
 * @param type $album
 * @return type
 */
function messages_select_by_album($album, $limit = 0)
{
    global $statements;

    $statements['messages_select_by_album']->bindParam(':albumName', $album);
    $statements['messages_select_by_album']->execute();
    $res = $statements['messages_select_by_album']->fetchAll(PDO::FETCH_ASSOC);

    if ($limit == 0) {
        return $res;
    }
    return array_slice($res, 0, $limit);
}
