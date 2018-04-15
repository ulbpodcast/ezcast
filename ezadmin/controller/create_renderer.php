<?php

$ssh_timeout;

function index($param = array()){
    global $input;
    global $ssh_public_key;
    global $apache_username;
    global $ssh_pub_key_location;
    global $ssh_timeout;
    global $basedir;
    global $renderers_options;

    $ssh_timeout = 30;

    if (!isset($input['renderer_step'])) {
        $input['renderer_step'] = "default";
    }

    switch ($input['renderer_step']) {


        // 1. Receive information about renderer (server address, username and name)
        case "1":
            stepOne();
            break;

        // 2. Ask user to copy SSH key on remote renderer and test connection
        case "2":
            stepTwo();
            break;

        // 3. Additional information for EZrenderer installation
        case "3":
            stepThree();
            break;

        // 4. Copies installation files, install EZrenderer and update renderers.inc
        case "4":
            stepFour();
            break;



        // 5. Display the renderers list.
        case "5":
            redirectToController('view_renderers');
            break;


        // Display the renderer creation form
        default:
            unset($_SESSION['renderer_root_path']);
            unset($_SESSION['renderer_user']);
            unset($_SESSION['renderer_address']);
            unset($_SESSION['renderer_name']);
            unset($_SESSION['renderer_enabled']);
            unset($_SESSION['renderer_num_jobs']);
            unset($_SESSION['renderer_num_threads']);
            unset($_SESSION['renderer_php']);
            unset($_SESSION['renderer_ffmpeg']);
            unset($_SESSION['renderer_ffprobe']);

            include template_getpath('div_main_header.php');
            include template_getpath('div_create_renderer_step1.php');
            include template_getpath('div_main_footer.php');
    }


    db_log('renderers', 'Created renderer ' . $_SESSION['renderer_name'], $_SESSION['user_login']);
    //   notify_changes();
}

function stepOne(){
    global $_SESSION;
    global $input;
    global $ssh_pub_key_location;
    global $apache_username;
    global $ssh_public_key;
    global $ssh_timeout;
    
    $renderer_name = $input['renderer_name'];
    $renderer_address = $input['renderer_address'];
    $renderer_user = $input['renderer_user'];
    $enabled = isset($input['enabled']) && $input['enabled'] ? 1 : 0;

    if (empty($renderer_name)) {
        $error = template_get_message('missing_renderer_name', get_lang());
    } elseif (renderer_exists($renderer_name) !== false) {
        $error = template_get_message('existing_renderer_name', get_lang());
    } elseif (empty($renderer_address)) {
        $error = template_get_message('missing_renderer_address', get_lang());
    } elseif (empty($renderer_user)) {
        $error = template_get_message('missing_renderer_user', get_lang());
    } else {

        // saves infos as session var
        $_SESSION['renderer_name'] = $renderer_name;
        $_SESSION['renderer_user'] = $renderer_user;
        $_SESSION['renderer_address'] = $renderer_address;
        $_SESSION['renderer_enabled'] = $enabled;

        // retrieves the ssh key to display
        if ($ssh_pub_key_location == "") {
             $ssh_pub_key_location = '~/.ssh/id_rsa.pub';
        }
        $ssh_public_key = shell_exec("bash -c 'cat $ssh_pub_key_location'");
        // ssh public key not found
        if ($ssh_public_key===false || $ssh_public_key == "") {
            $ssh_public_key = "Key not found ! &#10;&#10;You can specify the path to Apache user's "
                . "SSH public key by assigning a value to \$ssh_pub_key_location in commons/config.inc.";
        }

        // saves ssh key as session var if the user returns to the previous step afterward
        $_SESSION['renderer_ssh_key'] = $ssh_public_key;

        include template_getpath('div_main_header.php');
        include template_getpath('div_create_renderer_step2.php');
        include template_getpath('div_main_footer.php');

        die;
    }

    include template_getpath('div_main_header.php');
    include template_getpath('div_create_renderer_step1.php');
    include template_getpath('div_main_footer.php');

    die;
}

function stepTwo(){
    global $_SESSION;
    global $input;
    global $ssh_public_key;
    global $ssh_timeout;
    
    if (isset($input['submit_step_2_prev']) && $input['submit_step_2_prev']) {
        // back to step 1
        $input['renderer_name'] = $_SESSION['renderer_name'];
        $input['renderer_user'] = $_SESSION['renderer_user'];
        $input['renderer_address'] = $_SESSION['renderer_address'];

        include template_getpath('div_main_header.php');
        include template_getpath('div_create_renderer_step1.php');
        include template_getpath('div_main_footer.php');
    } else {
        $ssh_public_key = $_SESSION['renderer_ssh_key'];

        // test the SSH connection
        $res = ssh_connection_test($_SESSION['renderer_user'], $_SESSION['renderer_address'], $ssh_timeout);

        if ($res === true) {
            // SSH connection is correctly set. Ask for more information on remote renderer
            // set default values
            $input['renderer_php'] = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"which php\"");
            $input['renderer_php'] = ($input['renderer_php'] == "") ? "PHP binary not found !" : $input['renderer_php'];
            $input['renderer_root_path'] = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"echo ~" . $_SESSION['renderer_user'] . "\"/ezrenderer");
            $input['renderer_ffmpeg'] = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"which ffmpeg\"");
            $input['renderer_ffmpeg'] = ($input['renderer_ffmpeg'] == "") ? "FFMPEG binary not found !" : $input['renderer_ffmpeg'];
            $input['renderer_ffprobe'] = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"which ffprobe\"");
            $input['renderer_ffprobe'] = ($input['renderer_ffprobe'] == "") ? "FFPROBE binary not found !" : $input['renderer_ffprobe'];
            $input['renderer_rsync'] = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"which rsync\"");
            $input['renderer_rsync'] = ($input['renderer_rsync'] == "") ? "RSYNC binary not found !" : $input['renderer_rsync'];
            $input['renderer_num_threads'] = 4;
            $input['renderer_num_jobs'] = 4;

            include template_getpath('div_main_header.php');
            include template_getpath('div_create_renderer_step3.php');
            include template_getpath('div_main_footer.php');
            die;
        } elseif ($res === false) {
            // SSH connection has failed. Back to step 2
            $error = template_get_message('ssh_connection_failed', get_lang());
        } elseif ($res == "known_hosts_error") {
            // could not add SSH public key from remote renderer to known_hosts file
            $error = template_get_message('ssh_known_hosts_error', get_lang());
        } elseif ($res == "keyscan_error") {
            // SSH public key from remote renderer is not in known_hosts yet
            $error = template_get_message('ssh_keyscan_error', get_lang());
        } elseif ($res == "hostname_error") {
            // SSH public key from remote renderer is not in known_hosts yet
            $error = template_get_message('ssh_hostname_error', get_lang());
        }
        include template_getpath('div_main_header.php');
        include template_getpath('div_create_renderer_step2.php');
        include template_getpath('div_main_footer.php');
        die;
    }
}

function stepThree(){
    global $_SESSION;
    global $input;
    global $ssh_public_key;
    global $ssh_timeout;
    global $renderers_options;
    
    if (isset($input['submit_step_3_prev']) && $input['submit_step_3_prev']) {
        // back to step 2
        $ssh_public_key = $_SESSION['renderer_ssh_key'];
        include template_getpath('div_main_header.php');
        include template_getpath('div_create_renderer_step2.php');
        include template_getpath('div_main_footer.php');
        die;
    } else {
        // Go to next step
        $renderer_root_path = $input['renderer_root_path'];
        $renderer_option = $input['renderer_options'];
        $renderer_php = $input['renderer_php'];
        $renderer_rsync = $input['renderer_rsync'];
        $renderer_ffmpeg = $input['renderer_ffmpeg'];
        $renderer_ffprobe = $input['renderer_ffprobe'];
        $renderer_num_jobs = $input['renderer_num_jobs'];
        $renderer_num_threads = $input['renderer_num_threads'];

        $error = "";

        if (empty($renderer_root_path)) {
            $error = template_get_message('missing_renderer_root_path', get_lang());
        } elseif (exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"if [ -e " . dirname($renderer_root_path) . " ]; then echo 'exists'; fi;\"") != 'exists') {
            $error = template_get_message('bad_renderer_root_path', get_lang());
        } elseif (empty($renderer_php) || $renderer_php == "PHP binary not found !") {
            $error = template_get_message('missing_renderer_php', get_lang());
        } elseif (empty($renderer_ffmpeg)) {
            $error = template_get_message('missing_renderer_ffmpeg', get_lang());
        } elseif (empty($renderer_ffprobe) || $renderer_ffprobe == "FFPROBE binary not found !") {
            $error = template_get_message('missing_renderer_ffprobe', get_lang());
        } elseif (empty($renderer_rsync) || $renderer_rsync == "RSYNC binary not found !") {
            $error = template_get_message('missing_renderer_rsync', get_lang());
        }

        if ($error != "") {
            include template_getpath('div_main_header.php');
            include template_getpath('div_create_renderer_step3.php');
            include template_getpath('div_main_footer.php');
            die;
        }

        if (isset($input['submit_step_3_next']) && $input['submit_step_3_next']) {
            // tests PHP, FFMPEG and FFPROBE

            $error = "";

            // verification for PHP
            $res = test_php_over_ssh($_SESSION['renderer_user'], $_SESSION['renderer_address'], $ssh_timeout, $renderer_php);
            switch ($res) {
                case "php_not_found":
                    $error .= "- " . template_get_message('php_not_found', get_lang()) . "<br/>";
                    break;
                case "php_deprecated":
                    $error .= "- " . template_get_message('php_deprecated', get_lang()) . "<br/>";
                    break;
                case "php_missing_xml":
                    $error .= "- " . template_get_message('missing_module_xml', get_lang()) . "<br/>";
                    break;
                case "php_missing_gd":
                    $error .= "- " . template_get_message('missing_module_gd', get_lang()) . "<br/>";
                    break;
                case "gd_missing_freetype":
                    $error .= "- " . template_get_message('missing_freetype', get_lang()) . "<br/>";
                    break;
                default: $error .= "";
            }

            // verification for FFMPEG
            if ($renderer_option == 'ffmpeg_fdk_aac' || $renderer_option == 'ffmpeg_built_in_aac') {
                $res = test_ffmpeg_over_ssh($_SESSION['renderer_user'], $_SESSION['renderer_address'], $ssh_timeout, $renderer_ffmpeg, $renderer_option == 'ffmpeg_built_in_aac');
                switch ($res) {
                    case "ffmpeg_not_found":
                        $error .= "- " . template_get_message('ffmpeg_not_found', get_lang()) . "<br/>";
                        break;
                    case "missing_codec_aac":
                        $error .= "- " . template_get_message('missing_codec_aac', get_lang()) . "<br/>";
                        break;
                    case "missing_codec_h264":
                        $error .= "- " . template_get_message('missing_codec_h264', get_lang()) . "<br/>";
                        break;
                    default: $error .= "";
                }
            }

            // verification for FFPROBE
            $res = test_ffprobe_over_ssh($_SESSION['renderer_user'], $_SESSION['renderer_address'], $ssh_timeout, $renderer_ffprobe);
            if ($res == "ffprobe_not_found") {
                $error .= "- " . template_get_message('ffprobe_not_found', get_lang()) . "<br/>";
            }

            if ($error != "") {
                include template_getpath('div_main_header.php');
                include template_getpath('div_create_renderer_step3.php');
                include template_getpath('div_main_footer.php');
                die;
            } else {
                $tests_success = true;
            }
        }

        // Disables the remote renderer if tests have been skipped
        if (!$tests_success) {
            $_SESSION['renderer_enabled'] = false;
        }
        $_SESSION['renderer_root_path'] = $renderer_root_path;
        $_SESSION['renderer_php'] = $renderer_php;
        $_SESSION['renderer_rsync'] = $renderer_rsync;
        $_SESSION['renderer_option'] = $renderers_options[$renderer_option];
        $_SESSION['renderer_ffmpeg'] = $renderer_ffmpeg;
        $_SESSION['renderer_ffprobe'] = $renderer_ffprobe;
        $_SESSION['renderer_num_jobs'] = (empty($renderer_num_jobs) || is_nan($renderer_num_jobs)) ? 4 : $renderer_num_jobs;
        $_SESSION['renderer_num_threads'] = (empty($renderer_num_threads) || is_nan($renderer_num_threads)) ? 4 : $renderer_num_threads;


        include template_getpath('div_main_header.php');
        include template_getpath('div_create_renderer_step4.php');
        include template_getpath('div_main_footer.php');

        die;
    }
}

function stepFour(){
    global $_SESSION;
    global $input;
    global $ssh_timeout;
    global $basedir;
    global $output;
    
    $input['renderer_root_path'] = $_SESSION['renderer_root_path'];
    $input['renderer_php'] = $_SESSION['renderer_php'];
    $input['renderer_rsync'] = $_SESSION['renderer_rsync'];
    $input['renderer_ffmpeg'] = $_SESSION['renderer_ffmpeg'];
    $input['renderer_ffprobe'] = $_SESSION['renderer_ffprobe'];
    $input['renderer_num_jobs'] = $_SESSION['renderer_num_jobs'];
    $input['renderer_num_threads'] = $_SESSION['renderer_num_threads'];
    $input['renderer_options'] = $_SESSION['renderer_option']['name'];

    if (isset($input['submit_step_4_prev']) && $input['submit_step_4_prev']) {
        // back to step 3

        include template_getpath('div_main_header.php');
        include template_getpath('div_create_renderer_step3.php');
        include template_getpath('div_main_footer.php');
        die;
    } else {
        if ($input['installation_step'] == 1) {
            // 4.1. Copies EZrenderer installation files on the remote renderer
            // tests if ezrenderer is already installed
            if (exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . " \"if [ -e " . $_SESSION['renderer_root_path'] . " ]; then echo 'exists'; fi;\"") == 'exists') {
                // EZrenderer root path already exists on the remote renderer
                $response['error'] = true;
                $response['msg'] = "<div class='red'>" . template_get_message('root_already_exists', get_lang()) . "</div>";
                echo json_encode($response);
                die;
            }
            unset($output);
            // actually copies files
            exec("scp -r -o ConnectTimeout=600 $basedir/ezrenderer " . $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] . ":" . $_SESSION['renderer_root_path'], $output, $returncode);
            if ($returncode) {
                // an error occured while copying installation files
                $response['error'] = true;
                $response['msg'] = "<div class='red'>" . template_get_message('renderer_copy_failed', get_lang()) . "</div>";
                echo json_encode($response);
                die;
            }
            // EZrenderer has been remotely copied
            echo json_encode("<div class='green'>" . template_get_message('load_step_4_copy_success', get_lang()) . "</div>"
                    . template_get_message('load_step_4_install', get_lang()));
            die;
        }

        if ($input['installation_step'] == 2) {
            // 4.2. Installs ezrenderer on the remote renderer
            $returnString = exec("ssh -o ConnectTimeout=$ssh_timeout -o BatchMode=yes " .
                    $_SESSION['renderer_user'] . "@" . $_SESSION['renderer_address'] .
                    " \"" . $_SESSION['renderer_php'] . " " . $_SESSION['renderer_root_path'] . "/renderer_install.php " .
                    $_SESSION['renderer_php'] . " '" .
                    str_replace('"', '\"', serialize($_SESSION['renderer_option'])) . "' " .
                    $_SESSION['renderer_ffmpeg'] . " " .
                    $_SESSION['renderer_ffprobe'] . " " .
                    $_SESSION['renderer_num_threads'] . " " .
                    $_SESSION['renderer_num_jobs'] . "\"", $output, $returncode);

            if ($returncode || strpos($output[0], "0") === false) {
                // an error occured while installing EZrenderer
                $response['error'] = true;
                $response['msg'] = "<div class='red'>" . template_get_message('renderer_install_failed', get_lang()) . "</div><p>" . $returnString . "<p/>";
                echo json_encode($response);
                die;
            }
            // EZrenderer has been installed
            echo json_encode("<div class='green'>" . template_get_message('load_step_4_install_success', get_lang()) . "</div>"
                    . template_get_message('load_step_4_update', get_lang()));
            die;
        }

        if ($input['installation_step'] == 3) {
            // Adds the renderer to renderers.inc files of EZmanager and EZadmin
            if (!renderer_add($_SESSION['renderer_name'], $_SESSION['renderer_address'], $_SESSION['renderer_user'], $_SESSION['renderer_enabled'], $_SESSION['renderer_root_path'], $_SESSION['renderer_php'])) {
                // an error occured while updating renderers.inc
                $response['error'] = true;
                $response['msg'] = "<div class='red'>" . template_get_message('renderer_update_failed', get_lang()) . "</div>";
                echo json_encode($response);
                die;
            } else {
                // renderers.inc files have been updated
                
                    echo json_encode("<div class='green'>" . template_get_message('load_step_4_update_success', get_lang()) . "</div>");
                    die;
                 
            }
        }
    }
}
