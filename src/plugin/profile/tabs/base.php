<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\profile\tabs;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\language\language;
use phpbb\template\template;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

abstract class base implements ServiceSubscriberInterface
{
	public readonly string $name;
	public readonly bool $restrict_bots;

	public function __construct
	(
		protected ContainerInterface $container,
		protected config $config,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected language $language,
		protected template $template,
		protected string $php_ext,
		protected string $root_path
	)
	{
	}

	public static function getSubscribedServices(): array
	{
		return [];
	}

	/**
	 * Returns Twig namespace
	 */
	abstract protected function get_namespace(): string;

	/**
	 * Returns an icon
	 */
	abstract protected function get_icon(): string;

	/**
	 * Load current data
	 */
	abstract protected function load(array $member): void;

	public function set_tab_name(string $name): void
	{
		$this->name = $name;
	}

	public function restrict_bots(bool $restrict): void
	{
		$this->restrict_bots = $restrict;;
	}
}
