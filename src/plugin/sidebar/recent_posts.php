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

class recent_posts extends base
{
	/**
	* {@inheritdoc}
	*/
	public function load(int|null $id = null): void
	{
		$db = $this->get_db();
		$config = $this->get_config();

		$sql = 'SELECT p.post_id, t.topic_id, t.topic_title
				FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
				WHERE t.topic_last_post_id = p.post_id
					AND t.topic_status <> ' . ITEM_MOVED . '
					AND t.topic_visibility = 1
				ORDER BY p.post_id DESC';
		$result = $db->sql_query_limit($sql, (int) $config['baihu_limit'], 0, 3600);

		while ($row = $db->sql_fetchrow($result))
		{
			$this->get_template()->assign_block_vars('recent_posts', [
				'link'	=> $this->route('baihu_recent_post', ['aid' => $row['topic_id'], 'post_id' => $row['post_id']]),
				'title' => $this->truncate($row['topic_title'], $config['baihu_title_length']),
			]);
		}
		$db->sql_freeresult($result);
	}
}
