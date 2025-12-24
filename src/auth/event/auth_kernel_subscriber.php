<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\auth\event;

use baihu\baihu\src\auth\attribute\is_granted as isGranted;
use baihu\baihu\src\auth\auth;
use phpbb\exception\http_exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class auth_kernel_subscriber implements EventSubscriberInterface
{
	public function __construct(protected auth $auth)
	{
	}

	/**
	 * Borrowed from the Symfony IsGrantedAttributeListener
	 *
	 * @author Ryan Weaver <ryan@knpuniversity.com>
	 */
	public function on_kernel_controller_arguments(ControllerArgumentsEvent $event): void
	{
		if (!\is_array($attributes = $event->getAttributes()[isGranted::class] ?? null))
		{
			return;
		}

		foreach ($attributes as $attribute)
		{
			if (!$this->auth->is_granted($attribute))
			{
				throw new http_exception($attribute->status_code ?? 403, $attribute->message ?? 'AREA_NO_ADMIN');
			}
		}
	}

	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::CONTROLLER_ARGUMENTS => ['on_kernel_controller_arguments', 20],
		];
	}
}
