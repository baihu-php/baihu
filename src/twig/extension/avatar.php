<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\twig\extension;

use phpbb\avatar\helper;
use phpbb\avatar\manager;
use phpbb\template\twig\environment;
use phpbb\template\twig\extension\avatar as phpbb_avatar;
use Twig\Error\Error;

class avatar extends phpbb_avatar
{
	public function __construct(protected helper $helper)
	{
	}

	public function get_avatar(environment $environment, string $mode, array $row, string|null $alt, bool|null $ignore_config, bool|null $lazy, string $classes = '', bool $s_online = false): string
	{
		$alt = $alt ?? false;
		$ignore_config = $ignore_config ?? false;
		$lazy = $lazy ?? false;

		$avatar = $row;
		if (isset($avatar['AVATAR_SOURCE']))
		{
			$avatar = [
				'src'	 => $avatar['AVATAR_SOURCE'],
				'title'	 => $avatar['AVATAR_TITLE'],
				'width'	 => $avatar['AVATAR_WIDTH'],
				'height' => $avatar['AVATAR_HEIGHT']
			];
		}

		if (isset($row[$mode . '_avatar']))
		{
			$row = manager::clean_row($row, $mode);
			$avatar = $this->helper->get_avatar($row, $alt, $ignore_config, $lazy);
			$avatar['title'] = 'USER_AVATAR';
		}

		try
		{
			return $environment->render('@baihu_baihu/macros/user/avatar.twig', [
				'SRC'	   => $lazy ? $this->helper->get_no_avatar_source() : $avatar['src'],
				'DATA_SRC' => $lazy ? $avatar['src'] : '',
				'WIDTH'	   => $avatar['width'],
				'HEIGHT'   => $avatar['height'],
				'TITLE'	   => $avatar['title'],
				'LAZY'	   => $lazy,
				'CLASSES'  => $classes,
				'S_ONLINE' => $s_online
			]);
		}
		catch (Error $e)
		{
			return '';
		}
	}
}
