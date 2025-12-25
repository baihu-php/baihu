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

use baihu\baihu\src\user\loader as users_loader;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\routing\helper as routing_helper;
use phpbb\template\template;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

	/**
	 * @var ContainerInterface
	 */
	protected ContainerInterface $container;

	public function set_container(ContainerInterface $container): ContainerInterface|null
	{
		$previous = $this->container ?? null;
		$this->container = $container;

		return $previous;
	}

	public static function getSubscribedServices(): array
	{
		return [
			'baihu.users_loader' => users_loader::class,
			'config'			 => config::class,
			'dbal.conn'			 => driver_interface::class,
			'event_dispatcher'	 => dispatcher::class,
			'routing.helper'	 => routing_helper::class,
			'template'			 => template::class
		];
	}

	public function get_config(): config
	{
		return $this->container->get('config');
	}

	public function get_db(): driver_interface
	{
		return $this->container->get('dbal.conn');
	}

	public function get_dispatcher(): dispatcher
	{
		return $this->container->get('event_dispatcher');
	}

	public function get_routing_helper(): routing_helper
	{
		return $this->container->get('routing.helper');
	}

	public function get_template(): template
	{
		return $this->container->get('template');
	}

	/**
	 * Borrowed from the phpBB Controller helper class
	 */
	public function route($route, array $params = [], $is_amp = true, $session_id = false, $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH)
	{
		return $this->get_routing_helper()->route($route, $params, $is_amp, $session_id, $reference_type);
	}

	public function get_users_loader(): users_loader
	{
		return $this->container->get('baihu.users_loader');
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
	public function truncate(string $title, int $length, string|null $ellips = null): string
	{
		return truncate_string(censor_text($title), $length, 255, false, $ellips ?? '...');
	}
}
