<?php


function index($param = array())
{
    global $input;

    //if(in_array($input['job'], scheduler_queue_get())) { // In queue = not frozen
    scheduler_job_kill($input['job']);
    //}

    redirectToController('view_queue');
}
