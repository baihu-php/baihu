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

class posts_redirect_controller extends abstract_controller
{
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

		return $this->redirect_to_legacy_page('viewtopic', $params);
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

		return $this->redirect_to_legacy_page('viewforum', $params);
	}

	/**
	 * Post controller for /post/article{fid}
	 *	  Redirects to right forum's posting page
	 */
	public function post(int $fid): RedirectResponse
	{
		// Borrowed from Ideas extension (phpBB)
		if ($this->get_user()->data['user_id'] == ANONYMOUS)
		{
			throw new http_exception(404, 'LOGIN_REQUIRED');
		}

		$params = [
			'mode' => 'post',
			'f'	   => $fid,
		];

		return $this->redirect_to_legacy_page('posting', $params);
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

		return $this->redirect_to_legacy_page('viewtopic', $params);
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

		return $this->redirect_to_legacy_page('viewtopic', $params);
	}
}
