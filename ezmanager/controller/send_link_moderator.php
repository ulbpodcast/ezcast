<?php

function index($param = array())
{
	global $repository_basedir;

    if(!empty($_POST['email']) && !empty($_POST['url']) && !empty($_POST['album']))
	{
		$tab_email = explode (',', $_POST['email']);
		$url = $repository_basedir. '/repository/' . $_POST['album'].'/_test1';
		$str_chain = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$email_error = '';
		$tab_json = array();
		$cmp = 0;
		$count = 0;

		if(file_exists($url))
		{
			$array = json_decode(file_get_contents($url));

			foreach ($array as $key => $value)
			{
				$tab_json[$key] = $value;
				if(count($value) >= 0)
				{
					$count = count($value);
				}
			}
		}

		if($count > 0)
		{
			$cmp += $count;
		}

		$album = explode("-", $_POST['album']);

		foreach ($tab_email as $email)
		{
			if(check_email($email))
			{
				$str_chain = str_shuffle($str_chain);
				$str_shuffle = substr($str_chain, 0, 8);
				$tab_json[''.$_POST['album'].''][$cmp++] = $str_shuffle;
				$destinataire = $email; // adresse mail du destinataire
				$sujet = "Lien modérateur"; // sujet du mail
				$message = "Bonjour,".PHP_EOL.PHP_EOL."Voici le lien de partage de l'album ".strtoupper($album[0])." : ".PHP_EOL;
				$message .= "".$_POST['url']."&tokenmanager=".$str_shuffle.PHP_EOL.PHP_EOL;
				$message .= "Bien à vous,".PHP_EOL."".$_SESSION['user_full_name']."";
				//file_put_contents('/usr/local/ezcast/ezmanager/var/textLien.txt', $message);
				mail($destinataire, utf8_decode($sujet), utf8_decode($message), 'From: podcast@uclouvain.be'); // on envois le mail
			}
			else
			{
				$email_error .= ''.$email.' ';
			}
		}

		if(isset($email_error) && !empty($email_error))
		{
			$result_json =  array('identifiant' => 'email_error' , 'message' => "Une des adresses mail que vous avez transmise est incorrecte => ( ".$email_error.")");
		}
		else
		{
			$result_json =  array('identifiant' => 'success', 'message' => "L'envoi du mail a été réalisé avec succès.");
			file_put_contents($url, json_encode($tab_json));
		}

	}
	else
	{
		$result_json =  array('identifiant' => 'empty', 'message' => "Un problème s'est déroulé lors de la récupération des données.");
		//echo '2';
	}

	echo json_encode($result_json);
}

function check_email($email)
{
	return preg_match('/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i', $email);
}
	
