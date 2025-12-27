<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\controller\member;

use baihu\baihu\src\controller\abstract_controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class members_controller extends abstract_controller
{
	public function index(): RedirectResponse
	{
		$url = append_sid(generate_board_url() . "/memberlist.{$this->php_ext}", [], false);

		return new RedirectResponse($url);
	}
}
