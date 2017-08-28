<?php

include_once dirname(__FILE__) . '/lib_analytics.php';

$album = "PODC-I-020-pub";

// print analytics_album_assets_count($album);

// print_r(analytics_album_asset_count_by_origin($album));

print analytics_album_access_count($album, 20150601, 20150701);

?>

