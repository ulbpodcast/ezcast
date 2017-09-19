<?php
/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
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
 * This is a library through which you can manage the Media Repository Filesystem
 * Use this library whenever you can instead of going directly in directory structure
 * @package ezcast.ezplayer.lib.ezmam
 */

require_once 'config.inc';
require_once __DIR__.'/../commons/lib_error.php';
require_once 'lib_various.php';

/**
 *
 * @param [path $path of the repository (optional)]
 * @return false|path
 * @desc tells the library where the repository is. CALL THIS FUNCTION BEFORE ANYTHING ELSE!!!!
 * @desc if called without parameter, returns current repository
 */
function ezmam_repository_path($path = "")
{
    static $repository_path = false;

    if ($path == "") {
        if ($repository_path === false) {
            ezmam_last_error("1 Error: repository path not defined");
            return false;
        } else {
            return $repository_path;
        }//if $repository_path
    }//if $ath
    //if path exists then store it
    $res = is_dir($path);
    if ($res) {
        $repository_path = $path;
    } else {
        ezmam_last_error("2 Error: repository path not found: $path");
    }
    return $res;
}

/**
 *
 * @param [string $url of the repository (optional)]
 * @return false|path
 * @desc tells the library where the repository is. CALL THIS FUNCTION BEFORE ANYTHING ELSE RELATED TO RSS!!!!
 * @desc if called without parameter, returns URL to media distribution page
 */
function ezmam_video_distribution_url($url = "")
{
    static $video_distribution_url;

    if ($url == "") {
        if ($video_distribution_url === false) {
            ezmam_last_error("1 Error: video distribution URL not defined");
            return false;
        } else {
            return $video_distribution_url;
        }//if $repository_path
    }//if $ath
    //if path exists then store it
    $video_distribution_url = $url;
    return $video_distribution_url;
}

/**
 *
 * @param [string $msg error meesage (optional)]
 * @return string error message
 * @desc Store and return last error message in ezmam library
 */
function ezmam_last_error($msg = "")
{
    static $last_error = "";

    if ($msg == "") {
        return $last_error;
    } else {
        $last_error = $msg;
        return true;
    }
}

/**
 *
 * @param string $album_name  name of the album
 * @param assoc_array $metadata
 * @param string $description description for metadata
 * @return false|error_string
 */
function ezmam_album_new($album_name, $metadata)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    //create directory
    $album_path = $repository_path . "/" . $album_name;
    if (ezmam_album_exists($album_name)) {
        return "album already exists";
    }
    $res = mkdir($album_path);
    if (!$res) {
        return "could not create $album_path";
    }

    // Create a token
    $res = ezmam_album_token_create($album_name);
    if (!$res) {
        return "could not create token for $album_path";
    }

    //create the metadata
    $res = ezmam_album_metadata_set($album_name, $metadata);
    if (!$res) {
        return "could not create metadata for $album_path";
    }

    //log the operation
    log_append('album_new', $album_name);

    return $res;
}

/**
 *
 * @param string $album_name
 * @return bool
 * @desc tell if thet given album exists
 */
function ezmam_album_exists($album_name)
{
    return ezmam_asset_exists($album_name, "");
}

/**
 *
 * @param string $album
 * @return false|assoc_array
 * @desc returns metadata from the album
 */
function ezmam_album_metadata_get($album)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    if (!ezmam_album_exists($album)) {
        return false;
    }
    $album_path = $repository_path . "/" . $album;
    $assoc_metadata = metadata2assoc_array($album_path . "/_metadata.xml");
    return $assoc_metadata;
}

/**
 *
 * @param string $album
 * @param assoc_array $metadata_assoc_array
 * @return bool (true on success)
 * @desc saves the given metadata
 */
function ezmam_album_metadata_set($album, $metadata_assoc_array, $logging = true)
{
    $repository_path = ezmam_repository_path();
    // Sanity checks
    if ($repository_path === false) {
        return false;
    }
    if (!ezmam_album_exists($album)) {
        return false;
    }

    // Updating the metadata
    $album_path = $repository_path . "/" . $album;
    $metadata_xml = assoc_array2metadata($metadata_assoc_array);
    $res = file_put_contents($album_path . "/_metadata.xml", $metadata_xml);

    if (!$res) {
        return false;
    }

    // Logging the operation
    $metadata_str = '';
    if ($logging) {
        foreach ($metadata_assoc_array as $key => $val) {
            $metadata_str .= '[' . $key . ']' . $val . ' ';
        }
        log_append('album_metadata_set', 'Album: ' . $album . ', New metadata: ' . $metadata_str);
    }

    // Rebuilding the RSS
    ezmam_rss_generate($album, "high");
    ezmam_rss_generate($album, "low");
    ezmam_rss_generate($album, "ezplayer");

    return $res;
}

/**
 * Deletes an album from the repository
 * @return true|false error status
 */
function ezmam_album_delete($album_name)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }

    if (!ezmam_album_exists($album_name)) {
        ezmam_last_error("Trying to delete $album_name which doesn't exist!");
        return false;
    }

    $path = $repository_path . '/' . $album_name;

    // First we empty the directory
    $dir = opendir($path);
    if ($dir === false) {
        ezmam_last_error("Unable to open folder $path");
        return false;
    }

    while (($file = readdir($dir)) !== false) {
        if ($file != "." && $file != "..") {
            if (is_dir($path . '/' . $file)) {
                ezmam_asset_delete($album_name, $file, false);
            } else {
                unlink($path . '/' . $file);
            }
        }
    }

    // Then we delete it
    $res = rmdir($path);
    if (!$res) {
        ezmam_last_error("Unable to delete folder $path");
        return false;
    }

    // And finally we log the operation
    log_append("album_delete", "Album " . $album_name);

    return true;
}

/**
 * Deprecated.
 * @return
 * @desc NIY not implemented yet
 */
/* function ezmam_album_rename($album_org,$album_new){
  if(!ezmam_album_exists($album_orig)) {
  ezmam_last_error('ezmam_album_reame: source album does not exist!');
  return false;
  }
  if(ezmam_album_exists($album_new)) {
  ezmam_last_error('ezmam_album_rename: new name for $album_org already used');
  }

  // Sanity check: we wouldn't want people to move albums across our repository
  //str_replace("/", "_", $album_new);
  }

  /**
 * Deprecated/
 * @param <type> $album
 * @param <type> $type High/Low quality
 * @param <type> $path_of_movie
 * @desc add an asset to the RSS feeds
 */
/* function ezmam_rss_add($album,$type,$path_of_movie){

  } */

/**
 * @param string $album
 * @param string $type
 * @desc regenerates the complete RSS
 */
//TODO: Clean up!
function ezmam_rss_generate($album, $type)
{
    global $application_url;
    global $distribute_url;
    global $mailto_alert;
    $url = $distribute_url;

    if ($type != 'high' && $type != 'low' && $type != 'ezplayer') {
        ezmam_last_error('ezmam_rss_generate: only album qualities to be allowed are high and low');
        return false;
    }

    // 1) XML code creation
    $xml = ezmam_rss_new($album, $type);
    if (!$xml) {
        ezmam_last_error('ezmam_rss_generate: unable to open XML');
        return false;
    }

    $xmlh = new SimpleXMLElement($xml);
    $token = ezmam_album_token_get($album);

    if ($token === false) {
        ezmam_last_error('ezmam_rss_generate: unable to get token');
        return false;
    }

    // 2) We scan all the assets for the album
    $assets = ezmam_asset_list_metadata($album);

    foreach ($assets as $asset) {
        $metadata = $asset['metadata'];

        // Don't forget to add both videos if it was a camslide!
        if ($type == 'ezplayer') {
            add_item_to_rss(
                    $xmlh,
                $metadata['title'],
                $metadata['description'] . ' (prise de vue caméra)',
                $metadata['author'],
                get_RFC822_date($metadata['record_date']),
                '',
                ''
            );
        } else {
            if ($metadata['record_type'] == 'camslide') {

                // Camera
                $title = (isset($metadata['title'])) ? $metadata['title'] : $metadata['description'];
                $arguments = '?action=media&album=' . $album . '&asset=' . $asset['name'] . '&type=cam&quality=' . $type . '&token=' . $token;

                // An asset may exist but only contain an original (no high or low)
                // So, since we don't want our RSS feed to contain empty assets, we have to check
                // that at least one media is available
                if (ezmam_media_exists($album, $asset['name'], 'high_cam') || ezmam_media_exists($album, $asset['name'], 'low_cam')) {
                    add_item_to_rss(
                            $xmlh,
                        $title . ' (camera)',
                        $metadata['description'] . ' (prise de vue caméra)',
                        $metadata['author'],
                            //preg_replace('!([0-9]{4})\_([0-9]{2})\_([0-9]{2})\_([0-9]{2})h([0-9]{2})!', '$3/$2/$1 $4:$5', $metadata['record_date']),
                            get_RFC822_date($metadata['record_date']),
                        $url . $arguments,
                        $url . '/cam.m4v' . $arguments
                    );
                }

                // Slides
                $arguments = '?action=media&album=' . $album . '&asset=' . $asset['name'] . '&type=slide&quality=' . $type . '&token=' . $token;

                // See above
                if (ezmam_media_exists($album, $asset['name'], 'high_slide') || ezmam_media_exists($album, $asset['name'], 'low_slide')) {
                    add_item_to_rss(
                            $xmlh,
                        $title . ' (slides)',
                        $metadata['description'] . ' (slides et commentaires)',
                        $metadata['author'],
                            //preg_replace('!([0-9]{4})\_([0-9]{2})\_([0-9]{2})\_([0-9]{2})h([0-9]{2})!', '$3/$2/$1 $4:$5', $metadata['record_date']),
                            get_RFC822_date($metadata['record_date']),
                        $url . $arguments,
                        $url . '/slide.m4v' . $arguments
                    );
                }
            }

            // Only add one video otherwise
            else {
                $title = (isset($metadata['title'])) ? $metadata['title'] : $metadata['description'];
                $arguments = '?action=media&album=' . $album . '&asset=' . $asset['name'] . '&type=' . $metadata['record_type'] . '&quality=' . $type . '&token=' . $token;

                // Filter for empty assets (see above)
                if (ezmam_media_exists($album, $asset['name'], 'high_' . $metadata['record_type']) || ezmam_media_exists($album, $asset['name'], 'low_' . $metadata['record_type'])) {
                    add_item_to_rss(
                            $xmlh,
                        $title,
                        $metadata['description'],
                        $metadata['author'],
                            //preg_replace('!([0-9]{4})\_([0-9]{2})\_([0-9]{2})\_([0-9]{2})h([0-9]{2})!', '$3/$2/$1 $4:$5', $metadata['record_date']),
                            get_RFC822_date($metadata['record_date']),
                        $url . $arguments,
                        $url . '/' . $metadata['record_type'] . '.m4v' . $arguments
                    );
                }
            }
        }
    }

    // 3) We dump the result in a file
    $regfile = ezmam_repository_path() . '/' . $album . '/_rss_' . $type . '.xml';
    $res = file_put_contents($regfile, $xmlh->asXML());

    if ($res === false) {
        ezmam_last_error('ezmam_rss_generate: unable to write into XML file ' . $regfile);
        return false;
    }

    return true;
}

/**
 * Returns a XML string representing an RSS feed for an album.
 * This function should not be called as-is, call ezmam_rss_generate instead
 * @see ezmam_rss_generate
 * @param string $album_name
 * @param string $type May be "low" or "high"
 * @return string the XML code for an empty RSS feed
 */
//TODO:Clean up!
function ezmam_rss_new($album_name, $type)
{
    global $ezplayer_url;
    global $distribute_url;
    global $organization_name;
    global $mailto_alert;

    //
    // Sanity checks
    //
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    if (!ezmam_album_exists($album_name)) {
        return false;
    }
    $album_path = $repository_path . "/" . $album_name;
    $file_path = $album_path . "/$type.rss";

    //
    // Setting up information about the feed
    //
    $metadata = ezmam_album_metadata_get($album_name);

    // Quality
    $quality = 'HD';
    if ($type == 'low') {
        $quality = 'SD';
    }

    // Feed title
    $title = $metadata['name'] . ' ' . choose_title_from_metadata($metadata) . ' (' . $quality;
    if (album_is_private($album_name)) {
        $title .= ' - album privé';
    }
    $title .= ')';

    // Feed subtitle, for itunes
    $subtitle = $metadata['name'] . ' ' . choose_title_from_metadata($metadata) . ' (' . $quality . ')';

    // Album name
    $album_name = $metadata['name'];

    // Feed description
    $description = choose_title_from_metadata($metadata);

    // Path to the thumbnail image
    $thumbnail_url = $ezplayer_url . '/images/rss_logo_HD.jpg';
    if ($type == 'low') {
        $thumbnail_url = $ezplayer_url . '/images/rss_logo_SD.jpg';
    }

    // Path to the file
    $distribution_url = $distribute_url;

    //
    // And finally, generating the XML file
    //
    $xmlstr = '<?xml version="1.0" encoding="UTF-8"?><rss xmlns:itunes="http://www.itunes.com/dtds/pdcast-1.0.dtd" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0"></rss>';
    $xml = new SimpleXMLElement($xmlstr);
    $channel = $xml->addChild('channel');

    $channel->addChild('title', $title); // Feed title
    $channel->addChild('description', $description); // Feed description
    $channel->addChild('copyright', $organization_name); // Organization that broadcasts the podcast
    $channel->addChild('generator', 'EZcast feed generator'); // Script that generated the file
    $channel->addChild('lastBuildDate', date(DATE_RFC822)); // Date of the last update

    $thumbnail = $channel->addChild('image'); // All you want to know about the thumbnail image
    $thumbnail->addChild('url', $thumbnail_url);
    $thumbnail->addChild('link', $distribution_url);
    $thumbnail->addChild('title', $album_name);

    $channel->addChild('author', $organization_name, 'http://www.itunes.com/dtds/pdcast-1.0.dtd'); // Author, itunes-friendly version

    $cat = $channel->addChild('category', '', 'http://www.itunes.com/dtds/pdcast-1.0.dtd'); // Categories the feed belongs to
    $cat->addAttribute('text', 'Cours');

    $owner = $channel->addChild('owner', '', 'http://www.itunes.com/dtds/pdcast-1.0.dtd'); // Feed author, once again
    $owner->addChild('name', $organization_name, 'http://www.itunes.com/dtds/pdcast-1.0.dtd');
    $owner->addChild('email', $mailto_alert, 'http://www.itunes.com/dtds/pdcast-1.0.dtd');

    $channel->addChild('subtitle', $subtitle, 'http://www.itunes.com/dtds/pdcast-1.0.dtd');

    $image = $channel->addChild('image', '', 'http://www.itunes.com/dtds/pdcast-1.0.dtd');
    $image->addAttribute('href', $thumbnail_url);

    $atom_link = $channel->addChild('link', '', 'http://www.w3.org/2005/Atom');
    $atom_link->addAttribute('href', $file_path);
    $atom_link->addAttribute('rel', 'self');
    $atom_link->addAttribute('type', 'application/rss+xml');

    return $xml->asXML();
}

/**
 *
 * @param string $album
 * @param string $quality
 * @param bool(true) $relative return path relative to repository or absolute
 * @return string
 */
function ezmam_rss_getpath($album, $quality, $relative = "true")
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    if (!ezmam_album_exists($album)) {
        return false;
    }

    if ($relative) {
        $feed_path = $album;
    } else {
        $feed_path = $repository_path . "/" . $album . '/_rss_' . $quality . '.xml';
    }

    if (!is_file($feed_path)) {
        return false;
    }

    return $feed_path;
}

/**
 * Utility method: adds an item into the RSS feed
 * @param type $xmlh a SimpleXML object to the field
 * @param type $title
 * @param type $description
 * @param type $author
 * @param type $pubDate
 * @param type $link
 * @param type $media_link Link for iTunes (including a random .m4v file)
 * @return string The modified RSS feed
 */
function add_item_to_rss(&$xmlh, $title, $description, $author, $pubDate, $link, $media_link)
{
    $item = $xmlh->channel->addChild('item');
    $item->addChild('title', $title);
    $item->addChild('description', $description);
    //author has to be an valid email address itunes uses itunes:author with a specific namespace. Read http://stackoverflow.com/questions/2644284/add-rss-xmlns-namespace-definition-to-a-php-simplexml-document
    //$item->addChild('author',$author);

    $item->addChild('pubDate', $pubDate);
    $item->addChild('link', htmlentities($link));
    //$guid= $item->addChild('guid', uniqid());//guid should be a fixed absolute URL (if PermaLink=true which is the default) OR a unique id
    //$guid->addAttribute('isPermaLink','false');//we use a unique id so set ispermalink to false
    $item->addChild('guid', htmlentities($link));
    $item->addChild('enclosure', '');
    $item->enclosure->addAttribute('url', trim($media_link));
    //$item->enclosure->addAttribute('type', '');
    //$item->enclosure->addAttribute('length', '7292424');

    return $xmlh;
}

/**
 *
 * @param string $album_name
 * @param string $asset_name
 * @return bool
 * @desc tell if thet given asset exists
 */
function ezmam_asset_exists($album_name, $asset_name)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    $path = $repository_path . "/" . $album_name;
    if ($asset_name != "") {
        $path .= "/" . $asset_name;
    }
    $res = file_exists($path);
    return $res;
}

/**
 * counts the processed assets from a given album
 * @param type $album
 * @return int
 */
function ezmam_asset_count($album)
{
    $asset_list = ezmam_asset_list_metadata($album);
    if ($asset_list == "");
    return 0;

    $count = 0;
    foreach ($asset_list as $asset) {
        if ($asset['metadata']['status'] == 'processed') {
            ++$count;
        }
    }
    return $count;
}

function ezmam_asset_list($album)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    //check if album exists
    if (!ezmam_album_exists($album)) {
        ezmam_last_error("ezmam_asset_list: album $album does not exist");
        return false;
    }

    $asset_list = array();
    $album_path = $repository_path . "/" . $album;
    $dh = opendir($album_path);
    //$dh = scandir($album_path, SCANDIR_SORT_DESCENDING);
    if (!$dh) {
        return false;
    }
    while (($file = readdir($dh)) !== false) {
        //foreach($dh as $file) {
        if ($file[0] != '.' && $file[0] != "_") { //filter out names starting with . (including '.' and '..' )or _
            if (is_dir($album_path . "/" . $file)) {
                array_push($asset_list, $file);
            } //if its a directory add it to the list
        }
    }//end while
    return $asset_list;
}

/**
 *
 * @param string $album
 * @return array_of_assoc_array
 * @desc returns an array of all albums with details in   assoc_arrays: ['name'] & ['metadata']
 */
function ezmam_asset_list_metadata($album)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    //check if album exists
    if (!ezmam_album_exists($album)) {
        return false;
    }

    $asset_list = array();
    $idx = 0;
    $album_path = $repository_path . "/" . $album;
    //$dh=opendir($album_path);
    $dh = scandir($album_path, 1);
    if (!$dh) {
        return false;
    }
    //while (($file = readdir($dh)) !== false) {
    foreach ($dh as $file) {
        if ($file[0] != '.' && $file[0] != "_") { //filter out names starting with . (including '.' and '..' )or _
            if (is_dir($album_path . "/" . $file)) {
                //if its a directory add it to the list
                $asset = $file; //the album ref name is the directory name
                $asset_list[$idx]['name'] = $asset;
                $asset_list[$idx]['metadata'] = ezmam_asset_metadata_get($album, $asset);
                $idx+=1;
            }
        }
    }//end while
    return $asset_list;
}

/**
 *
 * @param string $album
 * @param string $asset
 * @return false|assoc_array
 * @desc returns  metadata associated with an asset. this returns an associative array with properties as key
 */
function ezmam_asset_metadata_get($album, $asset)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    if (!ezmam_album_exists($album)) {
        return false;
    }
    if (!ezmam_asset_exists($album, $asset)) {
        return false;
    }
    $asset_path = $repository_path . "/" . $album . "/" . $asset;
    $assoc_metadata = metadata2assoc_array($asset_path . "/_metadata.xml");
    return $assoc_metadata;
}

/**
 *
 * @param string $album
 * @param string $asset
 * @param string $media
 * @param assoc_array $metadata_assoc_array
 * @return bool
 * @desc saves the given metadata
 */
function ezmam_asset_metadata_set($album, $asset, $metadata_assoc_array)
{
    // Sanity checks
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    if (!ezmam_album_exists($album)) {
        return false;
    }
    if (!ezmam_asset_exists($album, $asset)) {
        return false;
    }

    // updating metadata
    $asset_path = $repository_path . "/" . $album . "/" . $asset;
    $metadata_xml = assoc_array2metadata($metadata_assoc_array);
    $res = file_put_contents($asset_path . "/_metadata.xml", $metadata_xml);

    if (!$res) {
        return false;
    }

    // Logging operation
    $metadata_str = '';
    foreach ($metadata_assoc_array as $key => $val) {
        $metadata_str .= '[' . $key . ']' . $val . ' ';
    }
    log_append('asset_metadata_set', 'Asset: ' . $asset . ', Album: ' . $album . ', New metadata: ' . $metadata_str);

    // Regenerating RSS feeds
    $res = ezmam_rss_generate($album, "high");
    if (!$res) {
        return false;
    }
    ezmam_rss_generate($album, "low");

    return $res;
}

/**
 *
 * @param string $album_name
 * @param string $asset_name
 * @return bool
 * @desc tell if thet given asset exists
 */
function ezmam_media_exists($album_name, $asset_name, $media_name)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    $res = file_exists($repository_path . "/" . $album_name . "/" . $asset_name . "/" . $media_name);
    return $res;
}

/**
 *
 * @param string $album
 * @param string $asset
 * @return array
 * $desc returns array of media matching $album and $asset
 */
function ezmam_media_list($album, $asset)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    //check if album exists
    if (!ezmam_album_exists($album)) {
        return false;
    }
    if (!ezmam_asset_exists($album, $asset)) {
        return false;
    }
    $media_list = array();
    $asset_path = $repository_path . "/" . $album . "/" . $asset;
    $dh = opendir($asset_path);
    if (!$dh) {
        return false;
    }
    while (($file = readdir($dh)) !== false) {
        if ($file[0] != '.' && $file[0] != "_") { //filter out names starting with . (including '.' and '..' )or _
            if (is_dir($asset_path . "/" . $file)) {
                array_push($media_list, $file);
            } //if its a directory add it to the list
        }
    }//end while
    return $media_list;
}

/**
 *
 * @param string $album
 * @param string $asset
 * @return array
 * @desc returns an array of all media in an asset with details in assoc_arrays: ['name'] & ['metadata']
 */
function ezmam_media_list_metadata($album, $asset)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    //check if album exists
    if (!ezmam_album_exists($album)) {
        return false;
    }
    if (!ezmam_asset_exists($album, $asset)) {
        return false;
    }
    $media_list = array();
    $asset_path = $repository_path . "/" . $album . "/" . $asset;
    $dh = opendir($asset_path);
    if (!$dh) {
        return false;
    }
    while (($file = readdir($dh)) !== false) {
        if ($file[0] != '.' && $file[0] != "_") { //filter out names starting with . (including '.' and '..' )or _
            if (is_dir($asset_path . "/" . $file)) {
                //if its a directory add it to the list
                $media = $file; //the album ref name is the directory name
                $media_list[$idx]['name'] = $media;
                $media_list[$idx]['metadata'] = ezmam_media_metadata_get($album, $asset, $media);
                $idx+=1;
            }
        }
    }//end while
    return $media_list;
}

/**
 * Returns an associative array with all the media within an asset, and their metadata.
 * Array key is the media name, value is an array with the content
 */
function ezmam_media_list_metadata_assoc($album, $asset)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    //check if album exists
    if (!ezmam_album_exists($album)) {
        return false;
    }
    if (!ezmam_asset_exists($album, $asset)) {
        return false;
    }
    $media_list = array();
    $asset_path = $repository_path . "/" . $album . "/" . $asset;
    $dh = opendir($asset_path);
    if (!$dh) {
        return false;
    }
    while (($file = readdir($dh)) !== false) {
        if ($file[0] != '.' && $file[0] != "_") { //filter out names starting with . (including '.' and '..' )or _
            if (is_dir($asset_path . "/" . $file)) {
                //if its a directory add it to the list
                $media = $file; //the album ref name is the directory name
                $media_list[$media] = ezmam_media_metadata_get($album, $asset, $media);
            }
        }
    }//end while
    return $media_list;
}

/**
 *
 * @param string $album
 * @param string $asset
 * @param string $media
 * @return false|assoc_array
 * @desc returns  metadata associated with a media. this returns an associative array with properties as key
 */
function ezmam_media_metadata_get($album, $asset, $media)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    if (!ezmam_album_exists($album)) {
        return false;
    }
    if (!ezmam_asset_exists($album, $asset)) {
        return false;
    }
    if (!ezmam_media_exists($album, $asset, $media)) {
        return false;
    }
    $media_path = $repository_path . "/" . $album . "/" . $asset . "/" . $media . "/";
    $assoc_metadata = metadata2assoc_array($media_path . "_metadata.xml");
    return $assoc_metadata;
}

/**
 *
 * @param string $album
 * @param string $asset
 * @param string $media
 * @param assoc_array $metadata_assoc_array
 * @return bool
 * @desc saves the given metadata
 */
function ezmam_media_metadata_set($album, $asset, $media, $metadata_assoc_array)
{
    // Sanity checks
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    if (!ezmam_album_exists($album)) {
        return false;
    }
    if (!ezmam_asset_exists($album, $asset)) {
        return false;
    }
    if (!ezmam_media_exists($album, $asset, $media)) {
        return false;
    }

    // Updating metadata
    $media_path = $repository_path . "/" . $album . "/" . $asset . "/" . $media;
    $metadata_xml = assoc_array2metadata($metadata_assoc_array);
    $res = file_put_contents($media_path . "/_metadata.xml", $metadata_xml);

    // Logging
    $metadata_str = '';
    foreach ($metadata_assoc_array as $key => $val) {
        $metadata_str .= '[' . $key . ']' . $val . ' ';
    }
    log_append('media_metadata_set', 'Media: ' . $media . ', Asset: ' . $asset . ', Album: ' . $album . ', New metadata: ' . $metadata_str);

    return $res;
}

/**
 *
 * @param string $album_name
 * @param string $asset_name
 * @param string $media_name
 * @param assoc_array $metadata
 * @param path $media_file_path
 * @param bool $copy=false wheter
 * @return false|error_string
 * @desc creates an asset container in the given album
 */
function ezmam_media_new($album_name, $asset_name, $media_name, $metadata, $media_file_path, $copy = false)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    //create directory
    $media_path = $repository_path . "/" . $album_name . "/" . $asset_name . "/" . $media_name;
    if (!ezmam_album_exists($album_name)) {
        ezmam_last_error('ezmam_media_new album non existant');
        return false;
    }
    if (!ezmam_asset_exists($album_name, $asset_name)) {
        ezmam_last_error('ezmam_media_new album non existant');
        return false;
    }
    if (ezmam_media_exists($album_name, $asset_name, $media_name)) {
        ezmam_last_error('ezmam_media_new media already exists');
        return false;
    }
    $res = mkdir($media_path);
    if (!$res) {
        ezmam_last_error('could not create $media_path');
        return false;
    }
    //create the metadata
    $res = ezmam_media_metadata_set($album_name, $asset_name, $media_name, $metadata);
    if (!$res) {
        ezmam_last_error("ezmam_media_new() error writing metadata for $album_name/$asset_name/$media_name");
        return false;
    }
    $filename = basename($media_file_path);
    if ($copy) {
        if (is_dir($media_file_path)) {//media can be a directory (e.g in case of a chapter list+images)
            $lines = exec("cp -r $media_file_path $media_path/$filename", $output, $return_code);
            if ($return_code) {
                ezmam_last_error("ezmam_media_new() error $return_code copying media directory $media_file_path for $album_name/$asset_name/$media_name");
                return false;
            } else {
                $res = true;
            } //returncode==0 so recursive copy went well
        } else {
            $res = copy($media_file_path, $media_path . "/" . $filename);
            if (!$res) {
                ezmam_last_error("ezmam_media_new() error copying media file/dir $media_file_path for $album_name/$asset_name/$media_name");
                return false;
            }
        }
    } else {
        $res = rename($media_file_path, $media_path . "/" . $filename);
        if (!$res) {
            ezmam_last_error("ezmam_media_new() error moving media file $media_file_path for $album_name/$asset_name/$media_name");
            return false;
        }
    }//endif $copy
    // Logging
    log_append('media_new', 'Media: ' . $media_name . ', Asset: ' . $asset_name . ', Album: ' . $album_name . ', File_path: ' . $media_file_path);

    return $res;
}

/**
 *
 * @param string $album
 * @param string $asset
 * @param string $media
 * @param bool(true) $relative return path relative to repository or absolute
 * @return string
 */
function ezmam_media_getpath($album, $asset, $media, $relative = true)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    if (!ezmam_album_exists($album)) {
        return false;
    }
    if (!ezmam_asset_exists($album, $asset)) {
        return false;
    }
    if (!ezmam_media_exists($album, $asset, $media)) {
        return false;
    }
    $media_metadata = ezmam_media_metadata_get($album, $asset, $media);

    if ($relative) {
        $media_path = $album . "/" . $asset . "/" . $media;
    } else {
        $media_path = $repository_path . "/" . $album . "/" . $asset . "/" . $media;
    }
    //check if media is a file or multifiles
    if (isset($media_metadata['disposition']) && $media_metadata['disposition'] == 'file' && isset($media_metadata['filename'])) {
        //it's a single file so return a path to the file instead of directory
        $media_path = $media_path . "/" . $media_metadata['filename'];
    }
    return $media_path;
}

/**
 * Returns a (non-urlencoded!) URL to a media (distribute file)
 * @global type $ezplayer_url
 * @param type $album
 * @param type $asset
 * @param type $media
 * @return type
 */
function ezmam_media_geturl($album, $asset, $media)
{
    global $ezplayer_url;
    global $distribute_url;

    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }
    if (!ezmam_album_exists($album)) {
        return false;
    }
    if (!ezmam_asset_exists($album, $asset)) {
        return false;
    }
    if (!ezmam_media_exists($album, $asset, $media)) {
        return false;
    }

    $media_infos = explode('_', $media);
    $type = $media_infos[1];
    $quality = $media_infos[0];
    $token = ezmam_asset_token_get($album, $asset);

    return $distribute_url . '?action=media&album=' . $album . '&asset=' . $asset . '&type=' . $type . '&quality=' . $quality . '&token=' . $token;
}

/**
 * Returns the number of times a certain media has been viewed or downloaded
 * @param type $album
 * @param type $asset
 * @param type $media
 * @return false|int The number of times the media has been viewed/downloaded
 */
function ezmam_media_viewcount_get($album, $asset, $media)
{
    // Sanity checks
    $repository_path = ezmam_repository_path();
    if ($repository_path == false) {
        ezmam_last_error("ezmam_media_get_view_count: repository_path not set");
        return false;
    }

    if (!ezmam_album_exists($album)) {
        ezmam_last_error("ezmam_media_get_view_count: album does not exist");
        return false;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        ezmam_last_error("ezmam_media_get_view_count: asset does not exist");
        return false;
    }

    /* if(!ezmam_media_exists($album, $asset, $media)) {
      ezmam_last_error("ezmam_media_get_view_count: media does not exist");
      return false;
      } */

    // Reading the view count, if the file exists.
    // Note that the _viewcount file is created when a media is seen for the first time
    // (through distribute.php). So _viewcount may not exist, if the media has never been seen.
    if (!file_exists($repository_path . '/' . $album . '/' . $asset . '/' . $media . '/_view_count')) {
        return 0;
    }

    $vc = file_get_contents($repository_path . '/' . $album . '/' . $asset . '/' . $media . '/_view_count');
    if ($vc === false) {
        $vc = 0;
    }

    return (int) $vc;
}

function ezmam_media_viewcount_increment($album, $asset, $media)
{
    // Sanity checks
    $repository_path = ezmam_repository_path();
    if ($repository_path == false) {
        ezmam_last_error("ezmam_media_get_view_count: repository_path not set");
        return false;
    }

    if (!ezmam_album_exists($album)) {
        ezmam_last_error("ezmam_media_get_view_count: album does not exist");
        return false;
    }

    if (!ezmam_asset_exists($album, $asset)) {
        ezmam_last_error("ezmam_media_get_view_count: asset does not exist");
        return false;
    }

    $viewcount_file = dirname(ezmam_media_getpath($album, $asset, $media, false)) . '/_view_count';
    /*
      $viewcount = 0;
      if(file_exists($viewcount_file)) {
      $viewcount = file_get_contents($viewcount_file);
      if(!$viewcount)
      $viewcount = 0;
      }

      ++$viewcount;
      $res = file_put_contents($viewcount_file, $viewcount, LOCK_EX);
     */

    $handle = fopen($viewcount_file, "c");
    if (flock($handle, LOCK_EX)) {
        $viewcount = file_get_contents($viewcount_file);
        if (!$viewcount) {
            $viewcount = 0;
        }
        ++$viewcount;
        $res = file_put_contents($viewcount_file, $viewcount);
        flock($handle, LOCK_UN);
    }
    fclose($handle);


    /*  if ($fl = fopen($viewcount_file)){
      if (flock($fl, LOCK_EX)){
      fseek($fl, 0);
      $viewcount = fread($fl, filesize($viewcount_file));
      if(!$viewcount)
      $viewcount = 0;
      ++$viewcount;
      fseek($fl, 0);
      ftruncate($fl, 0);
      fwrite($fl, $viewcount);
      fflush($fl);
      flock($fl, LOCK_UN);
      fclose($fl);
      }
      }
     *
     */

    if (!$res) {
        ezmam_last_error('ezmam_media_viewcount_increment: Couldn\'t update the view count for asset ' . $input['asset'] . ' of album ' . $input['album']);
        die;
    }
}

/**
 * Deletes the media from the asset
 * @param type $album
 * @param type $asset
 * @param type $media
 * @return type
 */
function ezmam_media_delete($album_name, $asset_name, $media_name)
{
    $repository_path = ezmam_repository_path();
    if ($repository_path === false) {
        return false;
    }

    if (!ezmam_album_exists($album_name)) {
        ezmam_last_error("Trying to delete $album_name which doesn't exist!");
        return false;
    }

    if (!ezmam_asset_exists($album_name, $asset_name)) {
        ezmam_last_error("Trying to delete $asset_name which doesn't exist!");
        return false;
    }

    if (!ezmam_media_exists($album_name, $asset_name, $media_name)) {
        ezmam_last_error("Trying to delete $media_name which doesn't exist!");
        return false;
    }

    $path = ezmam_repository_path() . '/' . ezmam_media_getpath($album_name, $asset_name, $media_name);

    if (is_file($path)) {
        $path = dirname($path);
    }

    // First we empty the directory
    $dir = opendir($path);
    if ($dir === false) {
        ezmam_last_error("Unable to open folder $path");
        return false;
    }

    while (($file = readdir($dir)) !== false) {
        if ($file != "." && $file != "..") {
            if (is_dir($path . '/' . $file)) {
                exec('rm -rf ' . $path . '/' . $file);
            } //chapterslide media are directories so we need to delete it recursively
            else {
                unlink($path . '/' . $file);
            } //single file media
        }
    }

    // Then we delete it
    $res = rmdir($path);
    if (!$res) {
        ezmam_last_error("Unable to delete folder $path");
        return false;
    }

    return true;
}

/**
 * @return string   new random token
 * @desc creates a random string to protect de videos
 */
function ezmam_token_generate_random()
{
    $token = "";
    for ($idx = 0; $idx < 8; $idx++) {
        $token.= chr(rand(65, 90));
    }

    return $token;
}

/**
 * Creates a _token file within $album containing the token for said album
 * @param string $album The album name
 * @return bool Error status
 */
function ezmam_album_token_create($album)
{
    if (!is_dir(ezmam_repository_path() . '/' . $album)) {
        ezmam_last_error("ezmam_album_token_file_create: album does not exist");
        return false;
    }

    $token = ezmam_token_generate_random();
    file_put_contents(ezmam_repository_path() . '/' . $album . '/_token', $token);
    return $token;
}

/**
 * Creates a _token file within $album/$asset containing the token for said asset
 * @param string $asset the asset
 * @param string $album The album name
 * @return bool Error status
 */
function ezmam_asset_token_create($album, $asset)
{
    if (!is_dir(ezmam_repository_path() . '/' . $album . '/' . $asset)) {
        ezmam_last_error("ezmam_asset_token_file_create: asset does not exist");
        return false;
    }

    $token = ezmam_token_generate_random();
    file_put_contents(ezmam_repository_path() . '/' . $album . '/' . $asset . '/_token', $token);
    return $token;
}

/**
 * Checks that the given token matches the one in the album
 * @param string $album The album we want to protect
 * @param token $token The token to verify
 * @return bool true|false
 */
function ezmam_album_token_check($album, $token)
{
    if (!file_exists(ezmam_repository_path() . '/' . $album . '/_token')) {
        ezmam_last_error('Access denied: token does not exist');
        return false;
    }

    $res = file_get_contents(ezmam_repository_path() . '/' . $album . '/_token');
    if ($res === false) {
        ezmam_last_error("Access denied: token does not exist");
        return false;
    }
    $true_token = trim($res);
    if ($true_token != $token) {
        ezmam_last_error('Access denied: incorrect token');
        return false;
    }

    return true;
}

/**
 * Returns the token of a specific album
 * @param string $album the album name
 * @return string Token
 */
function ezmam_album_token_get($album)
{
    if (!file_exists(ezmam_repository_path() . '/' . $album . '/_token')) {
        ezmam_last_error('Access denied: token does not exist');
        return false;
    }

    $res = file_get_contents(ezmam_repository_path() . '/' . $album . '/_token');
    if ($res === false) {
        ezmam_last_error("Access denied: token does not exist");
        return false;
    }
    return(trim($res));
}

function ezmam_asset_token_get($album, $asset)
{
    if (!file_exists(ezmam_repository_path() . '/' . $album . '/' . $asset . '/_token')) {
        ezmam_last_error('Access denied: token does not exist');
        return false;
    }

    $res = file_get_contents(ezmam_repository_path() . '/' . $album . '/' . $asset . '/_token');
    if ($res === false) {
        ezmam_last_error("Access denied: token does not exist");
        return false;
    }

    return trim($res);
}

/**
 * Checks that the given token matches the one for the asset
 * @param string $asset The asset we want to protect
 * @param string $album The album the asset is stored in
 * @param token $token The token to verify
 * @return bool true|false
 */
function ezmam_asset_token_check($album, $asset, $token)
{
    if (!file_exists(ezmam_repository_path() . '/' . $album . '/' . $asset . '/_token')) {
        ezmam_last_error('Access denied: token does not exist');
        return false;
    }

    $res = file_get_contents(ezmam_repository_path() . '/' . $album . '/' . $asset . '/_token');
    if ($res === false) {
        ezmam_last_error("ezmam_token_check: Access denied: token does not exist (album $album, asset $asset)");
        return false;
    }

    $true_token = trim($res);
    if ($true_token != $token) {
        //ezmam_last_error('ezmam_token_check: Access denied: incorrect token');
        return false;
    }

    return true;
}

/**
 * Resets the token of a specific album, and tokens of all the assets within
 * @param type $album
 * @return bool error status
 */
function ezmam_album_token_reset($album)
{
    // Sanity checks
    $repository_path = ezmam_repository_path();

    if ($repository_path == false) {
        ezmam_last_error("Please call ezmam_repository_path() before anything else");
        return false;
    }

    if (!ezmam_album_exists($album)) {
        ezmam_last_error("ezmam_album_token_reset: Album $album does not exist");
        return false;
    }

    $old_token = ezmam_album_token_get($album);

    // Resetting the album token
    $res = ezmam_album_token_create($album);
    if (!$res) {
        ezmam_last_error('ezmam_album_token_reset: Unable to change token');
        return false;
    }
    $assets = ezmam_asset_list($album);
    foreach ($assets as $asset) {
        ezmam_asset_token_reset($album, $asset, false);
    }

    // Resetting the RSS feed
    ezmam_rss_generate($album, "high");
    ezmam_rss_generate($album, "low");

    // Logging the operation
    log_append('album_token_reset', 'album ' . $album . ', old token: ' . $old_token . ', new token: ' . $res);

    return true;
}

/**
 * Resets a token for a specific asset
 * @param type $asset
 * @param type $album
 * @param bool $logging If set to false, the operation won't be logged
 * @return bool error status
 */
function ezmam_asset_token_reset($album, $asset, $logging = true)
{
    $repository_path = ezmam_repository_path();

    $old_token = ezmam_asset_token_get($album, $asset);
    $res = ezmam_asset_token_create($album, $asset);
    if (!$res) {
        ezmam_last_error("ezmam_asset_token_reset: unable to reset token");
        return false;
    }

    // Logging
    if ($logging) {
        log_append('asset_token_reset', 'Asset: ' . $asset . ', Album: ' . $album . ', Old token: ' . $old_token . ', New token: ' . $res);
    }
}

/* Create the folder for submit before metadata because for the progress bar in flash.
 * The progress bar need a folder created
 *
 */

function ezmam_media_submit($tmp_name)
{
    global $submit_upload_dir;
    // Sanity checks
    if (!is_dir($submit_upload_dir)) {
        ezmam_last_error($submit_upload_dir . ' is not a directory');
        return false;
    }



    // 1) create directory
    $folder_path = $submit_upload_dir . '/' . $tmp_name;

    mkdir($folder_path);

    if (!$res) {
        ezmam_last_error('ezmam_media_submit_create_metadata: could not save metadata');
        return false;
    }

    return true;
}

/**
 * Adds the metadata for the incoming submitted media into upload queue.
 * This functino does *not* add the media file itself.
 * @param string $tmp_name The directory name where the file will be stored in the queue
 * @param assoc_array $metadata Metadata as used by cli_mam_insert.php
 */
function ezmam_media_submit_create_metadata($tmp_name, $metadata)
{
    global $submit_upload_dir;

    // Sanity checks
    if (!is_dir($submit_upload_dir)) {
        ezmam_last_error($submit_upload_dir . ' is not a directory');
        return false;
    }

    if (!$metadata) {
        ezmam_last_error('not metadata provided');
        return false;
    }

    // 1) create directory
    $folder_path = $submit_upload_dir . '/' . $tmp_name;

    echo mkdir($folder_path);

    // 2) put metadata into xml file
    $res = assoc_array2metadata_file($metadata, $folder_path . '/_metadata.xml');
    if (!$res) {
        ezmam_last_error('ezmam_media_submit_create_metadata: could not save metadata');
        return false;
    }

    return true;
}

/**
 *
 * @param path $meta_path
 * @return assoc_array|false
 * @desc open a metadatafile (xml 1 level) and return all properties and values in an associative array
 */
function metadata2assoc_array($meta_path)
{
    @ $xml = simplexml_load_file($meta_path);
    if ($xml === false) {
        return false;
    }
    $assoc_array = array();
    foreach ($xml as $key => $value) {
        $assoc_array[$key] = (string) $value;
    }
    return $assoc_array;
}

/**
 *
 * @param <type> $assoc_array
 * @return <xml_string>
 * @desc takes an assoc array and transform it in a xml metadata string (which )
 */
function assoc_array2metadata($assoc_array)
{
    $xmlstr = "<?xml version='1.0' standalone='yes'?>\n<metadata>\n</metadata>\n";
    $xml = new SimpleXMLElement($xmlstr);
    foreach ($assoc_array as $key => $value) {
        $xml->addChild($key, htmlspecialchars($value));
    }
    $xml_txt = $xml->asXML();
    return $xml_txt;
}

/**
 * takes an assoc array, transform it in a xml metadata string and saves it to a file.
 * @param assoc_array $assoc_array
 * @param string $file_path
 * @return bool
 *
 */
function assoc_array2metadata_file($assoc_array, $file_path)
{
    //fwrite(fopen('./'.time().'.dump_input', 'w'), print_r($assoc_array, true));
    $xmlstr = "<?xml version='1.0' standalone='yes'?>\n<metadata>\n</metadata>\n";
    $xml = new SimpleXMLElement($xmlstr);
    foreach ($assoc_array as $key => $value) {
        $xml->addChild($key, htmlspecialchars($value));
    }
    $xml_txt = $xml->asXML();
    $res = file_put_contents($file_path, $xml_txt, LOCK_EX);
    //did we write all the characters
    if ($res != strlen($xml_txt)) {
        return false;
    } //no

    return true;
}

function ezmam_album_allow_anonymous($album)
{
    global $enable_anon_access_control;
    if (!$enable_anon_access_control) {
        return false;
    }
    
    $meta = ezmam_album_metadata_get($album);
    return isset($meta['anon_access']) && $meta['anon_access'] == 'true';
}
