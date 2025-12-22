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
	public const GZO_AREA_MODIFY_DATA = 'ganstaz.gzo.area_modify_data';

	/** Area base class */
	public const GZO_AREA_MODIFY_NAVIGATION = 'ganstaz.gzo.area_modify_navigation';

	/** Information plugin */
	public const GZO_INFORMATION_BEFORE = 'ganstaz.gzo.information_before';

	/** Who's Online plugin */
	public const GZO_ONLINE_DATA_AFTER = 'ganstaz.gzo.online_data_after';

	/** Posts plugin */
	public const GZO_POSTS_MODIFY_CATEGORY_DATA = 'ganstaz.gzo.posts_modify_category_data';

	/** Posts plugin */
	public const GZO_ARTICLE_MODIFY_TEMPLATE_DATA = 'ganstaz.gzo.article_modify_template_data';
}
