<?php

/*
 * Automaticly create event to see the reaction of the system
 */

require_once '../commons/config.inc';

echo 'Preparing DB ...';
var_dump(db_prepare());

/*
 * $type, $level, $message, array $context = array(), $asset = "dummy", $assetInfo = null
 */


/// VARIABLE ///
$listMessage = array("Pas de pierre : pas de construction. Pas de construction : pas de palais. Pas de palais... pas de palais.",
    "-Je suis mon cher ami, très heureux de te voir - C'est un Alexandrin !",
    "- C'est quoi ces lumières là-bas au loin ? - C'est les lumières du port d'Alexandrie... - ... font naufrager les papillons de ma jeunesse. "
    . "- Hein ? - Non, je ne sais pas pourquoi je dis ça...",
    "Et là... une grande allée avec plein d'statues, qu'on appellera la grande allée avec plein d'statues !",
    "On me voit. On me voit plus.",
    "Pas content, Pas content !",
    "Roh cette nuit j'ai fait un rêve, je gagnais un million de sesterces. Et pis avec j'achetais une paire de chaussures, trop grandes pis moche en plus",
    "Tu n'avances pas du tout cannabis",
    "Mais vous savez, moi je ne crois pas qu'il y ait de bonnes ou de mauvaises situations.".
    "Moi, si je devais résumer ma vie, aujourd'hui avec vous, je dirais que c'est d´abord des rencontres, des gens qui m'ont" .
    " tendu la main peut-être à un moment où je ne pouvais pas, où j'étais seul chez moi. Et c'est assez curieux de se dire que" .
    " les hasards, les rencontres forgent une destinée. Parce que quand on a le goût de la chose, quand on a le goût de la chose" .
    " bien faite, le beau geste, parfois on ne trouve pas l'interlocuteur en face, je dirais le miroir qui vous aide à avancer." .
    "Alors ce n'est pas mon cas, comme je disais là, puisque moi au contraire j'ai pu, et je dis merci à la vie, je lui dis merci" .
    ", je chante la vie, je danse la vie, je ne suis qu'amour. Et finalement quand beaucoup de gens aujourd'hui me disent : " .
    "\"Mais comment fais-tu pour avoir cette humanité ?\" eh bien je leur réponds très simplement, je leur dis : " .
    "\"C'est ce goût de l´amour\", ce goût donc, qui m'a poussé aujourd'hui à entreprendre une construction mécanique," .
    " mais demain qui sait ? Peut-être simplement à me mettre au service de la communauté, à faire le don, le don de soi.");

$listContext = array(
    array("Cont", "ext"),
    array("Aphrodite", "Vénus"),
    array("Asclépios", "Esculape"),
    array("Athéna", "Minerve"),
    array("Déméter", "Cérès"),
    array("Éros", "Cupidon"),
    array("Hébé", "Juventas"),
    array("Poséidon", "Neptune"),
    array("Zéphyr", "Favonius"),
    array("Zeus", "Jupiter", "Moi"),
    );


///// FOR ASSET INFO
$listAuthor = array(
    'Adam', 'Alex', 'Alexandre', 'Alexis',
    'Anthony', 'Antoine', 'Benjamin', 'Cédric',
    'Charles', 'Christopher', 'David', 'Dylan',
    'Édouard', 'Elliot', 'Émile', 'Étienne',
    'Félix', 'Gabriel', 'Guillaume', 'Hugo',
    'Isaac', 'Jacob', 'Jérémy', 'Jonathan',
    'Julien', 'Justin', 'Léo', 'Logan',
    'Loïc', 'Louis', 'Lucas', 'Ludovic',
    'Malik', 'Mathieu', 'Mathis', 'Maxime',
    'Michaël', 'Nathan', 'Nicolas', 'Noah',
    'Olivier', 'Philippe', 'Raphaël', 'Samuel',
    'Simon', 'Thomas', 'Tommy'
    );

$list_CamSlide = array('cam', 'slides', 'camslides');

$listCourse = array("INFO-F102", 'INFO-F106', 'BIOL-F102', 'ENVI-F1001', 'GEOL-F1001',
    'HIST-F1001', 'PHYS-F105', 'ELEC-H201', 'GEOL-F1001', 'ELEC-H310', 'GEST-S101');

$listClassroom = array("S.AW1.105", 'S.AW1.115 31', 'S.AW1.117 31', 'S.AW1.120 74', 'S.AW1.121 82',
    'S.AW1.124 59', 'S.AW1.125 76', 'S.AW1.126 82', 'S.AY2.107 74', 'S.AY2.108 89',
    'S.AY2.112 88', 'S.AY2.114 87', 'S.AZ1.101');



///// FUNCTION ////
$listType = array(EventType::ASSET_CREATED, EventType::ASSET_RECORD_END);
function getRandomType()
{
    global $listType;
    //return EventType::$event_type_id[array_rand(EventType::$event_type_id)];
    return $listType[array_rand($listType)];
}

function getRandomLogLevel()
{
    $key = array_keys(LogLevel::$log_levels);
    return $key[array_rand($key)];
}

function getRandomMessage()
{
    global $listMessage;
    return $listMessage[array_rand($listMessage)];
}

function getRandomContext()
{
    global $listContext;
    return $listContext[array_rand($listContext)];
}

function getRandomAsset($cours)
{
    return date("Y_m_d_H\hi", mt_rand(1262055681, 1469629530)).'_'.$cours;
}

function getRandomAuthor()
{
    global $listAuthor;
    return $listAuthor[array_rand($listAuthor)];
}

function getRandomCamSlide()
{
    global $list_CamSlide;
    return $list_CamSlide[array_rand($list_CamSlide)];
}

function getRandomCourse()
{
    global $listCourse;
    return $listCourse[array_rand($listCourse)];
}

function getRandomClassroom()
{
    global $listClassroom;
    return $listClassroom[array_rand($listClassroom)];
}

$i = 3;
while ($i > 0) {
    $course = getRandomCourse();
    $logger->log(
        getRandomType(),
        getRandomLogLevel(),
        getRandomMessage(),
            getRandomContext(),
        getRandomAsset($course),
        getRandomAuthor(),
        getRandomCamSlide(),
            $course,
        getRandomClassroom()
    );
    --$i;
}
