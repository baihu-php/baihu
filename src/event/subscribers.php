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

use baihu\baihu\src\plugin\loader as plugins;
use baihu\baihu\src\user\page;
use baihu\baihu\src\controller\controller_helper;
use phpbb\config\config;
use phpbb\language\language;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class subscribers implements EventSubscriberInterface
{
	public function __construct(
		protected config $config,
		protected controller_helper $controller_helper,
		protected language $language,
		protected template $template,
		protected plugins $plugins,
		protected page $page
	)
	{
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'core.user_setup'		 => 'add_language',
			'core.user_setup_after'	 => 'load_available_plugins',
			// 'core.page_header_after' => 'add_global_variables',
		];
	}

	/**
	* Event core.user_setup
	*/
	public function add_language(): void
	{
		$this->language->add_lang('common', 'baihu/baihu');
	}

	/**
	* Event core.user_setup_after
	*/
	public function load_available_plugins(): void
	{
		if ($this->config['baihu_plugins'] && $page_name = $this->page->get_current_page())
		{
			$this->plugins->load_available_plugins($page_name, $this->config);
		}
	}

	/**
	* Event core.page_header_after
	*/
	public function add_global_variables(): void
	{
		$this->template->assign_vars([
			'U_GZO_ADMIN' => $this->controller_helper->route('gzo_main'),
		]);
	}
}
