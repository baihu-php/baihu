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

class m3_plugins extends \phpbb\db\migration\migration
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
			['custom', [[$this, 'add_plugins_data']]],
		];
	}

	public function add_plugins_data(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . baihu::PLUGINS))
		{
			$plugins = [
				[
					'name'	   => baihu::PROFILE,
					'ext_name' => baihu::EXT_NAME,
					'position' => 1,
					'section'  => baihu::SIDE,
				],
				[
					'name'	   => baihu::GROUP,
					'ext_name' => baihu::EXT_NAME,
					'position' => 2,
					'section'  => baihu::SIDE,
				],
				[
					'name'	   => baihu::POSTER,
					'ext_name' => baihu::EXT_NAME,
					'position' => 3,
					'section'  => baihu::SIDE,
				],
				[
					'name'	   => baihu::POSTS,
					'ext_name' => baihu::EXT_NAME,
					'position' => 4,
					'section'  => baihu::SIDE,
				],
				[
					'name'	   => baihu::TOPICS,
					'ext_name' => baihu::EXT_NAME,
					'position' => 5,
					'section'  => baihu::SIDE,
				],
				[
					'name'	   => baihu::ONLINE,
					'ext_name' => baihu::EXT_NAME,
					'position' => 1,
					'section'  => baihu::BOTTOM,
				],
				[
					'name'	   => baihu::INFO,
					'ext_name' => baihu::EXT_NAME,
					'position' => 2,
					'section'  => baihu::SIDE,
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . baihu::PLUGINS);
			foreach ($plugins as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
