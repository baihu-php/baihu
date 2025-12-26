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

class m4_plugins_on_page extends \phpbb\db\migration\migration
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
			['custom', [[$this, 'add_plugins_page_data']]],
		];
	}

	public function add_plugins_page_data(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . core::PLUGINS_ON_PAGE))
		{
			$on_page = [
				[
					'name'		=> core::PROFILE,
					'page_name' => core::PAGE,
					'active'	=> true,
					'dynamic'	=> false,
				],
				[
					'name'		=> core::STATS,
					'page_name' => core::PAGE,
					'active'	=> false,
					'dynamic'	=> false,
				],
				[
					'name'		=> core::GROUP,
					'page_name' => core::PAGE,
					'active'	=> false,
					'dynamic'	=> false,
				],
				[
					'name'		=> core::POSTER,
					'page_name' => core::PAGE,
					'active'	=> true,
					'dynamic'	=> false,
				],
				[
					'name'		=> core::POSTS,
					'page_name' => core::PAGE,
					'active'	=> true,
					'dynamic'	=> false,
				],
				[
					'name'		=> core::TOPICS,
					'page_name' => core::PAGE,
					'active'	=> false,
					'dynamic'	=> false,
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . core::PLUGINS_ON_PAGE);

			foreach ($on_page as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
