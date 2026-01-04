<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\security;

use phpbb\auth\auth;
use phpbb\language\language;
use phpbb\user;
use phpbb\exception\http_exception;

class authorization
{
	public readonly bool $user_validate;
	protected static array $roles = ['ADMIN', 'USER', 'LIMITED'];

	public function __construct(
		public readonly auth $auth,
		protected language $language,
		protected user $user
	)
	{
	}

	public function is_granted(object $data): bool
	{
		if (!in_array($data->role, self::$roles))
		{
			return false;
		}

		return \is_array($data->option)
			? $this->auth->acl_gets($data->option)
			: $this->auth->acl_get($data->option);
	}

	public function validate_user(string $role)
	{
		return $role === 'LIMITED' ?: false;
	}

	public function validate_user_data($data): void
	{
		if (!$this->is_granted($data))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				throw new http_exception($data->status_code ?? 403, 'AREA_USER_ACCESS');
			}

			login_box('', $this->language->lang('BAIHU_LOGIN'));
		}
	}
}
