<?php
if (isset($threads_list) && sizeof($threads_list) > 0) {
    ?>

    <div class="threads_header">
        <span class="thread-logo"></span>
        <span id="threads_header-label">®Last_discussions®</span>
    </div>
    <div class="threads_list">
        <?php
        $DTZ = new DateTimeZone('Europe/Paris');
    foreach ($threads_list as $thread) {
        if (($thread['studentOnly'] == '0') || ($thread['studentOnly'] == '1' && !acl_has_moderated_album()) || acl_is_admin()) {
            $editDate = (get_lang() == 'fr') ? new DateTimeFrench($thread['lastEditDate'], $DTZ) : new DateTime($thread['lastEditDate'], $DTZ);
            $editDateVerbose = (get_lang() == 'fr') ? $editDate->format('j F Y à H\hi') : $editDate->format("F j, Y, g:i a");
            //@TODO go to real asset
            $meth_to_call = 'javascript:show_thread(\'' . $thread['albumName'] . '\', \'' . $thread['assetName'] . '\', \'' . $thread['timecode'] . '\', ' . $thread['id'] . ', \'\')'; ?>
                <div class="item-thread">
                    <div onclick="<?php echo $meth_to_call; ?>">
                        <div class="item-thread-content" >
                                                            <?php if ($thread['studentOnly'] == '1') {
                ?>
                                <img src="images/Generale/visibility-students.png" title="®Visibility_students®" class="visibility"/>
                                <?php
            } else {
                ?>
                                <img src="images/Generale/visibility-all.png" title="®Visibility_all®" class="visibility"/>
                                <?php
            } ?>
                            <span style="font-style: italic; font-size: 11px;"><?php echo $thread['assetTitle']; ?></span> 
                            <i class="slash-sm">//</i>
                            <!--<i class="slash item-thread-slash">//</i>-->
                            <div class="item-thread-title" ><?php echo $thread['title']; ?></div> 
                            <span class="item-thread-author">
                                ®On_date® <b><?php echo $editDateVerbose; ?></b>
                                ®By® <?php echo (isset($thread['lastEditAuthor']) && trim($thread['lastEditAuthor']) != '') ? $thread['lastEditAuthor'] : $thread['authorFullName']; ?>
                            </span>
                        </div>
                        <div class="eom"></div>
                    </div>
                </div>
                <?php
        }
    } ?>

    </div>
    <?php
}
?>
