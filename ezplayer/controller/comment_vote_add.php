<?php

/**
 * Upvote or downvote a comment
 * @global type $input
 */
function index($param = array())
{
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
    switch ($res_type) {
        case 0:
            $trace_action = 'vote_cancel';
            break;
        
        case 1:
            $trace_action = 'vote_up';
            break;

        default:
            $trace_action = 'vote_down';
            break;
    }
    trace_append(array('3', $trace_action, $_SESSION['album'], $_SESSION['asset'], $comment));
    
    return thread_details_update();
}
