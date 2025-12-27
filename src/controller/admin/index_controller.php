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

// phpcs:disable
use baihu\baihu\src\auth\attribute\is_granted as isGranted;
// phpcs:enable

use baihu\baihu\src\controller\abstract_controller;
use baihu\baihu\src\enum\core;
use Symfony\Component\HttpFoundation\Response;

class index_controller extends abstract_controller
{
	#[isGranted('ADMIN', 'a_board', 'AREA_NO_ADMIN', 403)]
	public function index(): Response
	{
		$this->template->assign_vars([
			'VERSION'		=> core::VERSION,
			'STYLE'			=> core::STYLE,
			'PHP_VERSION'	=> PHP_VERSION,
			'BOARD_VERSION'	=> PHPBB_VERSION,
		]);

		return $this->render('admin/index.twig', $this->language->lang('AREA_MAIN_PAGE'));
	}
}
