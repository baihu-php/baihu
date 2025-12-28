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

use phpbb\language\language;
use phpbb\routing\helper as routing_helper;
use phpbb\template\template;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Controller helper class
 */
class controller_helper
{
	public function __construct(
		public readonly language $language,
		protected routing_helper $routing_helper,
		public readonly template $template
	)
	{
	}

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

	/**
	 * Borrowed from the phpBB Controller helper class
	 */
	public function route(string $route, array $params = [], bool $is_amp = true, bool|string $session_id = false, int $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH): string
	{
		return $this->routing_helper->route($route, $params, $is_amp, $session_id, $reference_type);
	}
}
