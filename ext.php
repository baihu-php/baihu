<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu;

class ext extends \phpbb\extension\base
{
	/**
	* Compare versions & enable if equal or greater than 4.0.0
	*/
	public function is_enableable(): bool
	{
		return phpbb_version_compare(PHPBB_VERSION, '4.0.0-dev', '>=');
	}
}
