<?php
function st_ip($ip){
	global $wpdb;
	$st_options = get_option('st_options', TRUE);
	$sql = "select ".$wpdb->prefix."st_visits.*, ".$wpdb->prefix."st_stalkers.name, ".$wpdb->prefix."st_stalker_cookie.stalker_id ,".$wpdb->prefix."comments.comment_content from
		".$wpdb->prefix."st_visits left join   ".$wpdb->prefix."st_stalker_cookie on ".$wpdb->prefix."st_visits.cookie= ".$wpdb->prefix."st_stalker_cookie.cookie 
		left join ".$wpdb->prefix."st_stalkers on ".$wpdb->prefix."st_stalkers.id = ".$wpdb->prefix."st_stalker_cookie.stalker_id 
		left join ".$wpdb->prefix."comments on ".$wpdb->prefix."st_visits.comment_id = ".$wpdb->prefix."comments.comment_ID
	    where ".$wpdb->prefix."st_visits.ip like '".$ip.".%' 
	    and ".$wpdb->prefix."st_visits.time_visited>='".st_get_prev_date($st_options['stalker_days'])."' 
		order by ".$wpdb->prefix."st_visits.time_visited desc";
	$results = $wpdb->get_results($sql);
	st_make_table($results);
}
?>