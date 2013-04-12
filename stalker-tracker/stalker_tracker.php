<?php
/*
 Plugin Name: Stalker Tracker
 Plugin URI: http://xizhang.net/?p=216
 Description: Check out who's stalking you on your blog
 Version: 1.1.9
 Author: Xi Zhang
 Author URI: http://xizhang.net
 */
/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
define('ST_BASEFOLDER', '/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/');
define('ST_ADMIN_URL', 'options-general.php?page=stalker-tracker-options');

register_activation_hook(__FILE__,'stalker_tracker_install');
register_sidebar_widget(__('Stalker Tracker'), 'stalker_tracker_widget');

require 'stalker_tracker_lib.php';
switch (WPLANG){
	case 'zh_CN':
		require 'stalker_tracker_i18n_cn.php';
		break;
	default:
		require 'stalker_tracker_i18n_en.php';
		break;
}

if(is_admin()){

	require 'stalker_tracker_dispatcher.php';
}else{
	require 'stalker_tracker_log.php';
}


function stalker_tracker_widget()
{
	global $wpdb;
	global $st_lang;
	$sql = "select st_stalkers.name,distinct_visits.ip,distinct_visits.time_visited
		from ".$wpdb->prefix."st_stalkers as st_stalkers right join ".$wpdb->prefix."st_stalker_cookie as st_stalker_cookie 
		on st_stalkers.id = st_stalker_cookie.stalker_id
		right join (select max(time_visited) as time_visited, cookie,ip from ".$wpdb->prefix."st_visits group by cookie
		order by time_visited desc limit 0,20) as distinct_visits
 		on st_stalker_cookie.cookie = distinct_visits.cookie
 		where (st_stalkers.is_stat = 'y' or st_stalkers.is_stat is null)
 		 limit 0,10";
	$results = $wpdb->get_results($sql);
	echo '<div class="widget_stalker_tracker">
		<h3><span>'.$st_lang['widget_title'].'</span></h3>
		<ul id="recentstalkers">';
	
	foreach ($results as $r){
		 
		echo '<li class="recentstalker">';
		echo empty($r->name)? $r->ip:$r->name;
		echo ', <i>'.st_time_diff(time(),strtotime($r->time_visited)).'</i></li>';	
	}
	echo '</ul></div>';
}

function stalker_tracker_install(){
	global $wpdb;
	$sql_1 = "CREATE TABLE `".$wpdb->prefix."st_stalkers` (
  			`id` int(10) NOT NULL auto_increment,
  			`name` varchar(50) NOT NULL default '0',
  			`is_stat` char(10)  NOT NULL  default 'y',
  			`is_greet` char(10) NOT NULL  default 'n',
  			`is_block` CHAR(10) not NULL DEFAULT 'n',
  			`greet_text` text,
  			`greet_time` timestamp,
  			`response_text` text,
  			`response_time` timestamp,
  			PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$sql_2 = "CREATE TABLE `".$wpdb->prefix."st_stalker_cookie` (
  			`id` int(10) unsigned NOT NULL auto_increment,
  			`stalker_id` int(10) NOT NULL,
  			`cookie` varchar(32) NOT NULL,
  			PRIMARY KEY  (`id`),
  			KEY `stalker` (`stalker_id`),
  			KEY `cookie` (`cookie`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$sql_3 = "CREATE TABLE `".$wpdb->prefix."st_visits` (
			`id` int(10) NOT NULL auto_increment,
  			`agent` text,
		  	`time_visited` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  	`cookie` varchar(32) NOT NULL,
		  	`url` varchar(100) NOT NULL,
		  	`ip` varchar(15) NOT NULL,
		  	`browser` varchar(15) NOT NULL,
		  	`os` varchar(15) NOT NULL,
		  	`referer` varchar(100) NOT NULL,
		  	`comment_id` int(10) default NULL,
		  	`access` varchar(5) default 'y',
  		PRIMARY KEY  (`id`),
  		KEY `cookie` (`cookie`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql_1);
	dbDelta($sql_2);
	dbDelta($sql_3);
	$st_options = get_option('st_options', TRUE);
	if ($st_options['home_days']=='')
	$options['home_days'] = 24;
	else
	$options['home_days'] = $st_options['home_days'];
	if ($st_options['stalker_days']=='')
	$options['stalker_days'] = 60;
	else
	$options['stalker_days'] = $st_options['stalker_days'];
	if ($st_options['show_length']=='')
	$options['show_length'] = 80;
	else
	$options['show_length'] = $st_options['show_length'];
	if ($st_options['bots']=='')
	$options['bots'] = "Wget, Yahoo! Slurp, Jakarta, CommentReader, Java/1., Mail.Ru/, ia_archiver,
		 FeedFetcher,agentname, TencentTraveler, Mozilla/3.0, Mozilla/4.0 (compatible;), 
		 snprtz, bot, spider, Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1),
		feedzirra,Feedburner, AppEngine-Google,rome client,chifanblog,blogsearch,ips-agent,crawler";
	else
	$options['bots'] = $st_options['bots'];
	if ($st_options['ipblock']=='')
	$options['ipblock'] = "";
	else
	$options['ipblock'] = $st_options['ipblock'];
	if ($st_options['is_direct']=='')
	$options['is_direct'] = "n";
	else
	$options['is_direct'] = $st_options['is_direct'];
	$options['version'] = "1.1";
	update_option('st_options', $options);
	$m_options['span'] = 4;
	$m_options['visits'] = 6;
	update_option('st_mining_options', $m_options);
}
?>
