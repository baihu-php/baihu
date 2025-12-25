<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\migrations\v40;

use baihu\baihu\src\enum\core;

class m5_config extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritdoc}
	 */
	public static function depends_on(): array
	{
		return [core::MAIN_MIGRATION];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data(): array
	{
		return [
			['config.add', ['baihu_fid', 2]],

			['config.add', ['baihu_pagination', false]],
			['config.add', ['baihu_title_length', 26]],
			['config.add', ['baihu_content_length', 150]],

			['config.add', ['baihu_limit', 5]],
			['config.add', ['baihu_users_per_list', 10]],

			['config.add', ['baihu_app_global', false]],
			// Plugins
			['config.add', ['baihu_the_team_fid', 5]],
			// ['config.add', ['baihu_top_posters_fid', 0]],
			// ['config.add', ['baihu_recent_posts_fid', 0]],
			// ['config.add', ['baihu_recent_topics_fid', 0]],

			['config.add', [core::PLUGINS, true]],
			['config.add', [core::SIDE, true]],
			['config.add', [core::TOP, true]],
			['config.add', [core::BOTTOM, true]],
		];
	}
}
