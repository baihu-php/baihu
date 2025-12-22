<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace baihu\baihu\src\twig\extension;

use phpbb\template\twig\environment;

class form extends \Twig\Extension\AbstractExtension
{
	/**
	* Returns a list of functions to add to the existing list.
	 *
	 * @return \Twig\TwigFunction[]			Array of twig functions
	 */
	public function getFunctions()
	{
		return [
			new \Twig\TwigFunction('form_widget', [$this, 'form_widget'], ['needs_environment' => true]),
		];
	}

	public function form_widget(environment $env, array $form_data): void
	{
		if (!$form_data)
		{
			return;
		}

		foreach ($form_data as $row)
		{
			$s_custom = $row['OPTIONS']['s_custom'];
			$effix = is_bool($s_custom) && $s_custom === false ? '_custom' : '';
			$type = $row['type'] . $effix;

			$form = '@baihu_baihu/macros/form/' . $type . '.twig';
			if ($env->getLoader()->exists($form))
			{
				$env->loadTemplate($env->getTemplateClass($form), $form)->display($row);
			}
		}
	}
}
