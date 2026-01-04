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
use baihu\baihu\src\enum\core;
use baihu\baihu\src\plugin\profile\loader as tabs_loader;
// phpcs:disable
use baihu\baihu\src\security\attribute\is_granted as isGranted;
// phpcs:enable
use Symfony\Component\HttpFoundation\Response;

#[isGranted('LIMITED', ['u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'])]
class profile_controller extends abstract_controller
{
	public static function getSubscribedServices(): array
	{
		return array_merge(parent::getSubscribedServices(), [
			'baihu.profile.tabs.loader' => '?'.tabs_loader::class,
		]);
	}

	public function index($username, $tab): Response
	{
		// Load language
		$this->language->add_lang('memberlist');

		$tabs_loader = $this->container->get('baihu.profile.tabs.loader');
		$tabs_loader->generate_tabs_menu($username, $tab);
		$tabs_loader->generate_breadcrumb($username, $tab);

		// Load requested tab
		$current_tab = $tabs_loader->get($tab);
		$current_tab->load($username);

		$page_title = $this->get_user()->data['username'] === $username
			? $this->language->lang('CURRENT_PROFILE_TAB', ucfirst($tab))
			: $this->language->lang('PROFILE_TAB', $username, ucfirst($tab));
		$page_title = $tab === core::DEFAULT_TAB_NAME ? $page_title : $username;

		return $this->render("{$current_tab->get_namespace()}$tab.twig", $page_title, 200, true);
	}
}
