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

use baihu\baihu\src\controller\controller_helper;

use phpbb\auth\auth;
use phpbb\cache\driver\driver_interface as cache;
use phpbb\config\config;
use phpbb\cron\manager as cron_manager;
use phpbb\event\dispatcher;
use phpbb\language\language;
use phpbb\request\request_interface as request;
use phpbb\symfony_request;
use phpbb\template\template;
use phpbb\user;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
* Provides base functionality for controllers (Borrowed from phpBB Controller helper class)
*/
abstract class abstract_controller implements ServiceSubscriberInterface
{
	public function __construct(
		protected ContainerInterface $container,
		protected config $config,
		protected dispatcher $dispatcher,
		protected language $language,
		protected template $template,
		protected string $php_ext,
		protected string $admin_path
	)
	{
	}

	public static function getSubscribedServices(): array
	{
		return [
			'baihu.controller_helper' => controller_helper::class,
			'auth' => auth::class,
			'cache.driver' => cache::class,
			'cron_manager' => cron_manager::class,
			'request' => request::class,
			'symfony_request' => symfony_request::class,
			'user' => user::class,
		];
   }

	protected function get_controller_helper(): controller_helper
	{
		return $this->container->get('baihu.controller_helper');
	}

	/**
	* Automate setting up the page and creating the response object.
	*/
	public function render(string $template_file, string $page_title = '', int $status_code = 200, bool $display_online_list = false, int $item_id = 0, string $item = 'forum', bool $send_headers = false): Response
	{
		page_header($page_title, $display_online_list, $item_id, $item, $send_headers);

		$this->template->set_filenames([
			'body' => $template_file,
		]);

		$this->display_footer();

		$headers = !empty($this->user->data['is_bot']) ? ['X-PHPBB-IS-BOT' => 'yes'] : [];

		/**
		 * @event core.page_footer_after
		 */
		$this->dispatcher->trigger_event('core.page_footer_after', compact($vars));

		$response = new Response($this->template->assign_display('body'), $status_code, $headers);

		return $response;
	}

	/**
	 * Output a message
	 */
	public function message(string $message, array $parameters = [], string $title = 'INFORMATION', int $code = 200): JsonResponse|Response
	{
		array_unshift($parameters, $message);
		$message_text = call_user_func_array([$this->language, 'lang'], $parameters);
		$message_title = $this->language->lang($title);

		if ($this->container->get('request')->is_ajax())
		{
			global $refresh_data;

			return new JsonResponse(
				[
					'MESSAGE_TITLE'	 => $message_title,
					'MESSAGE_TEXT'	 => $message_text,
					'S_USER_WARNING' => false,
					'S_USER_NOTICE'	 => false,
					'REFRESH_DATA'	 => (!empty($refresh_data)) ? $refresh_data : null
				],
				$code
			);
		}

		$this->template->assign_vars([
			'MESSAGE_TEXT'	=> $message_text,
			'MESSAGE_TITLE'	=> $message_title,
		]);

		return $this->render('message_body.html', $message_title, $code);
	}

	/**
	 * Assigns automatic refresh time meta tag in template
	 */
	public function assign_meta_refresh_var(int $time, string $url): void
	{
		$this->template->assign_vars([
			'META' => '<meta http-equiv="refresh" content="' . $time . '; url=' . $url . '" />',
		]);
	}

	/**
	* Return the current url
	*/
	public function get_current_url(): string
	{
		return generate_board_url(true) . $this->container->get('request')->escape($this->container->get('symfony_request')->getRequestUri(), true);
	}

	/**
	 * Handle display actions for footer
	 */
	public function display_footer(): void
	{
		$auth = $this->container->get('auth');
		$user = $this->container->get('user');

		$this->template->assign_vars([
			'CREDIT_LINE' => $this->language->lang('POWERED_BY', '<a href="https://www.phpbb.com/">phpBB</a>&reg; Forum Software &copy; phpBB Limited'),
			'U_ACP'		  => ($auth->acl_get('a_') && !empty($user->data['is_registered'])) ? $this->get_controller_helper()->route('baihu_admin_redirect') : '',
		]);

		$run_cron = true;

		/**
		 * Execute code and/or overwrite page_footer()
		 *
		 * @event core.page_footer
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
		$cache = $this->container->get('cache.driver');
		$user = $this->container->get('user');

		// Call cron-type script
		$call_cron = false;
		if (!defined('IN_CRON') && !$this->config['use_system_cron'] && !$this->config['board_disable'] && !$user->data['is_bot'] && !$cache->get('_cron.lock_check'))
		{
			$call_cron = true;
			$time_now = (!empty($user->time_now) && is_int($user->time_now)) ? $user->time_now : time();

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
			$task = $this->container->get('cron_manager')->find_one_ready_task();

			if ($task)
			{
				$cron_task_tag = $task->get_html_tag();
				$this->template->assign_var('RUN_CRON_TASK', $cron_task_tag);
			}
			else
			{
				$cache->put('_cron.lock_check', true, 60);
			}
		}
	}
}
