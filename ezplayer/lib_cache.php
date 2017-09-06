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
 * Library for all cache functions
 * @package ezcast.ezplayer.lib.cache
 */
include_once '../commons/config.inc';


//===== C A C H E   Lv. 2 ======================================================
/**
 * Check either a thread cache exists for an album
 * @param type $albumName
 */
function cache_album_threads_isset($album)
{
    global $repository_basedir;
    $path_cache_album_threads_file = $repository_basedir."/repository/".$album."/_cache_album_threads.json";
    
    $isset = file_exists($path_cache_album_threads_file);
    
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache is set (Album threads) ? ".($isset ? 'true' : 'false').PHP_EOL, FILE_APPEND);
    
    return  $isset;
}

/**
 * Returns the content of the cache as an associcative array
 * @global type $repository_basedir
 * @param string $album
 * @param int $limite number of value needed
 * @return array
 */
function cache_album_threads_get($album, $limit)
{
    global $repository_basedir;
    $path_cache_album_threads_file = $repository_basedir."/repository/".$album."/_cache_album_threads.json";
    
    $json_data = file_get_contents($path_cache_album_threads_file);
    $threads_array = json_decode($json_data, true);
    
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache get (Album threads) : ".$album.PHP_EOL, FILE_APPEND);
    if ($limit == 0) {
        return $threads_array;
    }
    return array_slice($threads_array, 0, $limit);
}

/**
 * Unset the album cache
 * @global type $repository_basedir
 * @param type $album
 */
function cache_album_threads_unset($album)
{
    global $repository_basedir;
    $path_cache_album_threads_file = $repository_basedir."/repository/".$album."/_cache_album_threads.json";
    
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache unset (Album threads) ".PHP_EOL, FILE_APPEND);
    
    if (file_exists($path_cache_album_threads_file)) {
        unlink($path_cache_album_threads_file);
    }
}

/**
 * Replace the album's cache content
 * @global string $repository_basedir
 * @param string $album
 * @param array $threads_array
 */
function cache_album_threads_set($album, $threads_array)
{
    global $repository_basedir;
    $path_cache_album_threads_file = $repository_basedir."/repository/".$album."/_cache_album_threads.json";
    
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache set (Album threads) ".$album.PHP_EOL, FILE_APPEND);
    
    file_put_contents($path_cache_album_threads_file, json_encode($threads_array));
}

//==============================================================================
//===== C A C H E   Lv. 3 ======================================================
/**
 * Replace the asset's content
 * @global string $repository_basedir
 * @param string $album
 * @param string $asset
 * @param array $threads_array
 */
function cache_asset_threads_set($album, $asset, $threads_array)
{
    global $repository_basedir;
    if (!$threads_array) {
        return;
    }
    $path_to_threadsfile = $repository_basedir."/repository/".$album."/".$asset."/_threads.json";
    
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache set (Asset threads) ".$album." / ".$asset.PHP_EOL, FILE_APPEND);
    
    file_put_contents($path_to_threadsfile, json_encode($threads_array));
}

/**
 * Check either a thread cache exists for an asset
 * @param string $albumName
 * @param string $asset the asset name
 */
function cache_asset_threads_isset($album, $asset)
{
    global $repository_basedir;
    
    $path_to_threadsfile = $repository_basedir."/repository/".$album."/".$asset."/_threads.json";
    $isset = file_exists($path_to_threadsfile);
    
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache is set (Asset threads) ? ".($isset ? 'true' : 'false').PHP_EOL, FILE_APPEND);
    return $isset;
}

/**
 * Returns the content of the asset cache as an associcative array
 * @global string $repository_basedir
 * @param string $album
 * @param string $asset
 * @return array
 */
function cache_asset_threads_get($album, $asset)
{
    global $repository_basedir;
    
    $path_to_threadsfile = $repository_basedir."/repository/".$album."/".$asset."/_threads.json";
    $json_data = file_get_contents($path_to_threadsfile);
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache get (Asset threads) : ".$album.' / '.$asset.PHP_EOL, FILE_APPEND);
    $file_content = json_decode($json_data, true);
    if (!$file_content) {
        cache_asset_threads_unset($album, $asset);
    }
    return $file_content;
}
/**
 * Unset the asset cache
 * @global string $repository_basedir
 * @param string $album asset's album name
 * @param string $asset asset folder name
 */
function cache_asset_threads_unset($album, $asset)
{
    global $repository_basedir;
    $path_to_threadsfile = $repository_basedir."/repository/".$album."/".$asset."/_threads.json";
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache unset (Asset threads) ".PHP_EOL, FILE_APPEND);
    if (file_exists($path_to_threadsfile)) {
        unlink($path_to_threadsfile);
    }
}

/**
 * Replace the asset's chat message cache content
 * @global string $repository_basedir
 * @param string $album
 * @param string $asset
 * @param array $messages_array
 */
function cache_asset_chat_set($album, $asset, $messages_array)
{
    global $repository_basedir;
    if (!$messages_array) {
        return;
    }
    $path_to_messagesfile = $repository_basedir."/repository/".$album."/".$asset."/_chat_messages.json";
    
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache set (Asset chat) ".$album." / ".$asset.PHP_EOL, FILE_APPEND);
    
    file_put_contents($path_to_messagesfile, json_encode($messages_array));
}

/**
 * Check if a chat messages cache exists for an asset
 * @param string $albumName
 * @param string $asset the asset name
 */
function cache_asset_chat_isset($album, $asset)
{
    global $repository_basedir;
    
    $path_to_messagesfile = $repository_basedir."/repository/".$album."/".$asset."/_chat_messages.json";
    $isset = file_exists($path_to_messagesfile);
    
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache is set (Asset chat) ? ".($isset ? 'true' : 'false').PHP_EOL, FILE_APPEND);
    return $isset;
}

/**
 * Returns the content of the asset cache as an associcative array
 * @global string $repository_basedir
 * @param string $album
 * @param string $asset
 * @return array
 */
function cache_asset_chat_get($album, $asset)
{
    global $repository_basedir;
    
    $path_to_messagesfile = $repository_basedir."/repository/".$album."/".$asset."/_chat_messages.json";
    $json_data = file_get_contents($path_to_messagesfile);
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache get (Asset chat) : ".$album.' / '.$asset.PHP_EOL, FILE_APPEND);
    $file_content = json_decode($json_data, true);
    if (!$file_content) {
        cache_asset_chat_unset($album, $asset);
    }
    return $file_content;
}
/**
 * Unset the asset cache
 * @global string $repository_basedir
 * @param string $album asset's album name
 * @param string $asset asset folder name
 */
function cache_asset_chat_unset($album, $asset)
{
    global $repository_basedir;
    $path_to_messagesfile = $repository_basedir."/repository/".$album."/".$asset."/_chat_messages.json";
    // --- LOG
    file_put_contents($repository_basedir."/log/cache.log", date('Y-m-d H:i:s')." // Cache unset (Asset chat) ".PHP_EOL, FILE_APPEND);
    unlink($path_to_messagesfile);
}

//==============================================================================
