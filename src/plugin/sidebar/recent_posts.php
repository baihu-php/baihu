<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\sidebar;

use baihu\baihu\src\plugin\base;
use phpbb\user;

class recent_posts extends base
{
	public static function getSubscribedServices(): array
	{
		return array_merge(parent::getSubscribedServices(), [
			'user' => '?'.user::class
		]);
	}

	public function load(int|null $id = null): void
	{
		$users_loader = $this->get_users_loader();

		$sql = 'SELECT p.post_id, t.topic_id, t.topic_title, t.topic_last_post_time, u.user_id, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
				FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t, ' . USERS_TABLE . ' u
				WHERE t.topic_last_post_id = p.post_id
					AND t.topic_status <> ' . ITEM_MOVED . '
					AND t.topic_visibility = 1
					AND u.user_id = t.topic_last_poster_id
				ORDER BY p.post_id DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['baihu_limit'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$users_loader->load_user($row);
			$user_id = (int) $row['user_id'];

			$this->template->assign_block_vars('recent_posts', array_merge($users_loader->get_username_data($user_id), [
				'link'	 => $this->get_controller_helper()->route('baihu_recent_post', ['aid' => $row['topic_id'], 'post_id' => $row['post_id']]),
				'title'	 => $this->truncate($row['topic_title'], $this->config['baihu_title_length']),
				'avatar' => [$users_loader->get_avatar_data($user_id)],
				'time'	 => $this->container->get('user')->format_date($row['topic_last_post_time']),
			]));
		}
		$this->db->sql_freeresult($result);
	}
}
