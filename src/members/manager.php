<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\members;

use phpbb\di\service_collection;
use phpbb\controller\helper as controller_helper;
use phpbb\language\language;
use phpbb\template\template;

/**
* Tabs manager
*/
class manager
{
	protected static array $tabs = [];

	public function __construct(
		private service_collection $collection,
		private controller_helper $controller_helper,
		private language $language,
		private template $template
	)
	{
		if ($collection)
		{
			foreach ($collection as $tab)
			{
				self::$tabs[$tab->get_name()] = $tab;
			}
		}
	}

	/**
	* Get tab type by name
	*/
	public function get(string $name): object
	{
		return self::$tabs[$name] ?? (object) [];
	}

	/**
	* Get all available tabs
	*/
	public function available(): array
	{
		return array_keys(self::$tabs) ?? [];
	}

	/**
	* Remove tab
	*/
	public function remove(string $name): void
	{
		if (isset(self::$tabs[$name]) || array_key_exists($name, self::$tabs))
		{
			unset(self::$tabs[$name]);
		}
	}

	/**
	* Generate menu for tabs
	*/
	public function generate_tabs_menu(string $username, string $tab): void
	{
		if (count($this->available()) === 1)
		{
			return;
		}

		foreach ($this->available() as $tab)
		{
			$route = $this->controller_helper->route('ganstaz_gzo_member_tab', ['username' => $username, 'tab' => $tab]);
			if ($tab === 'profile')
			{
				$route = $this->controller_helper->route('ganstaz_gzo_member', ['username' => $username]);
			}

			$this->template->assign_block_vars('tabs', [
				'title' => $this->language->lang('GZO_' . strtoupper($tab)),
				'link' => $route,
				'icon' => $this->get($tab)->icon(),
			]);
		}
	}

	public function generate_breadcrumb(string $username, string $tab): void
	{
		$this->controller_helper->assign_breadcrumb('MEMBERLIST', 'ganstaz_gzo_members')
			->assign_breadcrumb($username, 'ganstaz_gzo_member', ['username' => $username]);

		if ($tab !== 'profile')
		{
			$this->controller_helper->assign_breadcrumb(ucfirst($tab), 'ganstaz_gzo_member_tab', ['username' => $username, 'tab' => $tab]);
		}
	}
}
