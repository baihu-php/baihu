<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\controller\admin;

use baihu\baihu\src\controller\abstract_controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class admin_redirect_main extends abstract_controller
{
	public function index(): RedirectResponse
	{
		$url = append_sid("{$this->admin_path}index.{$this->php_ext}", [], false);

		return new RedirectResponse($url);
	}
}
