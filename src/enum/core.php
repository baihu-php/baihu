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

final class core
{
	// Common
	public const VERSION = '4.0.0-dev';
	public const STYLE = 'Tempest';
	public const VENDOR = 'baihu';
	public const EXT_NAME = self::VENDOR . '_baihu';
	public const DATE_FORMAT = 'Y-m-d H:i';
	public const MAIN_MIGRATION = '\baihu\baihu\migrations\v40\m1_main';

	// Area
	public const TYPE = 'areaz';
	public const IN_AREAZ = 'IN_AREAZ';
	public const AREA_DEFAULT_ICON = 'AREA_DEFAULT_ICON';
	public const AREA_DASHBOARD = 'DASHBOARD';
	public const AREA_CONFIG = 'CONFIG';
	public const AREA_PLUGINS = 'PLUGINS';

	// Tables
	public const AREAZ = self::VENDOR . '_areaz';
	public const PLUGINS = self::VENDOR . '_plugins';
	public const PLUGINS_ON_PAGE = self::VENDOR . '_plugins_on_page';

	// Plugins
	public const PLUGIN_PREFIX = 'baihu.baihu.plugin.';
	public const PROFILE = self::PLUGIN_PREFIX . 'mini_profile';
	public const GROUP = self::PLUGIN_PREFIX . 'group';
	public const POSTER = self::PLUGIN_PREFIX . 'top_posters';
	public const POSTS = self::PLUGIN_PREFIX . 'recent_posts';
	public const TOPICS = self::PLUGIN_PREFIX . 'recent_topics';

	// Pages
	public const PAGE = 'app';

	// Sections
	public const TOP = self::VENDOR . '_top';
	public const SIDE = self::VENDOR . '_side';
	public const BOTTOM = self::VENDOR . '_bottom';
}
