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

use baihu\baihu\src\controller\controller_helper;
// use ganstaz\gzo\src\entity\manager as em;
// use ganstaz\gzo\src\form\form;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
* Provides base functionality for controllers
*/
abstract class abstract_controller implements ServiceSubscriberInterface
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;

	public function set_container(ContainerInterface $container): ContainerInterface|null
	{
		$previous = $this->container ?? null;
		$this->container = $container;

		return $previous;
	}

	public static function getSubscribedServices(): array
	{
		return [
			'baihu.controller_helper' => controller_helper::class,
			// 'ganstaz.gzo.entity.manager' => '?'.em::class,
			// 'ganstaz.gzo.form' => '?'.form::class
		];
   }

	protected function get_controller_helper(): controller_helper
	{
		return $this->container->get('baihu.controller_helper');
	}
}
