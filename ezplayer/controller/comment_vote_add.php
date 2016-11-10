<?php

/**
 * Upvote or downvote a comment
 * @global type $input
 */
function index($param = array()) {
    global $input;
    $login = $input['login'];
    $comment = intval($input['comment']);
    $vote_type = $input['vote_type'];

    $values = array(
        "login" => $login,
        "comment" => $comment,
        "voteType" => $vote_type
    );

    $res_type = vote_insert($values);
    if ($res_type == 0) {
        trace_append(array('3', 'vote_cancel', $_SESSION['album'], $_SESSION['asset'], $comment));
    } else if($res_type == 1) {
        trace_append(array('3', 'vote_up', $_SESSION['album'], $_SESSION['asset'], $comment));
    } else {
        trace_append(array('3', 'vote_down', $_SESSION['album'], $_SESSION['asset'], $comment));
    }
    
    return thread_details_update();
}