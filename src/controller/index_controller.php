<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\controller;

use baihu\baihu\src\plugin\article\posts;
use Symfony\Component\HttpFoundation\Response;

class index_controller extends abstract_controller
{
	public function __construct(protected posts $posts)
	{
	}

	public function index(): Response
	{
		$controller_helper = $this->get_controller_helper();
		$id = (int) $controller_helper->get_config()['baihu_fid'];

		// Assign breadcrumb
		$controller_helper->assign_breadcrumb($this->posts->get_category_name($id), 'baihu_articles', ['fid' => $id]);

		$this->posts->trim_messages(true)
			->load($id);

		return $controller_helper->render('index.twig', $controller_helper->get_language()->lang('HOME'), 200, true);
	}
}
