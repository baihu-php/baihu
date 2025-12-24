<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin;

use baihu\baihu\src\enum\core;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\di\service_collection;

final class loader
{
	protected array $plugins = [];

	public function __construct(
		protected driver_interface $db,
		protected service_collection $plugins_collection,
		public readonly data $data,
		protected readonly string $plugins_table,
		protected readonly string $plugins_on_page_table
	)
	{
	}

	/**
	 * @param string $page_name
	 * @param object $config
	 */
	public function load_available_plugins(string $page_name, config $config): void
	{
		$this->get_requested_plugins($page_name, $config);

		if (count($this->plugins))
		{
			foreach ($this->plugins as $item)
			{
				$item->load_plugin();
			}
		}
	}

	/**
	 * @param string $page_name
	 * @param object $config
	 */
	protected function get_requested_plugins(string $page_name, config $config): void
	{
		$sql_array = [
			'SELECT'	=> 'p.name, p.ext_name, p.section, op.page_name',
			'FROM'		=> [
				$this->plugins_table => 'p',
				$this->plugins_on_page_table => 'op',
			],
			'WHERE'		=> "p.name = op.name
				AND op.page_name = '" . $this->db->sql_escape($page_name)  . "'" . '
				AND op.active = 1',
			'ORDER_BY'	=> 'p.position',
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql, 86400);

		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($config[$row['section']])
			{
				$this->set_plugin_data($row);
			}
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * @param array $row Plugins data array
	 */
	private function set_plugin_data(array $row): void
	{
		if (null === $plugin = $this->plugins_collection[$this->get_service_name($row['name'], $row['ext_name'])] ?? null)
		{
			return;
		}

		if ($plugin->loadable)
		{
			$this->plugins[$row['name']] = $plugin;
		}

		if ($plugin->type === 'block')
		{
			$name = $this->remove_gzo_prefix($row['name'], $row['ext_name']);

			$this->data->set_section_data($row['section'], $name, $row['ext_name']);
		}
	}

	/**
	 * @param string $service
	 * @param string $ext_name
	 */
	public function get_service_name(string $service, string $ext_name): string
	{
		return str_replace('_', '.', $ext_name) . '.plugin.' . utf8_substr($service, utf8_strpos($service, '_') + 1);
	}

	/**
	 * @param string $name
	 * @param string $ext_name
	 */
	public function remove_gzo_prefix(string $name, string $ext_name): string
	{
		return str_contains($ext_name, core::VENDOR) ? str_replace(core::VENDOR . '_', '', $name) : $name;
	}
}
