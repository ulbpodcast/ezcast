<?php

/**
 * Called by client to save a use trace
 */
function index($param = array()) {
    global $input;

    trace_append($input['info']);
}