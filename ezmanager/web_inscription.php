<?php

require_once(__DIR__.'/config.inc');
if(!$ezmanager_subscription_form)
    die("disabled in config");

//this form allows users already known in db to set their recorder password as well as enabling all their courses for recording in classrooms

const USERNAME_MAX_LENGHT = 8;
const PASSWD_MIN_LENGHT = 4;
const PASSWD_MAX_LENGHT = 20;
const CONTACT_STR = "Cellule Podcast <podcast@ulb.ac.be> (02 650 4464)";
const BROKEN_DB_MESSAGE = "Si vous voyez cette erreur c'est que le formulaire est tout cassé, problement à cause d'une modification de la structure DB d'EZcast. Fix needed."; //part of the mail to send to podcast stafff

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Inscription EZcast</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <h2>Inscription EZcast</h2>
  <form method="post">
    <div class="form-group">
      <label for="netid">netid :</label>
      <input type="netid" class="form-control" id="email" placeholder="Entrer netid" name="netid" maxlength="<?php echo USERNAME_MAX_LENGHT; ?>" value="<?php if(isset($_POST["netid"])) echo $_POST["netid"]; ?>">
    </div>
    <div class="form-group">
      <label for="pwd">Mot de passe :</label>
      <input type="password" class="form-control" id="pwd" placeholder="Entrer mot de passe" name="pwd" maxlength="<?php echo PASSWD_MAX_LENGHT; ?>" value="<?php if(isset($_POST["pwd"])) echo $_POST["pwd"]; ?>">
    </div>
    <button id="send_button" type="submit" class="btn btn-default">Enregistrer</button>
  </form>
  <hr/>
  <div id="success">  </div>
  <div id="error"></div>

</body>
</html>

<?php

// ## INPUT VALIDATION 

if (filter_input(INPUT_SERVER, "REQUEST_METHOD") != "POST")
    die;

if(empty($_POST["netid"])) 
    error("Netid manquant", true);

if(!isset($_POST["pwd"]))
    error("Mot de passe manquant", true);

$netid_raw = $_POST["netid"];
$netid = preg_replace("/[^a-zA-Z]+/", "", $netid_raw);
if(strlen($netid) > USERNAME_MAX_LENGHT)
    error("netid invalide", true);

$passwd_raw = $_POST["pwd"];
if(strlen($passwd_raw) < PASSWD_MIN_LENGHT)
    error("Mot de passe invalide: Trop court", true);

if(strlen($passwd_raw) > PASSWD_MAX_LENGHT) { 
    error("Mot de passe invalide: Trop long", true);
}

require_once(__DIR__ . "/../commons/lib_sql_management.php");
require_once(__DIR__ . "/../ezadmin/lib_push_changes.php");

$db_user = db_user_read($netid);
if(!$db_user) {
    error("Netid introuvable dans la DB EZcast. Contactez la " . htmlspecialchars(CONTACT_STR), true);
}

if($db_user['passNotSet'] == false) {
    error("Mot de passe déjà défini pour cet utilisateur. Contactez la " . htmlspecialchars(CONTACT_STR), true);
}


// ## SETTING PASSWORD

$mail_title = "EZCast: User $netid subscription";

$affected_passwd = db_user_set_recorder_passwd($netid, $passwd_raw);
if($affected_passwd === false) {
    $logger->log(EventType::MANAGER_SUBSCRIPTION_FORM, LogLevel::ERROR, "Failed to set password for user $netid", array(basename(__FILE__)));
    mail($mailto_alert, $mail_title, "Modification du mot de passe: Erreur db. ".BROKEN_DB_MESSAGE);
    error("Modification du mot de passe: Erreur interne, contactez la " . htmlspecialchars(CONTACT_STR), true);
}

success("Mot de passe défini avec succès");

// ## ENABLING COURSES IN CLASSROOMS

$affected_enable = db_user_enable_recorder_for_all_courses($netid);
if($affected_enable === false) {
    $logger->log(EventType::MANAGER_SUBSCRIPTION_FORM, LogLevel::ERROR, "Failed to enable recorder for all courses for user $netid", array(basename(__FILE__)));
    mail($mailto_alert, $mail_title, "Activation des cours en auditoire: Erreur db 1.".BROKEN_DB_MESSAGE);
    error("Activation des cours en auditoire: Erreur interne 1, contactez la " . htmlspecialchars(CONTACT_STR), true);
}

$enabled_courses = db_user_get_courses_with_recorder($netid);
if($enabled_courses === false) {
    $logger->log(EventType::MANAGER_SUBSCRIPTION_FORM, LogLevel::ERROR, "Failed to check if user has courses with recorder enabled", array(basename(__FILE__)));
    mail($mailto_alert, $mail_title, "Activation des cours en auditoire: Erreur db 2.".BROKEN_DB_MESSAGE);
    error("Activation des cours en auditoire: Erreur interne 2, contactez la " . htmlspecialchars(CONTACT_STR), true);
}

$mail_msg = "";
$enabled_course_count = sizeof($enabled_courses);
if($enabled_course_count == 0) {
    error("Le mot de passe a été défini avec succès, mais ce netid ne semble associé à aucun cours. Contactez la " . htmlspecialchars(CONTACT_STR), false);
    $mail_msg = "User $netid has subscribed using online form (".$_SERVER['REQUEST_URI'].").\n". 
                "Password was set BUT USER HAS NO LINKED COURSES. He won't be able to use EZcast until he's linked to a course.";
} else {
    $msg = "<div>$enabled_course_count cours activé(s) en auditoire avec succès :</div>";
    $msg .= "<ul>";
    foreach($enabled_courses as $course)
        $msg .= "<li>".$course["course_code"]." / ".$course["course_name"]."</li>";
    $msg .= "</ul>";
    success($msg);
    $mail_msg =   "User $netid has subscribed using online form (".$_SERVER['REQUEST_URI'].").\n".
                  "Password was set and all courses for this user were enabled in classrooms.\n".
                  "$enabled_course_count courses are enabled for this user ($affected_enable were enabled in this script).\n".
                  "\n".
                  "Enabled course(s):\n";
     foreach($enabled_courses as $course)
        $mail_msg .= "- ".$course["course_code"]." / ".$course["course_name"]."\n";
}
$logger->log(EventType::MANAGER_SUBSCRIPTION_FORM, LogLevel::NOTICE, $mail_msg, array(basename(__FILE__)));
mail($mailto_alert, $mail_title, $mail_msg);

disable_send_button();

start_background_push_changes();

// ## 

function success($notice_msg) 
{
    //warning, no escaping
    $notice_div = '<div class="alert alert-success"><strong>'.$notice_msg.'</strong>';
    echo "<script>$('#success').append('".$notice_div."');</script>";
}

function error($notice_msg, $die = true)
{
    //warning, no escaping
    $notice_div = '<div class="alert alert-danger"><strong>'.$notice_msg.'</strong>';
    echo "<script>$('#error').append('".$notice_div."');</script>";
    if($die)
        die();
}

function disable_send_button() 
{
    echo "<script>$('#send_button').attr('disabled',true);</script>";
}

function start_background_push_changes()
{
    global $mailto_alert;
    global $mail_title;
    
    $push_script = __DIR__.'/../ezadmin/cli_push_changes.php';
    $cmd = 'nohup sh -c "php '.$push_script.'" > /dev/null 2>&1 &';
    $res = 0;
    $output = array();
    exec($cmd, $output, $res);
    if($res != 0)
        mail($mailto_alert, $mail_title, "Failed to start pushing changes! New passwords and other changes won't have effect on manager and recorder until we push them.");

}