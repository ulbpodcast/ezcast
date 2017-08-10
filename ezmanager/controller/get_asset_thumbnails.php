<?php
function index($param = array()) {    
    global $input;
    global $repository_basedir;
    readfile($repository_basedir.'/repository/'.$input["album"].'/'.$input["asset"].'/thumbnails/'.$input["image"]);
}