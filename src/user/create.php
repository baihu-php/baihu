<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\user;

use baihu\baihu\src\event\events;
use baihu\baihu\src\controller\controller_helper;
use phpbb\auth\auth;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\template\template;

final class create
{
	public function __construct(
		protected controller_helper $controller_helper,
		protected auth $auth,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected template $template
	)
	{
	}

	public function post_template_data(): void
	{
		$create_data = $this->categories();

		/**
		 * @event baihu.core.user_create_data
		 * @var array create_data	 Modify data
		 */
		$vars = ['create_data'];
		extract($this->dispatcher->trigger_event(events::BAIHU_CORE_USER_CREATE_DATA, compact($vars)));

		foreach ($create_data as $category => $data)
		{
			$this->template->assign_block_vars('create', [
				'heading' => $category,
			]);

			foreach ($data as $name => $route)
			{
				$this->template->assign_block_vars('create.item', [
					'name'	=> $name,
					'route' => \is_string($route) ? $route : [$route],
				]);
			}
		}
	}

	public function categories(): array
	{
		$sql = 'SELECT forum_id, forum_name
			FROM ' . FORUMS_TABLE . '
			WHERE forum_type = ' . FORUM_POST . '
			ORDER BY forum_id ASC';
		$result = $this->db->sql_query($sql, 86400);

		$forum_ary = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			if (!$this->auth->acl_get('f_list', $row['forum_id']))
			{
				// if the user does not have permissions to list this forum skip
				continue;
			}

			$forum_ary['forum'][$row['forum_name']] = [
				'route' => $this->controller_helper->route('baihu_post_create', ['fid' => (int) $row['forum_id']]),
			];
		}
		$this->db->sql_freeresult($result);

		return $forum_ary;
	}
}
