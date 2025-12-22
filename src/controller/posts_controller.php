<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\controller;

use phpbb\exception\http_exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class posts_controller extends abstract_controller
{
	public function __construct(protected string $php_ext)
	{
	}

	/**
	 * Article controller for route /article/{aid}
	 */
	public function article(int $aid): RedirectResponse
	{
		if (!$aid)
		{
			throw new http_exception(404, 'NO_TOPICS', [$aid]);
		}

		$params = [
			't' => $aid
		];

		$url = append_sid(generate_board_url() . "/viewtopic.{$this->php_ext}", $params, false);

		return new RedirectResponse($url);
	}

	/**
	 * Article controller for route /articles/{fid}
	 */
	public function articles(int $fid): RedirectResponse
	{
		if (!$fid)
		{
			throw new http_exception(404, 'NO_FORUM', [$fid]);
		}

		$params = [
			'f' => $fid
		];

		$url = append_sid(generate_board_url() . "/viewforum.{$this->php_ext}", $params, false);

		return new RedirectResponse($url);
	}

	/**
	 * Post controller for /post/article{fid}
	 *	  Redirects to right forum's posting page
	 */
	public function post(int $fid): RedirectResponse
	{
		// Borrowed from Ideas extension (phpBB)
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			throw new http_exception(404, 'LOGIN_REQUIRED');
		}

		$params = [
			'mode' => 'post',
			'f'	   => $fid,
		];

		$url = append_sid(generate_board_url() . "/posting.{$this->php_ext}", $params, false);

		return new RedirectResponse($url);
	}

	/**
	 * Rescent post controller for route /recent-post/aid-{aid}-p-{post_id}
	 */
	public function recent_post(int $aid, int $post_id): RedirectResponse
	{
		if (!$aid)
		{
			throw new http_exception(404, 'NO_TOPICS', [$aid]);
		}

		if (!$post_id)
		{
			throw new http_exception(404, 'NO_POSTS', [$post_id]);
		}

		$params = [
			't' => "{$aid}#p{$post_id}",
		];

		$url = append_sid(generate_board_url() . "/viewtopic.{$this->php_ext}", $params, false);

		return new RedirectResponse($url);
	}

	/**
	 * Recent topic controller for route /recent-article/{aid}
	 */
	public function recent_topic(int $aid): RedirectResponse
	{
		if (!$aid)
		{
			throw new http_exception(404, 'NO_TOPICS', [$aid]);
		}

		$params = [
			't' => $aid
		];

		$url = append_sid(generate_board_url() . "/viewtopic.{$this->php_ext}", $params, false);

		return new RedirectResponse($url);
	}
}
