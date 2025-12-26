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

use baihu\baihu\src\enum\core;
use baihu\baihu\src\event\events;
use baihu\baihu\src\plugin\base;

class statistics extends base
{
	/**
	* {@inheritdoc}
	*/
	public function load(int|null $id = null): void
	{
		/** @event events::BAIHU_STATISTICS_BEFORE */
		$this->get_dispatcher()->trigger_event(events::BAIHU_STATISTICS_BEFORE);

		$config = $this->get_config();
		$total_posts  = (int) $config['num_posts'];
		$total_topics = (int) $config['num_topics'];
		$total_users  = (int) $config['num_users'];

		$boarddays = (time() - $config['board_startdate']) / 86400;

		$posts_per_day	= sprintf('%.2f', $total_posts / $boarddays);
		$topics_per_day = sprintf('%.2f', $total_topics / $boarddays);
		$users_per_day	= sprintf('%.2f', $total_users / $boarddays);

		// Set template vars
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
		]);
	}
}
