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

use baihu\baihu\src\plugin\plugin;

class top_posters extends plugin
{
	/**
	* {@inheritdoc}
	*/
	public function load_plugin(): void
	{
		$sql = 'SELECT user_id, username, user_posts, user_colour, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height
				FROM ' . $this->users_loader->users_table . '
				WHERE user_id <> ' . (int) ANONYMOUS . '
					AND user_type <> ' . (int) USER_IGNORE . '
					AND user_posts > 0
				ORDER BY user_posts DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['baihu_users_per_list'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->users_loader->load_user($row);
			$user_id = (int) $row['user_id'];

			$this->template->assign_block_vars('top_posters', array_merge($this->users_loader->get_username_data($user_id), [
				'avatar' => [$this->users_loader->get_avatar_data($user_id)],
				'posts'  => (int) $row['user_posts']
			]));
		}
		$this->db->sql_freeresult($result);
	}
}
