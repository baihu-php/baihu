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
use phpbb\language\language;
use phpbb\request\request_interface as request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class manager
{
	protected readonly string $u_action;

	public function __construct(
		protected driver_interface $db,
		protected language $language,
		protected request $request
	)
	{
	}

	public function create(string $table, array $data)
	{
		$this->db->sql_query('INSERT INTO ' . $table . ' ' .
			$this->db->sql_build_array('INSERT', $data)
		);
	}

	public function delete(string $table, array $data)
	{
		$sql = 'DELETE FROM ' . $table . '
			WHERE ' . $data[0] . ' = ' . $data[1];

		if (isset($data[4]))
		{
			$sql = $sql . ' AND ' . $data[2] . ' = ' . $data[3];
		}

		$this->db->sql_query($sql);
	}

	public function action($mode, $table, $data, string $message, array $params = []): JsonResponse
	{
		if (confirm_box(true))
		{
			// Load action (Insert, Delete, Update)
			$this->{$mode}($table, $data);

			return $this->message($message);
		}
		else
		{
			confirm_box(false, $this->language->lang('CONFIRM_OPERATION'), build_hidden_fields($params));
		}
	}

	public function message(string $message, array $parameters = [], string $title = 'INFORMATION', int $code = 200): JsonResponse
	{
		array_unshift($parameters, $message);
		$message_text = call_user_func_array([$this->language, 'lang'], $parameters);
		$message_title = $this->language->lang($title);

		if ($this->request->is_ajax())
		{
			return new JsonResponse(
				[
					'success'		=> true,
					'MESSAGE_TITLE' => $message_title,
					'MESSAGE_TEXT'	=> $message_text,
					'refresh'		=> true
				],
				$code
			);
		}
	}
}
