<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\members\event;

use baihu\baihu\src\controller\controller_helper;
use baihu\baihu\src\user\page;
use baihu\baihu\src\user\loader as users_loader;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class subscribers implements EventSubscriberInterface
{
	public function __construct(
		protected controller_helper $controller_helper,
		protected page $page,
		protected users_loader $users_loader
	)
	{
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'core.memberlist_modify_viewprofile_sql' => 'redirect_profile',
			'core.memberlist_prepare_profile_data'	 => 'modify_profile_data',
			'core.modify_username_string'			 => 'modify_username_string',
		];
	}

	/**
	* Event core.memberlist_modify_viewprofile_sql
	*/
	public function redirect_profile($event): void
	{
		if ($this->page->get_current_page() === 'memberlist')
		{
			$url = $this->controller_helper->route('baihu_member', ['username' => $this->users_loader->get_username((int) $event['user_id'])]);

			$response = new RedirectResponse($url);
			$response->send();
		}
	}

	/**
	* Event core.memberlist_prepare_profile_data
	*/
	public function modify_profile_data($event): void
	{
		$data = $event['data'];
		$event['template_data'] = array_merge($event['template_data'], [
			'user_id'  => $data['user_id'],
			'username' => $data['username'],
			'color'	   => $data['user_colour'],
		]);
	}

	/**
	* Event core.modify_username_string
	*/
	public function modify_username_string($event): void
	{
		$user  = $event['username'];
		$route = $this->controller_helper->route('baihu_member', ['username' => $user]);

		if ($event['mode'] === 'full')
		{
			$color = $event['username_colour'];

			// TODO: remove this html ASAP
			// Can be removed/modified when html part will be removed from phpBB
			$username_string = '<a href="' . $route . '" class="username">' . $user . '</a>';
			if ($color)
			{
				$username_string = '<a href="' . $route . '" style="color:' . $color . ';" class="username-coloured">' . $user . '</a>';
			}

			$event['username_string'] = $username_string;
		}
		else if ($event['mode'] === 'profile')
		{
			$username_string = $this->controller_helper->route('baihu_member', ['username' => $user]);
			$event['username_string'] = $username_string;
		}
	}
}
