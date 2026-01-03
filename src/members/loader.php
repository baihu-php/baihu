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

use baihu\baihu\src\controller\controller_helper;
use phpbb\di\service_collection;
use phpbb\language\language;
use phpbb\template\template;

class loader
{
	protected static array $tabs = [];

	public function __construct(
		protected service_collection $collection,
		protected controller_helper $controller_helper,
		protected language $language,
		protected template $template
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

	public function get(string $name): object
	{
		return self::$tabs[$name] ?? (object) [];
	}

	public function available(): array
	{
		return array_keys(self::$tabs) ?? [];
	}

	public function generate_tabs_menu(string $username, string $tab): void
	{
		if (count($this->available()) === 1)
		{
			return;
		}

		foreach ($this->available() as $tab)
		{
			$route = $this->controller_helper->route('baihu_member_tab', ['username' => $username, 'tab' => $tab]);
			if ($tab === 'profile')
			{
				$route = $this->controller_helper->route('baihu_member', ['username' => $username]);
			}

			$this->template->assign_block_vars('tabs', [
				'title' => $this->language->lang('GZO_' . strtoupper($tab)),
				'link' => $route,
				'icon' => $this->get($tab)->get_icon(),
			]);
		}
	}

	public function generate_breadcrumb(string $username, string $tab): void
	{
		$this->controller_helper->assign_breadcrumb('MEMBERLIST', 'baihu_members_redirect')
			->assign_breadcrumb($username, 'baihu_member', ['username' => $username]);

		if ($tab !== 'profile')
		{
			$this->controller_helper->assign_breadcrumb(ucfirst($tab), 'baihu_member_tab', ['username' => $username, 'tab' => $tab]);
		}
	}
}
