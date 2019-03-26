<?php

function index($param = array())
{
    global $input;

    $filename = "./csv_assets.csv";
    $header = array('First post datetime', 'Album name', 'Asset', 'Discussions');

    if (!session_key_check($input['sesskey'])) {
        echo "Usage: Session key is not valid";
        die;
    }

    $values = stat_threads_count_all_by_asset();
    $handle = fopen($filename, 'w');
    fputcsv($handle, array('sep=,'));
    fputcsv($handle, $header);
    foreach ($values as $fields) {
        fputcsv($handle, $fields);
    }
    fclose($handle);
    if (file_exists($filename)) {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=assets.csv");
        header("Content-Type: application/csv");
        header("Content-Transfer-Encoding: binary");
        echo file_get_contents($filename);
        /*
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();
        readfile($filename);
         */
    }
    unlink($filename);
}
