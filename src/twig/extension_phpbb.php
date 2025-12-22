<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\twig;

use phpbb\template\twig\extension;

class extension_phpbb extends extension
{
	/**
	 * Returns the token parser instance to add to the existing list.
	 */
	public function getTokenParsers(): array
	{
		return [
			new \phpbb\template\twig\tokenparser\defineparser,
			new \phpbb\template\twig\tokenparser\includeparser,
			new \phpbb\template\twig\tokenparser\includejs,
			new \phpbb\template\twig\tokenparser\includecss,
			new \baihu\baihu\src\twig\tokenparser\event($this->environment),
		];
	}
}
