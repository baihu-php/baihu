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
		$group_id = (int) $this->get_config()['baihu_the_team_id'] ?: 5;
		$db = $this->get_db();

		$sql = 'SELECT group_name
				FROM ' . GROUPS_TABLE . '
				WHERE group_id = ' . $group_id;
		$result = $db->sql_query($sql, 3600);
		$group_name = $db->sql_fetchfield('group_name');
		$db->sql_freeresult($result);

		$template = $this->get_template();
		$template->assign_var('team_name', $group_name);

		$users_loader = $this->get_users_loader();
		$sql = 'SELECT u.user_id, u.username, u.user_posts, u.user_rank, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
				FROM ' . USER_GROUP_TABLE . ' ug, ' . $users_loader->users_table . ' u
				WHERE ug.user_id = u.user_id
					AND ug.user_pending = 0
					AND ug.group_id = ' . $group_id;
		$result = $db->sql_query($sql, 3600);

		while ($row = $db->sql_fetchrow($result))
		{
			$users_loader->load_user($row);
			$user_id = (int) $row['user_id'];

			$template->assign_block_vars('the_team', array_merge($users_loader->get_username_data($user_id), [
				'avatar' => [$users_loader->get_avatar_data($user_id)],
				'rank'	 => $users_loader->get_rank_data($row)['rank_title']
			]));
		}
		$db->sql_freeresult($result);
	}
}
