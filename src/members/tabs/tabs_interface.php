<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\members\tabs;

/**
* Tabs interface
*/
interface tabs_interface
{
	/**
	* Set tab name
	*
	* @param string $name Name of the tab
	* @return void
	*/
	public function set_name(string $name);

	/**
	* Returns the name of the tab
	*
	* @return string Name of the tab
	*/
	public function get_name();

	/**
	* Return icon name
	*
	* @return string Icon name
	*/
	public function icon();
}
