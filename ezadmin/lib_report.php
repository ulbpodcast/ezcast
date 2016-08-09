<?php

global $EZCAST_FOLDER;
$EZCAST_FOLDER = "/var/lib/ezcast/";
global $REPOSITOR_FOLDER;
$REPOSITOR_FOLDER = 'repository';

global $NOT_VALID_COURS;
$NOT_VALID_COURS = array('TEST', 'APR-POD', 'DEMO');

global $allXML;


function repository_get_all() {
    global $EZCAST_FOLDER;
    global $REPOSITOR_FOLDER;
    global $allXML;
    
    $res = array();
    repository_get_xml($EZCAST_FOLDER.$REPOSITOR_FOLDER, $res);
    
    $allXML = array();
    foreach($res as $filePath) {
        $xml = simplexml_load_file($filePath);
        
        $output_array = array();
        preg_match("/".preg_quote($EZCAST_FOLDER.$REPOSITOR_FOLDER, '/')."\/(([\w-_]*)\-[^\-^\/]*)\/(\w*)\/_metadata.xml/", 
                $filePath, $output_array);
        $xml->cours = $output_array[2];
        $xml->album = $output_array[1];
        $xml->asset = $output_array[3].'_'.$xml->cours = $output_array[2];
        
        $allXML[] = $xml;
    }
    
    echo '<pre>';
    print_r($allXML);
    echo '</pre>';
}

function repository_get_general_infos() {
    global $allXML;
    global $NOT_VALID_COURS;
    
    $listAuthor = array();
    $submitAuthor = array();
    $classroomAuthor = array();
    
    $listCours = array();
    $listCoursSubmit = array();
    $listCoursClassroom = array();
    
    $countAsset = 0;
    $countRecordAsset = 0;
    $countClassroomAsset = 0;
    
    
    foreach($allXML as $asset) {
        
        // ASSET
        ++$countAsset;
        if($asset->origin == 'SUBMIT') {
            ++$countRecordAsset;
        } else {
            ++$countClassroomAsset;
        }
        
        $author = (String) $asset->author;
        
        // Total Author
        if(array_key_exists($author, $listAuthor)) {
            ++$listAuthor[$author];
        } else {
            $listAuthor[$author] = 1;
        }
        
        // Submit author
        if($asset->origin == 'SUBMIT') {
            if(array_key_exists($author, $submitAuthor)) {
                ++$submitAuthor[$author];
            } else {
                $submitAuthor[$author] = 1;
            }
        } else {
            if(array_key_exists($author, $classroomAuthor)) {
                ++$classroomAuthor[$author];
            } else {
                $classroomAuthor[$author] = 1;
            }
        }
        
        $cours = (String) $asset->cours;
        if(!in_array($cours, $NOT_VALID_COURS)) {
            if(array_key_exists($cours, $listCours)) {
                ++$listCours[$cours];
            } else {
                $listCours[$cours] = 1;
            }
            
            if($asset->origin == 'SUBMIT') {
                if(array_key_exists($cours, $listCoursSubmit)) {
                    ++$listCoursSubmit[$cours];
                } else {
                    $listCoursSubmit[$cours] = 1;
                }
            } else {
                if(array_key_exists($cours, $listCoursClassroom)) {
                    ++$listCoursClassroom[$author];
                } else {
                    $listCoursClassroom[$author] = 1;
                }
            }
            
        }
        
    }
    
    return array(
        'countAsset' => $countAsset, 'countRecordAsset' => $countRecordAsset, 
        'countClassroomAsset' => $countClassroomAsset,
        'listAuthor' => $listAuthor, 'submitAuthor' => $submitAuthor,
        'classroomAuthor' => $classroomAuthor,
        'listCours' => $listCours, 'listCoursSubmit' => $listCoursSubmit,
        'listCoursClassroom' => $listCoursClassroom);
}



function repository_get_xml($folder, &$res = array(), $rec = 0) {
    if($rec > 2) {
        return;
    }
    foreach(scandir($folder) as $file) {
        if (!in_array($file, array(".","..")))  { 

            if(is_dir($folder.'/'.$file)) {
                ++$rec;
                repository_get_xml($folder.'/'.$file, $res, $rec);
                --$rec;

            } else if($file == '_metadata.xml' && $rec == 2) {
                $res[] = $folder.'/'.$file;

            }
        }
    }
    
}