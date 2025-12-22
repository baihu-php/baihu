<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin;

use baihu\baihu\src\controller\controller_helper;
use baihu\baihu\src\user\loader as users_loader;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\template\template;

abstract class plugin
{
	/** @var bool Returns true if plugin service is loadable  */
	public readonly bool $loadable;

	/** @var bool Returns true if plugin id is changeable  */
	public readonly bool $dynamic_id;

	/** @var string Plugin type (blocks, block or event)  */
	public readonly string $type;

	public function __construct(
		protected config $config,
		protected controller_helper $controller_helper,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected template $template,
		protected users_loader $users_loader,
		protected readonly string $root_path,
		protected readonly string $php_ext
	)
	{
	}

	/**
	 * @param bool $set Is plugin loadable
	 */
	public function loadable(bool $set): void
	{
		$this->loadable = $set;
	}

	/**
	 * @param bool $set Is id changeable
	 */
	public function dynamic_id(bool $set): void
	{
		$this->dynamic_id = $set;
	}

	/**
	 * @param string $type Plugin type
	 */
	public function set_type(string $type): void
	{
		$this->type = $type;
	}

	/**
	 * Load plugin
	 */
	public function load_plugin(): void
	{
	}

	/**
	 * Truncate title
	 */
	public function truncate(string $title, int $length, string|null $ellips = null): string
	{
		return truncate_string(censor_text($title), $length, 255, false, $ellips ?? '...');
	}
}
