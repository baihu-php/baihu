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

class recent_topics extends plugin
{
	/**
	* {@inheritdoc}
	*/
	public function load_plugin(): void
	{
		$sql = 'SELECT topic_id, topic_title
				FROM ' . TOPICS_TABLE . '
				WHERE topic_status <> ' . ITEM_MOVED . '
					AND topic_visibility = 1
				ORDER BY topic_id DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['baihu_limit'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('recent_topics', [
				'link'	=> $this->controller_helper->route('ganstaz_gzo_recent_topic', ['t' => $row['topic_id']]),
				'title' => $this->truncate($row['topic_title'], $this->config['baihu_title_length']),
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
