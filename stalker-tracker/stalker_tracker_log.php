<?php



add_action('init', 'st_log_stalker');
add_action('comment_post', 'st_log_comment',10,1);

function st_welcome(){
	//check the cookie and do things
	$cookie = $_COOKIE["ST_STALKER"];
	if (!empty($cookie)){
		global $wpdb;
		global $st_lang;
		$sql = "select ".$wpdb->prefix."st_stalkers.id, greet_text,is_greet,is_block from
			".$wpdb->prefix."st_stalkers join ".$wpdb->prefix."st_stalker_cookie
			on ".$wpdb->prefix."st_stalkers.id = ".$wpdb->prefix."st_stalker_cookie.stalker_id
			where cookie='$cookie'";
		$results = $wpdb->get_results($sql);
		if(count($results)>0){
			if ($results[0]->is_block=='y'){
				//access denied
				return 'n';
			}
			if ($results[0]->is_greet=='y'){
				if (!empty($_POST['st_response'])){					
					$sql ="update ".$wpdb->prefix."st_stalkers set is_greet='n', response_text='".$_POST['st_response']."', response_time='".gmdate("Y-n-d H:i:s")."'
						where id='".$results[0]->id."'";
					$wpdb->query($sql);
					echo '<div style="border-bottom:1px solid #000000;color:#ff0000;text-align:center;padding:10px;">'.$st_lang['greet_sent'].'</div>';
					return 'g_s';
				}else{
					echo "
					<html><body>
					<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head>
					<form name='form1' method='post' action='".$_SERVER['REQUEST_URI']."'>
					<div style='background-color:#ffffff;border:5px solid #99aaff
					; margin:20px;width:500px;'>
					<div style='background-color:#99aaff;padding:5px;font-weight:bold;'>".$st_lang['greet_blogger']."</div>
					<div style='margin:10px;'>".$results[0]->greet_text."</div>
					<div style='background-color:#99aaff;padding:5px;font-weight:bold;'>".$st_lang['greet_response']."</div>
					<div><textarea style='width:100%;height:70px;padding:5px;' name='st_response'></textarea></div>
					<div style='text-align:right;'>
					<input type='submit' style='padding:2px;' value='".$st_lang['greet_send']."' />
					</div>
					</div></form></body></html>";
					return 'g';
				}
			}
		}
	}
	return 'y';
}


function st_log_db($commentId){
	
	if (stIsRobot($_SERVER['HTTP_USER_AGENT']))
		return true;
	global $wpdb;
	$ip = stGetRealIpAddr();
	$st_options = get_option('st_options', TRUE);
	$ips = explode(',',$st_options['ipblock']);
	foreach ($ips as $ipp){
		$ipp = trim($ipp,"*");
		if(empty($ipp))
			break;
		if (strpos($ip, $ipp)===0){
			//do not log
			return true;
		}
	}
	$os = stGetOs($_SERVER['HTTP_USER_AGENT']);
	$referer = $_SERVER['HTTP_REFERER'];
	$browser = stGetBrowser($_SERVER['HTTP_USER_AGENT']);
	$url = $_SERVER['REQUEST_URI'];
	$cookie = $_COOKIE["ST_STALKER"];
	if ($cookie==''){
		$cookie = md5(time(). rand(0,99999999));
		setcookie("ST_STALKER", $cookie, time()+3600*24*3650,"/");
	}
	//--
	$access=st_welcome();
	$sql = "insert into ".$wpdb->prefix."st_visits (access, agent,  time_visited , cookie, url, ip, browser, os, referer, comment_id) 
		values('$access','".$_SERVER['HTTP_USER_AGENT']."','".gmdate("Y-n-d H:i:s")."','$cookie','$url','$ip','$browser','$os','$referer','$commentId')";
	$wpdb->query($sql);
	if ($access=='n'){
		header("HTTP/1.0 404 Not Found");
		die();
	}elseif ($access=='g'){
		die();
	}
}
function st_log_stalker(){
	st_log_db(-1);
}

function st_log_comment($commentId){
	st_log_db($commentId);
}

function stIsRobot($agent){
	if (!(strpos($agent, get_bloginfo('wpurl'))===false))//if self loop, then stop logging
		return true;
	if ($agent =='')// if empty, then stop logging
		return true;
	if (strlen($agent)<20)//if <20, it's too short to be a valid agent
		return true;
	$agent = strtolower($agent);
	$st_options = get_option('st_options', TRUE);
	$bots = explode(',',$st_options['bots']);
	foreach ($bots as $bot){
		if (!(strpos($agent, strtolower(trim($bot)))===false))
				return true;
	}
	return false;
}


function stGetRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])){//check ip from share internet
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else{
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function stGetOS($agent){
	$oss = array('blackberry'=>'BlackBerry','wordpress'=>'WordPress','android'=>'Android','nokia'=>'Nokia', 'win'=> 'Windows',  'iphone'=>'iPhone', 'mac'=>'Mac','linux'=>'Linux');
	$agent = strtolower($agent);
	foreach ($oss as $key=>$os)
		if(strpos($agent,$key)>-1)
			return $os;
	return 'unknown';
}

function stGetBrowser($agent){
	$browsers = array('ucweb'=>'UCWeb','wordpress'=>'WP','msie'=>'IE' ,'firefox'=>'Firefox','chrome'=> 'Chrome', 'opera'=>'Opera', 'apple'=>'Safari', 'safari'=>'Safari');
	$agent = strtolower($agent);
	foreach ($browsers as $key=>$browser)
			if(strpos($agent,$key)>-1)
				return $browser;
	return 'unknown';
}

?>