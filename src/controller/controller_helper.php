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

use phpbb\controller\helper as phpbb_helper;

/**
* Controller helper class
*/
class controller_helper extends phpbb_helper
{
	public function add_language(string $name, string $path): self
	{
		$this->language->add_lang($name, $path);

		return $this;
	}

	public function assign_breadcrumb(string $name, string $route, array $params = []): self
	{
		$this->template->assign_block_vars('navlinks', [
			'BREADCRUMB_NAME' => $this->language->lang($name) ?? $name,
			'U_BREADCRUMB'	  => $this->route($route, $params),
		]);

		return $this;
	}

	public function get_config()
	{
		return $this->config;
	}

	public function get_language()
	{
		return $this->language;
	}

	public function get_template()
	{
		return $this->template;
	}
}
