<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\profile\model;

use phpbb\db\driver\driver_interface;

final class zebra
{
	public function __construct(
		protected driver_interface $db
	)
	{
	}

	public function get_data($user_id, $user): array
	{
		// What colour is the zebra
		$sql = 'SELECT friend, foe
			FROM ' . ZEBRA_TABLE . "
			WHERE zebra_id = $user_id
				AND user_id = {$user->data['user_id']}";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		$friend	   = $row ? (bool) $row['friend'] : false;
		$blacklist = $row ? (bool) $row['foe'] : false;

		$this->db->sql_freeresult($result);

		return [
			'friend' => $friend,
			'blacklist' => $blacklist
		];
	}
}
