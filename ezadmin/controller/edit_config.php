<?php

function index($param = array())
{
    global $input;

    if (isset($input['confirm']) && !empty($input['confirm'])) {
        $allow_recorder = (array_key_exists('recording_enabled', $input) && $input['recording_enabled'] == 'on');
        $add_users = (array_key_exists('add_users_enabled', $input) && $input['add_users_enabled'] == 'on');
        $pwd_storage = (array_key_exists('password_storage_enabled', $input) && $input['password_storage_enabled'] == 'on');
        $use_user_name = (array_key_exists('users_by_name', $input) && $input['users_by_name'] == 'on');
        $control_panel = (array_key_exists('enable_control_panel', $input) && $input['enable_control_panel'] == 'on');
        $control_panel_options = (array_key_exists('enable_control_panel_options', $input) && $input['enable_control_panel_options'] == 'on');


        update_config_file($allow_recorder, $add_users, $pwd_storage, $use_user_name, $control_panel, $control_panel_options);

        $alert = '<div class="alert alert-success">' . template_get_message('save_successful', get_lang()) . '</div>';
    }

    $params = parse_config_file();
    //update_config_file(false, true, true);

    include template_getpath('div_main_header.php');
    include template_getpath('div_edit_config.php');
    include template_getpath('div_main_footer.php');
}
