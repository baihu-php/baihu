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
use phpbb\auth\auth;
use phpbb\user;

final class friends extends base
{
	public static function getSubscribedServices(): array
	{
		return array_merge(parent::getSubscribedServices(), [
			'baihu.users_loader' => '?'.users_loader::class,
			'auth' => '?'.auth::class,
			'user' => '?'.user::class,
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
		$auth = $this->container->get('auth');
		$user = $this->container->get('user');

		// Output listing of friends online
		$update_time = $this->config['load_online_time'] * 60;

		$sql_ary = [
			'SELECT'	=> 'u.user_id, u.username, u.username_clean, u.user_colour, u.user_posts, u.user_rank, u.user_regdate, u.user_last_active, u.user_allow_viewonline, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, MAX(s.session_time) as online_time, MIN(s.session_viewonline) AS viewonline',

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
		$result = $this->db->sql_query_limit($sql, (int) $this->config['limit'], 0);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$users_loader->load_user($row);
			$user_id = (int) $row['user_id'];
			$rank = $users_loader->get_rank_data($row);

			$last_active = '';
			if ($row['user_allow_viewonline'] || $auth->acl_get('u_viewonline'))
			{
				$last_active = $row['user_last_active'] ?: ($row['session_time'] ?? 0);
			}

			$this->template->assign_block_vars('friends', array_merge($users_loader->get_username_data($user_id), [
				'avatar'   => [$users_loader->get_avatar_data($user_id)],
				'rank'	   => $rank['rank_title'],
				'rank_img' => $rank['rank_img'],
				'posts'	   => $row['user_posts'],
				'joined'   => $user->format_date($row['user_regdate']),
				'last_active' => (empty($last_active)) ? ' - ' : $user->format_date($last_active),
				's_online' => (time() - $update_time < $row['online_time'] && ($row['viewonline'] || $auth->acl_get('u_viewonline')))
			]));
		}
		$this->db->sql_freeresult($result);
	}
}
