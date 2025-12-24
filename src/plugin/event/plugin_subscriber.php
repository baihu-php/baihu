<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\event;

use baihu\baihu\src\enum\core;
use baihu\baihu\src\plugin\loader as plugins;
use baihu\baihu\src\user\page;
use phpbb\config\config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class plugin_subscriber implements EventSubscriberInterface
{
	public function __construct(
		protected config $config,
		protected plugins $plugins,
		protected page $page
	)
	{
	}

	/**
	 * Event core.user_setup_after
	 */
	public function load_available_plugins(): void
	{
		if ($this->config[core::PLUGINS] && $page_name = $this->page->get_current_page())
		{
			$this->plugins->load_available_plugins($page_name, $this->config);
		}
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'core.user_setup_after' => 'load_available_plugins',
		];
	}
}
