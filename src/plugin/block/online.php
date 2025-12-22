<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\block;

use baihu\baihu\src\event\events;
use baihu\baihu\src\plugin\plugin;
use baihu\baihu\src\user\loader as users_loader;
use phpbb\config\config;
use phpbb\controller\helper as controller;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\template\template;

use baihu\baihu\src\info;

class online extends plugin
{
	public function __construct(
		config $config,
		controller $controller,
		driver_interface $db,
		dispatcher $dispatcher,
		template $template,
		users_loader $users_loader,
		$root_path,
		$php_ext,
		protected info $info
	)
	{
		parent::__construct($config, $controller, $db, $dispatcher, $template, $users_loader, $root_path, $php_ext);
	}

	/**
	* {@inheritdoc}
	*/
	public function load_plugin(): void
	{
		$total_posts  = (int) $this->config['num_posts'];
		$total_topics = (int) $this->config['num_topics'];
		$total_users  = (int) $this->config['num_users'];

		$boarddays = (time() - $this->config['board_startdate']) / 86400;

		$posts_per_day	= sprintf('%.2f', $total_posts / $boarddays);
		$topics_per_day = sprintf('%.2f', $total_topics / $boarddays);
		$users_per_day	= sprintf('%.2f', $total_users / $boarddays);

		// Generate birthday list if required...
		if ($this->info->show_birthdays())
		{
			$this->info->birthdays();
		}

		$this->info->legend();

		$this->template->assign_vars([
			'TOTAL_POSTS'  => $total_posts,
			'TOTAL_TOPICS' => $total_topics,
			'TOTAL_USERS'  => $total_users,
			'N_USER_ID'	   => (int) $this->config['newest_user_id'],
			'N_USER_NAME'  => $this->config['newest_username'],
			'N_USER_COLOR' => $this->config['newest_user_colour'],

			'ppd' => $posts_per_day,
			'tpd' => $topics_per_day,
			'upd' => $users_per_day,
			'S_DISPLAY_BIRTHDAY_LIST' => $this->info->show_birthdays(),
		]);

		/** events::GZO_ONLINE_DATA_AFTER */
		$this->dispatcher->trigger_event(events::GZO_ONLINE_DATA_AFTER);
	}
}
