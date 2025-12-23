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

use baihu\baihu\src\enum\baihu;
use baihu\baihu\src\event\events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class area_subscriber implements EventSubscriberInterface
{
	public function gzo_modify_data($event): void
	{
		$area = $event['areas'];
		$area[baihu::TYPE] = [
			'type'		=> baihu::TYPE,
			'auth'		=> 'a_',
			'lang'		=> 'area_baihu',
			'ext_name'	=> 'baihu/baihu',
			'dashboard' => 'DASHBOARD',
			'route'		=> 'baihu_main',
		];

		$event['areas'] = $area;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			events::GZO_AREA_MODIFY_DATA => 'gzo_modify_data'
		];
	}
}
