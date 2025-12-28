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
use phpbb\pagination;
use phpbb\textformatter\s9e\renderer;
use phpbb\user;

final class posts extends base
{
	protected int $page = 0;
	protected bool $trim_messages = false;
	protected bool $is_trimmed = false;
	protected string $order = 'p.post_id DESC';

	public static function getSubscribedServices(): array
	{
		return array_merge(parent::getSubscribedServices(), [
			'auth' => '?'.auth::class,
			'pagination' => '?'.pagination::class,
			'text_formatter.s9e.renderer' => '?'.renderer::class,
			'user' => '?'.user::class
		]);
	}

	public function set_page_offset(int $page): self
	{
		$this->page = ($page - 1) * (int) $this->config['baihu_limit'];

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
	 * Get forum category name
	 */
	public function get_category_name(int $fid): string
	{
		$sql = 'SELECT forum_name
				FROM ' . FORUMS_TABLE . '
				WHERE forum_id = ' . $fid;
		$result = $this->db->sql_query($sql, 3600);
		$category_name = $this->db->sql_fetchfield('forum_name');
		$this->db->sql_freeresult($result);

		return $category_name ?? '';
	}

	/**
	* Articles base
	*/
	public function load(int|null $forum_id = null): void
	{
		// Check permissions
		if (!$this->container->get('auth')->acl_gets('f_list', 'f_read', $forum_id))
		{
			if ($this->container->get('user')->data['user_id'] != ANONYMOUS)
			{
				throw new http_exception(403, 'SORRY_AUTH_READ', [$forum_id]);
			}

			login_box('', $this->get_language()->lang('LOGIN_VIEWFORUM'));
		}

		// Build sql data
		$sql_ary = $this->get_sql_data($forum_id);
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query_limit($sql, (int) $this->config['baihu_limit'], $this->page, 60);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('articles', $this->get_template_data($row));
		}
		$this->db->sql_freeresult($result);

		// Pagination
		if ($this->config['baihu_pagination'] && null !== $this->page)
		{
			// Get total posts
			$sql_ary['SELECT'] = 'COUNT(p.post_id) AS num_posts';
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$total = (int) $this->db->sql_fetchfield('num_posts');
			$this->db->sql_freeresult($result);

			$base = [
				'routes' => [
					'baihu_articles',
					'baihu_articles_page',
				],
				'params' => ['id' => $forum_id],
			];

			$this->container->get('pagination')->generate_template_pagination($base, 'pagination', 'page', $total, (int) $this->config['baihu_limit'], $this->page);

			$this->template->assign_var('total_news', $total);
		}
	}

	/**
	* @param int	$id	  Either forum or topic id
	* @param string $type By default it's forum, but could be topic
	*/
	public function get_sql_data(int $id, string $type = 'forum'): array
	{
		$build = new \baihu\baihu\src\db\helper($this->db);
		$build
			->select('t.topic_id, t.topic_title, t.topic_time, t.topic_views, t.topic_posts_approved, p.post_id, p.poster_id, p.post_text,
				u.user_id, u.username, u.user_posts, u.user_rank, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height')
			->from([
				TOPICS_TABLE => 't',
				POSTS_TABLE => 'p',
				USERS_TABLE => 'u',
			])
			->where('t.' . $type . '_id = ' . $id . '
				AND p.post_id = t.topic_first_post_id
				AND u.user_id = p.poster_id
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
		$users_loader->load_user($row);
		$user_id = (int) $row['poster_id'];
		$user = $users_loader->get_user($user_id);
		$rank = $users_loader->get_rank_data($user);
		$helper = $this->get_controller_helper();
		$text = $this->container->get('text_formatter.s9e.renderer')->render($row['post_text']);

		return [
			'id'			  => $row['post_id'],
			'link'			  => $helper->route('baihu_article', ['aid' => $row['topic_id']]),
			'title'			  => $this->truncate($row['topic_title'], $this->config['baihu_title_length']),
			'date'			  => $this->container->get('user')->format_date($row['topic_time']),

			'author'		  => $user_id,
			'author_name'	  => $user['username'],
			'author_color'	  => $user['user_colour'],
			'author_profile'  => $helper->route('baihu_member', ['username' => $user['username']]),
			'author_avatar'	  => [$users_loader->get_avatar_data($user_id)],
			'author_rank'	  => $rank['rank_title'] ?? '',
			'author_rank_img' => $rank['rank_img'] ?? '',

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

		if (utf8_strlen($text) > (int) $this->config['baihu_content_length'])
		{
			$this->is_trimmed = true;

			$offset = ((int) $this->config['baihu_content_length'] - 3) - utf8_strlen($text);
			$text	= utf8_substr($text, 0, utf8_strrpos($text, ' ', $offset));
		}

		return $text;
	}
}
