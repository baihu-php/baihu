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

use baihu\baihu\src\controller\controller_helper;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\user;
use phpbb\exception\http_exception;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

abstract class base implements ServiceSubscriberInterface
{
	protected string $name;
	protected bool $active_session = false;

	public function __construct
	(
		protected ContainerInterface $container,
		protected controller_helper $controller_helper,
		protected auth $auth,
		protected config $config,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected language $language,
		protected template $template,
		protected user $user,
		protected string $php_ext,
		protected string $admin_path,
		protected string $root_path
	)
	{
	}

	public static function getSubscribedServices(): array
	{
		return [];
	}

	/**
	 * Returns Twig namespace
	 */
	abstract protected function get_namespace(): string;

	/**
	 * Returns an icon
	 */
	abstract protected function get_icon(): string;

	/**
	 * Load current data
	 */
	abstract protected function load(string $username): void;

	public function set_tab_name(string $name): void
	{
		$this->name = $name;
	}

	public function get_name(): string
	{
		return $this->name;
	}

	protected function get_member_data(string $username): array
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
