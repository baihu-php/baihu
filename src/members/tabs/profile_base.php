<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\members\tabs;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\controller\helper as controller;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\user;
use phpbb\exception\http_exception;

abstract class profile_base implements tabs_interface
{
	protected string $name;
	protected string $icon = 'bug';
	protected bool $active_session = false;

	public function __construct
	(
		protected auth $auth,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected controller $controller,
		protected language $language,
		protected template $template,
		protected user $user
	)
	{
	}

	/**
	* Returns Twig namespace
	*/
	abstract protected function namespace(): string;

	/**
	* Load current user
	*/
	abstract protected function load(string $username): void;

	/**
	* {@inheritdoc}
	*/
	public function set_name(string $name): void
	{
		$this->name = $name;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_name(): string
	{
		return $this->name;
	}

	/**
	* {@inheritdoc}
	*/
	public function icon(): string
	{
		return 'bug';
	}

	/**
	* Get user data
	*/
	protected function get_user_data(string $username): array
	{
		// Can this user view profiles/memberlist?
		if (!$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				throw new http_exception(403, 'NO_VIEW_USERS');
			}

			login_box('', $this->language->lang('LOGIN_EXPLAIN_VIEWPROFILE'));
		}

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
		$vars = [
			'username',
			'sql_array',
		];
		extract($this->dispatcher->trigger_event('core.memberlist_modify_viewprofile_sql', compact($vars)));

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$member = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$member)
		{
			throw new http_exception(404, 'NO_USER');
		}

		$this->active_session = $this->user->data['username'] === $member['username'];

		return $member;
	}

	/**
	* Is active session
	*/
	public function is_active_session(): bool
	{
		return $this->active_session;
	}
}
