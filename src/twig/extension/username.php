<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\twig\extension;

use phpbb\auth\auth;
use phpbb\template\twig\environment;
use phpbb\user;
use Twig\Error\Error;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class username extends AbstractExtension
{
	public function __construct(
		protected auth $auth,
		protected user $user
	)
	{
	}

	/**
	 * Returns a list of functions to add to the existing list.
	 *
	 * @return TwigFunction[]			Array of twig functions
	 */
	public function getFunctions(): array
	{
		return [
			new TwigFunction('baihu_username', [$this, 'username'], ['needs_environment' => true]),
		];
	}

	public function username(environment $environment, string $mode, int $user_id, string $username, string $color, string $classes = ''): string
	{
		$s_granted = false;
		if ($user_id && $user_id != ANONYMOUS && ($this->user->data['user_id'] == ANONYMOUS || $this->auth->acl_get('u_viewprofile')))
		{
			$s_granted = true;
		}

		try
		{
			return $environment->render('@baihu_baihu/macros/user/username.twig', [
				'MODE'		=> $mode,
				'USERNAME'	=> $username,
				'COLOR'		=> $color,
				'CLASSES'	=> $classes,
				'S_GRANTED' => (bool) $s_granted
			]);
		}
		catch (Error $e)
		{
			return '';
		}
	}
}
