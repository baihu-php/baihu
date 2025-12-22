<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin;

class data
{
	protected static array $data = [];

	public function set_section_data(string $section, string $name, string $ext_name): void
	{
		self::$data[$section][$name] = $ext_name;
	}

	public function get(string $section): array
	{
		return self::$data[$section] ?? [];
	}

	public function has(string $section): bool
	{
		return count($this->get($section));
	}

	public function all(): array
	{
		return self::$data;
	}
}
