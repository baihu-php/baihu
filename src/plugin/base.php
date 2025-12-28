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
use phpbb\language\language;
use phpbb\template\template;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

abstract class base implements ServiceSubscriberInterface
{
	/**
	 * @var bool Returns true if plugin service needs to be loaded
	 */
	public readonly bool $loadable;

	/**
	 *	@var bool Returns true if data id (for example $forum_id) for plugin is changeable
	 */
	public bool $dynamic_id = false;

	/**
	 * @var bool Return true if plugin type is block
	 */
	public readonly bool $block;

	public function __construct(
		protected ContainerInterface $container,
		protected config $config,
		protected driver_interface $db,
		protected template $template
	)
	{
	}

	public static function getSubscribedServices(): array
	{
		return [
			'baihu.controller_helper' => '?'.controller_helper::class,
			'baihu.users_loader' => '?'.users_loader::class,
			'event_dispatcher'	 => '?'.dispatcher::class,
			'language' => '?'.language::class
		];
	}

	protected function get_controller_helper(): controller_helper
	{
		return $this->container->get('baihu.controller_helper');
	}

	protected function get_users_loader(): users_loader
	{
		return $this->container->get('baihu.users_loader');
	}

	protected function get_dispatcher(): dispatcher
	{
		return $this->container->get('event_dispatcher');
	}

	protected function get_language(): language
	{
		return $this->container->get('language');
	}

	/**
	 * @param bool $set Is plugin service loadable
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
	 * @param bool $set Is plugin type block
	 */
	public function block(bool $set): void
	{
		$this->block = $set;
	}

	/**
	 * Load plugin data
	 */
	abstract public function load(int|null $id = null): void;

	/**
	 * Truncate title
	 */
	protected function truncate(string $title, int $length, string|null $ellips = null): string
	{
		return truncate_string(censor_text($title), $length, 255, false, $ellips ?? '...');
	}
}
