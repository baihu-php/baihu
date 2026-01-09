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
use baihu\baihu\src\plugin\profile\loader as profile_loader;
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
			'baihu.profile.loader' => '?'.profile_loader::class,
		]);
	}

	public function index($username, $tid): Response
	{
		// Load language
		$this->language->add_lang('memberlist');

		$profile_loader = $this->container->get('baihu.profile.loader');
		$member = $profile_loader->get_member_data($username);
		$profile_loader->build_profile_data($member, $this->get_auth(), $this->config, $this->get_user());
		$profile_loader->generate_breadcrumb($username, $tid);
		$profile_loader->generate_tabs_menu($username);

		// Load requested tab
		$current_tab = $profile_loader->get_tab($tid);
		$current_tab->load($member);

		$page_title = $this->get_user()->data['username'] === $username
			? $this->language->lang('CURRENT_PROFILE_TAB', ucfirst($tid))
			: $this->language->lang('PROFILE_TAB', $username, ucfirst($tid));
		$page_title = $tid === core::DEFAULT_TAB_NAME ? $page_title : $username;

		return $this->render("{$current_tab->get_namespace()}$tid.twig", $page_title);
	}
}
