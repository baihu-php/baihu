<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\plugin\profile\tabs;

use baihu\baihu\src\plugin\article\posts;
use phpbb\auth\auth;
use phpbb\profilefields\manager as cp;
use phpbb\user;

final class profile extends base
{
	public static function getSubscribedServices(): array
	{
		return array_merge(parent::getSubscribedServices(), [
			'baihu.posts' => '?'.posts::class,
			'auth' => '?'.auth::class,
			'profilefields.manager' => '?'.cp::class,
			'user' => '?'.user::class,
		]);
	}

	public function get_namespace(): string
	{
		return '@baihu_baihu/';
	}

	public function get_icon(): string
	{
		return 'overview';
	}

	public function load(array $member): void
	{
		$auth = $this->container->get('auth');
		$user = $this->container->get('user');

		// a_user admins and founder are able to view inactive users and bots to be able to manage them more easily
		// Normal users are able to see at least users having only changed their profile settings but not yet reactivated.
		if (!$auth->acl_get('a_user') && $user->data['user_type'] != USER_FOUNDER)
		{
			if ($member['user_type'] == USER_IGNORE)
			{
				throw new http_exception(404, 'NO_USER');
			}
			else if ($member['user_type'] == USER_INACTIVE && $member['user_inactive_reason'] != INACTIVE_PROFILE)
			{
				throw new http_exception(404, 'NO_USER');
			}
		}

		$user_id = (int) $member['user_id'];

		// Do the relevant calculations
		$percentage = ($this->config['num_posts']) ? min(100, ($member['user_posts'] / $this->config['num_posts']) * 100) : 0;

		// Custom Profile Fields
		$profile_fields = [];
		$cp_manager = $this->container->get('profilefields.manager');
		if ($this->config['load_cpf_viewprofile'])
		{
			$profile_fields = $cp_manager->grab_profile_fields_data($user_id);
			$profile_fields = (isset($profile_fields[$user_id])) ? $cp_manager->generate_profile_fields_template_data($profile_fields[$user_id]) : [];
		}

		/**
		* Modify user data before we display the profile
		*
		* @event core.memberlist_view_profile
		*/
		$vars = ['member', 'profile_fields',];
		extract($this->dispatcher->trigger_event('core.memberlist_view_profile', compact($vars)));

		$member['posts_in_queue'] = 0;

		// If the user has m_approve permission or a_user permission, then display unapproved posts
		if ($auth->acl_getf_global('m_approve') || $auth->acl_get('a_user'))
		{
			$sql = 'SELECT COUNT(post_id) as posts_in_queue
				FROM ' . POSTS_TABLE . '
				WHERE poster_id = ' . $user_id . '
					AND ' . $this->db->sql_in_set('post_visibility', [ITEM_UNAPPROVED, ITEM_REAPPROVE]);
			$result = $this->db->sql_query($sql);
			$member['posts_in_queue'] = (int) $this->db->sql_fetchfield('posts_in_queue');
			$this->db->sql_freeresult($result);
		}

		// Define the main array of vars to assign to memberlist_view.html
		$template_ary = [
			'POSTS_IN_QUEUE'   => $member['posts_in_queue'],
			'L_POSTS_IN_QUEUE' => $this->language->lang('NUM_POSTS_IN_QUEUE', $member['posts_in_queue']),
			'U_MCP_QUEUE'	   => ($auth->acl_getf_global('m_approve')) ? append_sid("{$this->root_path}mcp.$this->php_ext", 'i=queue', true, $user->session_id) : '',

			'POSTS_PCT'		   => $this->language->lang('POST_PCT', $percentage),

			'S_CUSTOM_FIELDS'  => isset($profile_fields['row']) && count($profile_fields['row']),
		];

		// Assign vars to profile controller
		$this->template->assign_vars($template_ary);

		if (!empty($profile_fields['row']))
		{
			$this->template->assign_vars($profile_fields['row']);
		}

		if (!empty($profile_fields['blockrow']))
		{
			foreach ($profile_fields['blockrow'] as $field_data)
			{
				$this->template->assign_block_vars('custom_fields', $field_data);
			}
		}

		// Inactive reason/account?
		if ($member['user_type'] == USER_INACTIVE)
		{
			$this->language->add_lang('acp/common');

			$this->template->assign_vars([
				'S_USER_INACTIVE'	   => true,
				'USER_INACTIVE_REASON' => $this->get_inactivity_reason($member['user_inactive_reason'])
			]);
		}

		$this->get_user_posts($member);
	}

	protected function get_inactivity_reason(string $user_inactive_reason): string
	{
		return match($user_inactive_reason)
		{
			INACTIVE_REGISTER => $this->language->lang('INACTIVE_REASON_REGISTER'),
			INACTIVE_PROFILE  => $this->language->lang('INACTIVE_REASON_PROFILE'),
			INACTIVE_MANUAL	  => $this->language->lang('INACTIVE_REASON_MANUAL'),
			INACTIVE_REMIND	  => $this->language->lang('INACTIVE_REASON_REMIND'),
			default			  => $this->language->lang('INACTIVE_REASON_UNKNOWN')
		};
	}

	protected function get_user_posts(array $member)
	{
		$sql = 'SELECT t.topic_id, t.topic_first_post_id, t.forum_id, t.topic_title, t.topic_time, t.topic_views, t.topic_posts_approved, p.post_id, p.poster_id, p.post_text, p.post_time
				FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
				WHERE t.topic_id = p.topic_id
					AND p.poster_id = ' . (int) $member['user_id'] . '
					AND t.topic_status <> ' . ITEM_MOVED . '
					AND t.topic_visibility = 1
				ORDER BY p.post_id DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['baihu_limit'], 0, 60);

		$posts = $this->container->get('baihu.posts');
		$posts->trim_messages(true)
			->set_profile_posts(true);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('user_posts', $posts->get_template_data(array_merge($row, $member)));
		}
		$this->db->sql_freeresult($result);
	}
}
