<?php
function st_stalkers(){
	global $wpdb;
	global $st_lang;
	$timezone = st_get_timezone();
	if(!empty($_POST)){
		if(!empty($_POST['st_del'])){
			foreach($_POST as $key=>$p){
				if (!is_numeric($p))
					continue;
				$sql = "delete from ".$wpdb->prefix."st_stalkers where id='$p'";
				$wpdb->query($sql);
				$sql = "delete from ".$wpdb->prefix."st_stalker_cookie where stalker_id='$p'";
				$wpdb->query($sql);
			}
		}
		if(!empty($_POST['st_del_visits'])){
			foreach($_POST as $p){
				if (!is_numeric($p))
					continue;
				$sql = "delete from ".$wpdb->prefix."st_visits 
					where cookie in (select cookie from ".$wpdb->prefix."st_stalker_cookie where stalker_id='$p')";
				$wpdb->query($sql);
			}
		}
	}
	$st_options = get_option('st_options', TRUE);
	$sql="select * from ".$wpdb->prefix."st_stalkers 
		left join 
		(select stalker_id, count(stalker_id) as v_count from ".$wpdb->prefix."st_stalker_cookie
		join ".$wpdb->prefix."st_visits on ".$wpdb->prefix."st_stalker_cookie.cookie = ".$wpdb->prefix."st_visits.cookie
		and ".$wpdb->prefix."st_visits.time_visited>='".st_get_prev_date($st_options['stalker_days'])."'
		group by stalker_id) as stalker_count
		on ".$wpdb->prefix."st_stalkers.id = stalker_count.stalker_id
		left join 
		(select stalker_id, count(stalker_id) as c_count from ".$wpdb->prefix."st_stalker_cookie
		group by stalker_id) as cookie_count
		on ".$wpdb->prefix."st_stalkers.id = cookie_count.stalker_id
		left join 
		(	select max(time_visited) as last_visit_time, ".$wpdb->prefix."st_stalker_cookie.stalker_id from ".$wpdb->prefix."st_visits join
			".$wpdb->prefix."st_stalker_cookie on ".$wpdb->prefix."st_visits.cookie = ".$wpdb->prefix."st_stalker_cookie.cookie
			group by ".$wpdb->prefix."st_stalker_cookie.stalker_id
		) as last_visit
		 on ".$wpdb->prefix."st_stalkers.id = last_visit.stalker_id
		";
	$results = $wpdb->get_results($sql);
	if(empty($results)){
		echo $st_lang['stalkers_no_data'];
		return true;
	}
	echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">
		<input type="submit" name="st_del" value="'.$st_lang['stalkers_del'].'" style="height:25px;width:130px" onClick="return confirmSubmit();"/>
		<input type="submit"  name="st_del_visits" value="'.$st_lang['stalkers_del_visits'].'" style="height:25px;width:130px" onClick="return confirmSubmit();"/>
		<table class="widefat" cellspacing="0"><tr class="st_menu_title">
		<td></td>
		<td>'.$st_lang['stalkers_name'].'</td>
		<td>'.$st_lang['stalkers_visits'].'</td>
		<td>'.$st_lang['stalkers_fp'].'</td>
		<td>'.$st_lang['stalkers_is_stats'].'</td>
		<td>'.$st_lang['stalkers_is_block'].'</td>
		<td>'.$st_lang['stalkers_last_visit'].'</td>
		<td>'.$st_lang['stalkers_greet'].'</td>
		
		</tr>';
	foreach($results as $r){
		$v_count = empty($r->v_count)? 0:$r->v_count;
		$c_count = empty($r->c_count)? 0:$r->c_count;
		echo "<tr>
		<td><input type='checkbox'' name='delete_".$r->id."' value='".$r->id."' /></td>
		<td><a href='".ST_ADMIN_URL."&st_page=stalkers&st_id=".$r->id."&st_name=".$r->name."'>".$r->name."</a></td>
		<td>$v_count</td><td>$c_count</td><td>";
		
		
		if ($r->is_stat=='y')
			echo "<div style='color:#00ff00'>YES</div>";
		else
			echo "<div style='color:#ff0000'>NO</div>";
		echo "</td><td>";
		if ($r->is_block=='y')
			echo '<img src="'.ST_BASEFOLDER.'img/denied.png" title="'.$st_lang['menu_denied'].'"/>';
		else
			echo '<img src="'.ST_BASEFOLDER.'img/granted.png" title="'.$st_lang['menu_granted'].'"/>';
		echo "</td><td>".st_time_diff(time(),strtotime($r->last_visit_time))."</td><td>";
		if ($r->is_greet=='y'){
			//out mail
			echo '<img src="'.ST_BASEFOLDER.'img/outmail.png"/>';
			if(strlen($r->greet_text)<100){
				echo strip_tags($r->greet_text);				
			}else{
				echo substr(strip_tags($r->greet_text),0,100);
			}
		}elseif($r->greet_time<$r->response_time){
			//inmail
			echo '<img src="'.ST_BASEFOLDER.'img/inmail.png"/>';
			if(strlen($r->response_text)<100){
				echo strip_tags($r->response_text);				
			}else{
				echo substr(strip_tags($r->response_text),0,100);
			}
		}else{
			echo '-----';
		}
		
		echo "</td></tr>";
	}
	echo '</table><script LANGUAGE="JavaScript">function confirmSubmit(){
	if (confirm("Are you sure?"))
		return true ;
	else
		return false ;
	}</script>
	</form>';
}

function st_stalker($id){
	global $st_lang;
	global $wpdb;
	if(!empty($_POST)){
		if ($_POST['group1'] == 'stat_yes')
			$isStat ='y';
		elseif ($_POST['group1'] == 'stat_no')
			$isStat ='n';
		if ($_POST['group2'] == 'greet_no')
			$isGreet ='n';
		elseif ($_POST['group2'] == 'greet_yes')
			$isGreet ='y';
		if ($_POST['group_block'] == 'block_no')
			$isBlock ='n';
		elseif ($_POST['group_block'] == 'block_yes')
			$isBlock ='y';
		$sql ="update wp_st_stalkers set name='".$_POST['st_name']."', 
			is_stat ='$isStat', is_greet ='$isGreet', is_block ='$isBlock', greet_text='".$_POST['st_greet_text']."'"; 
		if ($isGreet =='y')
			$sql.=", greet_time='".gmdate("Y-n-d H:i:s")."' ";
		$sql.=" where id=$id";
		$wpdb->query($sql);
	}
	$st_options = get_option('st_options', TRUE);
	$sql = "select  ".$wpdb->prefix."st_visits.access, ".$wpdb->prefix."st_visits.id as visit_id, ".$wpdb->prefix."st_visits.browser, ".$wpdb->prefix."st_visits.os, ".$wpdb->prefix."st_visits.referer,".$wpdb->prefix."st_visits.comment_id, 
		".$wpdb->prefix."st_visits.agent, ".$wpdb->prefix."st_visits.time_visited, ".$wpdb->prefix."st_visits.cookie, ".$wpdb->prefix."st_visits.url, ".$wpdb->prefix."st_visits.ip,
		".$wpdb->prefix."st_stalkers.id as stalker_id, ".$wpdb->prefix."st_stalkers.name, ".$wpdb->prefix."st_stalkers.is_stat,
		".$wpdb->prefix."comments.comment_content  
		from
		wp_st_visits left join   ".$wpdb->prefix."st_stalker_cookie on ".$wpdb->prefix."st_visits.cookie= ".$wpdb->prefix."st_stalker_cookie.cookie 
		left join ".$wpdb->prefix."st_stalkers on ".$wpdb->prefix."st_stalkers.id = ".$wpdb->prefix."st_stalker_cookie.stalker_id 
		left join ".$wpdb->prefix."comments on ".$wpdb->prefix."st_visits.comment_id = ".$wpdb->prefix."comments.comment_ID
	    where stalker_id ='$id'
	    and ".$wpdb->prefix."st_visits.time_visited>='".st_get_prev_date($st_options['stalker_days'])."' 
		order by ".$wpdb->prefix."st_visits.time_visited desc";
	$results = $wpdb->get_results($sql);
	if(empty($results)){
		echo $st_lang['stalker_no_data'];
		return;
	}
	foreach($results as $r){
		$cookies[$r->cookie] = '<div> - <a href="'.ST_ADMIN_URL.'&st_page=fingerprint&st_param='.$r->cookie.'" title="'.$st_lang['title_cookie'].'">'.$r->cookie.'</a></div>';
		$ips[$r->ip] = '<a href="'.ST_ADMIN_URL.'&st_page=ip&st_param='.$r->ip.'"  title="'.$st_lang['title_ip'].'">'.$r->ip.'</a>';
		$ips1[$r->ip] = " ip = '$r->ip' "; 
	}
	$sql ="select a.cookie from ".$wpdb->prefix."st_stalker_cookie 
		right join (select distinct cookie from ".$wpdb->prefix."st_visits 
		where (".implode(" or ", $ips1).") ) as a
		on ".$wpdb->prefix."st_stalker_cookie.cookie = a.cookie
		where id is null";
	$cookieresults = $wpdb->get_results($sql);
	foreach($cookieresults as $r){
		$newcookies[$r->cookie] = '<div> - <a href="'.ST_ADMIN_URL.'&st_page=fingerprint&st_param='.$r->cookie.'" title="'.$st_lang['title_cookie'].'">'.$r->cookie.'</a></div>';
	}
	$sql ="select * from ".$wpdb->prefix."st_stalkers where id ='$id' ";
	$stalkerresults = $wpdb->get_results($sql);
	//----------------
	echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo '<table style="width:100%;"><tr><td style="vertical-align:top;">';
	echo '<li>'.$st_lang['stalker_name'].':<input name="st_name" type="text" value="'.$stalkerresults[0]->name.'" size="15"/></li>';
	echo '<li>'.$st_lang['stalker_block'].': 
		<input type="radio" name="group_block" value="block_yes" ';
	if ($stalkerresults[0]->is_block =='y')
		echo "checked";
	echo '> Yes <input type="radio" name="group_block" value="block_no" ';
	if ($stalkerresults[0]->is_block =='n')
		echo "checked";
	echo '> No</li><li>'.$st_lang['stalker_stat'].': 
		<input type="radio" name="group1" value="stat_yes" ';
	if ($stalkerresults[0]->is_stat =='y')
		echo "checked";
	echo '> Yes <input type="radio" name="group1" value="stat_no" ';
	if ($stalkerresults[0]->is_stat =='n')
		echo "checked";
	echo '> No</li>';
	if (!empty($stalkerresults[0]->response_text))
		echo '<li>'.$st_lang['stalker_response'].": ".strip_tags($stalkerresults[0]->response_text).'</li>';
	echo '<li>'.$st_lang['stalker_greet'].': <input type="radio" name="group2" value="greet_yes" onchange="javascript: document.getElementById(\'st_greet_wrapper\').style.display=\'\'"';
	if ($stalkerresults[0]->is_greet =='y')
		echo "checked";
	echo '> Yes	<input type="radio" name="group2" value="greet_no" onchange="javascript: document.getElementById(\'st_greet_wrapper\').style.display=\'none\'"';
	if ($stalkerresults[0]->is_greet =='n')
		echo "checked";
	echo '> No</li>';
	echo '<div id="st_greet_wrapper"';
	if ($stalkerresults[0]->is_greet =='n')
		echo ' style="display:none;" ';
	echo '><textarea name="st_greet_text" id="st_greet_text" style="width:95%;margin-bottom:5px;" >';
	if (empty($stalkerresults[0]->greet_text))
		echo sprintf($st_lang['stalker_default_greet'],$stalkerresults[0]->name);
	else
		echo $stalkerresults[0]->greet_text;
	echo '</textarea>
	</div>
	<input type="submit" value="'.$st_lang['stalker_update'].'" style="height:25px;width:140px"/>';
	echo '<br><br>
		<li>IP: '.implode(', ',$ips).'</li>
		<li>'.$st_lang['menu_fingerprint'].': '.implode('',$cookies).'</li>';
	if (!empty($newcookies)){
		echo '<li>'.$st_lang['stalker_other_fp'].': '.implode('',$newcookies).'</li>';
	}
	echo '</td><td style="width:680px;vertical-align:top;">';
	st_make_stat_chart($results,true);
	echo '</td></tr></table></form>';
	st_make_table($results);
}
?>