<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\twig\node;

use phpbb\template\twig\environment;
use Twig\Node\Expression\AbstractExpression;
use Twig\Compiler;
use Twig\Node\Node;

class event extends Node
{
	protected string $listener_directory = 'event/';

	/** @var array */
	protected $template_event_priority_array;

	public function __construct(AbstractExpression $expr, protected environment $environment, int $lineno, $tag = null, $template_event_priority_array = [])
	{
		$this->template_event_priority_array = $template_event_priority_array;

		parent::__construct(['expr' => $expr], [], $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 */
	public function compile(Compiler $compiler)
	{
		$compiler->addDebugInfo($this);

		$location = $this->listener_directory . $this->getNode('expr')->getAttribute('name');
		$file_ext = '.html';

		$template_event_listeners = [];

		// Group and sort extension template events in according to their priority (0 by default if not set)
		foreach ($this->environment->get_phpbb_extensions() as $ext_namespace => $ext_path)
		{
			$ext_namespace = str_replace('/', '_', $ext_namespace);
			$priority_key = intval($this->template_event_priority_array[$ext_namespace][$location] ?? 0);
			$template_event_listeners[$priority_key][] = $ext_namespace;
		}
		krsort($template_event_listeners);

		$template_event_listeners = array_merge(...$template_event_listeners);
		foreach ($template_event_listeners as $ext_namespace)
		{
			if (str_contains($ext_namespace, 'baihu'))
			{
				$file_ext = '.twig';
			}
			$event = '@' . $ext_namespace . '/' . $location . $file_ext;

			if ($this->environment->isDebug())
			{
				// If debug mode is enabled, lets check for new/removed EVENT
				//  templates on page load rather than at compile. This is
				//  slower, but makes developing extensions easier (no need to
				//  purge the cache when a new event template file is added)
				$compiler
					->write("if (\$this->env->getLoader()->exists('$event')) {\n")
					->indent()
				;
			}

			if ($this->environment->isDebug() || $this->environment->getLoader()->exists($event))
			{
				$compiler
					->write("\$previous_look_up_order = \$this->env->getNamespaceLookUpOrder();\n")

					// We set the namespace lookup order to be this extension first, then the main path
					->write("\$this->env->setNamespaceLookUpOrder(['{$ext_namespace}', '__main__']);\n")
					->write("\$this->env->loadTemplate(\$this->env->getTemplateClass('$event'), '$event')->display(\$context);\n")
					->write("\$this->env->setNamespaceLookUpOrder(\$previous_look_up_order);\n")
				;
			}

			if ($this->environment->isDebug())
			{
				$compiler
					->outdent()
					->write("}\n\n")
				;
			}
		}
	}
}
