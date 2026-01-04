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

use phpbb\group\helper as group;
use phpbb\profilefields\manager as cp;

final class profile extends base
{
	public static function getSubscribedServices(): array
	{
		return array_merge(parent::getSubscribedServices(), [
			'group_helper' => '?'.group::class,
			'profilefields.manager' => '?'.cp::class,
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

	protected function get_group(): group
	{
		return $this->container->get('group_helper');
	}

	protected function get_cp_manager(): cp
	{
		return $this->container->get('profilefields.manager');
	}

	public function load(string $username): void
	{
		$member = $this->get_member_data($username);
		$auth	= $this->get_auth();
		$user	= $this->get_user();

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

		// Get group memberships
		// Also get visiting user's groups to determine hidden group memberships if necessary.
		$auth_hidden_groups = $user_id === (int) $user->data['user_id'] || $auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel');
		$sql_uid_ary = ($auth_hidden_groups) ? [$user_id] : [$user_id, (int) $user->data['user_id']];

		// Do the SQL thang
		$sql_ary = [
			'SELECT'	=> 'g.group_id, g.group_name, g.group_type, ug.user_id',

			'FROM'		=> [
				GROUPS_TABLE => 'g',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [USER_GROUP_TABLE => 'ug'],
					'ON'   => 'g.group_id = ug.group_id',
				],
			],

			'WHERE'		=> $this->db->sql_in_set('ug.user_id', $sql_uid_ary) . '
				AND ug.user_pending = 0',
		];

		/**
		* Modify the query used to get the group data
		*
		* @event core.modify_memberlist_viewprofile_group_sql
		* @var array	sql_ary			Array containing the query
		* @since 3.2.6-RC1
		*/
		$vars = ['sql_ary',];
		extract($this->dispatcher->trigger_event('core.modify_memberlist_viewprofile_group_sql', compact($vars)));

		$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_ary));

		// Divide data into profile data and current user data
		$profile_groups = $user_groups = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$row['user_id'] = (int) $row['user_id'];
			$row['group_id'] = (int) $row['group_id'];

			if ($row['user_id'] == $user_id)
			{
				$profile_groups[] = $row;
			}
			else
			{
				$user_groups[$row['group_id']] = $row['group_id'];
			}
		}
		$this->db->sql_freeresult($result);

		// Filter out hidden groups and sort groups by name
		$group_data = $group_sort = [];
		foreach ($profile_groups as $row)
		{
			if (!$auth_hidden_groups && $row['group_type'] == GROUP_HIDDEN && !isset($user_groups[$row['group_id']]))
			{
				// Skip over hidden groups the user cannot see
				continue;
			}

			$row['group_name'] = $this->get_group()->get_name($row['group_name']);

			$group_sort[$row['group_id']] = utf8_clean_string($row['group_name']);
			$group_data[$row['group_id']] = $row;
		}
		unset($profile_groups);
		unset($user_groups);
		asort($group_sort);

		/**
		* Modify group data before options is created and data is unset
		*
		* @event core.modify_memberlist_viewprofile_group_data
		* @var array	group_data			Array containing the group data
		* @var array	group_sort			Array containing the sorted group data
		* @since 3.2.6-RC1
		*/
		$vars = ['group_data', 'group_sort',];
		extract($this->dispatcher->trigger_event('core.modify_memberlist_viewprofile_group_data', compact($vars)));

		$group_current = '';
		$group_options = [];
		foreach ($group_sort as $group_id => $null)
		{
			$row = $group_data[$group_id];

			if ($row['group_id'] == $member['group_id'])
			{
				$group_current = $row['group_id'];
			}

			$group_options[$row['group_id']] = $row['group_name'];
		}

		unset($group_data);
		unset($group_sort);

		// What colour is the zebra
		$sql = 'SELECT friend, foe
			FROM ' . ZEBRA_TABLE . "
			WHERE zebra_id = $user_id
				AND user_id = {$user->data['user_id']}";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		$foe = $row ? (bool) $row['foe'] : false;
		$friend = $row ? (bool) $row['friend'] : false;

		$this->db->sql_freeresult($result);

		if ($this->config['load_onlinetrack'])
		{
			$sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
				FROM ' . SESSIONS_TABLE . "
				WHERE session_user_id = $user_id";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			$member['session_time'] = (isset($row['session_time'])) ? $row['session_time'] : 0;
			$member['session_viewonline'] = (isset($row['session_viewonline'])) ? $row['session_viewonline'] : 0;
			unset($row);
		}

		// Display a listing of board admins, moderators
		if (!function_exists('display_user_activity'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}

		if ($this->config['load_user_activity'])
		{
			display_user_activity($member);
		}

		// Do the relevant calculations
		$memberdays = max(1, round((time() - $member['user_regdate']) / 86400));
		$posts_per_day = $member['user_posts'] / $memberdays;
		$percentage = ($this->config['num_posts']) ? min(100, ($member['user_posts'] / $this->config['num_posts']) * 100) : 0;

		if ($member['user_sig'])
		{
			$parse_flags = ($member['user_sig_bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
			$member['user_sig'] = generate_text_for_display($member['user_sig'], $member['user_sig_bbcode_uid'], $member['user_sig_bbcode_bitfield'], $parse_flags, true);
		}

		// We need to check if the modules 'zebra', 'notes' and 'warn' are accessible to decide if we can display appropriate links
		$zebra_enabled = $friends_enabled = $foes_enabled = $user_notes_enabled = $warn_user_enabled = false;

		// Only check if the user is logged in
		if ($user->data['is_registered'])
		{
			if (!class_exists('p_master'))
			{
				include($this->root_path . 'includes/functions_module.' . $this->php_ext);
			}
			$module = new \p_master();

			$module->list_modules('ucp');
			$module->list_modules('mcp');

			$user_notes_enabled = $module->loaded('mcp_notes', 'user_notes');
			$warn_user_enabled = $module->loaded('mcp_warn', 'warn_user');
			$zebra_enabled = $module->loaded('ucp_zebra');
			$friends_enabled = $module->loaded('ucp_zebra', 'friends');
			$foes_enabled = $module->loaded('ucp_zebra', 'foes');

			unset($module);
		}

		// Custom Profile Fields
		$profile_fields = [];
		$cp_manager = $this->get_cp_manager();
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
		$vars = [
			'member',
			'user_notes_enabled',
			'warn_user_enabled',
			'zebra_enabled',
			'friends_enabled',
			'foes_enabled',
			'friend',
			'foe',
			'profile_fields',
		];
		extract($this->dispatcher->trigger_event('core.memberlist_view_profile', compact($vars)));

		$this->template->assign_vars(phpbb_show_profile($member, $user_notes_enabled, $warn_user_enabled));

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
			'L_POSTS_IN_QUEUE'			=> $this->language->lang('NUM_POSTS_IN_QUEUE', $member['posts_in_queue']),

			'POSTS_DAY'					=> $this->language->lang('POST_DAY', $posts_per_day),
			'POSTS_PCT'					=> $this->language->lang('POST_PCT', $percentage),

			'SIGNATURE'					=> $member['user_sig'],
			'POSTS_IN_QUEUE'			=> $member['posts_in_queue'],

			'S_PROFILE_ACTION'			=> append_sid("{$this->root_path}memberlist.$this->php_ext", 'mode=group'),
			'S_GROUP_CURRENT'			=> $group_current,
			'S_GROUP_OPTIONS'			=> $group_options,
			'S_CUSTOM_FIELDS'			=> isset($profile_fields['row']) && count($profile_fields['row']),

			'U_USER_ADMIN'				=> ($auth->acl_get('a_user')) ? append_sid(generate_board_url() . "/{$this->admin_path}index.$this->php_ext", 'i=users&amp;mode=overview&amp;u=' . $user_id, true, $user->session_id) : '',

			'U_USER_BAN'				=> ($auth->acl_get('m_ban') && $user_id != $user->data['user_id']) ? append_sid("{$this->root_path}mcp.$this->php_ext", 'i=ban&amp;mode=user&amp;u=' . $user_id, true, $user->session_id) : '',
			'U_MCP_QUEUE'				=> ($auth->acl_getf_global('m_approve')) ? append_sid("{$this->root_path}mcp.$this->php_ext", 'i=queue', true, $user->session_id) : '',

			'U_SWITCH_PERMISSIONS'		=> ($auth->acl_get('a_switchperm') && $user->data['user_id'] != $user_id) ? append_sid("{$this->root_path}ucp.$this->php_ext", "mode=switch_perm&amp;u={$user_id}&amp;hash=" . generate_link_hash('switchperm')) : '',
			'U_EDIT_SELF'				=> ($user_id == $user->data['user_id'] && $auth->acl_get('u_chgprofileinfo')) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=ucp_profile&amp;mode=profile_info') : '',

			'S_USER_NOTES'				=> $user_notes_enabled,
			'S_WARN_USER'				=> $warn_user_enabled,
			'S_ZEBRA'					=> $user->data['user_id'] != $user_id && $user->data['is_registered'] && $zebra_enabled,
			'U_ADD_FRIEND'				=> (!$friend && !$foe && $friends_enabled) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=zebra&amp;add=' . urlencode(html_entity_decode($member['username'], ENT_COMPAT))) : '',
			'U_ADD_FOE'					=> (!$friend && !$foe && $foes_enabled) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=zebra&amp;mode=foes&amp;add=' . urlencode(html_entity_decode($member['username'], ENT_COMPAT))) : '',
			'U_REMOVE_FRIEND'			=> ($friend && $friends_enabled) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=zebra&amp;remove=1&amp;usernames[]=' . $user_id) : '',
			'U_REMOVE_FOE'				=> ($foe && $foes_enabled) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=zebra&amp;remove=1&amp;mode=foes&amp;usernames[]=' . $user_id) : '',
		];

		/**
		* Modify user's template vars before we display the profile
		*
		* @event core.memberlist_modify_view_profile_template_vars
		*/
		$vars = ['template_ary',];
		extract($this->dispatcher->trigger_event('core.memberlist_modify_view_profile_template_vars', compact($vars)));

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
}
