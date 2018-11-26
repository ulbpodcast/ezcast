<?php

/*
 * This is a CLI script that compiles the templates in $tmpl_source into a specific language
 * Usage: template_generate.php $tmpl_source $language
 *
 * Note: you can add languages by editing config.inc
 */

$in_install = true;

require_once 'config.inc';
require_once 'lib_template.php';

//
// Inits and sanity checks
//
if ($argc < 5) {
    echo 'Usage: php cli_template_generate.php source_folder language output_folder <path to translations.xml>' . PHP_EOL;
    die;
}

$source_folder = $argv[1];
$lang = $argv[2];
$output_folder = $argv[3];
$translation_xml_file = $argv[4];

if (!is_dir($source_folder)) {
    echo 'Error: source folder '.$source_folder.' does not exist' . PHP_EOL;
    die;
}

global $accepted_languages;

if (!in_array($lang, $accepted_languages)) {
    echo 'Error: language ' . $lang . ' not supported' . PHP_EOL;
    die;
}

template_set_errors_visible();
template_set_warnings_visible();

echo 'Translation of *all* templates in '.$source_folder.' will now start.' . PHP_EOL;
echo 'Output language: '. $lang . PHP_EOL;

$files = template_list_files($source_folder);

//
// Parsing each template file
//
foreach ($files as $file) {
    //echo 'Translating ' . $file . '...' . PHP_EOL;
    template_parse($file, $lang, $output_folder, $translation_xml_file);
    //echo 'Translation complete' . PHP_EOL . PHP_EOL;
}

if (template_last_error() != '' || template_last_warning() != '') {
    echo PHP_EOL;
}
echo 'Translation finished, you can find your files in \''.$output_folder.'/'.$lang . '\'' . PHP_EOL . PHP_EOL;
