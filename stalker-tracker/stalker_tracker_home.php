<?php
function st_home() {
	global $wpdb;
	global $st_lang;
	$st_options = get_option('st_options', TRUE);
	$sql = "select option_value from wp_options where option_name ='timezone_string'";
	$timezone_results = $wpdb->get_results($sql);
	$timezone = $timezone_results[0]->option_value;
	$sql = "select ".$wpdb->prefix."st_visits.access, ".$wpdb->prefix."st_visits.id as visit_id, ".$wpdb->prefix."st_visits.browser, ".$wpdb->prefix."st_visits.os, ".$wpdb->prefix."st_visits.referer,".$wpdb->prefix."st_visits.comment_id, 
		".$wpdb->prefix."st_visits.agent, ".$wpdb->prefix."st_visits.time_visited, ".$wpdb->prefix."st_visits.cookie, ".$wpdb->prefix."st_visits.url, ".$wpdb->prefix."st_visits.ip,
		".$wpdb->prefix."st_stalkers.id as stalker_id, ".$wpdb->prefix."st_stalkers.name, ".$wpdb->prefix."st_stalkers.is_stat,
		".$wpdb->prefix."comments.comment_content  
		from
		".$wpdb->prefix."st_visits left join   ".$wpdb->prefix."st_stalker_cookie on ".$wpdb->prefix."st_visits.cookie= ".$wpdb->prefix."st_stalker_cookie.cookie 
		left join ".$wpdb->prefix."st_stalkers on ".$wpdb->prefix."st_stalkers.id = ".$wpdb->prefix."st_stalker_cookie.stalker_id
		
		left join ".$wpdb->prefix."comments on ".$wpdb->prefix."st_visits.comment_id = ".$wpdb->prefix."comments.comment_ID
		where ".$wpdb->prefix."st_visits.time_visited>='".st_get_prev_date($st_options['home_days'])."' ";
	if ($st_options['is_direct']=='y'){
		$sql.= " and ".$wpdb->prefix."st_visits.referer ='' ";
	}
	$sql.="	and  (".$wpdb->prefix."st_stalkers.is_stat='y' or ".$wpdb->prefix."st_stalkers.is_stat is null) 
		order by ".$wpdb->prefix."st_visits.time_visited desc";
	$results = $wpdb->get_results($sql);
	echo '<table style="width:100%;" ><tr><td style="vertical-align:top;width:200px;">
	<div class="st_menu_title">'.$st_lang['menu_menu'].'</div>
	<ul class="st_menu_item">';
	echo '<li><a href="'.ST_ADMIN_URL.'&st_page=stalkers">'.$st_lang['menu_stalker_list'].'</a></li>';
	echo '<li><a href="'.ST_ADMIN_URL.'&st_page=mining">'.$st_lang['menu_mining'].'</a></li>';
	echo '<li><a href="'.ST_ADMIN_URL.'&st_page=comments">'.$st_lang['menu_comments'].'</a></li>';
	echo '<li><a href="'.ST_ADMIN_URL.'&st_page=delete">'.$st_lang['menu_delete'].'</a></li>';
	echo '<li><a href="'.ST_ADMIN_URL.'&st_page=settings">'.$st_lang['menu_settings'].'</a></li>';
	echo '<li><a href="'.$st_lang['menu_help_link'].'">'.$st_lang['menu_help'].'</a></li>';
	echo '</ul></td>
	<td style="vertical-align:top;padding:0px 5px 0px 5px;"></td>
	<td style="width:680px;">';
	if (empty($results)){
		echo $st_lang['menu_no_data'].'</td></tr></table>';
	}else{
		st_make_stat_chart($results,true);
		echo '</td></tr></table>';
		st_make_table($results);
	}
}
?>