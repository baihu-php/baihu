<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\profile\tabs;

use baihu\baihu\src\user\loader as users_loader;

final class friends extends base
{
	public static function getSubscribedServices(): array
	{
		return array_merge(parent::getSubscribedServices(), [
			'baihu.users_loader' => '?'.users_loader::class,
		]);
	}

	public function get_namespace(): string
	{
		return '@baihu_baihu/';
	}

	public function get_icon(): string
	{
		return 'mdi--users-outline';
	}

	public function load(array $member): void
	{
		$users_loader = $this->container->get('baihu.users_loader');

		// Output listing of friends online
		$update_time = $this->config['load_online_time'] * 60;

		$sql_ary = [
			'SELECT'	=> 'u.user_id, u.username, u.username_clean, u.user_colour, u.user_posts, u.user_rank, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, MAX(s.session_time) as online_time, MIN(s.session_viewonline) AS viewonline',

			'FROM'		=> [
				USERS_TABLE		=> 'u',
				ZEBRA_TABLE		=> 'z',
			],

			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [SESSIONS_TABLE => 's'],
					'ON'	=> 's.session_user_id = z.zebra_id',
				],
			],

			'WHERE'		=> 'z.user_id = ' . (int) $member['user_id'] . '
				AND z.friend = 1
				AND u.user_id = z.zebra_id',

			'GROUP_BY'	=> 'z.zebra_id, u.user_id, u.username_clean, u.user_colour, u.username',
			'ORDER_BY'	=> 'u.username_clean ASC',
		];

		$sql = $this->db->sql_build_query('SELECT_DISTINCT', $sql_ary);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$users_loader->load_user($row);
			$user_id = (int) $row['user_id'];
			$rank = $users_loader->get_rank_data($row);

			// $which = (time() - $update_time < $row['online_time'] && ($row['viewonline'] || $auth->acl_get('u_viewonline'))) ? 'online' : 'offline';

			$this->template->assign_block_vars('friends', array_merge($users_loader->get_username_data($user_id), [
				'avatar'   => [$users_loader->get_avatar_data($user_id)],
				'rank'	   => $rank['rank_title'],
				'rank_img' => $rank['rank_img'],
				'posts'	   => $row['user_posts'],
				's_online' => (time() - $update_time < $row['online_time'] && ($row['viewonline']))
			]));
		}
		$this->db->sql_freeresult($result);
	}
}
