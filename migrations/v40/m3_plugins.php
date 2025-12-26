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

class m3_plugins extends \phpbb\db\migration\migration
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
			['custom', [[$this, 'add_plugins_data']]],
		];
	}

	public function add_plugins_data(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . core::PLUGINS))
		{
			$plugins = [
				[
					'name'	   => core::PROFILE,
					'ext_name' => core::EXT_NAME,
					'position' => 1,
					'section'  => core::SIDE,
				],
				[
					'name'	   => core::GROUP,
					'ext_name' => core::EXT_NAME,
					'position' => 2,
					'section'  => core::SIDE,
				],
				[
					'name'	   => core::POSTER,
					'ext_name' => core::EXT_NAME,
					'position' => 3,
					'section'  => core::SIDE,
				],
				[
					'name'	   => core::POSTS,
					'ext_name' => core::EXT_NAME,
					'position' => 4,
					'section'  => core::SIDE,
				],
				[
					'name'	   => core::TOPICS,
					'ext_name' => core::EXT_NAME,
					'position' => 5,
					'section'  => core::SIDE,
				],
				[
					'ext_name' => core::EXT_NAME,
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . core::PLUGINS);
			foreach ($plugins as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
