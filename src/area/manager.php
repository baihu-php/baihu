<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\area;

use baihu\baihu\src\enum\core;
use baihu\baihu\src\event\events;
use baihu\baihu\src\controller\controller_helper;
use phpbb\auth\auth;
use phpbb\cache\service as cache;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\template\template;

final class manager
{
	protected readonly string $type;
	protected array|bool $areas = [];
	protected array|bool $navigation = [];
	protected array $icons = ['GZO_DEFAULT' => 'ic--outline-home'];

	public function __construct(
		protected auth $auth,
		protected cache $cache,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected template $template,
		protected controller_helper $controller_helper,
		protected readonly string $table
	)
	{
	}

	public function available(string $type): bool
	{
		$areas = $this->areas;

		/** @event events::BAIHU_AREA_MODIFY_DATA */
		$vars = ['areas'];
		extract($this->dispatcher->trigger_event(events::BAIHU_AREA_MODIFY_DATA, compact($vars)));

		$this->areas = $areas;
		unset($areas);

		return isset($this->areas[$type]);
	}

	public function authorize(string $type): bool
	{
		$auth = $this->get_area_type($type)['auth'] ?? '';
		$this->type = $type;

		return $auth ? $this->auth->acl_get($auth) : true;
	}

	public function get_area_type(string $type): array
	{
		return $this->areas[$type] ?? [];
	}

	public function all(): array
	{
		return array_keys($this->areas);
	}

	public function build_area_data(): self
	{
		$area = $this->get_area_type($this->type);

		$this->controller_helper->add_language($area['lang'], $area['ext_name']);

		if (($this->navigation = $this->cache->get('_baihu_areaz')) === false)
		{
			$sql = 'SELECT *
					FROM ' . $this->table . core::AREAZ . '
					ORDER BY id';
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->navigation[$row['type']][$row['cat']][] = $row;
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_baihu_areaz', $this->navigation);
		}

		foreach ($this->navigation[$this->type] as $cat => $data)
		{
			$this->filter_navigation_data($cat, $data);
		}

		$this->assign_template_data($area['dashboard'], $area['route']);

		return $this;
	}

	protected function filter_navigation_data(string $cat, array $data): void
	{
		foreach ($data as $key => $row)
		{
			if ($row['parent'])
			{
				$this->set_category_icon($row['cat'], $row['icon']);
				unset($this->navigation[$this->type][$cat][$key]);
			}

			// Unset Area controller if user doesn't have permissions to view it
			if ($row['auth'] && !$this->auth->acl_get($row['auth']))
			{
				unset($this->navigation[$this->type][$cat][$key]);
			}
		}
	}

	protected function assign_template_data(string $breadcrumb_name, string $breadcrumb_route): void
	{
		$this->controller_helper->assign_breadcrumb($breadcrumb_name, $breadcrumb_route);

		$icons = $this->icons;
		$type = $this->type;
		$navigation = $this->navigation[$type];

		/** @event events::BAIHU_AREA_MODIFY_NAVIGATION */
		$vars = ['icons', 'navigation', 'type'];
		extract($this->dispatcher->trigger_event(events::BAIHU_AREA_MODIFY_NAVIGATION, compact($vars)));

		$this->icons = $icons;
		$this->navigation = $navigation;
		unset($navigation, $icons, $type);

		foreach ($this->navigation as $category => $data)
		{
			$this->template->assign_block_vars('menu', [
				'heading' => $category,
				'icon'	  => $this->icons[$category] ?? $this->icons['GZO_DEFAULT'],
			]);

			foreach ($data as $item)
			{
				$this->template->assign_block_vars('menu.item', [
					'title' => $item['title'],
					'route' => $item['route'],
					'icon'	=> $item['icon'] ?? ''
				]);
			}
		}
	}

	protected function set_category_icon(string $name, string $icon): self
	{
		if ($icon && !isset($this->icons[$name]))
		{
			$this->icons[$name] = $icon;
		}

		return $this;
	}

	public function set_template_var(bool $value): void
	{
		$this->template->assign_var(core::IN_AREAZ, $value);
	}
}
