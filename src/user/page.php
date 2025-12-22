<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\user;

use phpbb\config\config;
use phpbb\user;

class page
{
	public function __construct(
		protected config $config,
		protected user $user,
		public readonly string $php_ext
	)
	{
	}

	public function get_current_page(): string
	{
		$on_page = explode('/', str_replace('.' . $this->php_ext, '', $this->user->page['page_name']));
		$page_name = $on_page[0];

		if ($page_name === 'app')
		{
			// For example - app or app/index
			$page_name	= $on_page[1] ?? $page_name;
			$last_param = end($on_page);

			// Is it second or last param?
			$second_last_param = count($on_page) > 2 && is_numeric($last_param) ? $page_name : $last_param;
			$page_name = $page_name ?? $second_last_param;

			// This is global for app.php & will apply to all route params.
			$page_name = $this->config['baihu_app_global'] ? $on_page[0] : $page_name;
		}

		return $page_name;
	}

	/**
	 * @param string $page_name
	 */
	public function is_control_panel(string $page_name): bool
	{
		return $this->user->page['page_dir'] === 'adm' || $page_name === 'mcp' || $page_name === 'ucp';
	}
}
