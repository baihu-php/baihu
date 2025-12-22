<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\enum;

final class baihu
{
	// Common
	public const VERSION = '4.0.0-dev';
	public const STYLE = 'Tempest';
	public const VENDOR = 'baihu';
	public const EXT_NAME = 'baihu_baihu';
	public const DATE_FORMAT = 'Y-m-d H:i';
	public const MAIN_MIGRATION = '\baihu\baihu\migrations\v40\m1_main';

	// Area
	public const TYPE = 'baihu';
	public const AREA_DASHBOARD = 'DASHBOARD';
	public const AREA_CONFIG = 'GZO_CONFIG';
	public const AREA_PLUGINS = 'GZO_PLUGINS';

	// Tables
	public const AREAZ = 'baihu_areaz';
	public const PLUGINS = 'baihu_plugins';
	public const PLUGINS_ON_PAGE = 'baihu_plugins_on_page';

	// Plugins
	public const PROFILE = self::VENDOR . '_mini_profile';
	public const GROUP = self::VENDOR . '_group';
	public const POSTER = self::VENDOR . '_top_posters';
	public const POSTS = self::VENDOR . '_recent_posts';
	public const TOPICS = self::VENDOR . '_recent_topics';
	public const ONLINE = self::VENDOR . '_online';
	public const INFO = self::VENDOR . '_information';

	// Pages
	public const PAGE = 'app';

	// Sections
	public const TOP = 'baihu_top';
	public const SIDE = 'baihu_side';
	public const BLOCK = 'baihu_block';
	public const BOTTOM = 'baihu_bottom';
}
