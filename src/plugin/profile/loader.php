<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\profile;

use baihu\baihu\src\controller\controller_helper;
use baihu\baihu\src\enum\core;
use baihu\baihu\src\plugin\profile\model\zebra;
use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\di\service_collection;
use phpbb\event\dispatcher;
use phpbb\user;
use phpbb\exception\http_exception;

class loader
{
	protected static array $tabs = [];

	public function __construct(
		protected controller_helper $controller_helper,
		protected zebra $zebra,
		protected service_collection $collection,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected string $admin_path,
		protected string $php_ext,
		protected string $root_path
	)
	{
		if ($collection)
		{
			foreach ($collection as $tab)
			{
				self::$tabs[$tab->name] = $tab;
			}
		}
	}

	public function get_tab(string $tid): object
	{
		return self::$tabs[$tid] ?? (object) [];
	}

	public function available(): array
	{
		return array_keys(self::$tabs) ?? [];
	}

	public function generate_tabs_menu(string $username, string $tid): void
	{
		if (count($this->available()) === 1)
		{
			return;
		}

		foreach ($this->available() as $tab)
		{
			$route = $this->controller_helper->route('baihu_profile_tab', ['username' => $username, 'tid' => $tab]);
			if ($tab === core::DEFAULT_TAB_NAME)
			{
				$route = $this->controller_helper->route('baihu_member', ['username' => $username]);
			}

			$this->controller_helper->assign_block_vars('tabs', [
				'title'	 => $tab,
				'link'	 => $route,
				'icon'	 => $this->get_tab($tab)->get_icon(),
				'active' => $tab === $tid,
			]);
		}
	}

	public function generate_breadcrumb(string $username, string $tab): void
	{
		$route = 'baihu_member';
		$params = ['username' => $username];

		$this->controller_helper->assign_breadcrumb('MEMBERLIST', 'baihu_members_redirect')
			->assign_breadcrumb($username, $route, $params);

		if ($tab !== core::DEFAULT_TAB_NAME)
		{
			$route = 'baihu_profile_tab';
			$params = ['username' => $username, 'tid' => $tab];

			$this->controller_helper->assign_breadcrumb(ucfirst($tab), $route, $params);
		}

		$this->controller_helper->add_canonical($route, $params);
	}

	public function get_member_data(string $username): array
	{
		$sql_array = [
			'SELECT'	=> 'u.*',
			'FROM'		=> [
				USERS_TABLE		=> 'u'
			],
			'WHERE'		=> "u.username_clean = '" . $this->db->sql_escape(utf8_clean_string($username)) . "'",
		];

		/**
		* Modify user data SQL before member profile row is created
		*
		* @event core.memberlist_modify_viewprofile_sql
		* @var string	username			The username
		* @var array	sql_array			Array containing the main query
		* @since 3.2.6-RC1
		*/
		$vars = ['username', 'sql_array',];
		extract($this->dispatcher->trigger_event('core.memberlist_modify_viewprofile_sql', compact($vars)));

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$member = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$member)
		{
			throw new http_exception(404, 'NO_USER');
		}

		return $member;
	}

	public function build_profile_data(array $member, auth $auth, config $config, user $user): void
	{
		$user_id = (int) $member['user_id'];
		$user_notes_enabled = true;
		$warn_user_enabled = true;

		if ($config['load_onlinetrack'])
		{
			$sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
				FROM ' . SESSIONS_TABLE . "
				WHERE session_user_id = $user_id";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			$member['session_time'] = (isset($row['session_time'])) ? $row['session_time'] : 0;
			$member['session_viewonline'] = (isset($row['session_viewonline'])) ? $row['session_viewonline'] : 0;
			unset($row);
		}

		if (!function_exists('phpbb_show_profile'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}

		$this->controller_helper->template->assign_vars(phpbb_show_profile($member, $user_notes_enabled, $warn_user_enabled));

		// Load zebra data
		$zebra = $this->zebra->get_data($user_id, $user);
		$friend = $zebra['friend'];
		$blacklist = $zebra['blacklist'];

		// Main array of vars
		$template_ary = [
			'U_USER_ADMIN'	=> ($auth->acl_get('a_user')) ? append_sid(generate_board_url() . "/{$this->admin_path}index.$this->php_ext", 'i=users&amp;mode=overview&amp;u=' . $user_id, true, $user->session_id) : '',

			'U_USER_BAN'	=> ($auth->acl_get('m_ban') && $user_id != $user->data['user_id']) ? append_sid("{$this->root_path}mcp.$this->php_ext", 'i=ban&amp;mode=user&amp;u=' . $user_id, true, $user->session_id) : '',

			'U_SWITCH_PERMISSIONS' => ($auth->acl_get('a_switchperm') && $user->data['user_id'] != $user_id) ? append_sid("{$this->root_path}ucp.$this->php_ext", "mode=switch_perm&amp;u={$user_id}&amp;hash=" . generate_link_hash('switchperm')) : '',
			'U_EDIT_SELF'	=> ($user_id == $user->data['user_id'] && $auth->acl_get('u_chgprofileinfo')) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=ucp_profile&amp;mode=profile_info') : '',

			'S_USER_NOTES'	=> $user_notes_enabled,
			'S_WARN_USER'	=> $warn_user_enabled,

			'S_ZEBRA'		=> $user->data['user_id'] != $user_id && $user->data['is_registered'],
			'U_BEFRIEND'	=> (!$friend && !$blacklist) ? $this->controller_helper->route('baihu_member_friend_add', ['user_id' => $user_id]) : '',
			'U_UNFRIEND'	=> ($friend) ? $this->controller_helper->route('baihu_member_friend_remove', ['user_id' => $user_id]) : '',

			'U_BLACKLIST'	=> (!$friend && !$blacklist) ? $this->controller_helper->route('baihu_member_blacklist', ['user_id' => $user_id]) : '',
			'U_UNBLACKLIST' => ($blacklist) ? $this->controller_helper->route('baihu_member_unblacklist', ['user_id' => $user_id]) : '',
		];

		/**
		* Modify user's template vars before we display the profile
		*
		* @event core.memberlist_modify_view_profile_template_vars
		*/
		$vars = ['template_ary',];
		extract($this->dispatcher->trigger_event('core.memberlist_modify_view_profile_template_vars', compact($vars)));

		// Assign vars to profile controller
		$this->controller_helper->template->assign_vars($template_ary);
	}
}
