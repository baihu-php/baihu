<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\user;

use phpbb\language\language;

class time
{
	protected int $length = 1;

	public function __construct(private language $language)
	{
	}

	public function ago(string $date): string
	{
		$interval = date_create('now')->diff(new \DateTime($date));

		// Assign units that we will use for our time ago method.
		$units = array_filter([
			'year'	 => $interval->y,
			'month'	 => $interval->m,
			'day'	 => $interval->d,
			'hour'	 => $interval->h,
			'minute' => $interval->i,
			'second' => $interval->s,
		]);

		if (!$units || $this->length !== 1)
		{
			return $this->language->lang('UNKNOWN');
		}

		return $this->plural(array_slice($units, 0, (int) $this->length));
	}

	/**
	* Plural
	*
	* @param array $unit Time units (1, 2, 3... [numbers] & s, i, h... [sec, min aso])
	*/
	protected function plural(array $unit): string
	{
		$uot = (string) key($unit);
		$int = (int) $unit[$uot];

		return $this->language->lang('gzo_ago', $int, $this->language->lang($uot, $int));
	}

	/**
	* Calculate decade (Will be removed.. maybe not)
	*
	* @param string $uot Unit of time [second, minute...]
	* @param int	$int Time value [1, 2...]
	*/
	protected function calculate(string $uot, int $int): array|null
	{
		$arr = [];
		if ($uot !== 'year' || $int < 10)
		{
			return null;
		}

		$arr['decade'] = substr($int, 0, -1);

		return $arr;
	}
}
