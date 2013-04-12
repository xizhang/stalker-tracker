<?php
function st_mining(){
	if(!empty($_POST)){
		if (is_numeric($_POST['st_span'])){
			$options['span'] = floor($_POST['st_span']);
		}
		if (is_numeric($_POST['st_visits'])){
			$options['visits'] = floor($_POST['st_visits']);
		}
		update_option('st_mining_options', $options);
	}
	global $wpdb;
	global $st_lang;
	$m_options = get_option('st_mining_options', TRUE);
	$st_options = get_option('st_options', TRUE);
	$timezone = st_get_timezone();
	$sql="select * from (select ".$wpdb->prefix."st_visits.cookie, count(cookie) as co,  max(time_visited) as max_time
		,min(time_visited) as min_time from ".$wpdb->prefix."st_visits 
		where time_visited>='".st_get_prev_date($st_options['stalker_days'])."'
 		group by cookie) as cookie_count
		where cookie_count.cookie not in (select cookie from ".$wpdb->prefix."st_stalker_cookie)
		and co>".$m_options['visits']." 
		order by co desc";
	$results = $wpdb->get_results($sql);
	echo $st_lang['mining_1'];
	echo '<form name="form1" method="post" action="'.$currentUrl.'">';
	echo "<li>".sprintf($st_lang['mining_span_option'],"<input type='text' size='3' name='st_span' value='".$m_options['span']."'/>")."</li>";
	echo "<li>".sprintf($st_lang['mining_visits_option'],"<input type='text' size='3' name='st_visits' value='".$m_options['visits']."'/>")."</li>";
	echo "<input type='submit' value ='".$st_lang['mining_refresh']."' style='width:70px;height:25px;'/></form><br> ";
	echo '<table class="widefat" cellspacing="0" style="width:800px;">
		<tr class="st_menu_title"><td>'.$st_lang['menu_fingerprint'].'</td><td>'.$st_lang['mining_visits'].'</td>
		<td>'.$st_lang['mining_frequency'].'</td><td>'.$st_lang['mining_span'].'</td><td>'.$st_lang['menu_time'].'</td></tr><tbody>';
	$isData = false;
	foreach($results as $r){
		$isData = true;
		$timeSpan = ceil((strtotime($r->max_time) - strtotime($r->min_time))/(3600*24));
		if ($timeSpan<=$m_options['span'])
			continue;
		$frequency =  floor($r->co/$timeSpan);
		$likelihood = 255-($timeSpan + $r->co)*15;
		if ($likelihood<=0)
			$likelihood = "00";
		elseif ($likelihood<=15)
			$likelihood = "0".dechex($likelihood);
		else 
			$likelihood = dechex($likelihood);
		echo '<tr style="color:#ff'.$likelihood.'00">';
		echo '<td>
		<a href="'.ST_ADMIN_URL.'&st_page=fingerprint&st_param='.$r->cookie.'">'.$r->cookie.'</a></td>';
		echo '<td>'.$r->co.'</td>';
		echo '<td>'.$frequency.' visits/day</td>';
		echo '<td>'.$timeSpan.' days</td>';
		echo '<td>'.st_get_localtime($r->min_time, $timezone).' ~ '.st_get_localtime($r->max_time, $timezone).'</td>';
		echo '</tr>';
	}
	
	echo '</tbody></table>';
	if(!$isData)
		echo $st_lang['mining_no_data'];
	
}
?>