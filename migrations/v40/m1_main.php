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

class m1_main extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritdoc}
	 */
	public function effectively_installed(): bool
	{
		return $this->check(core::AREAZ) && $this->check(core::PLUGINS) && $this->check(core::PLUGINS_ON_PAGE);
	}

	/**
	 * Check if given table exists or not
	 */
	public function check(string $name): bool
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . $name);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function depends_on(): array
	{
		return ['\phpbb\db\migration\data\v400\dev'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_schema(): array
	{
		return [
			'add_tables' => [
				$this->table_prefix . core::AREAZ => [
					'COLUMNS' => [
						'id'	 => ['UINT', null, 'auto_increment'],
						'cat'	 => ['VCHAR', ''],
						'title'	 => ['VCHAR', ''],
						'type'	 => ['VCHAR', ''],
						'parent' => ['BOOL', 0],
						'auth'	 => ['VCHAR', ''],
						'route'	 => ['VCHAR', ''],
						'crud'	 => ['BOOL', 0],
						'icon'	 => ['VCHAR', ''],
					],
					'PRIMARY_KEY' => ['id'],
				],
				$this->table_prefix . core::PLUGINS => [
					'COLUMNS' => [
						'id'	   => ['UINT', null, 'auto_increment'],
						'name'	   => ['VCHAR', ''],
						'ext_name' => ['VCHAR:255', ''],
						'section'  => ['VCHAR:255', ''],
					],
					'PRIMARY_KEY' => ['id'],
				],
				$this->table_prefix . core::PLUGINS_ON_PAGE => [
					'COLUMNS' => [
						'id'		=> ['UINT', null, 'auto_increment'],
						'name'		=> ['VCHAR', ''],
						'page_name' => ['VCHAR:255', ''],
						'position'	=> ['UINT:10', 0],
						'active'	=> ['BOOL', 0],
						'dynamic'	=> ['BOOL', 0],
					],
					'PRIMARY_KEY' => ['id'],
				],
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function revert_schema(): array
	{
		return [
			'drop_tables' => [
				$this->table_prefix . core::AREAZ,
				$this->table_prefix . core::PLUGINS,
				$this->table_prefix . core::PLUGINS_ON_PAGE,
			],
		];
	}
}
