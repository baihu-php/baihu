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

use baihu\baihu\src\model\admin\settings as sm;
use baihu\baihu\src\controller\abstract_controller;
use Symfony\Component\HttpFoundation\Response;

class settings_controller extends abstract_controller
{
	public function __construct(protected sm $sm)
	{
	}

	#[isGranted('ADMIN', 'a_board', 'AREA_NO_ADMIN', 403)]
	public function index(): Response
	{
		$controller_helper = $this->get_controller_helper();
		$controller_helper->assign_breadcrumb('GZO_SETTINGS', 'gzo_settings');

	//	$this->helper->language->add_lang('acp_gzo', 'ganstaz/gzo');

	//	$this->form->build($this->sm->data(), true);
	//	$this->form->add_form_key('ganstaz_gzo_settings');

	//	$emc = $this->em->type('config');

	//	if ($this->form->is_submitted() && $this->form->is_valid())
	//	{
	//		if ($this->sm->s_forum_ids())
	//		{
	//			$emc->set($this->form->_get('special'));
	//		}

	//		$emc->set($this->form->_get('common'));

	//		$this->settings_saved_message();
	//	}

	//	$this->form->create_view($this->u_action);

		return $controller_helper->render('admin/index.twig', $controller_helper->get_language()->lang('GZO_SETTINGS'));
	}
}
