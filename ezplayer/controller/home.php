<?php
function index($param = array()) {
	
	include "../ezadmin/lib_sql_management.php";
	global $action;
    global $ezplayer_url;
    global $error, $input;
    global $template_folder;
	global $max_video_per_page;

	if (isset($input['no_flash']))
		$_SESSION['has_flash'] = false;
	$url = $ezplayer_url;

	$lang = isset($input['lang']) ? $input['lang'] : 'fr';
	set_lang($lang);

	template_repository_path($template_folder . get_lang());
	
	$_SESSION['ezplayer_mode']='home';	
	
	if(isset($_GET['search']))$search=$_GET['search']; else $search="";
	$assets=get_anon_assets($search);
	
	if($max_video_per_page>count($assets)) $max_video_per_page=count($assets);
	$nbpage=ceil(count($assets)/$max_video_per_page);
	if(isset($_GET['page']) && $_GET['page']<=$nbpage)$first=($_GET['page']-1)*$max_video_per_page;
	else{	
		$_GET['page']=1;
		$first=0;
	}
	include_once template_getpath('main.php');
}
?>