<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\event;

use baihu\baihu\src\controller\controller_helper;
use phpbb\auth\auth;
use phpbb\cache\driver\driver_interface as cache;
use phpbb\config\config;
use phpbb\cron\manager as cron_manager;
use phpbb\event\dispatcher;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class subscribers implements EventSubscriberInterface
{
	public function __construct(
		protected controller_helper $controller_helper,
		protected auth $auth,
		protected cache $cache,
		protected config $config,
		protected cron_manager $cron_manager,
		protected dispatcher $dispatcher,
		protected user $user
	)
	{
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'core.user_setup'		 => 'add_language',
			'core.page_header_after' => 'add_global_variables',
			'baihu.core.page_footer' => 'display_footer',
		];
	}

	/**
	 * Event core.user_setup
	 */
	public function add_language(): void
	{
		$this->controller_helper->add_language('common', 'baihu/baihu');
	}

	/**
	 * Event core.page_header_after
	 */
	public function add_global_variables(): void
	{
		$this->controller_helper->template->assign_vars([
			'U_AREAZ_MAIN' => $this->controller_helper->route('areaz_main'),
		]);
	}

	public function display_footer(): void
	{
		$this->controller_helper->template->assign_vars([
			'CREDIT_LINE' => $this->controller_helper->language->lang('POWERED_BY', '<a href="https://www.phpbb.com/">phpBB</a>&reg; Forum Software &copy; phpBB Limited'),
			'U_ACP'		  => ($this->auth->acl_get('a_') && !empty($this->user->data['is_registered'])) ? $this->controller_helper->route('baihu_admin_redirect') : '',
		]);

		$run_cron = true;

		/**
		 * @event baihu.core.page_footer_after
		 * @var bool run_cron	 Shall we run cron tasks
		 */
		$vars = ['run_cron'];
		extract($this->dispatcher->trigger_event('core.page_footer', compact($vars)));

		// Move into subscribers
		if ($run_cron)
		{
			$this->set_cron_task();
		}
	}

	/**
	 * Set cron task for footer
	 */
	protected function set_cron_task(): void
	{
		// Call cron-type script
		$call_cron = false;
		if (!defined('IN_CRON') && !$this->config['use_system_cron'] && !$this->config['board_disable'] && !$this->user->data['is_bot'] && !$this->cache->get('_cron.lock_check'))
		{
			$call_cron = true;
			$time_now = (!empty($this->user->time_now) && is_int($this->user->time_now)) ? $this->user->time_now : time();

			// Any old lock present?
			if (!empty($this->config['cron_lock']))
			{
				$cron_time = explode(' ', $this->config['cron_lock']);

				// If 1 hour lock is present we do not set a cron task
				if ($cron_time[0] + 3600 >= $time_now)
				{
					$call_cron = false;
				}
			}
		}

		// Call cron job?
		if ($call_cron)
		{
			$task = $this->cron_manager->find_one_ready_task();

			if ($task)
			{
				$cron_task_tag = $task->get_html_tag();
				$this->controller_helper->template->assign_var('RUN_CRON_TASK', $cron_task_tag);
			}
			else
			{
				$this->cache->put('_cron.lock_check', true, 60);
			}
		}
	}
}
