<?php
function st_settings(){
	global $st_lang;
	if(!empty($_POST)){
		if (is_numeric($_POST['st_home_days'])){
			$options['home_days'] = floor($_POST['st_home_days']);
		}
		if (is_numeric($_POST['st_stalker_days'])){
			$options['stalker_days'] = floor($_POST['st_stalker_days']);
		}
		if (is_numeric($_POST['st_length'])){
			$options['show_length'] = floor($_POST['st_length']);
		}
		if ($_POST['group_direct'] == 'direct_no')
			$options['is_direct'] ='n';
		elseif ($_POST['group_direct'] == 'direct_yes')
			$options['is_direct'] ='y';
		$options['bots'] = $_POST['st_bots'];
		$options['ipblock'] = $_POST['st_ips'];
		update_option('st_options', $options);
	}
	$st_options = get_option('st_options', TRUE);
	$currentUrl = $_SERVER['REQUEST_URI'];
	echo '<form name="form1" method="post" action="'.$currentUrl.'">';
	echo "<li>".sprintf($st_lang['settings_home'],"<input type='text' size='3' name='st_home_days' value='".$st_options['home_days']."'/>")."</li>";
	echo "<li>".sprintf($st_lang['settings_page'],"<input type='text' size='3' name='st_stalker_days' value='".$st_options['stalker_days']."'/>")."</li>";
	echo "<li>".sprintf($st_lang['settings_list'],"<input type='text' size='3' name='st_length'  value='".$st_options['show_length']."'/>")."</li>";
	echo "<li>".$st_lang['settings_direct'].': <input type="radio" name="group_direct" value="direct_yes" ';
	if ($st_options['is_direct'] =='y')
		echo "checked";
	echo '> Yes <input type="radio" name="group_direct" value="direct_no" ';
	if ($st_options['is_direct'] =='n')
		echo "checked";
	echo '> No</li>';
	echo "<li>".$st_lang['settings_bots'].":<br> 
	<textarea style='width:800px;height:60px;' row='5' name='st_bots' >".$st_options['bots']."</textarea></li>";
	echo "<li>".$st_lang['settings_ip'].":<br> 
	<textarea style='width:800px;height:60px;' row='5' name='st_ips' >".$st_options['ipblock']."</textarea></li>";
	echo "<input type='submit' value ='".$st_lang['settings_update']."' style='width:140px;height:25px;'/> ";
	echo "</form>";
	
}
?>