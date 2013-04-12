<?php
function st_delete(){
	global $wpdb;
	global $st_lang;
	if (!empty($_POST)){
		if (!empty($_POST["st_fp"])){
			$whereClauses[] = " ".$wpdb->prefix."st_visits.cookie = '".$_POST["st_fp"]."' ";
		}
		if (!empty($_POST["st_agent"])){
			$whereClauses[] = " ".$wpdb->prefix."st_visits.agent like '%".$_POST["st_agent"]."%' ";
		}
		if (!empty($_POST["st_ip"])){
			$whereClauses[] = " ".$wpdb->prefix."st_visits.ip like '".trim($_POST["st_ip"],"*")."%' ";
		}
		if (!empty($_POST["st_days"])){
			$whereClauses[] = " ".$wpdb->prefix."st_visits.time_visited<'".st_get_prev_date($_POST["st_days"])."' ";
		}
		if (!empty($whereClauses)){
			$whereClause = " where ".implode(' and ', $whereClauses);
			if (!empty($_POST["st_delete"])){
				$sql = "delete from ".$wpdb->prefix."st_visits $whereClause";
				$wpdb->query($sql);
			}
		}
	}
	echo '<script LANGUAGE="JavaScript">function confirmSubmit(){
		return confirm("Are you sure?")?true:false;
		}</script>';
	echo $st_lang['delete_title'].":";
	echo '<form name="form1" method="post" action="'.$currentUrl.'">';
	echo "<li>".$st_lang['menu_fingerprint'].": <input type='text' size='50' name='st_fp' value='".$_POST["st_fp"]."'/></li>";
	echo "<li>".$st_lang['menu_agent'].": <input type='text' size='50' name='st_agent' value='".$_POST["st_agent"]."'/></li>";
	echo "<li>IP: <input type='text' size='50' name='st_ip' value='".$_POST["st_ip"]."'/></li>";
	echo "<li>".sprintf($st_lang['delete_days'],"<input type='text' size='5' name='st_days' value='".$_POST["st_days"]."'/>")."</li>";
	echo "<input type='submit' style='width:150px;height:25px;' value='".$st_lang['delete_search']."'/>";
	if (empty($whereClause))
		return true;
	echo "<input type='submit' name ='st_delete' style='width:150px;height:25px;' value='".$st_lang['delete_delete']."' onClick='return confirmSubmit();'/>";	
	echo "</form>"; 
	echo "<br>";
	$sql = "select ".$wpdb->prefix."st_visits.id as visit_id, ".$wpdb->prefix."st_visits.browser, ".$wpdb->prefix."st_visits.os, ".$wpdb->prefix."st_visits.referer,".$wpdb->prefix."st_visits.comment_id, 
		".$wpdb->prefix."st_visits.agent, ".$wpdb->prefix."st_visits.time_visited, ".$wpdb->prefix."st_visits.cookie, ".$wpdb->prefix."st_visits.url, ".$wpdb->prefix."st_visits.ip,
		".$wpdb->prefix."st_stalkers.id as stalker_id, ".$wpdb->prefix."st_stalkers.name, ".$wpdb->prefix."st_stalkers.is_stat,
		".$wpdb->prefix."comments.comment_content  
		from
		".$wpdb->prefix."st_visits left join   ".$wpdb->prefix."st_stalker_cookie on ".$wpdb->prefix."st_visits.cookie= ".$wpdb->prefix."st_stalker_cookie.cookie 
		left join ".$wpdb->prefix."st_stalkers on ".$wpdb->prefix."st_stalkers.id = ".$wpdb->prefix."st_stalker_cookie.stalker_id
		left join ".$wpdb->prefix."comments on ".$wpdb->prefix."st_visits.comment_id = ".$wpdb->prefix."comments.comment_ID
		$whereClause		
		order by ".$wpdb->prefix."st_visits.time_visited desc";
	$results = $wpdb->get_results($sql);
	echo "<div>".$st_lang['delete_count'].": ".count($results)."</duv><br><br>";
	st_make_table($results);
}
?>