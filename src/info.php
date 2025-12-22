<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\template\twig\twig;
use phpbb\user;

final class info
{
	public function __construct
	(
		protected auth $auth,
		protected config $config,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected twig $twig,
		protected user $user,
		protected readonly string $root_path,
		protected readonly string $php_ext
	)
	{
	}

	public function birthdays(): void
	{
		$birthdays = [];

		$time = $this->user->create_datetime();
		$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

		// Display birthdays of 29th february on 28th february in non-leap-years
		$leap_year_birthdays = '';
		if ($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L'))
		{
			$leap_year_birthdays = " OR u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
		}

		$sql_ary = $this->db->sql_build_query('SELECT', [
			'SELECT' => 'u.user_id, u.username, u.user_colour, u.user_birthday',
			'FROM' => [
				USERS_TABLE => 'u',
			],
			'LEFT_JOIN' => [
				[
					'FROM' => [BANS_TABLE => 'b'],
					'ON' => 'u.user_id = b.ban_userid',
				],
			],
			'WHERE' => 'b.ban_id IS NULL
				AND u.user_type IN (' . USER_NORMAL . ', ' . USER_FOUNDER . ")
				AND (u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%' $leap_year_birthdays)",
		]);

		/**
		* Event to modify the SQL query to get birthdays data
		*
		* @event core.index_modify_birthdays_sql
		* @var	array	now			The assoc array with the 'now' local timestamp data
		* @var	array	sql_ary		The SQL array to get the birthdays data
		* @var	object	time		The user related Datetime object
		* @since 3.1.7-RC1
		*/
		$vars = ['now', 'sql_ary', 'time'];
		extract($this->dispatcher->trigger_event('core.index_modify_birthdays_sql', compact($vars)));

		$result = $this->db->sql_query($sql_ary);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		foreach ($rows as $row)
		{
			$birthday_year = (int) substr($row['user_birthday'], -4);
			$birthday_age = ($birthday_year) ? max(0, $now['year'] - $birthday_year) : '';

			$birthdays[] = [
				'id'	=> (int) $row['user_id'],
				'name'	=> $row['username'],
				'color' => $row['user_colour'],
				'age'	=> $birthday_age,
			];
		}

		/**
		* Event to modify the birthdays list
		*
		* @event core.index_modify_birthdays_list
		* @var	array	birthdays		Array with the users birthdays data
		* @var	array	rows			Array with the birthdays SQL query result
		* @since 3.1.7-RC1
		*/
		$vars = ['birthdays', 'rows'];
		extract($this->dispatcher->trigger_event('core.index_modify_birthdays_list', compact($vars)));

		$this->twig->assign_block_vars_array('birthdays', $birthdays);
	}

	public function legend(): void
	{
		$order_legend = $this->config['legend_sort_groupname'] ? 'group_name' : 'group_legend';

		// Grab group details for legend display
		$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type, g.group_legend
			FROM ' . GROUPS_TABLE . ' g
			LEFT JOIN ' . USER_GROUP_TABLE . ' ug
				ON (
					g.group_id = ug.group_id
					AND ug.user_id = ' . (int) $this->user->data['user_id'] . '
					AND ug.user_pending = 0
				)
			WHERE g.group_legend > 0
				AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . (int) $this->user->data['user_id'] . ')
			ORDER BY g.' . $order_legend . ' ASC';

		if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
				FROM ' . GROUPS_TABLE . '
				WHERE group_legend > 0
				ORDER BY ' . $order_legend . ' ASC';
		}
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->twig->assign_block_vars('legend', [
				'color' => $row['group_colour'],
				'name'	=> $row['group_name'],
				'link'	=> $this->is_authed($row) ? append_sid("{$this->root_path}memberlist.$this->php_ext", "mode=group&amp;g={$row['group_id']}") : '',
			]);
		}
		$this->db->sql_freeresult($result);
	}

	public function show_birthdays(): bool
	{
		return ($this->config['load_birthdays'] && $this->config['allow_birthdays'] && $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'));
	}

	/**
	* Is visitor a bot or does he/she have permissions
	*/
	protected function is_authed(array $row): bool
	{
		return $row['group_name'] != 'BOTS' && $this->auth->acl_get('u_viewprofile');
	}
}
