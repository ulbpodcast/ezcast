<?php

/**
 * Sends an email with user's message
 * @global type $input
 * @return boolean
 */
function index($param = array())
{
    global $input;
    global $ezplayer_url;
    global $mailto_alert;
    global $antispam_filter_words;
    
    if (!isset($input['message']) || rtrim($input['message'] == '')) {
        return false;
    }
    
    //spam prevention
    $ignore_message = false;
    foreach ($antispam_filter_words as $spam_word) {
        if (strpos($input['message'], $spam_word) !== false) {
            $ignore_message = true;
            break;
        }
    }

    if (!$ignore_message) {
        $message = $input['message'];
        $subject = $input['subject'];
        $mail = $input['email'];

        $header = '------------------------------------------------------------' . PHP_EOL;
        $header.= "from : $mail [" . $_SESSION['user_email'] . ']' . PHP_EOL;
        $header.= "Name: " . $_SESSION['user_full_name'] . PHP_EOL;
        $header.= "Netid: " . $_SESSION['user_login'] . PHP_EOL;
        $header.= "OS: " . $_SESSION['user_os'] . " - version: " . $_SESSION['user_os_version'] . PHP_EOL;
        $header.= "Browser: " . $_SESSION['browser_name'] . " - version: " . $_SESSION['browser_version'] . PHP_EOL;
        $header.= "Album: " . $_SESSION['album'] . PHP_EOL;
        $header.= "Asset: " . $_SESSION['asset'] . PHP_EOL;
        $header.= "Current view: " . $_SESSION['ezplayer_mode'] . PHP_EOL;
        $header.= '------------------------------------------------------------' . PHP_EOL . PHP_EOL;

        mail($mailto_alert, $_SESSION['user_full_name'] . " - $subject", $header . $message);
        global $organization_name;
        if (rtrim($mail) !== '') {
            $header = template_get_info("report_success", $organization_name, get_lang()).'.'. PHP_EOL . PHP_EOL;
            mail($mail, "Confirmation: $subject", $header . $message);
        }
        trace_append(array('0', 'contact_send', $mail, $subject, $message));
    }
    
    // loads the previous action
    $input['action'] = $_SESSION['ezplayer_mode'];
    $new_url = $ezplayer_url.'/index.php?'.http_build_query($input);
    
    // Displaying the previous page
    header("Location: " . $new_url);
    load_page();
}
