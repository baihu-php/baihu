<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\controller\member;

use baihu\baihu\src\controller\abstract_controller;
use baihu\baihu\src\members\loader as tabs_loader;
use Symfony\Component\HttpFoundation\Response;

class profile_controller extends abstract_controller
{
	public static function getSubscribedServices(): array
	{
		return array_merge(parent::getSubscribedServices(), [
			'baihu.members.tabs.loader' => '?'.tabs_loader::class,
		]);
	}

	public function index($username, $tab): Response
	{
		// Load language
		$this->language->add_lang('memberlist');

		$manager = $this->container->get('baihu.members.tabs.loader');
		$manager->generate_tabs_menu($username, $tab);
		$manager->generate_breadcrumb($username, $tab);

		$current = $manager->get($tab);
		$current->load($username);

		$current_tab = $this->language->lang(ucfirst($tab));
		$page_title = $current->is_active_session() ? $this->language->lang('CURRENT_USERS_PROFILE_TAB', $current_tab) : $this->language->lang('USERS_PROFILE_TAB', $username, $current_tab);

		$page_title = $tab !== 'profile' ? $page_title : $username;

		return $this->render("{$current->get_namespace()}$tab.twig", $page_title, 200, true);
	}
}
