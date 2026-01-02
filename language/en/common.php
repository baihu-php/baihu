<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	// Navigation
	'DASHBOARD'		 => 'Dashboard',
	'AREA_MAIN_PAGE' => 'Baihu AreaZ',
	'BAIHU_CREATE'	 => 'Create',
	'BAIHU_FORUM'	 => 'New forum topic',

	// Drawer
	'DRAWER_GENERAL' => 'General',
	'DRAWER_LEGAL'	 => 'Help & Legal',

	'BAIHU_LIKE' => 'Like',

	'NEWS' => 'News',

	'LATEST_NEWS'  => 'Viewing category',
	'ARTICLE'	   => 'Viewing article',
	'READ_FULL'	   => 'Read full article',
	'CATEGORIES'   => 'Categories',
	'NEW_ARTICLE'  => 'New article',
	'POST_ARTICLE' => 'Post new article',
	'VIEW_NEWS'	   => 'News id - %s',
	'VIEW_ARTICLE' => 'Article id - %s',

	'UNKNOWN' => 'Unknown',
	'year'	  => '%d year',
	'month'	  => '%d month',
	'week'	  => '%d week',
	'day'	  => '%d day',
	'hour'	  => '%d hour',
	'minute'  => '%d minute',
	'second'  => '%d second',
	'time_ago' => [
		1 => '%2$s ago',
		2 => '%2$ss ago',
	],

	// 'GZO_PROFILE' => 'Overview',
	'CURRENT_USERS_PROFILE_TAB' => 'Your %s',
	'USERS_PROFILE_TAB' => '%s / %s',

	'BAIHU_STATS_PER_DAY' => '%s per day',
	'BAIHU_PER_DAY' => 'Per day',

	'LEADERS'		=> 'Top posters',
	'RECENT_POSTS'	=> 'Recent posts',
	'RECENT_TOPICS' => 'Recent topics',

	'WELCOME' => 'Welcome back, ',
	'WELCOME_GUEST' => 'Welcome guest',
	'NEW_PM'  => ' new message',
	'NEW_PMS' => ' new messages',

	// Statistics
	'BAIHU_TOPICS'	=> 'Topics',
	'BAIHU_MEMBERS' => 'Members',
	'BAIHU_NEWEST'	=> 'Newest',

	'IN_TOPIC'	=> 'In ',

	'DAYS_HERE' => 'Membership',
	'PROGRESS'	=> 'Progress',
	'LEVEL'		=> 'Level',

	'STATUSES'	=> [
		0 => 'Fresh As A Mint',
		1 => 'Self Made',
	],

	'STATUS' => 'Status: %s',

	// Auth
	'AREA_NO_ADMIN'	   => 'You are not authorized to access this area!',
	'AREA_USER_ACCESS' => 'You do not have permissions to access this area!',
]);
