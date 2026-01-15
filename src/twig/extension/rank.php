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
use Twig\Error\Error;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class rank extends AbstractExtension
{
	public function __construct()
	{
	}

	/**
	 * Returns a list of functions to add to the existing list.
	 *
	 * @return TwigFunction[]			Array of twig functions
	 */
	public function getFunctions(): array
	{
		return [
			new TwigFunction('baihu_rank', [$this, 'rank'], ['needs_environment' => true]),
		];
	}

	public function rank(environment $environment, array $data): string
	{
		$path = $data['ext_name'] ?? 'baihu_baihu';
		// $rank = $data['rank_special'] ? strtolower($data['rank_title']) : 'default';
		$rank = 'default';

		try
		{
			return $environment->render("@{$path}/macros/rank/{$rank}.twig", [
				'COLOR' => $data['rank_color'],
				'TITLE' => $data['rank_title'],
			]);
		}
		catch (Error $e)
		{
			return '';
		}
	}
}
