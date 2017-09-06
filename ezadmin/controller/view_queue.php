<?php


function index($param = array())
{
    //$jobs = scheduler_queue_get();
    $jobs = array_merge(scheduler_processing_get(), scheduler_queue_get(), scheduler_frozen_get());
    
    /*
     *  <uid></uid>
     *  <id></id>
     *  <file></file>
     *  <origin></origin>
     *  <sender></sender>
     *  <priority></priority>
     *  <renderer></renderer>
     *  <created></created>
     *  <sent></sent>
     *  <done></done>
     */

    require_once template_getpath('div_main_header.php');
    //require_once template_getpath('div_search_job.php');
    require_once template_getpath('div_list_jobs.php');
    require_once template_getpath('div_main_footer.php');
}
