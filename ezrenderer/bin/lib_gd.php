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

include_once 'config.inc';

// Generates an image using title_array for text to display
function gd_image_create($title_array, $width, $height, $file)
{
    global $fontfile;
    global $quality;
    global $fontratio;
    global $linelength;

    // creates the image
    $img = imagecreate($width, $height);
    // defines the background color
    $bg_color = imagecolorallocate($img, 8, 89, 155);
    // setup the text color
    $txt_color['white'] = imagecolorallocate($img, 255, 255, 255);
    $txt_color['blue'] = imagecolorallocate($img, 105, 139, 205);

    if (isset($title_array['album'])) {
        // font size relative to the global height of the image
        $fontsize = $height / (32 * $fontratio);
        //   gd_push_text($img, $fontsize, $txt_color['blue'], wordwrap($title_array['album'], 45,"\n"), ($height * 0.25));
        $dimensions = imagettfbbox($fontsize, 0, $fontfile, $title_array['album']);
        $txt_height = $dimensions[1] - $dimensions[7];

        // splits the string in multiple strings if its size is too long
        $album = split_title($title_array['album'], $linelength * 1.27);
        switch (count($album)) {
            case 1: $pct_height = 0.25;
                break;
            case 2: $pct_height = 0.20;
                break;
            case 3: $pct_height = 0.15;
                break;
            default: $pct_height = 0.25;
                break;
        }
        // adds each value of the array in the generated image
        foreach ($album as $index => $string) {
            gd_push_text($img, $fontsize, $txt_color['blue'], $string, ($height * $pct_height + $index * ($txt_height + 5)));
        }
    }

    if (isset($title_array['title'])) {
        $fontsize = $height / (25 * $fontratio);

        $dimensions = imagettfbbox($fontsize, 0, $fontfile, $title_array['title']);
        $txt_height = $dimensions[1] - $dimensions[7];

        // splits the string in multiple strings if its size is too long
        $title = split_title($title_array['title'], $linelength);
        // adds each value of the array in the generated image
        foreach ($title as $index => $string) {
            gd_push_text($img, $fontsize, $txt_color['white'], $string, ($height * 0.40 + $index * ($txt_height + 5)));
        }
    }

    if (isset($title_array['author'])) {
        $fontsize = $height / (32 * $fontratio);
        gd_push_text($img, $fontsize, $txt_color['white'], $title_array['author'], ($height * 0.70));
    }

    if (isset($title_array['date']) && $title_array['hide_date'] != true) {
        $fontsize = $height / (42 * $fontratio);
        gd_push_text($img, $fontsize, $txt_color['white'], $title_array['date'], ($height * 0.75));
    }

    if (isset($title_array['copyright']) || isset($title_array['organization'])) {
        $string = $title_array['copyright'] .
                (isset($title_array['copyright']) && isset($title_array['organization']) ? " - " : "") .
                $title_array['organization'];

        $fontsize = $height / (55 * $fontratio);
        gd_push_text($img, $fontsize, $txt_color['blue'], escape_path($string), ($height * 0.95));
    }

    // generates the image
    return imagejpeg($img, $file, $quality);
}

// Adds the text to the image
function gd_push_text($img, $fontsize, $txt_color, $string, $top)
{
    global $fontfile;

    // global height of the generated image
    $height = imagesy($img);
    // global width of the generated image
    $width = imagesx($img);

    // dimensions of the text box to include in the generated image
    $dimensions = imagettfbbox($fontsize, 0, $fontfile, $string);
    $txt_width = $dimensions[4] - $dimensions[6];
    $txt_height = $dimensions[1] - $dimensions[7];

    // positions for the text to include
    $x = ($width - $txt_width) / 2;
    $y = $top;
    imagettftext($img, $fontsize, 0, $x, $y, $txt_color, $fontfile, $string);
}

function gd_center_text($string, $font_size, $img_width)
{
    global $fontfile;

    $dimensions = imagettfbbox($font_size, 0, $fontfile, $string);

    return ceil(($img_width - $dimensions[4]) / 2);
}

function gd_gradient($image_width, $image_height, $c1_r, $c1_g, $c1_b, $c2_r, $c2_g, $c2_b, $vertical = false)
{
    global $quality;
    global $file;

    // first: lets type cast;
    $image_width = (integer) $image_width;
    $image_height = (integer) $image_height;
    $c1_r = (integer) $c1_r;
    $c1_g = (integer) $c1_g;
    $c1_b = (integer) $c1_b;
    $c2_r = (integer) $c2_r;
    $c2_g = (integer) $c2_g;
    $c2_b = (integer) $c2_b;
    $vertical = (bool) $vertical;

    // create a image
    $image = imagecreatetruecolor($image_width, $image_height);

    // make the gradient
    for ($i = 0; $i < $image_height; $i++) {
        $color_r = floor($i * ($c2_r - $c1_r) / $image_height) + $c1_r;
        $color_g = floor($i * ($c2_g - $c1_g) / $image_height) + $c1_g;
        $color_b = floor($i * ($c2_b - $c1_b) / $image_height) + $c1_b;

        $color = ImageColorAllocate($image, $color_r, $color_g, $color_b);
        imageline($image, 0, $i, $image_width, $i, $color);
        imagejpeg($image, $file, $quality);
    }
}

function split_title($string, $line_length = 35)
{
    $result = array();
    if (strlen($string) <= $line_length) {
        return array($string);
    }
    // isolates each words of the string
    $words = explode(' ', $string);
    $tmp = '';
    $index = 0;
    // uses the words to create multiple lines, according to the max length
    foreach ($words as $word) {
        $tmp .= $word . ' ';
        if (strlen($tmp) <= $line_length) {
            $result[$index] = $tmp;
        } else {
            ++$index;
            $tmp = $word . ' ';
            $result[$index] = $tmp;
        }
    }
    return $result;
}
function get_title_info($title_meta_path, $title_filename, &$title_assoc)
{
    if (!file_exists($title_meta_path . "/" . $title_filename)) {
        $title_assoc = false;
        return true; //no title file means no title to generate
    }
    $title_assoc = metadata2assoc_array($title_meta_path . "/" . $title_filename);
    if (!is_array($title_assoc)) {
        myerror("Title metadata file read error $title_meta_path/$title_filename\n");
    }

    //check if we dont have any invalid properties
    $valid_title_elems = array("album", "title", "author", "date", "organization", "copyright", "keywords");
    $badmeta = "";
    foreach ($title_assoc as $key => $value) {
        if (!in_array($key, $valid_title_elems)) {
            $badmeta.="'$key',";
        }
    }

    if ($badmeta != "") {
        $badmeta = "Error with metadata elements: " . $badmeta . "\n";
        // myerror($badmeta);
    }

    return true;
}
/*
function generate_title($repo,$encoder,$title_assoc){

    $title_movieout = $repo . "/title.mov";
    $title_image = $repo . "/title.jpg";
    $encoder_values = explode('_', $encoder);
    $resolution_values = explode('x', $encoder_values[2]);
    $width = $resolution_values[0];
    $height = $resolution_values[1];
    $ratio = explode(":", $qtinfo["aspectRatio"]);
    if ($ratio[0] > 0 && $ratio[1] > 0)
        $height = $resolution_values[0] * $ratio[1] / $ratio[0];

    processing_status("title $camslide");
    $res = gd_image_create($title_assoc, $width, $height, $title_image);
    if (!$res || !file_exists($title_image))
        myerror("couldn't generate title $title_image");
    //   $res = movie_title($title_movieout, $title_assoc, $encoder, 8); //duration is hardcoded to 8
    $res = movie_title_from_image($title_movieout, $title_image, $encoder);
    if ($res)
        myerror("couldn't generate title $title_movieout");

    return $title_movieout;
}*/
function generate_new_title($repo, $encoder, $title_assoc)
{ //rendre compatible avec l'autre ( generate_title())
    
    $title_movieout = $repo . "/title.mov";
    $title_image = $repo . "/title.jpg";
    $encoder_values = explode('_', $encoder);
    $resolution_values = explode('x', $encoder_values[2]);
    $width = $resolution_values[0];
    $height = $resolution_values[1];
    $res = gd_image_create($title_assoc, $width, $height, $title_image);
    $res = movie_title_from_image($title_movieout, $title_image, $encoder);

    return $title_movieout;
}
