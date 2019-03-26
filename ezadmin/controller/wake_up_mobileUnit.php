<?php
	require_once __DIR__ . '/../lib_push_changes.php';
	require_once 'lib_sql_event.php';

	function index($param = array())
	{
		$array_classroom = db_classrooms_list();
		$array_ip_classroom = array();

		foreach ($listClassrooms as $currClass){
			$array_ip_classroom[] = $currClass['IP'];
		}

		$ip_adresse = get_ip();
		$url_file = __DIR__.'/../var/save_ip_wake_up.txt';
		$tab_json = array();
		$cmp = 0;

		if(in_array($ip_adresse, $array_ip_classroom)){
			if(file_exists($url_file)){

				$ip_exist = false;
				$file = json_decode(file_get_contents($url_file));
				$file_save = '';

				foreach ($file as $key => $value){
					$tab_line = explode(':', $value);
					if($ip_adresse == $tab_line[0]){
						if(strtotime("now") - $tab_line[1] > 120){
							$array = array();
							push_users_courses_to_recorder($test , array($ip_adresse));
						}

						$tab_json[$cmp++] = $ip_adresse.":".strtotime("now"); 
						$ip_exist = true;
					} else {
						$tab_json[$cmp++] = $value; 
					}
				}

				if(!$ip_exist){
					$tab_json[$cmp++] = $ip_adresse.":".strtotime("now"); 
				}

				file_put_contents($url_file, json_encode($tab_json));
			} else {
				$tab_json[$cmp++] = $ip_adresse.':'.strtotime("now");
				file_put_contents($url_file, json_encode($tab_json), FILE_APPEND);
				push_users_courses_to_recorder(array() , array($ip_adresse));
			}
		} else {
			die;
		}
	}

	function get_ip()
	{
		// IP si internet partagé
		if (isset($_SERVER['HTTP_CLIENT_IP'])){
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		// IP derriére un proxy
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		// Sinon : IP normale
		else {
			return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
		}
	}