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

class group extends base
{
	/**
	* {@inheritdoc}
	*/
	public function load(int|null $id = null): void
	{
		// Will have a dynamic config value later
		$group_id = (int) $this->config['baihu_the_team_id'] ?: 5;

		$sql = 'SELECT group_name
				FROM ' . GROUPS_TABLE . '
				WHERE group_id = ' . $group_id;
		$result = $this->db->sql_query($sql, 3600);
		$group_name = $this->db->sql_fetchfield('group_name');
		$this->db->sql_freeresult($result);

		$this->template->assign_var('team_name', $group_name);

		$users_loader = $this->get_users_loader();
		$sql = 'SELECT u.user_id, u.username, u.user_posts, u.user_rank, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
				FROM ' . USER_GROUP_TABLE . ' ug, ' . $users_loader->users_table . ' u
				WHERE ug.user_id = u.user_id
					AND ug.user_pending = 0
					AND ug.group_id = ' . $group_id;
		$result = $this->db->sql_query($sql, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$users_loader->load_user($row);
			$user_id = (int) $row['user_id'];

			$this->template->assign_block_vars('the_team', array_merge($users_loader->get_username_data($user_id), [
				'avatar' => [$users_loader->get_avatar_data($user_id)],
				'rank'	 => $users_loader->get_rank_data($row)['rank_title']
			]));
		}
		$this->db->sql_freeresult($result);
	}
}
