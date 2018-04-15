<?php
include 'config.inc';
global $classrooms_category_enabled;
global $recorders_category_enabled;

$threshold_num_options = 100; // The number of options in the sidebar above which we choose to collapse the categories together

// Each element is a category in the list; each element of the subarray
// is an entry in the category. Syntax: $options[category][webobject]=text (shown)
// IMPORTANT NOTE: webobjects ending in _list means the page is a list
// webobjects ending in _new are pages for creating new items
$options = array();
$options['Courses'] = array(
    array(
        'name' => '®list_courses®',
        'action' => 'view_courses'
        //'args' => array('table' => 'podcastcours_users_courses')
    ),
    array(
        'name' => '®create_course®',
        'action' => 'create_course'
    )
);

$options['Users'] = array(
    array(
        'name' => '®list_users®',
        'action' => 'view_users'
    )
);

global $add_users_enabled;
if ($add_users_enabled) {
    $options['Users']['create_user'] = array(
        'name' => '®create_user®',
        'action' => 'create_user'
    );
}

if ($classrooms_category_enabled) {
    $options['Classrooms'] = array(
        array(
            'name' => '®list_classrooms®',
            'action' => 'view_classrooms'
        ),
        array(
            'name' => '®create_classroom®',
            'action' => 'create_classroom'
        )
    );
}

$options['EZadmin'] = array(
    array(
        'name' => '®config®',
        'action' => 'edit_config'
    ),
    array(
        'name' => '®admins®',
        'action' => 'edit_admins'
    ),
    array(
        'name' => '®logs®',
        'action' => 'view_logs'
    )
);

$options['Renderers'] = array(
    array(
        'name' => '®renderers_queue®',
        'action' => 'view_queue'
    ),
    array(
        'name' => '®renderers_log®',
        'action' => 'view_renderer_logs'
    ),
    array(
        'name' => '®renderers_list®',
        'action' => 'view_renderers'
    ),
    array(
        'name' => '®create_renderer®',
        'action' => 'create_renderer'
    )
);

$options['Monitoring'] = array(
    array(
        'name' => '®list_event_title®',
        'action' => 'view_events'
    ),
    array(
        'name' => '®track_asset_title®',
        'action' => 'view_track_asset'
    ),
    array(
        'name' => '®classroom_calendar_title®',
        'action' => 'view_classroom_calendar'
    ),
    array(
        'name' => '®event_calendar_title®',
        'action' => 'view_event_calendar'
    )
);

$options['Stats'] = array(
    array(
       'name' => '®stats_ezplayer_threads®',
       'action' => 'view_stats_ezplayer_threads'
    ),
    array(
       'name' => '®stats_ezplayer_bookmarks®',
       'action' => 'view_stats_ezplayer_bookmarks'
    ),
    array(
        'name' => '®stats_view_report®',
        'action' => 'view_report'
    )
);

// Each element is the translation in the destination language of the keyword used to reference the category in the above array.
// Used for display purposes only.
$category_names = array(
    'Courses' => '®courses_category®',
    'Classrooms' => '®classrooms_category®',
    'Recorders' => '®recorders_category®',
    'EZadmin' => '®ezadmin_category®',
    'Stats' => '®stats_category®',
    'Users' => '®users_category®',
    'Renderers' => '®renderers_category®',
    'Monitoring' => '®monitoring_category®'
);

?>

<div class="col-md-2 hidden-print">
<ul class="nav nav-list">
    <?php foreach ($options as $cat => $suboptions) {
    ?>
        <li class="nav-header">
            <?php echo $category_names[$cat]; ?>
        </li>
        <?php $nb_options = count($options, COUNT_RECURSIVE) - count($options); ?>
        <?php foreach ($suboptions as $option) {
        ?>
            <li <?php if ($nb_options > $threshold_num_options) {
            echo 'style="display: none;"';
        } ?> 
                class="sidebar <?php // TODO not work when no operation with input in this page
                if (isset($input) && isset($input['action']) && ($option['action'] == $input['action'])) {
                    echo ' active ';
                } ?> ">
                
                <a href="index.php?&action=<?php echo $option['action'] ?>">
                    <?php echo $option['name']; ?>
                </a>
                
            </li>
            <?php
    } // end foreach?>
    <?php
} // end foreach?>
    <li class="nav-header" style="cursor: pointer;">®additional_options®</li>
    <li class="sidebar" title="®push_changes_title®"><a style="<?php echo (isset($_SESSION['changes_to_push']) && $_SESSION['changes_to_push']) ? 'color: #dd0000;' : ''; ?>" href="index.php?action=push_changes">®push_changes®</a></li>
    <li class="sidebar"><a href="index.php?action=sync_externals">®sync_externals®</a></li>
    <li class="sidebar"><a href="index.php?action=db_updater">®db_updater®</a></li>
    <li class="sidebar"><a href="?<?php echo SID."&action=logout"?>">®logout®</a></li>
</ul>
<!-- <a class="btn" style="margin-top: 10px; width: 80%;" href="?<?php echo SID."&action=logout"?>">®logout®</a> -->
</div>
