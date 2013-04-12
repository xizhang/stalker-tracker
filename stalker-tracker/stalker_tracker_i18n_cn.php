<?php
$st_lang=array(
	'menu_help_link'=>"http://chifanblog.net/?p=625",
	'menu_fingerprint'=>"指纹",
	'menu_timezone'=>"请先去WordPress设置页面设置您的时区",
	'menu_stalker'=>"读者",
	'menu_time'=>"时间",
	'menu_referer'=>"来源",
	'menu_agent'=>"客户端",
	'menu_trends'=>"每日流量",
	'menu_hourly'=>"时间分布",
	'menu_days'=>"天",
	'menu_hours'=>"小时",
	'menu_visits'=>"人数",
	'menu_menu'=>"选项",
	'menu_stalker_list'=>"读者列表",
	'menu_mining'=>"找出潜在读者",
	'menu_comments'=>"读者留言",
	'menu_settings'=>"设置",
	'menu_help'=>"使用说明",
	'menu_no_data'=>"安装插件之后还没有人访问过你的博客，请耐心等待。",
	'menu_delete' => "删除读者访问记录",
	'menu_stats' => "统计数据",
	'menu_os' => "操作系统",
	'menu_browser' => "浏览器",
	'menu_count' => "总访问次数",
	'menu_denied' => "访问被阻止",
	'menu_granted' => "成功访问",
	'menu_access_greet' => "向读者发消息",
	'menu_access_send' => "读者回复消息",
	'greet_blogger' => "博主消息",
	'greet_response' => "你的回复（回复之后显示博客内容）",
	'greet_send' => "发送消息",
	'greet_sent' => "消息已发送",
	'comments_no_data'=>"安装插件之后还没有人留过言。",
	'title_stalker'=>"查看读者记录",
	'title_cookie'=>"查看指纹记录",
	'title_ip'=>"查看IP记录",
	'title_iplocation'=>"找出IP地理位置",
	'fp_belong'=>'这是%s的指纹',
	'fp_remove'=>'删除',
	'fp_add'=>'添加',
	'fp_create'=>'创建',
	'fp_add_to'=>'添加到读者',
	'fp_create_new'=>'创建一个新的读者',
	'stalker_name'=>'姓名',
	'stalker_stat'=>'是否包括在统计数据中',
	'stalker_greet'=>'是否跟这个读者发信息',
	'stalker_block'=>'是否封锁这个读者',
	'stalker_update'=>'更新读者设置',
	'stalker_other_fp'=>'以下指纹可能也属于这个读者',
	'stalker_default_greet' => '你好%s，最近过得怎么样？',
	'stalker_response' => '读者回复消息',
	'stalker_no_data' => '该读者还没有访问记录',
	'stalkers_visits'=>'访问量',
	'stalkers_name'=>'读者',
	'stalkers_last_visit' => '最后访问',
	'stalkers_chk'=>'选择',
	'stalkers_fp'=>'指纹数量',
	'stalkers_is_stats'=>'计入统计',
	'stalkers_is_block'=>'访问权限',
	'stalkers_del'=>'删除读者',
	'stalkers_del_visits'=>'删除读者访问记录',	
	'stalkers_greet' => '消息',
	'stalkers_no_data'=>'目前你还没有辨认出任何读者',
	'mining_1' => '<div>从陌生指纹中，根据可疑程度不同，找出潜在的读者. 
				图例:	<span style="color:#ff0000">很可疑</span>,  
				<span style="color:#ff7f00">有点可疑</span>,  
				<span style="color:#ffff00">不太可疑</span>.</div>',
	'mining_visits' => '访问量',
	'mining_frequency' => '访问频率',
	'mining_span' => '时间跨度',
	'mining_span_option' => '显示时间跨度超过%s天的',
	'mining_visits_option' => '显示访问数量超过%s次的',
	'mining_refresh' => '刷新',
	'mining_no_data' => '目前没有可疑的指纹', 
	'settings_home' => "在首页上，分析最近%s天的数据。",
	'settings_page' => '对于读者、指纹等页面，分析最近%s天的数据。',
	'settings_list' => '在列表中显示%s条数据。',
	'settings_bots' => "过滤含有以下关键字的客户端（以半角逗号分隔）",
	'settings_update' => "更新设置",
	'settings_ip' => "封锁下列IP（以半角逗号分隔，如127.0.0.*）",
	'settings_direct' => "首页上只显示直接访问记录",
	'delete_title' => "删除包含有下列信息的记录",
	'delete_search' => "查找记录",
	'delete_delete' => "删除以下记录",
	'delete_days' => "删除%s天以前的记录",
	'delete_count' => "记录数量",
	'widget_title' => '最近来访',
	'timediff_now' => '正是现在',
	'timediff_days' => '天之前',
	'timediff_seconds' => '秒之前',
	'timediff_minutes' => '分钟之前',
	'timediff_hours' => '小时之前',
	'timediff_weeks' => '周之前',
	'help_content' =>"<div style='width:60%'>
		<p><b>Stalker Tracker 是用来监视博客读者的。</b></p><p>使用流程如下:</p>
		<ol><li>读者访问博客，留下一个指纹。</li>
		<li>通过IP地址、留言或其它信息，确定读者身份。</li>
		<li>监视该读者其他行为。</li></ol>
		<p><b>访问</b></p>
		
		<p>插件激活以后，所有对博客的访问都会留下一个指纹。但有的时候，是搜索引擎的机器人访问博客，一般我们不希望记录这样的信息。
		可以通过“设置”页面，添加该搜索引擎的关键词到。把鼠标悬停在表的“客户端”栏上，可以看到客户端的信息。
		从中你可以自己提取一个关键词，比如msnbot这样的，添加到设置里。</p>
		<img src='".ST_BASEFOLDER."img/help_visits.jpg'/>
		<p><b>确定身份</b></p>
		<p>去“找出潜在读者”页面，可以查出哪个指纹经常访问你的博客。点击IP地址旁边的问号可以查询IP地址的来源。
		然后你可以自己猜猜看这些指纹属于谁。点击指纹，然后添加读者姓名。</p>
		<img src='".ST_BASEFOLDER."img/help_mining.jpg'/>
		<p><b>监视：</b></p>
		<p>确定了读者身份之后，你可以继续监视这个读者其他的活动，还可以给他/她发信息。</p>
		<img src='".ST_BASEFOLDER."img/help_stats.jpg'/>
		<p>---- 作者: <a href='http://chifanblog.net/?p=582'>Xi Zhang</a></p></div>",
);

?>