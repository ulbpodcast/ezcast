<?php

/*
 * EZCAST EZmanager
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	   Arnaud Wijns <awijns@ulb.ac.be>
 *         Antoine Dewilde
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
 * @package ezcast.ezmanager.cli
 */
require_once dirname(__FILE__) . '/config.inc';
require_once dirname(__FILE__) . '/lib_ezmam.php';
require_once dirname(__FILE__) . '/lib_various.php';

//always initialize repository path before using ezmam library
ezmam_repository_path($repository_path);

/*
 * This program creates a new asset from an existing asset:
 * calls renderer to process the movies
 */

if ($argc != 2) {
    echo "usage: " . $argv[0] . ' <xml_file>
        Where <xml_file> should point to an xml file containing the information for the new asset to be processed
        (We expect an absolute path to the file)
        xml_file.xml example:
        <?xml version="1.0" standalone="yes"?>
            <rendering>
                <src_album> origin album </src_album>
                <src_asset> origin asset </src_asset>
                <cutlist> (optional)
                    <start> start time in seconds </start>
                    <pause> pause in seconds </pause> (optional)
                    <resume> resuming time in seconds </resume> (optional)
                    ...
                    <stop> stop in seconds </stop> (optional)
                </cutlist>
            <slide_bias> difference in seconds between cam and slide </slide_bias> (optional)
            <new_title> The new title for the asset </new_title> (optional)
            <new_author> The new author for the asset </new_author> (optional)
            <new_date> The new date for the asset </new_date> (optional)
            <new_album> The new album (mnemo) for the asset </new_album> (optional)
            <add_title> FlyingTitle | false </add_title> (optional)
            <title_fields> (optional)
		<mnemo> true | false </mnemo> 
		<album> true | false </album>
		<title> true | false </title>
		<author> true | false </author>
		<date> true | false </date>
            </title_fields>
            <intro_movie> Semeur | false </intro_movie> (optional)
            <new_type> cam | slide | camslide </new_type> (optional)
        </rendering>
';
    die;
}



$rendering_file = $argv[1]; //first (and only) parameter in command line
if (!file_exists($rendering_file)) {
    //no file found
    echo 'rendering xml file not found';
    die;
}
// retrieves information for the new asset to be rendered
$rendering_array = xml_file2assoc_array($rendering_file, 'rendering');

if (!ezmam_asset_exists($rendering_array['src_album'], $rendering_array['src_asset'])) {
    echo 'Source asset not found';
    die;
}

// gets information about the original asset
$origin_asset_meta = ezmam_asset_metadata_get($rendering_array['src_album'], $rendering_array['src_asset']);
// gets information about the original titling and jingle
$origin_to_process_file = $render_finished_upload_dir . '/' . $rendering_array['src_asset'] . '_' . $rendering_array['src_album'] . '_intro_title_movie/toprocess.xml';
if (file_exists($origin_to_process_file)) {
    $to_process_array = xml_file2assoc_array($origin_to_process_file, 'metadata');
    $rendering_array['previous_title'] = ($to_process_array['add_title'] === 'FlyingTitle');
    $rendering_array['previous_intro'] = ($to_process_array['intro_movie'] === 'Semeur');
} else {
    $to_process_array = array(
        'intro_movie' => 'Semeur',
        'add_title' => 'FlyingTitle'
    );
}

// prepares the final array containing information for the asset to be rendered
// --> Uses the new values if available
// --> Uses previous values otherwise

if (!isset($rendering_array['slide_bias']) || $rendering_array['slide_bias'] === '') {
    $rendering_array['slide_bias'] = 0;
}
if (!isset($rendering_array['new_title']) || $rendering_array['new_title'] === '') {
    $rendering_array['new_title'] = $origin_asset_meta['title'];
}
if (!isset($rendering_array['new_author']) || $rendering_array['new_author'] === '') {
    $rendering_array['new_author'] = $origin_asset_meta['author'];
}
if (!isset($rendering_array['new_album']) || $rendering_array['new_album'] === '' || !ezmam_album_exists($rendering_array['new_album'])) {
    $rendering_array['new_album'] = $rendering_array['src_album'];
}
if (!isset($rendering_array['new_asset']) || $rendering_array['new_asset'] === '' || ezmam_asset_exists($rendering_array['new_album'], $rendering_array['new_asset'])) {
    $rendering_array['new_asset'] = date('Y_m_d_H\hi');
    if (ezmam_asset_exists($rendering_array['new_alnum'], $rendering_array['new_asset'])) {
        echo 'Asset already exists. Use a <new_date> that is not used by another asset';
        die;
    }
}
if (!isset($rendering_array['new_date']) || $rendering_array['new_date'] === '') {
    $rendering_array['new_date'] = get_user_friendly_date($rendering_array['new_asset'], ' ', true, 'fr', false);
}
if (!isset($rendering_array['new_album_name']) || $rendering_array['new_album_name'] === '') {
    $album_meta = ezmam_album_metadata_get($rendering_array['new_album']);
    $rendering_array['new_album_name'] = '[' . suffix_remove($rendering_array['new_album']) . '] ' . get_album_title($rendering_array['new_album']);
}
if (!isset($rendering_array['organization']) || $rendering_array['organization'] === '') {
    $rendering_array['organization'] = $organization_name;
}
if (!isset($rendering_array['copyright']) || $rendering_array['copyright'] === '') {
    $rendering_array['copyright'] = $copyright;
}
if (!isset($rendering_array['add_title']) || $rendering_array['add_title'] === '') {
    $rendering_array['add_title'] = $to_process_array['add_title'];
}
if (!isset($rendering_array['intro_movie']) || $rendering_array['intro_movie'] === '') {
    $rendering_array['intro_movie'] = $to_process_array['intro_movie'];
}
if (!isset($rendering_array['new_type']) || $rendering_array['new_type'] === '') {
    $rendering_array['new_type'] = $origin_asset_meta['record_type'];
}
if (!isset($origin_asset_meta['super_highres']) || $origin_asset_meta['super_highres'] === '') {
    $rendering_array['super_highres'] = false;
} else {
    $rendering_array['super_highres'] = true;
}

print 'Original source: ' . PHP_EOL;
print '*******************************************' . PHP_EOL;
print_r($origin_asset_meta);
print_r($to_process_array);
print 'New asset: ' . PHP_EOL;
print '*******************************************' . PHP_EOL;
print_r($rendering_array);

$renderers = require dirname(__FILE__) . '/renderers.inc';
$renderer = false;
$index = 0;
while ($renderer === false && $index < (count($renderers))) {
    if (strtolower($renderers[$index]["status"]) === "enabled") {
        $renderer = $renderers[$index];
    }
    $index++;
}
if ($renderer === false) {
    echo 'No renderer enabled' . PHP_EOL;
    die;
}
$rerender_dir = $render_root_path . '/processing/' . $rendering_array['new_asset'] . '_' . $rendering_array['new_album'] . '_rerender';
print "rerender_dir: " . $rerender_dir . PHP_EOL;
mkdir($rerender_dir, 0755, true);
$cmd = "ln -s " . $repository_path . "/" . $rendering_array['src_album'] . "/" . $rendering_array['src_asset'] . "/_metadata.xml " . $rerender_dir . "/.";
print $cmd . PHP_EOL;
exec($cmd);
if (strpos($rendering_array['new_type'], 'cam') !== false) {
    $cmd = "ln -s " . $repository_path . "/" . $rendering_array['src_album'] . "/" . $rendering_array['src_asset'] . "/*_cam " . $rerender_dir . "/.";
    print $cmd . PHP_EOL;
    exec($cmd);
}
if (strpos($rendering_array['new_type'], 'slide') !== false) {
    $cmd = "ln -s " . $repository_path . "/" . $rendering_array['src_album'] . "/" . $rendering_array['src_asset'] . "/*_slide " . $rerender_dir . "/.";
    print $cmd . PHP_EOL;
    exec($cmd);
}
file_put_contents($rerender_dir . '/torender.inc', '<?php return ' . var_export($rendering_array, true) . ';' . PHP_EOL . ' ?>');

// Send the video to the renderer download dir
$cmd = $rsync_pgm . ' -L -r -e ssh -tv  --partial-dir=' . $renderer['downloading_dir'] . ' ' . $rerender_dir . ' ' . $renderer['client'] . '@' . $renderer['host'] . ':' . $renderer['downloaded_dir'] . ' 2>&1';
print $cmd . PHP_EOL;

// try 3 times
for ($i = 0; $i < 3; $i++) {
    exec($cmd, $out, $err);
    if ($err) {
        echo "Waiting for rsync to retry";
        sleep(60);
    } else {
        $cmd = ("rm -rf $rerender_dir");
        exec($cmd);
    };
}

$cmd = $ssh_pgm . ' -oBatchMode=yes ' . $renderer['client'] . '@' . $renderer['host'] . ' " ' .
        $renderer['php'] . ' ' . $renderer['home'] . '/cli_rerender.php '.
        $renderer['downloaded_dir'] . '/' . $rendering_array['new_asset'] . '_' . $rendering_array['new_album'] . '_rerender' . "/"
        . '"';

print $cmd . PHP_EOL;
exec($cmd, $out, $err);

$cmd = 'scp -r ' . $renderer['client'] . '@' . $renderer['host'] . ':' .
        $renderer['processed_dir'] . '/' . $rendering_array['new_asset'] . '_' . $rendering_array['new_album'] . ' ' .
        $repository_path . '/' . $rendering_array['new_album'] . '/' . $rendering_array['new_asset'];
print $cmd . PHP_EOL;
exec($cmd);

$cmd = $ssh_pgm . ' -oBatchMode=yes ' . $renderer['client'] . '@' . $renderer['host'] . ' " ' .
        'rm -rf ' . $renderer['processed_dir'] . '/' . $rendering_array['new_asset'] . '_' . $rendering_array['new_album']
        . '"';
print $cmd . PHP_EOL;
exec($cmd);

$asset_meta = ezmam_asset_metadata_get($rendering_array['new_album'], $rendering_array['new_asset']);
$asset_meta['author'] = $rendering_array['new_author'];
$asset_meta['title'] = $rendering_array['new_title'];
$asset_meta['record_date'] = $rendering_array['new_asset'];
$asset_meta['record_type'] = $rendering_array['new_type'];
$asset_meta['intro'] = $rendering_array['intro_movie'];
$asset_meta['add_title'] = $rendering_array['add_title'];
ezmam_asset_metadata_set($rendering_array['new_album'], $rendering_array['new_asset'], $asset_meta);
ezmam_asset_token_reset($rendering_array['new_album'], $rendering_array['new_asset']);

print "finished" . PHP_EOL;
die;

/**
 * converts a SimpleXMLElement in an associative array
 * @param SimpleXMLElement $xml
 * @anonymous_key the name of root tag we don't want to get for each item
 * @return type
 */
function xml_file2assoc_array($xml, $anonymous_key = 'anon')
{
    if (is_string($xml)) {
        $xml = new SimpleXMLElement(file_get_contents($xml));
    }
    $children = $xml->children();
    if (!$children) {
        return (string) $xml;
    }
    $arr = array();
    foreach ($children as $key => $node) {
        $node = xml_file2assoc_array($node);
        // support for 'anon' non-associative arrays
        if ($key == $anonymous_key) {
            $key = count($arr);
        }

        // if the node is already set, put it into an array
        if (isset($arr[$key])) {
            if (!is_array($arr[$key]) || $arr[$key][0] == null) {
                $arr[$key] = array($arr[$key]);
            }
            $arr[$key][] = $node;
        } else {
            $arr[$key] = $node;
        }
    }
    return $arr;
}
