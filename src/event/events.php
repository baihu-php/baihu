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

	/** Core user events */
	public const BAIHU_CORE_USER_CREATE_DATA = 'baihu.core.user_create_data';

	/** Core page events */
	public const BAIHU_CORE_PAGE_FOOTER = 'baihu.core.page_footer';
	public const BAIHU_CORE_PAGE_FOOTER_AFTER = 'baihu.core.page_footer_after';

	/** Statistics plugin */
	public const BAIHU_STATISTICS_BEFORE = 'baihu.statistics_before';

	/** Posts plugin */
	public const BAIHU_POSTS_MODIFY_CATEGORY_DATA = 'baihu.posts_modify_category_data';
	public const BAIHU_ARTICLE_MODIFY_TEMPLATE_DATA = 'baihu.article_modify_template_data';
}
