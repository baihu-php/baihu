<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\auth;

use phpbb\auth\auth as phpbb_auth;

class auth
{
	protected static array $roles = ['ADMIN', 'USER'];

	public function __construct(public readonly phpbb_auth $phpbb_auth)
	{
	}

	public function is_granted(object $data): bool
	{
		if (!in_array($data->role, self::$roles))
		{
			return false;
		}

		return str_contains($data->option, ',')
			? $this->phpbb_auth->acl_gets($data->option)
			: $this->phpbb_auth->acl_get($data->option);
	}
}
