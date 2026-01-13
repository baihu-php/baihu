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
	'BAIHU_TOOLS'	=> 'Tools',

	// Profile tabs
	'CURRENT_PROFILE_TAB' => 'Your %s',
	'PROFILE_TAB'	=> '%s [%s]',
	'BAIHU_PROFILE' => 'Overview',
	'BAIHU_FRIENDS' => 'Friends',

	// Profile posts
	'BAIHU_POSTED_IN' => [
		0 => 'Replied to a topic in',
		1 => 'Posted a topic in',
	],

	'BAIHU_POSTS_IN_QUEUE' => [
		0 => 'No posts in queue',
		1 => 'Post in queue',
		2 => 'Posts in queue',
	],

	'BAIHU_NO_ACTIVITY'	 => 'No activity at current moment',
	'BAIHU_NO_FRIENDS'	 => 'No friends at the moment',
]);
