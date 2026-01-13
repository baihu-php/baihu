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
use baihu\baihu\src\plugin\profile\model\zebra;
// phpcs:disable
use baihu\baihu\src\security\attribute\is_granted as isGranted;
// phpcs:enable
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[isGranted('LIMITED', ['u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'])]
class blacklist_controller extends abstract_controller
{
	public static function getSubscribedServices(): array
	{
		return array_merge(parent::getSubscribedServices(), [
			'baihu.profile.model.zebra' => '?'.zebra::class,
		]);
	}

	public function create(int $user_id): JsonResponse|Response
	{
		$this->language->add_lang('ucp');
		$entity_manager = $this->get_entity_manager();
		$user = $this->get_user();

		// Load zebra data
		$s_zebra = (int) $user->data['user_id'] !== $user_id;
		$zebra = $this->container->get('baihu.profile.model.zebra')->get_data($user_id, $user);
		$friend = $zebra['friend'];
		$blacklist = $zebra['blacklist'];

		if ($s_zebra && !$friend && !$blacklist)
		{
			return $entity_manager->action(
				'create',
				ZEBRA_TABLE,
				['user_id' => (int) $user->data['user_id'], 'zebra_id' => $user_id, 'foe' => 1],
				'BAIHU_BLACKLIST_UPDATED',
				['submit' => true]
			);
		}

		return $this->message('HELLO');
	}

	public function delete(int $user_id): JsonResponse|Response
	{
		$this->language->add_lang('ucp');
		$entity_manager = $this->get_entity_manager();
		$user = $this->get_user();

		if ($this->container->get('baihu.profile.model.zebra')->get_data($user_id, $user)['blacklist'])
		{
			return $entity_manager->action(
				'delete',
				ZEBRA_TABLE,
				['user_id', $user->data['user_id'], 'zebra_id', $user_id, true],
				'BAIHU_BLACKLIST_UPDATED',
				['submit' => true]
			);
		}

		return $this->message('HELLO');
	}
}
