<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\event;

use baihu\baihu\src\controller\controller_helper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class subscribers implements EventSubscriberInterface
{
	public function __construct(
		protected controller_helper $controller_helper
	)
	{
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'core.user_setup'		 => 'add_language',
			'core.page_header_after' => 'add_global_variables',
		];
	}

	/**
	 * Event core.user_setup
	 */
	public function add_language(): void
	{
		$this->controller_helper->add_language('common', 'baihu/baihu');
	}

	/**
	 * Event core.page_header_after
	 */
	public function add_global_variables(): void
	{
		$this->controller_helper->template->assign_vars([
			'U_AREAZ_MAIN' => $this->controller_helper->route('areaz_main'),
		]);
	}
}
