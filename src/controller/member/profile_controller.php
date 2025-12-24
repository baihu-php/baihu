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
use baihu\baihu\src\members\manager;
use Symfony\Component\HttpFoundation\Response;

class profile_controller extends abstract_controller
{
	public function __construct(protected manager $manager)
	{
	}

	public function index($username, $tab): Response
	{
		$controller_helper = $this->get_controller_helper();
		$language = $controller_helper->get_language();

		// Load language
		$language->add_lang('memberlist');

		$this->manager->generate_tabs_menu($username, $tab);
		$this->manager->generate_breadcrumb($username, $tab);

		$current = $this->manager->get($tab);
		$current->load($username);

		$current_tab = $language->lang(ucfirst($tab));
		$page_title = $current->is_active_session() ? $language->lang('CURRENT_USERS_PROFILE_TAB', $current_tab) : $language->lang('USERS_PROFILE_TAB', $username, $current_tab);

		$page_title = $tab !== 'profile' ? $page_title : $username;

		return $controller_helper->render("{$current->namespace()}$tab.twig", $page_title, 200, true);
	}
}
