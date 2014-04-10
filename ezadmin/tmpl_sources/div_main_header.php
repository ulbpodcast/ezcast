<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Note: for details about the layout and Boostrap framwork, see http://twitter.github.com/bootstrap/ -->

        <!--
        * EZCAST EZadmin 
        * Copyright (C) 2014 Université libre de Bruxelles
        *
        * Written by Michel Jansens <mjansens@ulb.ac.be>
        * 		    Arnaud Wijns <awijns@ulb.ac.be>
        *                   Antoine Dewilde
        *                   Thibaut Roskam
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
        -->

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title><?php
            global $appname;
            echo $appname;
            if ($_SESSION['changes_to_push'])
                echo ' (®unsaved_changes®)';
            ?></title>
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="js/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="js/modernizr.custom.23345.js"></script>
        <script>
            /**
             * HTML5 Placeholder Text, jQuery Fallback with Modernizr
             *
             * @url        http://uniquemethod.com/
             * @author    Unique Method
             */
            $(function()
            {
                // check placeholder browser support
                if (!Modernizr.input.placeholder)
                {

                    // set placeholder values
                    $(this).find('[placeholder]').each(function()
                    {
                        if ($(this).val() == '') // if field is empty
                        {
                            $(this).val($(this).attr('placeholder'));
                        }
                    });

                    // focus and blur of placeholders
                    $('[placeholder]').focus(function()
                    {
                        if ($(this).val() == $(this).attr('placeholder'))
                        {
                            $(this).val('');
                            $(this).removeClass('placeholder');
                        }
                    }).blur(function()
                    {
                        if ($(this).val() == '' || $(this).val() == $(this).attr('placeholder'))
                        {
                            $(this).val($(this).attr('placeholder'));
                            $(this).addClass('placeholder');
                        }
                    });

                    // remove placeholders on submit
                    $('[placeholder]').closest('form').submit(function()
                    {
                        $(this).find('[placeholder]').each(function()
                        {
                            if ($(this).val() == $(this).attr('placeholder'))
                            {
                                $(this).val('');
                            }
                        })
                    });

                }
            });
        </script>
        <script src="bootstrap/js/bootstrap-dropdown.js"></script>
        <script>
            function toggleVisibility(thingId)
            {
                var targetElement;
                targetElement = document.getElementById(thingId);
                if (targetElement.style.display == "none")
                {
                    targetElement.style.display = "";
                } else {
                    targetElement.style.display = "none";
                }
            }
        </script>

    </head>
    <body link="#000088" vlink="#000044" alink="#0000ff" <?php if ($GLOBALS['debugmode'] == "devl") echo 'background="#99ff99"' ?>>

        <h3 style="border-bottom: 1px solid black; text-align: center;">®service_name® <?php if ($_SESSION['changes_to_push']) echo '<small class="badge badge-important" style="bottom: 10px;" title="®unsaved_changes®">!</small>'; ?></h3>
        <div class="container-fluid">
            <div class="row-fluid">
<?php include template_getpath('div_main_menu.php'); ?>
                <div class="span9">
