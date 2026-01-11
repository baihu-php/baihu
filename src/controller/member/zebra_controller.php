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
// phpcs:disable
use baihu\baihu\src\security\attribute\is_granted as isGranted;
// phpcs:enable
use Symfony\Component\HttpFoundation\JsonResponse;

#[isGranted('LIMITED', ['u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'])]
class zebra_controller extends abstract_controller
{
	public function create(int $user_id): JsonResponse
	{
		$this->language->add_lang('ucp');
		$entity_manager = $this->get_entity_manager();
		$entity_manager->set_u_action($this->get_current_url());

		if ($user_id)
		{
			return $entity_manager->action(
				'create',
				ZEBRA_TABLE,
				['user_id' => (int) $this->get_user()->data['user_id'], 'zebra_id' => (int) $user_id, 'friend' => 1],
				'FRIENDS_UPDATED',
				['submit' => true]
			);
		}
	}

	public function delete(int $user_id): JsonResponse
	{
		$this->language->add_lang('ucp');
		$entity_manager = $this->get_entity_manager();
		$entity_manager->set_u_action($this->get_current_url());

		if ($user_id)
		{
			return $entity_manager->action(
				'delete',
				ZEBRA_TABLE,
				['user_id', $this->get_user()->data['user_id'], 'zebra_id', $user_id, true],
				'FRIENDS_UPDATED',
				['submit' => true]
			);
		}
	}
}
