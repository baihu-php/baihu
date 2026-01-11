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
	'DRAWER_GENERAL'  => 'General',
	'DRAWER_LEGAL'	  => 'Help & Legal',

	'BAIHU_READ_FULL' => 'Read full post',

	// Time ago
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

	// Blocks
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
	'BAIHU_STATS_PER_DAY' => '%s per day',
	'BAIHU_PER_DAY' => 'Per day',

	'BAIHU_CONTACT'	=> 'Contact',

	'IN_TOPIC'	 => 'In',

	'BAIHU_LIKE' => 'Like',
	'BAIHU_UC'	 => 'Under construction...',

	// Auth
	'BAIHU_LOGIN'	   => 'You need to login to access this page.',
	'AREA_NO_ADMIN'	   => 'You are not authorized to access this area!',
	'AREA_USER_ACCESS' => 'You do not have permissions to access this area.',
]);
