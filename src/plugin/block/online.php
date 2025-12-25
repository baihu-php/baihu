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
use baihu\baihu\src\plugin\base;

use baihu\baihu\src\info;

class online extends base
{
	public function __construct(
		protected info $info
	)
	{
	}

	/**
	* {@inheritdoc}
	*/
	public function load(int|null $id = null): void
	{
		$config = $this->get_config();
		$total_posts  = (int) $config['num_posts'];
		$total_topics = (int) $config['num_topics'];
		$total_users  = (int) $config['num_users'];

		$boarddays = (time() - $config['board_startdate']) / 86400;

		$posts_per_day	= sprintf('%.2f', $total_posts / $boarddays);
		$topics_per_day = sprintf('%.2f', $total_topics / $boarddays);
		$users_per_day	= sprintf('%.2f', $total_users / $boarddays);

		// Generate birthday list if required...
		if ($this->info->show_birthdays())
		{
			$this->info->birthdays();
		}

		$this->info->legend();

		$this->get_template()->assign_vars([
			'TOTAL_POSTS'  => $total_posts,
			'TOTAL_TOPICS' => $total_topics,
			'TOTAL_USERS'  => $total_users,
			'N_USER_ID'	   => (int) $config['newest_user_id'],
			'N_USER_NAME'  => $config['newest_username'],
			'N_USER_COLOR' => $config['newest_user_colour'],

			'ppd' => $posts_per_day,
			'tpd' => $topics_per_day,
			'upd' => $users_per_day,
			'S_DISPLAY_BIRTHDAY_LIST' => $this->info->show_birthdays(),
		]);

		/** events::BAIHU_ONLINE_DATA_AFTER */
		$this->get_dispatcher()->trigger_event(events::BAIHU_ONLINE_DATA_AFTER);
	}
}
