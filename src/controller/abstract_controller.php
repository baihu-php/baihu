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
use phpbb\config\config;
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
			'request' => request::class,
			'symfony_request' => symfony_request::class,
			'user' => user::class,
		];
   }

	protected function get_controller_helper(): controller_helper
	{
		return $this->container->get('baihu.controller_helper');
	}

	protected function get_auth(): auth
	{
		return $this->container->get('auth');
	}

	protected function get_user(): user
	{
		return $this->container->get('user');
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

		/**
		 * @event baihu.core.page_footer
		 */
		$this->dispatcher->trigger_event('baihu.core.page_footer');

		$headers = !empty($this->get_user()->data['is_bot']) ? ['X-PHPBB-IS-BOT' => 'yes'] : [];

		return new Response($this->template->assign_display('body'), $status_code, $headers);
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
}
