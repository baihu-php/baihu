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
	'CONFIG'   => 'Configurations',
	'PLUGINS'  => 'Plugins',

	// Pages
	'AREA_MAIN_PAGE_DESC' => 'Welcome to GZO Admin Dashboard!',

	'SETTINGS' => 'Global settings',
	'PAGES'	   => 'Page settings',

	// Inforomation
	'AREA_BAIHU_INFO' => 'Baihu version',
	'AREA_STYLE_INFO' => 'Baihu style',
	'AREA_PHP_INFO'   => 'PHP version',
	'AREA_BOARD_INFO' => 'Board version',
]);
