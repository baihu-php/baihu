<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\area\event;

use baihu\baihu\src\area\manager as area_manager;
use phpbb\exception\http_exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class area_kernel_subscriber implements EventSubscriberInterface
{
	public function __construct(protected area_manager $area_manager)
	{
	}

	/**
	 * If this is an Area request and authorization check passes, it will create
	 * the data and set up template variable.
	 */
	public function on_kernel_request(RequestEvent $event): void
	{
		$request = $event->getRequest();
		$type = strstr($request->attributes->get('_route'), '_', true);

		if (!$this->area_manager->available($type))
		{
			return;
		}

		if (!$this->area_manager->authorize($type))
		{
			throw new http_exception(403, 'GZO_NO_ADMIN');
		}

		$this->area_manager->build_area_data();

		$this->area_manager->set_template_var(true);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::REQUEST => 'on_kernel_request'
		];
	}
}
