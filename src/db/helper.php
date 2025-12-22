<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\db;

use phpbb\db\driver\driver_interface;

final class helper
{
	protected array $sql_array = [];

	public function __construct(protected driver_interface $db)
	{
	}

	public function select($data): self
	{
		$this->sql_array['SELECT'] = $data;

		return $this;
	}

	public function from($data): self
	{
		$this->sql_array['FROM'] = $data;

		return $this;
	}

	public function where($data): self
	{
		$this->sql_array['WHERE'] = $data;

		return $this;
	}

	public function order(string $data, bool $set = false): self
	{
		if ($set)
		{
			$this->sql_array['ORDER_BY'] = $data;
		}

		return $this;
	}

	public function get_sql_data(): array
	{
		return $this->sql_array;
	}

	public function unset_data(): void
	{
		$this->sql_array = [];
	}

	public function sql_build_query()
	{}

	public function sql_query()
	{}

	public function sql_query_limit()
	{}

	public function sql_fetchrow()
	{}
}
