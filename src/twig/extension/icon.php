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
use phpbb\template\twig\extension\icon as phpbb_icon;

class icon extends phpbb_icon
{
	public function icon(environment $environment, $type, $icon, $title = '', $hidden = false, $classes = '', array $attributes = [])
	{
		$type = strtolower($type);
		$icon = is_array($icon) ? $this->get_first_icon($icon) : $icon;

		if (empty($icon))
		{
			return '';
		}

		$not_found	= false;
		$source		= '';
		$view_box	= '';

		$path = $type === 'gzo' ? '@baihu_baihu/' : '';

		switch ($type)
		{
			case 'font':
				$classes = $this->insert_fa_class($classes);
			break;

			case 'png':
				$filesystem	= $environment->get_filesystem();
				$root_path	= $environment->get_web_root_path();

				// Iterate over the user's styles and check for icon existance
				foreach ($this->get_style_list() as $style_path)
				{
					if ($filesystem->exists("{$root_path}styles/{$style_path}/theme/png/{$icon}.png"))
					{
						$source = "{$root_path}styles/{$style_path}/theme/png/{$icon}.png";

						break;
					}
				}

				// Check if the icon was found or not
				$not_found = empty($source);
			break;

			case 'gzo':
			case 'svg':
				try
				{
					// Try to load and prepare the SVG icon
					$file	= $environment->load($path . 'svg/' . $icon . '.svg');
					$source	= $this->prepare_svg($file, $view_box);

					if (empty($view_box))
					{
						return '';
					}
				}
				catch (\Twig\Error\LoaderError $e)
				{
					// Icon was not found
					$not_found = true;
				}
				catch (\Twig\Error\Error $e)
				{
					return $e->getMessage();
				}
			break;

			default:
				return '';
		}

		// If no PNG or SVG icon was found, display a default 404 SVG icon.
		if ($not_found)
		{
			try
			{
				$file	= $environment->load($path . 'svg/404.svg');
				$source	= $this->prepare_svg($file, $view_box);
			}
			catch (\Twig\Error\Error $e)
			{
				return $e->getMessage();
			}

			$type = 'svg';
			$icon = '404';
		}

		try
		{
			return $environment->render("{$path}macros/icons/{$type}.twig", [
				'ATTRIBUTES'	=> (string) $this->implode_attributes($attributes),
				'CLASSES'		=> (string) $classes,
				'ICON'			=> (string) $icon,
				'SOURCE'		=> (string) $source,
				'TITLE'			=> (string) $title,
				'TITLE_ID'		=> $title && $type === 'svg' ? unique_id() : '',
				'VIEW_BOX'		=> (string) $view_box,
				'S_HIDDEN'		=> (bool) $hidden,
			]);
		}
		catch (\Twig\Error\Error $e)
		{
			return $e->getMessage();
		}
	}
}
