<?php
/*
 * EZCAST EZrenderer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Antoine Dewilde
 *            Thibaut Roskam
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 *
 * @param path $meta_path
 * @return assoc_array|false
 * @desc open a metadatafile (xml 1 level) and return all properties and values in an associative array
 */
function metadata2assoc_array($meta_path)
{
    @   $xml= simplexml_load_file($meta_path);
    if ($xml===false) {
        return false;
    }
    $assoc_array=array();
    foreach ($xml as $key => $value) {
        $assoc_array[$key]=(string)$value;
    }
    return $assoc_array;
}

/**
 *
 * @param <type> $assoc_array
 * @return <xml_string>
 * @desc takes an assoc array and transform it in a xml metadata string
 */
function assoc_array2metadata($assoc_array)
{
    $xmlstr="<?xml version='1.0' standalone='yes'?>\n<metadata>\n</metadata>\n";
    $xml = new SimpleXMLElement($xmlstr);
    foreach ($assoc_array as $key => $value) {
        $xml->addChild($key, $value);
    }
    $xml_txt=$xml->asXML();
    return $xml_txt;
}

/**
 * takes an assoc array, transform it in a xml metadata string and saves it to a file.
 * @param assoc_array $assoc_array
 * @param string $file_path
 * @return bool
 *
 */
function assoc_array2metadata_file($assoc_array, $file_path)
{
    $xmlstr="<?xml version='1.0' standalone='yes'?>\n<metadata>\n</metadata>\n";
    $xml = new SimpleXMLElement($xmlstr);
    foreach ($assoc_array as $key => $value) {
        $xml->addChild($key, $value);
    }
    $xml_txt=$xml->asXML();
    $res=file_put_contents($file_path, $xml_txt, LOCK_EX);
    
    //did we write all the characters
    if ($res!=strlen($xml_txt)) {
        return false;
    }//no

    return true;
}
