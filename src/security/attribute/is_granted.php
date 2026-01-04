<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\security\attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class is_granted
{
	public function __construct(
		/**
		* First argument
		*/
		public string $role,

		/**
		* Second argument
		*/
		public array|string $option,

		/**
		* Exception message
		*/
		public string|null $message = null,

		/**
		* Exception status code
		*/
		public int|null $status_code = null,
	)
	{
	}
}
