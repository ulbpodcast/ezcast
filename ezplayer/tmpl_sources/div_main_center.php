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
?>

<script>
    lvl = 1;

    history.replaceState({"url": 'index.php'}, '', '');
    ezplayer_mode = '<?php echo $_SESSION['ezplayer_mode']; ?>';
</script>
<?php
include_once 'lib_print.php';
global $first_connexion;
?> 
<div class="search_wrapper">
    <div id="search">
        <?php include_once template_getpath('div_search.php'); ?>
    </div>
</div>

<div class="albums">
    <div id="tuto">
        <a href="#" onclick="$('#tuto_video').toggle();" id="tuto_label">®tuto®</a>
        <video id='tuto_video' width="720" controls  type="video/mp4" src="./videos/tuto_<?php echo get_lang(); ?>.mp4" 
               style="display: <?php echo (!isset($albums) || sizeof($albums) == 0 || $first_connexion) ? 'block' : 'none'; ?>;">
            ®tuto®</video></div>
    <?php
    if (!isset($albums) || sizeof($albums) == 0) {
        ?>
        <span>®No_consulted_album®</span>
        <?php
    } else {
        ?>
        <!--a class="album_options_button" href="javascript:toggle('.album_options');"></a-->
        <ul>
            <?php
            foreach ($albums as $index => $album) {
                $private = false;
                if (suffix_get($album['album']) == '-priv') {
                    $private = true;
                } ?>
                <li>    
                    <a class="item <?php if ($private) {
                    echo 'private';
                } ?>" href="javascript:show_album_assets('<?php echo $album['album']; ?>', '<?php echo $album['token']; ?>');">
                        <b style="text-transform:uppercase;"><?php if (isset($album['course_code_public']) && $album['course_code_public']!="") {
                    echo $album['course_code_public'];
                } else {
                    echo suffix_remove($album['album']);
                } ?></b> 
                        <?php if ($private) {
                    echo '(®Private_album®)';
                } ?>
                        <br/><?php print_info($album['title']); ?>

                    </a>
                </li>
                <?php if (acl_user_is_logged()) {
                    ?>
                    <div class="album_options left">
                        <a class="up-arrow" <?php if ($index == 0) {
                        ?>style="visibility:hidden"<?php
                    } ?> href="javascript:album_token_move('<?php echo $album['album']; ?>', <?php echo $index; ?>, 'up');" title="®Move_up®"></a>
                        <?php if ($index != count($albums) - 1) {
                        ?><a class="down-arrow" href="javascript:album_token_move('<?php echo $album['album']; ?>', <?php echo $index; ?>, 'down');" title="®Move_down®"></a><?php
                    } ?>
                    </div>
                    <?php
                    if (acl_user_is_logged() && acl_show_notifications()) {
                        $count = acl_global_count($album['album']);
                        if (($count - acl_watched_count($album['album'])) > 0) {
                            ?>
                            <div class="album_count green" title="<?php print_new_video($count - acl_watched_count($album['album'])); ?>"><?php echo($count - acl_watched_count($album['album'])); ?></div>
                            <?php
                        }
                    } ?> 

                    <div class="album_options pull-right inline-block">
                        <a  class="button-rect green pull-right inline-block share-rss" href="javascript:popup_album('<?php echo $album['album'] ?>', 'rss');">®subscribe_rss®</a>
                        <?php if (suffix_get($album['album']) == '-priv' || !acl_has_album_moderation($album['album'])) {
                        ?> 
                            <a class="delete-album" title="®Delete_album®" href="javascript:popup_album('<?php echo $album['album'] ?>', 'delete');"></a>
                        <?php
                    } ?>
                    </div>

                                                <!--span class="delete_album" onclick="delete_album_token('<?php echo $album['album']; ?>');">x</span-->
                    <?php
                }
            } ?>
        </ul>
        <?php
    }
    ?>
</div>