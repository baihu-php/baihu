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

use baihu\baihu\src\plugin\plugin;

class recent_posts extends plugin
{
	/**
	* {@inheritdoc}
	*/
	public function load_plugin(): void
	{
		$sql = 'SELECT p.post_id, t.topic_id, t.topic_title
				FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
				WHERE t.topic_last_post_id = p.post_id
					AND t.topic_status <> ' . ITEM_MOVED . '
					AND t.topic_visibility = 1
				ORDER BY p.post_id DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['baihu_limit'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('recent_posts', [
				'link'	=> $this->controller_helper->route('ganstaz_gzo_recent_post', ['aid' => $row['topic_id'], 'post_id' => $row['post_id']]),
				'title' => $this->truncate($row['topic_title'], $this->config['baihu_title_length']),
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
