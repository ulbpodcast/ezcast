<?php
/**
 * @package ezcast.ezadmin.test
 */

require_once 'lib_sql_stats.php';
//require_once 'lib_sql_requests.php';
//init_db();

$albumName = "TEST-ADMIN-pub";
$assetName = "2014_05_21_07h57";
$thread_id = 12;
$month = "05-2014";
$year_month = "2014-05";
$nDays = 15;

$todayTemp = date('Y-m-d H:i:s');
$later = $todayTemp;
$earlier = date('Y-m-d H:i:s', strtotime('-' . $nDays . ' days', strtotime($todayTemp)));


echo "testing threads_select_by_id" . '<br/>';
var_dump(threads_select_by_id($thread_id));

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing threads_select_all" . '<br/>';
var_dump(threads_select_all());

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing threads_select_all_by_asset" . '<br/>';
var_dump(threads_select_all_by_asset($albumName, $assetName));



echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing comments_select_by_threadId" . '<br/>';
var_dump(comments_select_by_threadId($thread_id));


##### STATS FUNCTIONS #######################################################

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing album_get_all" . '<br/>';
var_dump(album_get_all());


echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing threads_count_by_album" . '<br/>';
print_r(threads_count_by_album($albumName));

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing threads_count_by_album_and_month" . '<br/>';
print_r(threads_count_by_album_and_month($albumName, $month));

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing threads_count_by_album_and_date_interval" . '<br/>';
print_r(threads_count_by_album_and_date_interval($albumName, $earlier, $later));

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing comments_count_by_album" . '<br/>';
print_r(comments_count_by_album($albumName));

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing comments_count_by_album_and_month" . '<br/>';
print_r(comments_count_by_album_and_month($albumName, $month));

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing threads_count_all" . '<br/>';
print_r(threads_count_all());

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing comments_count_all" . '<br/>';
print_r(comments_count_all());

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing threads_count_by_month" . '<br/>';
print_r(threads_count_by_month($year_month));

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing comments_count_by_month" . '<br/>';
print_r(comments_count_by_month($year_month));


echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing threads_count_by_date_interval" . '<br/>';
print_r(threads_count_by_date_interval($earlier, $later));

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing comments_count_by_date_interval" . '<br/>';
print_r(comments_count_by_date_interval($earlier, $later));

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing comments_count_by_album_and_date_interval" . '<br/>';
var_dump(comments_count_by_album_and_date_interval($albumName, $earlier, $later));

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing date_select_oldest" . '<br/>';
print_r(date_select_oldest());

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing date_select_newest" . '<br/>';
print_r(date_select_newest());

echo '<br/><br/>' . "*************************************************************************" . '<br/>';
echo "testing threads_count_all_by_asset" . '<br/>';
print_r(threads_count_all_by_asset());

die;
