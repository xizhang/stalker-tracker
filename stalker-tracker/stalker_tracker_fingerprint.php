<?php
function st_fingerprint($cookie) {
	global $wpdb;
	global $st_lang;
	if(!empty($_POST)){
		if (!empty($_POST['st_remove'])){
			$sql = "delete from ".$wpdb->prefix."st_stalker_cookie where stalker_id ='".$_POST['st_stalkerId']."' and cookie='$cookie'";
			$wpdb->query($sql);
		}
		if (!empty($_POST['st_add'])){
			$sql = "insert into ".$wpdb->prefix."st_stalker_cookie (stalker_id,cookie) 
				values ('".$_POST['st_stalkers']."','$cookie')";
			$wpdb->query($sql);
		}
		if (!empty($_POST['st_create'])){
			$sql = "insert into ".$wpdb->prefix."st_stalkers (name) values ('".$_POST['st_name']."')";
			$wpdb->query($sql);
			$sql = "select id from ".$wpdb->prefix."st_stalkers order by id desc limit 0,1";
			$results = $wpdb->get_results($sql);
			$sql = "insert into ".$wpdb->prefix."st_stalker_cookie (stalker_id,cookie) 
				values ('".$results[0]->id."','$cookie')";
			$wpdb->query($sql);
		}
		
	}
	
	
	$st_options = get_option('st_options', TRUE);
	$sql = "select  ".$wpdb->prefix."st_visits.access, ".$wpdb->prefix."st_visits.id as visit_id, ".$wpdb->prefix."st_visits.browser, ".$wpdb->prefix."st_visits.os, ".$wpdb->prefix."st_visits.referer,".$wpdb->prefix."st_visits.comment_id, 
		".$wpdb->prefix."st_visits.agent, ".$wpdb->prefix."st_visits.time_visited, ".$wpdb->prefix."st_visits.cookie, ".$wpdb->prefix."st_visits.url, ".$wpdb->prefix."st_visits.ip,
		".$wpdb->prefix."st_stalkers.id as stalker_id, ".$wpdb->prefix."st_stalkers.name, ".$wpdb->prefix."st_stalkers.is_stat,
		".$wpdb->prefix."comments.comment_content  
		from
		".$wpdb->prefix."st_visits left join   ".$wpdb->prefix."st_stalker_cookie on ".$wpdb->prefix."st_visits.cookie= ".$wpdb->prefix."st_stalker_cookie.cookie 
		left join ".$wpdb->prefix."st_stalkers on ".$wpdb->prefix."st_stalkers.id = ".$wpdb->prefix."st_stalker_cookie.stalker_id 
		left join ".$wpdb->prefix."comments on ".$wpdb->prefix."st_visits.comment_id = ".$wpdb->prefix."comments.comment_ID
	    where ".$wpdb->prefix."st_visits.cookie ='$cookie'
	    and ".$wpdb->prefix."st_visits.time_visited>='".st_get_prev_date($st_options['stalker_days'])."' 
		order by ".$wpdb->prefix."st_visits.time_visited desc";
	$results = $wpdb->get_results($sql);
	$stalkerId = $results[0]->stalker_id;
	$stalkerName = $results[0]->name;
	
	echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo '<table style="width:100%;"><tr><td style="vertical-align:top;">';
	if ($stalkerId>0){
		echo sprintf($st_lang['fp_belong'],"<a href='".ST_ADMIN_URL."&st_page=stalkers&st_id=$stalkerId&st_name=$stalkerName'>$stalkerName</a>");
		echo "<input type='submit' value ='".$st_lang['fp_remove']."' name='st_remove' style='width:60px;height:25px;'/>
		<input type='text' value ='$stalkerId' name='st_stalkerId' style='visibility:hidden;width:0px;'/>";		
	}else{
		$sql = "select * from ".$wpdb->prefix."st_stalkers";
		$data = $wpdb->get_results($sql);
		echo "<li>".$st_lang['fp_create_new'].":<input type='text'' size='10' name ='st_name'>
		<input type='submit'  name='st_create' value ='".$st_lang['fp_create']."' style='width:60px;height:25px;'/></li>";
		echo '<li>'.$st_lang['fp_add_to'].':<select name="st_stalkers">';
		foreach($data as $d){
			echo '<option value="'.$d->id.'">'.$d->name.'</option>';
		}
		echo "</select><input type='submit' name='st_add' value ='".$st_lang['fp_add']."' style='width:40px;height:25px;'/></li>";

	}

	echo '</td><td style="width:680px;">';
	st_make_stat_chart($results,true);
	echo '</td></tr></table></form>';
	st_make_table($results);
}
?>