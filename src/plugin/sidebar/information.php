<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\sidebar;

use baihu\baihu\src\enum\core;
use baihu\baihu\src\event\events;
use baihu\baihu\src\plugin\plugin;

class information extends plugin
{
	/**
	* {@inheritdoc}
	*/
	public function load_plugin(): void
	{
		/** @event events::BAIHU_INFORMATION_BEFORE */
		$this->dispatcher->trigger_event(events::BAIHU_INFORMATION_BEFORE);

		// Set template vars
		$this->template->assign_vars([
			'phpbb_version' => (string) PHPBB_VERSION,
			'gzo_version'	=> (string) core::VERSION,
			'gzo_style'		=> (string) core::STYLE,
		]);
	}
}
