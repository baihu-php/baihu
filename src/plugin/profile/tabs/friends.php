<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\profile\tabs;

final class friends extends base
{
	// public static function getSubscribedServices(): array
	// {
	// 	return array_merge(parent::getSubscribedServices(), [
	// 		'baihu.posts' => '?'.posts::class,
	// 		'auth' => '?'.auth::class,
	// 		'profilefields.manager' => '?'.cp::class,
	// 		'user' => '?'.user::class,
	// 	]);
	// }

	public function get_namespace(): string
	{
		return '@baihu_baihu/';
	}

	public function get_icon(): string
	{
		return 'mdi--users-outline';
	}

	public function load(array $member): void
	{
	}
}
