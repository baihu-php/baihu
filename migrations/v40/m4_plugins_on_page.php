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

use baihu\baihu\src\enum\baihu;

class m4_plugins_on_page extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritdoc}
	 */
	public static function depends_on(): array
	{
		return [baihu::MAIN_MIGRATION];
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
		if ($this->db_tools->sql_table_exists($this->table_prefix . baihu::PLUGINS_ON_PAGE))
		{
			$on_page = [
				[
					'name'		=> baihu::PROFILE,
					'page_name' => baihu::PAGE,
					'active'	=> 1,
				],
				[
					'name'		=> baihu::GROUP,
					'page_name' => baihu::PAGE,
					'active'	=> 1,
				],
				[
					'name'		=> baihu::POSTER,
					'page_name' => baihu::PAGE,
					'active'	=> 1,
				],
				[
					'name'		=> baihu::POSTS,
					'page_name' => baihu::PAGE,
					'active'	=> 1,
				],
				[
					'name'		=> baihu::TOPICS,
					'page_name' => baihu::PAGE,
					'active'	=> 0,
				],
				[
					'name'		=> baihu::ONLINE,
					'page_name' => baihu::PAGE,
					'active'	=> 1,
				],
				[
					'name'		=> baihu::INFO,
					'page_name' => baihu::PAGE,
					'active'	=> 1,
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . baihu::PLUGINS_ON_PAGE);

			foreach ($on_page as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
