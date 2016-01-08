
<?php
/*
* EZCAST EZadmin 
* Copyright (C) 2014 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*                   Thibaut Roskam
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
?>

<script type="text/javascript">
    function showHiddenCategory(cat) {
        //window.alert(cat);
        
        var realcat = 'sidebar_'+cat;
        var elements = document.getElementsByClassName(realcat);
         
        for(i=0; i<elements.length; ++i) {
            if(elements[i].style.display=='inline')
                elements[i].style.display='hidden';
            else
                elements[i].style.display='inline';
        }
    }
</script>

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
    'podcastcours_users_courses_list' => array(
        'name' => '®list_courses®',
        'action' => 'view_courses'
        //'args' => array('table' => 'podcastcours_users_courses')
    ),
    'create_course' => array(
        'name' => '®create_course®',
        'action' => 'create_course'
    )
);

$options['Users'] = array(
    'list_users' => array(
        'name' => '®list_users®',
        'action' => 'view_users'
    )
);

global $add_users_enabled;
if($add_users_enabled) {
    $options['Users']['create_user'] = array(
        'name' => '®create_user®',
        'action' => 'create_user'
    );
}

if($classrooms_category_enabled) {
    $options['Classrooms'] = array(
        'podcastcours_classrooms_list' => array(
            'name' => '®list_classrooms®',
            'action' => 'view_classrooms'
        ),
        'create_classroom' => array(
            'name' => '®create_classroom®',
            'action' => 'create_classroom'
        )
    );
}

$options['EZadmin'] = array(
    'podcastcours_config' => array(
        'name' => '®config®',
        'action' => 'edit_config'
    ),
    'podcastcours_admins' => array(
        'name' => '®admins®',
        'action' => 'edit_admins'
    ),
    'podcastcours_logs' => array(
        'name' => '®logs®',
        'action' => 'view_logs'
    )
);

$options['Renderers'] = array(
   'podcastcours_queue' => array(
       'name' => '®renderers_queue®',
       'action' => 'view_queue'
   ),
   'podcastcours_renderers' => array(
       'name' => '®renderers_list®',
       'action' => 'view_renderers'
   ),
   'create_renderer' => array(
       'name' => '®create_renderer®',
       'action' => 'create_renderer'
   )
);

$options['Stats'] = array(
   'stats_ezplayer_threads' => array(
       'name' => '®stats_ezplayer_threads®',
       'action' => 'view_stats_ezplayer_threads'
   ),
   'stats_ezplayer_bookmarks' => array(
       'name' => '®stats_ezplayer_bookmarks®',
       'action' => 'view_stats_ezplayer_bookmarks'
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
    'Renderers' => '®renderers_category®'
);

?>

<div class="span2">
<ul class="nav nav-list">
    <?php foreach($options as $cat => $suboptions) { ?>
        <li class="nav-header" onclick="showHiddenCategory('<?php echo $category_names[$cat]; ?>');" style="cursor: pointer;"><?php echo $category_names[$cat]; ?></li>
        <?php $nb_options = count($options, COUNT_RECURSIVE) - count($options); ?>
        <?php foreach($suboptions as $key => $option) {
        if($key == 'additional_buttons') {
            foreach($option as $operation => $txt) {
                ?>
                <li><a href="?<?php echo SID."&action=$operation"?>"><?php echo $txt; ?></a></li>
                <?php
            }
        }
        else { ?>
            <li <?php if($key == $input['objname']) echo 'class="active"'; ?> <?php if($nb_options > $threshold_num_options) echo 'style="display: none;"'; ?> class="sidebar_<?php echo $cat; ?>"><a href="index.php?&action=<?php echo $option['action'] ?>"><?php echo $option['name']; ?></a></li>
        <?php } // end if
        } // end foreach?>
    <?php } // end foreach ?>
    <li class="nav-header" style="cursor: pointer;">®additional_options®</li>
    <li><a style="<?php echo ($_SESSION['changes_to_push']) ? 'color: #dd0000;' : ''; ?>" href="index.php?action=push_changes">®push_changes®</a></li>
    <li><a href="index.php?action=sync_externals">®sync_externals®</a></li>
    <li><a href="?<?php echo SID."&action=logout"?>">®logout®</a></li>
</ul>
<!-- <a class="btn" style="margin-top: 10px; width: 80%;" href="?<?php echo SID."&action=logout"?>">®logout®</a> -->
</div>