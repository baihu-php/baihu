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

class m2_area extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	public static function depends_on(): array
	{
		return [core::MAIN_MIGRATION];
	}

	/**
	* Add the initial data in the database
	*/
	public function update_data(): array
	{
		return [
			['custom', [[$this, 'add_area_data']]],
		];
	}

	/**
	* Custom function to add area data
	*/
	public function add_area_data(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . core::AREAZ))
		{
			$items = [
				[
					'cat'	 => core::AREA_DASHBOARD,
					'title'	 => '',
					'type'	 => core::TYPE,
					'parent' => 1,
					'auth'	 => '',
					'route'	 => '',
					'crud'	 => 0,
					'icon'	 => '',
				],
				[
					'cat'	 => core::AREA_CONFIG,
					'title'	 => '',
					'type'	 => core::TYPE,
					'parent' => 1,
					'auth'	 => '',
					'route'	 => '',
					'crud'	 => 0,
					'icon'	 => 'fa--cogs',
				],
				[
					'cat'	 => core::AREA_PLUGINS,
					'title'	 => '',
					'type'	 => core::TYPE,
					'parent' => 1,
					'auth'	 => '',
					'route'	 => '',
					'crud'	 => 0,
					'icon'	 => 'dashicons--plugins',
				],
				[
					'cat'	 => core::AREA_DASHBOARD,
					'title'	 => 'AREA_MAIN_PAGE',
					'type'	 => core::TYPE,
					'parent' => 0,
					'auth'	 => '',
					'route'	 => 'areaz_main',
					'crud'	 => 0,
					'icon'	 => 'mdi--view-dashboard-outline',
				],
				[
					'cat'	 => core::AREA_CONFIG,
					'title'	 => 'SETTINGS',
					'type'	 => core::TYPE,
					'parent' => 0,
					'auth'	 => '',
					'route'	 => 'areaz_settings',
					'crud'	 => 0,
					'icon'	 => '',
				],
				[
					'cat'	 => core::AREA_CONFIG,
					'title'	 => 'PLUGINS',
					'type'	 => core::TYPE,
					'parent' => 0,
					'auth'	 => '',
					'route'	 => 'areaz_plugins',
					'crud'	 => 0,
					'icon'	 => '',
				],
				[
					'cat'	 => core::AREA_CONFIG,
					'title'	 => 'PAGES',
					'type'	 => core::TYPE,
					'parent' => 0,
					'auth'	 => '',
					'route'	 => 'areaz_pages',
					'crud'	 => 0,
					'icon'	 => '',
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . core::AREAZ);

			foreach ($items as $item)
			{
				$insert_buffer->insert($item);
			}

			$insert_buffer->flush();
		}
	}
}
