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
	public static function getSubscribedServices(): array
	{
		return array_merge(parent::getSubscribedServices(), [
			'baihu.posts' => '?'.posts::class,
		]);
	}

	public function index(): Response
	{
		$id = (int) $this->config['baihu_fid'];
		$posts = $this->container->get('baihu.posts');

		// Assign breadcrumb
		$this->get_controller_helper()->assign_breadcrumb($posts->get_category_name($id), 'baihu_articles', ['fid' => $id]);

		$posts->trim_messages(true)
			->load($id);

		return $this->render('index.twig', $this->language->lang('HOME'), 200, true);
	}
}
