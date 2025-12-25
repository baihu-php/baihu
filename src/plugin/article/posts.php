<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\article;

use baihu\baihu\src\plugin\base;

use phpbb\auth\auth;
use phpbb\exception\http_exception;
use phpbb\language\language;
use phpbb\pagination;
use phpbb\textformatter\s9e\renderer;
use phpbb\user;

final class posts extends base
{
	protected int $page = 0;
	protected bool $trim_messages = false;
	protected bool $is_trimmed = false;
	protected string $order = 'p.post_id DESC';

	public function __construct
	(
		protected auth $auth,
		protected language $language,
		protected pagination $pagination,
		protected renderer $renderer,
		protected user $user
	)
	{
	}

	public function set_page_offset(int $page): self
	{
		$this->page = ($page - 1) * (int) $this->get_config()['baihu_limit'];

		return $this;
	}

	/**
	* Trim messages [Set to true if you want news to be trimmed]
	*/
	public function trim_messages(bool $bool): self
	{
		$this->trim_messages = $bool;

		return $this;
	}

	/**
	* News categories
	*/
	public function categories(int $fid): string
	{
		$db = $this->get_db();
		$sql_ary = [
			'SELECT' => 'forum_id, forum_name',
			'FROM'	 => [
				FORUMS_TABLE => 'f',
			],

			'WHERE'	 => 'forum_type = ' . FORUM_POST,
		];

		$sql = $db->sql_build_query('SELECT', $sql_ary);
		$result = $db->sql_query($sql, 86400);

		$forum_ary = [];
		while ($row = $db->sql_fetchrow($result))
		{
			$forum_ary[(int) $row['forum_id']] = (string) $row['forum_name'];
		}
		$db->sql_freeresult($result);

		return $forum_ary[$fid] ?? '';
	}

	/**
	* Articles base
	*/
	public function load(int|null $forum_id = null): void
	{
		// Check permissions
		if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				throw new http_exception(403, 'SORRY_AUTH_READ', [$forum_id]);
			}

			login_box('', $this->language->lang('LOGIN_VIEWFORUM'));
		}

		// Assign breadcrumb TODO: Move into controller
		// $controller_helper->assign_breadcrumb($this->categories($forum_id), 'baihu_articles', ['fid' => $forum_id]);

		// Build sql data
		$db = $this->get_db();
		$sql_ary = $this->get_sql_data($forum_id);
		$sql = $db->sql_build_query('SELECT', $sql_ary);
		$result = $db->sql_query_limit($sql, (int) $this->get_config()['baihu_limit'], $this->page, 60);

		while ($row = $db->sql_fetchrow($result))
		{
			$this->get_template()->assign_block_vars('articles', $this->get_template_data($row));
		}
		$db->sql_freeresult($result);

		// Pagination
		if ($this->get_config()['baihu_pagination'] && null !== $this->page)
		{
			// Get total posts
			$sql_ary['SELECT'] = 'COUNT(p.post_id) AS num_posts';
			$sql = $db->sql_build_query('SELECT', $sql_ary);
			$result = $db->sql_query($sql);
			$total = (int) $db->sql_fetchfield('num_posts');
			$db->sql_freeresult($result);

			$base = [
				'routes' => [
					'baihu_articles',
					'baihu_articles_page',
				],
				'params' => ['id' => $forum_id],
			];

			$this->pagination->generate_template_pagination($base, 'pagination', 'page', $total, (int) $this->get_config()['baihu_limit'], $this->page);

			$this->get_template()->assign_var('total_news', $total);
		}
	}

	/**
	* @param int	$id	  Either forum or topic id
	* @param string $type By default it's forum, but could be topic
	*/
	public function get_sql_data(int $id, string $type = 'forum'): array
	{
		$build = new \baihu\baihu\src\db\helper($this->get_db());
		$build
			->select('t.topic_id, t.topic_title, t.topic_time, t.topic_views, t.topic_posts_approved, p.post_id, p.poster_id, p.post_text')
			->from([
				TOPICS_TABLE => 't',
				POSTS_TABLE => 'p',
			])
			->where('t.' . $type . '_id = ' . $id . '
				AND p.post_id = t.topic_first_post_id
				AND t.topic_status <> ' . ITEM_MOVED . '
				AND t.topic_visibility = 1')
			->order($this->order, $type === 'forum');

		return $build->get_sql_data();
	}

	/**
	* @param array $row Article data array
	*/
	public function get_template_data(array $row): array
	{
		$users_loader = $this->get_users_loader();
		$user_id = (int) $row['poster_id'];
		$user = $users_loader->get_user($user_id);
		$rank = $users_loader->get_rank_data($user);
		$text = $this->renderer->render($row['post_text']);

		return [
			'id'			  => $row['post_id'],
			'link'			  => $this->route('baihu_article', ['aid' => $row['topic_id']]),
			'title'			  => $this->truncate($row['topic_title'], $this->get_config()['baihu_title_length']),
			'date'			  => $this->user->format_date($row['topic_time']),

			'author'		  => $user_id,
			'author_name'	  => $user['username'],
			'author_color'	  => $user['user_colour'],
			'author_profile'  => $this->route('baihu_member', ['username' => $user['username']]),
			'author_avatar'	  => [$users_loader->get_avatar_data($user_id)],
			'author_rank'	  => $rank['rank_title'],
			'author_rank_img' => $rank['rank_img'],

			'views'		 => $row['topic_views'],
			'replies'	 => $row['topic_posts_approved'] - 1,
			'text'		 => $this->trim_messages ? $this->trim_message($text) : $text,
			'is_trimmed' => $this->is_trimmed,
		];
	}

	/**
	* Trim message
	*/
	public function trim_message(string $text): string
	{
		$this->is_trimmed = false;

		if (utf8_strlen($text) > (int) $this->get_config()['baihu_content_length'])
		{
			$this->is_trimmed = true;

			$offset = ((int) $this->get_config()['baihu_content_length'] - 3) - utf8_strlen($text);
			$text	= utf8_substr($text, 0, utf8_strrpos($text, ' ', $offset));
		}

		return $text;
	}
}
