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
// @codingStandardsIgnoreStart
use baihu\baihu\src\auth\attribute\is_granted as isGranted;
// @codingStandardsIgnoreEnd
// phpcs:enable

use baihu\baihu\src\controller\abstract_controller;
use baihu\baihu\src\enum\baihu as gzo;
use Symfony\Component\HttpFoundation\Response;

class index_controller extends abstract_controller
{
	#[isGranted('ADMIN', 'a_board', 'GZO_NO_ADMIN', 403)]
	public function index(): Response
	{
		$controller_helper = $this->get_controller_helper();

		$controller_helper->get_template()->assign_vars([
			'GZO_VERSION'	   => gzo::VERSION,
			'GZO_STYLE'		   => gzo::STYLE,

			'PHP_VERSION_INFO' => PHP_VERSION,
			'BOARD_VERSION'	   => PHPBB_VERSION,
		]);

		return $controller_helper->render('admin/index.twig', $controller_helper->get_language()->lang('AREA_MAIN_PAGE'));
	}
}
