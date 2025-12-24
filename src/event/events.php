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

/**
* Event
*/
final class events
{
	/** Area manager */
	public const BAIHU_AREA_MODIFY_DATA = 'baihu.area_modify_data';

	/** Area base class */
	public const BAIHU_AREA_MODIFY_NAVIGATION = 'baihu.area_modify_navigation';

	/** Information plugin */
	public const BAIHU_INFORMATION_BEFORE = 'baihu.information_before';

	/** Who's Online plugin */
	public const BAIHU_ONLINE_DATA_AFTER = 'baihu.online_data_after';

	/** Posts plugin */
	public const BAIHU_POSTS_MODIFY_CATEGORY_DATA = 'baihu.posts_modify_category_data';
	public const BAIHU_ARTICLE_MODIFY_TEMPLATE_DATA = 'baihu.article_modify_template_data';
}
