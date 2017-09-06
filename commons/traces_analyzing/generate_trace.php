<?php 

/* Generate dummy traces for testing purposes */

date_default_timezone_set('Europe/Brussels');
$filename = "generate.trace";

$nbr_users = 200;
$nbr_cours = 100;
$nbr_video = 50;

$max_time_video = 7200; // In second


class Generate_Trace
{
    const USER_NAME = "User_";
    const COURS_NAME = "Cours_";
    const ASSET_TITLE = "Title_";
    const ANONYME = "nologin";
    const LEVEL = 4; // Level of the generate trace
    const ACTION = "video_play_time";
    const SPLIT_TIME = 30;
    const NBR_MAX_VIEW_VIDEO = 10; // Max number of time the user has watched the video


    public function __construct(
        $filename,
        $nbr_user,
        $nbr_cours,
        $nbr_videos,
            $max_time_video
    ) {
        $this->generateUsers($nbr_user);
        $this->generateCours($nbr_cours);
        $this->generateVideo($nbr_videos, $max_time_video);

        $this->writeLogs($filename);
    }

    private function generateUsers($nbr_user)
    {
        $this->listUser = array();
        for ($i=0; $i < $nbr_user; $i++) {
            $this->listUser[] = array(
                    'netid' => $this->getNetid($i),
                    'ip' => $this->generateIp(),
                    'session' => $this->generateSession()
                );
        }
    }

    /**
     * Get the netid of the user or "nologin"
     *
     * @param id of the user
     * @return String user netid
     */
    private function getNetid($id)
    {
        if (rand(0, 4) < 1) {
            return self::ANONYME;
        }
        return self::USER_NAME . $id;
    }

    private function generateIp()
    {
        return rand(0, 255) . "." . rand(0, 255) . "." . rand(0, 255) . "." . rand(0, 255);
    }

    private function generateSession()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 32; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function generateCours($nbr_cours)
    {
        $this->listCours = array();
        for ($i=0; $i < $nbr_cours; $i++) {
            $this->listCours[] = array(
                    'name' => self::COURS_NAME . $i
                );
        }
    }

    private function generateVideo($nbr_videos, $max_time_video)
    {
        $this->listVideos = array();
        for ($i=0; $i < $nbr_videos; $i++) {
            $this->listVideos[] = array(
                    'name' => $this->generateRandomVideoName(),
                    'title' => self::ASSET_TITLE . $i,
                    'max_time' => $this->generateRandomMaxVideoTime($max_time_video)
                );
        }
    }

    private function generateRandomMaxVideoTime($max_time_video)
    {
        return rand(90, $max_time_video);
    }

    private function generateRandomVideoName()
    {
        return date("Y_m_d_H\hi", mt_rand(1388559600, time()));
    }

    private function getRandomVideoType()
    {
        $listType = array('cam', 'slide');
        $randomId = array_rand($listType);
        return $listType[$randomId];
    }

    private function writeLogs($filename)
    {
        $file = fopen($filename, "a") or die("Unable to open file!");

        foreach ($this->listUser as $key => $userData) {
            foreach ($this->listCours as $key => $coursData) {
                foreach ($this->listVideos as $key => $videoData) {
                    // TODO
                    for ($i=0; $i < rand(1, self::NBR_MAX_VIEW_VIDEO); $i++) {
                        $trace = $this->generateTrace($userData, $coursData, $videoData);
                        fwrite($file, $trace . "\n");
                    }
                }
            }
        }
        fclose($file);
    }

    private function getRandomVideoPlayTime()
    {
        $result = rand(1, 2*self::SPLIT_TIME);

        if ($result > self::SPLIT_TIME) {
            $result = self::SPLIT_TIME;
        }

        return $result;
    }

    private function generateTrace($userData, $coursData, $videoData)
    {
        // Example:
        // 2017-08-16-13:32:58 | e411akoqsqe6das7b2q7uhm795 | ::1 | nologin | 4 | video_play_time |
        //   PODC-I-000-pub | 2016_12_08_11h47 | cam | 1 | 1
        $allInfos = array();

        // Date
        $allInfos[] = date("Y-m-d-H:i:s");

        // Session
        $allInfos[] = $userData['session'];

        // IP
        $allInfos[] = $userData['ip'];

        // netId
        $allInfos[] = $userData['netid'];

        // Level
        $allInfos[] = self::LEVEL;

        // Action
        $allInfos[] = self::ACTION;

        // Cours
        $allInfos[] = $coursData['name'];

        // Asset
        $allInfos[] = $videoData['name'];

        // Asset_title
        $allInfos[] = $videoData['title'];

        // Type
        $allInfos[] = $this->getRandomVideoType();

        // Start
        $allInfos[] = rand(0, ceil($videoData['max_time'] / self::SPLIT_TIME));

        // Time played
        $allInfos[] = $this->getRandomVideoPlayTime();

        return implode($allInfos, ' | ');
    }
}


/////////////////////////////////////

function help_view()
{
    echo '-=- Generate trace -=-' . PHP_EOL;
    echo 'Usage: php generate_trace.php [param]' . PHP_EOL;
    echo "\t-help,--h\t\tView this help" . PHP_EOL;
    echo "\t-filename,-f <file>\tDefine the file to put generate trace" . PHP_EOL;
    echo "\t-cours,-c <nbr>\t\tDefine number of cours" . PHP_EOL;
    echo "\t-video,-v <nbr>\t\tDefine number of video" . PHP_EOL;
    echo "\t-user,-u <nbr>\t\tDefine number of user" . PHP_EOL;
    echo "\t-maxvideo <time>\tDefine the maximum time of a video" . PHP_EOL;
}

for ($i=1; $i < count($argv); ++$i) {
    switch ($argv[$i]) {
        case '--h':
        case '-h':
        case '--help':
        case '-help':
            help_view();
            return;
            break;

        case '-f':
        case '-filename':
            if (isset($argv[$i+1])) {
                $filename = $argv[++$i];
            } else {
                echo 'File name is not defined!' . PHP_EOL;
                help_view();
                return;
            }
            break;

        case '-c':
        case '-cours':
            if (isset($argv[$i+1])) {
                $nbr_cours = $argv[++$i];
            } else {
                echo 'Number of cours is not defined!' . PHP_EOL;
                help_view();
                return;
            }
            break;

        case '-v':
        case '-video':
            if (isset($argv[$i+1])) {
                $nbr_video = $argv[++$i];
            } else {
                echo 'Number of videos is not defined!' . PHP_EOL;
                help_view();
                return;
            }
            break;

        case '-u':
        case '-user':
            if (isset($argv[$i+1])) {
                $nbr_users = $argv[++$i];
            } else {
                echo 'Number of users is not defined!' . PHP_EOL;
                help_view();
                return;
            }
            break;

        case '-maxvideo':
            if (isset($argv[$i+1])) {
                $max_time_video = $argv[++$i];
            } else {
                echo 'The maximum of video time is not defined!' . PHP_EOL;
                help_view();
                return;
            }
            break;

        default:
            echo 'Command: ' . $argv[$i] . ' is not valid!' . PHP_EOL;
            help_view();
            return;
    }
}

new Generate_Trace($filename, $nbr_users, $nbr_cours, $nbr_video, $max_time_video);
