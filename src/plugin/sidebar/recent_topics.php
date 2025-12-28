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

class recent_topics extends base
{
	/**
	* {@inheritdoc}
	*/
	public function load(int|null $id = null): void
	{
		$sql = 'SELECT topic_id, topic_title
				FROM ' . TOPICS_TABLE . '
				WHERE topic_status <> ' . ITEM_MOVED . '
					AND topic_visibility = 1
				ORDER BY topic_id DESC';
		$result = $this->db->sql_query_limit($sql, (int) $config['baihu_limit'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('recent_topics', [
				'link'	=> $this->get_controller_helper()->route('baihu_recent_topic', ['t' => $row['topic_id']]),
				'title' => $this->truncate($row['topic_title'], $this->config['baihu_title_length']),
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
