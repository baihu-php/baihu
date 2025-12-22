<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\twig\tokenparser;

use phpbb\template\twig\environment;
use Twig\TokenParser\AbstractTokenParser;
use Twig\Node\Node;
use Twig\Token;

class event extends AbstractTokenParser
{
	/** @var array */
	protected $template_event_priority_array;

	public function __construct(protected environment $environment)
	{
		$phpbb_dispatcher = $this->environment->get_phpbb_dispatcher();

		$template_event_priority_array = [];
		/**
		 * Allows assigning priority to template event listeners
		 *
		 * @event core.twig_event_tokenparser_constructor
		 * @var	array	template_event_priority_array	Array with template event priority assignments per extension namespace
		 *
		 * @since 4.0.0-a1
		 */
		if ($phpbb_dispatcher)
		{
			$vars = ['template_event_priority_array'];
			extract($phpbb_dispatcher->trigger_event('core.twig_event_tokenparser_constructor', compact($vars)));
		}

		$this->template_event_priority_array = $template_event_priority_array;
		unset($template_event_priority_array);
	}

	/**
	 * Parses a token and returns a node.
	 */
	public function parse(Token $token): Node
	{
		$expr = $this->parser->getExpressionParser()->parseExpression();

		$stream = $this->parser->getStream();
		$stream->expect(Token::BLOCK_END_TYPE);

		return new \baihu\baihu\src\twig\node\event($expr, $this->environment, $token->getLine(), $this->getTag(), $this->template_event_priority_array);
	}

	/**
	 * Gets the tag name associated with this token parser.
	 */
	public function getTag(): string
	{
		return 'EVENT';
	}
}
