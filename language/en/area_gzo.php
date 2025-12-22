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
	// Categories
	'GZO_CONFIG'   => 'Configurations',
	'GZO_PLUGINS'  => 'Plugins',

	// Pages
	'GZO_MAIN_PAGE_DESC' => 'Welcome to GZO Admin Dashboard!',

	'GZO_SETTINGS' => 'Global settings',
	'GZO_PAGES'	   => 'Page settings',

	'GZO_PHP'	   => 'PHP version',
	'GZO_BOARD'	   => 'Board version',
]);
