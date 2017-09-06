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


require_once 'lib_ezmam.php';

// Set to true to see changes step by step; false for all tests at once
$step_by_step = false;

ezmam_repository_path('/path/to/test/repository');

//
// Creating albums for our tests
//

if ($step_by_step) {
    echo 'Step by step mode enabled. Press enter between each test to continue.';
}

if ($step_by_step) {
    echo 'Creating album COURSE-MNEMO';
}

$metadata = array(
    'name' => 'COURSE-MNEMO-priv',
    'description' => 'RSS Feed test',
    'date' => '2011-07-26',
    'anac' => '2011-2012'
);
ezmam_album_new("COURSE-MNEMO-priv", $metadata);

if ($step_by_step) {
    exec('read');
}
if ($step_by_step) {
    echo 'adding asset in COURSE-MNEMO-priv';
}

$metadata = array(
    'author' => 'X',
    'title' => 'Test d\'accentuation UTF-8: tètètè',
    'description' => 'Ceci est un accent: à',
    'record_date' => '2011_07_26_16h00',
    'record_type' => 'camslide',
    'language' => 'français'
);
ezmam_asset_new("COURSE-MNEMO-priv", "2011-07-26-14h32", $metadata);

if ($step_by_step) {
    exec('read');
}
if ($step_by_step) {
    echo 'adding asset in COURSE-MNEMO-priv';
}

$metadata = array(
    'author' => 'X',
    'title' => 'Test numéro 2',
    'description' => 'Ceci est un autre test',
    'record_date' => '2011_07_26_16h02',
    'record_type' => 'camslide',
    'language' => 'français'
);
ezmam_asset_new("COURSE-MNEMO-priv", "2011-07-27-09h28", $metadata);

if ($step_by_step) {
    exec('read');
}
if ($step_by_step) {
    echo 'adding asset in COURSE-MNEMO-priv';
}

$metadata = array(
    'author' => 'X',
    'title' => 'Test numéro 3',
    'description' => 'Ce test est slides-only',
    'record_date' => '2011_07_26_16h15',
    'record_type' => 'slide',
    'language' => 'français'
);
ezmam_asset_new("COURSE-MNEMO-priv", "2011-07-27-16h09", $metadata);

if ($step_by_step) {
    exec('read');
}
if ($step_by_step) {
    echo 'adding album COURSE-MNEMO-pub';
}

$metadata = array(
    'name' => 'COURSE-MNEMO-pub',
    'description' => 'Test de flux RSS: réréré',
    'date' => '2011-07-26',
    'anac' => '2011-2012'
);
ezmam_album_new("COURSE-MNEMO-pub", $metadata);

if ($step_by_step) {
    exec('read');
}
if ($step_by_step) {
    echo 'adding asset in COURSE-MNEMO-pub';
}

$metadata = array(
    'author' => 'X',
    'title' => 'Test d\'accentuation UTF-8: tètètè',
    'description' => 'Ceci est un accent: à',
    'record_date' => '2011_07_26_16h00',
    'record_type' => 'camslide',
    'language' => 'français'
);
ezmam_asset_new("COURSE-MNEMO-pub", "2011-07-27-14h32", $metadata);

if ($step_by_step) {
    exec('read');
}
/*
echo 'deleting album COURSE-MNEMO-pub';
ezmam_album_delete('COURSE-MNEMO-pub');

echo 'Trying to delete a non-existant album';
ezmam_album_delete("FOO-I-000");*/

ezmam_asset_delete("COURSE-MNEMO-pub", "2011-07-27-14h32");

if ($step_by_step) {
    exec('read');
}
if ($step_by_step) {
    echo 'adding asset in COURSE-MNEMO-pub again';
}

$metadata = array(
    'author' => 'X',
    'title' => 'Test d\'accentuation UTF-8: tètètè',
    'description' => 'Ceci est un accent: à',
    'record_date' => '2011_07_26_16h00',
    'record_type' => 'camslide',
    'language' => 'français'
);
ezmam_asset_new("COURSE-MNEMO-pub", "2011-07-27-14h32", $metadata);

/*if($step_by_step)
    exec('read');
if($step_by_step)
    echo "Unpublishing asst 2011-07-27-14h32";
$res=ezmam_asset_unpublish("COURSE-MNEMO-pub", "2011-07-27-14h32");

if(!$res)
    echo ezmam_last_error();

if($step_by_step)
    exec('read');
if($step_by_step)
    echo "Publishing asst 2011-07-26-14h32";
ezmam_asset_publish("COURSE-MNEMO-priv", "2011-07-26-14h32");

if($step_by_step)
    exec('read');
if($step_by_step)
    echo 'Deleting the whole COURSE-MNEMO-pub album';*/

//ezmam_album_delete("COURSE-MNEMO-pub");

ezmam_album_token_reset("COURSE-MNEMO-priv");
