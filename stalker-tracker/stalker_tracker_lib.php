<?php
function st_time_diff($time1, $time2){
	global $st_lang;
	$diff = floor($time1 - $time2);
	if ($diff<1)
		return $st_lang['timediff_now'];
	if ($diff<60)
		return $diff.$st_lang['timediff_seconds'];
	else
		$diff = floor($diff/60);
	if ($diff<60)
		return $diff.$st_lang['timediff_minutes'];
	else
		$diff = floor($diff/60);
	if ($diff<24)
		return $diff.$st_lang['timediff_hours'];
	else
		$diff = floor($diff/24);
	if ($diff<7)
		return $diff.$st_lang['timediff_days'];
	else
		$diff = floor($diff/7);
	return $diff.$st_lang['timediff_weeks'];
}
?>