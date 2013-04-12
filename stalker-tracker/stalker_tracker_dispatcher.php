<?php
add_action('admin_menu', 'st_menu');
add_action('activity_box_end', 'st_menu');


function st_menu() {
	add_options_page('Stalker Tracker Options', 'Stalker Tracker', 'manage_options', 'stalker-tracker-options', 'st_dispatcher');
	add_dashboard_page( 'Stalker Tracker Options',  'Stalker Tracker', 'manage_options', 'stalker-tracker-options',  'st_dispatcher');
}


function st_dispatcher(){
	global $st_lang;
	$timezone =  st_get_timezone();
	if (empty($timezone)){
		die($st_lang['menu_timezone']);
	}	
   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $starttime = $mtime; 
	require 'graphs.inc.php';
	echo '<link href="'.ST_BASEFOLDER.'jquery-ui.css" rel="stylesheet" type="text/css"/>
  	<script src="'.ST_BASEFOLDER.'jquery.min.js"></script>
  	<script src="'.ST_BASEFOLDER.'jquery-ui.min.js"></script>';
	echo '<link href="'.ST_BASEFOLDER.'style.css" rel="stylesheet" type="text/css" />';
	echo '<div style="background-color:#ffffff; border-bottom: 1px dotted #cccccc; padding:3px; margin-bottom:4px;">';
	echo '<a href="'.ST_ADMIN_URL.'">Stalker Tracker</a> ';
	
	if (isset($_GET['st_page'])){
		switch ($_GET['st_page']){
			case "settings":
				echo '&gt; '.$st_lang['menu_settings'].'</div>';
				include 'stalker_tracker_settings.php';
				st_settings();
				break;
			case "fingerprint":
				echo '&gt; '.$st_lang['menu_fingerprint'].': '.urldecode($_GET['st_param']);
				echo '</div>';
				include 'stalker_tracker_fingerprint.php';
				st_fingerprint($_GET['st_param']);
				break;
			case "mining":
				echo '&gt; '.$st_lang['menu_mining'].'</div>';
				include 'stalker_tracker_mining.php';
				st_mining();
				break;
			case "ip":
				$ips=explode('.',$_GET['st_param']);
				$ip = $ips[0].".".$ips[1].".".$ips[2];
				echo '&gt; IP: '.$ip.'.*</div>';
				include 'stalker_tracker_ip.php';
				st_ip($ip);
				break;
			case "comments":
				echo '&gt; '.$st_lang['menu_comments'].'</div>';
				include 'stalker_tracker_comments.php';
				st_comments();
				break;
			case "stalkers":
				include 'stalker_tracker_stalkers.php';
				$id = $_GET['st_id'];
				$name = urldecode($_GET['st_name']);
				if (empty($id)){
					echo ' &gt; '.$st_lang['menu_stalker_list'].'</div> ';
					st_stalkers();
				}else{
					echo ' &gt; <a href="'.ST_ADMIN_URL.'&st_page=stalkers">'.$st_lang['menu_stalker_list'].'</a> ';
					echo ' &gt; '.$name.'</div>';
					st_stalker($id);
				}
				
				break;
	
			case "help":
				echo '&gt; '.$st_lang['menu_help'].'</div>';
				include 'stalker_tracker_help.php';
				st_help();
				break;
			case "delete":
				echo '&gt; '.$st_lang['menu_delete'].'</div>';
				include 'stalker_tracker_delete.php';
				st_delete();
				break;
		}
	}else{
		echo '</div>';
		include 'stalker_tracker_home.php';
		st_home();
	}
	$mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $endtime = $mtime; 
   $totaltime = ($endtime - $starttime); 
   //echo "<br>Stalker Tracker execution time ".$totaltime." seconds"; 
	
}

function st_get_prev_date($days){
	return date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$days,date("Y")));
}


function st_make_stat_chart($results, $isJQuery){
	global $st_lang;
	$timezone = st_get_timezone();
	for($i=0;$i<=23;$i++){
		if ($i<10)
			$hourIndex='0'.$i;
		else
			$hourIndex=''.$i;
		$hourStat[$hourIndex] = 0;
		$dayStat[$i] = 0;
	}
	$referrerStat[direct] = 0;
	$referrerStat[itself] = 0;
	$referrerStat[referred] = 0;
	$homeurl = get_bloginfo('wpurl');
	
	foreach($results as $r){
		$dateTime2 = new DateTime($r->time_visited);
		$dateTime2->setTimezone(new DateTimeZone($timezone));
		$dateTime1 = new DateTime(date('Y-m-d H:i:s'));
		$dateTime1->setTimezone(new DateTimeZone($timezone));
		$date1 =  strtotime($dateTime1->format("Y-m-d 0:0:0"));
		$date2 =  strtotime($dateTime2->format("Y-m-d H:i:s"));
		$diff = 22-floor(($date1 -$date2)/(60*60*24));
		if ($diff>=0 && $diff<=23){
			$dayStat[$diff] +=1;
		}
		$hourStat[$dateTime2->format("H")] += 1; 
		if(!isset($osStat[$r->os])){
			$osStat[$r->os] = 1;
		}else{
			$osStat[$r->os]++;
		}
		if(!isset($browserStat[$r->browser])){
			$browserStat[$r->browser] = 1;
		}else{
			$browserStat[$r->browser]++;
		}
		 
		if($r->referer == ""){
			$referrerStat[direct]++;
		}elseif(strpos($r->referer,$homeurl)!==false){
			$referrerStat[itself]++;
		}else{
			$referrerStat[referred]++;
		}
	}
	$hourLabel ="";
	$dayLabel = "";
	for($i=0;$i<24;$i++){
		$hourLabel .= $i;
		$dayLabel .= (23-$i);
		if ($i!=23){
			$hourLabel .= ',';
			$dayLabel .= ',';
		}
	}
	
	$hourGraph = new BAR_GRAPH("vBar");
	$hourGraph->values = implode(",", $hourStat);;
	$hourGraph->titles = $st_lang['menu_hours'].",".$st_lang['menu_visits'];
	$hourGraph->labels = $hourLabel;
	$hourGraph->barColors = "#3333ff";
	
	$dayGraph = new BAR_GRAPH("vBar");
	$dayGraph->values = implode(",", $dayStat);;
	$dayGraph->labels = $dayLabel;
	$dayGraph->titles = $st_lang['menu_days'].",".$st_lang['menu_visits'];
	$dayGraph->barColors = "#3333ff";
	
	arsort($osStat);
	arsort($browserStat);
	$count = count($results);
	$dataStats.="<li><b>".$st_lang['menu_count'].":</b> $count</li>";
	$dataStats.="<li><b>".$st_lang['menu_os'].":</b> ";
	foreach ($osStat as $key=>$os){
		$dataStats.= $key.": ".ceil($os/$count*100)."%, ";
	}
	$dataStats.= "</li><li><b>".$st_lang['menu_browser'].":</b> ";
	foreach ($browserStat as $key=>$browser){
		$dataStats.= $key.": ".ceil($browser/$count*100)."%, ";
	}
	$dataStats.= "</li><li><b>".$st_lang['menu_referer'].":</b> ";
	foreach ($referrerStat as $key=>$referrer){
		$dataStats.= $key.": ".ceil($referrer/$count*100)."%, ";
	}
	$dataStats.= "</li>";
	
	if($isJQuery){
		echo '<div id="tabs" style="border:none;background:transparent;"><ul style="background:none;border:none;">
			<li style="font-size:12px;"><a href="#tabs-3">'.$st_lang['menu_trends'].'</a></li>
			<li style="font-size:12px;"><a href="#tabs-2">'.$st_lang['menu_hourly'].'</a></li>
			<li style="font-size:12px;"><a href="#tabs-1">'.$st_lang['menu_stats'].'</a></li>
			</ul>
			<div id="tabs-3" style="padding:2px;border:1px solid #cccccc;background:#ffffff;">'.$dayGraph->create().'</div>
			<div id="tabs-2" style="padding:2px;border:1px solid #cccccc;background:#ffffff;">'.$hourGraph->create().'</div>
			<div id="tabs-1" style="padding:5px;border:1px solid #cccccc;background:#ffffff;height:150px;font-size:12px;">'.$dataStats.'</div>
			</div>
			<script type="text/javascript">
			$(function() {
				$("#tabs").tabs();
			});
			</script>';
	}
}

function st_make_table($results){
	global $st_lang;
	$timezone = st_get_timezone();
	$st_options = get_option('st_options', TRUE);
	$rows = $st_options['show_length'];
	echo '<script type="text/javascript">
		function toggleAgent(id){
			var s = document.getElementById("short_"+id);
			var l = document.getElementById("long_"+id);
			if (s.style.display=="none"){
				l.style.display = "none";
				s.style.display = "inline";
			}else{
				s.style.display ="none";
				l.style.display = "inline";
			}
		}
		</script>';
	echo '<div style="width:100%;height:400px; overflow-y:scroll; background-color:#ffffff">';
	echo '<table class="widefat" cellspacing="0">
		<tr class="st_menu_title">
		<td>'.$st_lang['menu_stalker'].'</td>
		<td>'.$st_lang['menu_fingerprint'].'</td><td>'.$st_lang['menu_time'].'</td><td>URL</td>
		<td>'.$st_lang['menu_referer'].'</td><td>IP</td><td width="150">'.$st_lang['menu_agent'].'</td></tr><tbody>';
	foreach($results as $r){
		echo '<tr><td>';
		switch($r->access){
			case 'y':
				echo '<img src="'.ST_BASEFOLDER.'img/granted.png" title="'.$st_lang['menu_granted'].'"/>';
				break;
			case 'n':
				echo '<img src="'.ST_BASEFOLDER.'img/denied.png" title="'.$st_lang['menu_denied'].'"/>';
				break;
			case 'g':
				echo '<img src="'.ST_BASEFOLDER.'img/outmail.png" title="'.$st_lang['menu_access_greet'].'"/>';
				break;
			case 'g_s':
				echo '<img src="'.ST_BASEFOLDER.'img/inmail.png" title="'.$st_lang['menu_access_send'].'"/>';
				break;
		}
		echo '&nbsp;<a href="'.ST_ADMIN_URL.'&st_page=stalkers&st_id='.$r->stalker_id.'&st_name='.urlencode($r->name).'"
			title="'.$st_lang['title_stalker'].'">'.$r->name.'</a>';
		if ($r->comment_id > -1){
			echo ' <a href="'.$r->referer.'" title="'.strip_tags($r->comment_content).'" target="_blank">
			<img src="'.ST_BASEFOLDER.'img/comment-icon.gif"/></a>';
		}
		echo '</td>';
		
		echo '<td><a href="'.ST_ADMIN_URL.'&st_page=fingerprint&st_param='.$r->cookie.'"
			title="'.$st_lang['title_cookie'].'">'.$r->cookie.'</a></td>';
		echo '<td>'.st_get_localtime($r->time_visited, $timezone).'</td>';
		if (strlen($r->url)>10)
			echo '<td><a href="'.$r->url.'" title="'.$r->url.'" target="_blank">'.substr($r->url,0,10).'...</a></td>';
		else
			echo '<td><a href="'.$r->url.'" title="'.$r->url.'" target="_blank">'.$r->url.'</a></td>';
		if ($r->referer ==''){
			echo '<td>direct</td>';
		}else{
			echo '<td><a target="_blank" href="'.$r->referer.'" title="'.$r->referer.'">'.st_get_referer_domain($r->referer).'</a></td>';
		}
		echo '<td><a href="'.ST_ADMIN_URL.'&st_page=ip&st_param='.$r->ip.'" title="'.$st_lang['title_ip'].'">'.$r->ip.'</a>
		<a title="'.$st_lang['title_iplocation'].'" target="_blank" href="http://www.geobytes.com/IpLocator.htm?GetLocation&IpAddress='.$r->ip.'"><img src="'.ST_BASEFOLDER.'img/questionmark-icon.gif"/></a>
		</td>';
		echo '<td><a id ="short_'.$r->visit_id.'" href="#" title="'.$r->agent.'" onClick="toggleAgent('.$r->visit_id.')">'.$r->os.'/'.$r->browser.'</a>
		<a href="#" id="long_'.$r->visit_id.'" style="display:none;" onClick="toggleAgent('.$r->visit_id.')">'.$r->agent.'</a>
		</td>';
		echo '</tr>';
		if(--$rows==0)
			break;
	}
	echo '</tbody></table></div>';
}

function st_get_referer_domain($url){
	$pieces = explode("/", $url);
	if (count($pieces)<3){
		return 'direct';
	}else{
		return $pieces[2];
	}
}

function st_get_localtime($utcTime,$timeZone){
	$dateTime = new DateTime($utcTime);
	$dateTime->setTimezone(new DateTimeZone($timeZone));
	return $dateTime->format("m-d H:i:s");
}

function st_get_timezone(){
	$timezone = get_option('timezone_string');
	if (empty($timezone)){
		$x = floor(get_option('gmt_offset'));
		if ($x>0)
			$x="+$x";
		$timezone = "Etc/GMT$x";
	}
	return $timezone;
}
?>