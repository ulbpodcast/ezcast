<?php
function index($param = array()) {
global $repository_basedir;
    $resultat = unlink($repository_basedir.'/repository/'.$_GET['album'].'/'.$_GET['asset'].'/thumbnails/thumbnail.png'); 

}
?>