<?php

/*
 * This file is part of the installation process
 */

if ($argc != 11) {
    echo "usage: " . $argv[0] . " <php_path> <rsync_path> <apache_documentroot> <ezcast_basedir> <repository_basedir> "
            . "<user> <passwd> <fullname> <apache_username>" .
    "\n <php_path> the path to the php binary" .
    "\n <rsync_path> the path to the rsync binary" .
    "\n <apache_documentroot> the path Apache's documentroot" .
    "\n <ezcast_basedir> the root directory for the ezcast application" .
    "\n <repository_basedir> the root directory for the repository".
    "\n <user> the username for the web installer" .
    "\n <passwd> the password for the web installer".
    "\n <firstname> first name of the first user".
    "\n <lastname> last name of the first user" ;
    die;
}

$php_cli_cmd = $argv[1];
$rsync_pgm = $argv[2];
$apache_documentroot = $argv[3];
$basedir = $argv[4];
$repository_basedir = $argv[5];
$username = $argv[6];
$password = $argv[7];
$firstname = $argv[8];
$lastname = $argv[9];
$apache_username = $argv[10];

/*
 * Then, we add user's configuration in commons/config.inc
 */

$config = file_get_contents($basedir . "/commons/config-sample.inc");

$config = preg_replace('/\$repository_basedir = (.+);/', '\$repository_basedir = "' . $repository_basedir . '";', $config);
$config = preg_replace('/\$basedir = (.+);/', '\$basedir = "' . $basedir . '";', $config);
$config = preg_replace('/\$php_cli_cmd = (.+);/', '\$php_cli_cmd = "' . $php_cli_cmd . '";', $config);
$config = preg_replace('/\$rsync_pgm = (.+);/', '\$rsync_pgm = "' . $rsync_pgm . '";', $config);
$config = preg_replace('/\$apache_username = (.+);/', '\$apache_username = "' . $apache_username . '";', $config);
$config = preg_replace('/\$apache_documentroot = (.+);/', '\$apache_documentroot = "' . $apache_documentroot . '";', $config);
file_put_contents($basedir . "/commons/config.inc", $config);

/*
 * Finally, create the first user to access the web installer
 */

$des_seed = chr(rand(33, 126)) . chr(rand(33, 126));
$password = crypt($password, $des_seed);

file_put_contents("$basedir/first_user", $username);
file_put_contents("first_user", " , $password", FILE_APPEND);
file_put_contents("first_user", " , $firstname", FILE_APPEND);
file_put_contents("first_user", " , $lastname", FILE_APPEND);

//create config file or append string in it
function updateConfig($filePath, $string)
{
    if (!file_exists($filePath)) {
        $res = file_put_contents($filePath, '<?php ' . PHP_EOL . $string);
        if ($res === false) {
            trigger_error("Could not create config file at $filePath", E_USER_WARNING);
        }
    } else {
        $res = file_put_contents($filePath, PHP_EOL . $string, FILE_APPEND);
        if ($res === false) {
            trigger_error("Could not update config file at $filePath", E_USER_WARNING);
        }
    }
}

//creating/appending to admin file
$admins_str = '$users[\'' . addslashes($username) . '\']=1;' . PHP_EOL;
$filePath = $basedir . '/ezadmin/admin.inc';
updateConfig($filePath, $admins_str);

//create/append to user file
$user_str = '$users[\'' . addslashes($username) . '\'][\'password\']="' . $password . '";' . PHP_EOL;
$user_str .= '$users[\'' . addslashes($username) . '\'][\'full_name\']="Admin";' . PHP_EOL;
$user_str .= '$users[\'' . addslashes($username) . '\'][\'email\']="admin@admin.admin";' . PHP_EOL . PHP_EOL;
$filePath = $basedir . '/commons/pwfile.inc';
updateConfig($filePath, $user_str);

//install cron for cli_fill_assets_status, fill status every 2 hours
system("(crontab -l 2>/dev/null; echo \"0 */2 * * * php /usr/local/ezcast/ezmanager/cli_fill_assets_status.php\") | crontab -");

system("$php_cli_cmd ".__DIR__."/cli_htdocs_install.php $apache_documentroot");
system("$php_cli_cmd ".__DIR__."/cli_tmpl_install.php");
