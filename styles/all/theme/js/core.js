const $page = '[data-container="page"]';

const $tooltip = '[data-tooltip="true"]';
const $tooltipContainer = '[data-tooltip-container="true"]';

/**
 * Returns clearance object for displaying content to avoid edges
 *
 * @param	  {object} $target	Element to test
 * @param	  {number} x		custom top offset
 * @param	  {number} y		custom left offset
 * @returns	  {object}			boolean for each edge of window and all dimensions and positions
 */
const edgeDetect = ($target, x, y) => {
	const $win = $(window);
	const tgt = {};

	if (!x) {
		x = $target.offset().left;
	}

	if (!y) {
		y = $target.offset().top;
	}

	tgt.left = x;
	tgt.top = y;
	tgt.width = $target.innerWidth();
	tgt.height = $target.innerHeight();
	tgt.bottom = (tgt.top + tgt.height);
	tgt.right = (tgt.left + tgt.width);

	tgt.currentTop = tgt.top - $win.scrollTop();
	tgt.currentLeft = tgt.left - $win.scrollLeft();
	tgt.currentRight = (tgt.currentLeft + tgt.width);
	tgt.currentBottom = (tgt.currentTop + tgt.height);

	tgt.isTop = (tgt.top - tgt.bottom) < 0;
	tgt.isLeft = (tgt.left - tgt.right) < 0;
	tgt.isRight = ($win.width - tgt.right) > tgt.width;
	tgt.isBottom = ($win.height - tgt.bottom) > tgt.height;

	return tgt;
};

/**
 * Handles state and creation of Tool Tips
 *
 * @param	  {object} event_	   Event
 * @constant  {object} $this
 * @constant  {object} $that
 * @constant  {string} tip
 * @constant  {object} link
 * @constant  {object} tipPos
 * @event	  Toggle#ShowTip
 * @event	  Toggle#HideTip
 */
$($tooltip).each(function () {
	const $this = $(this);
	let $that = {};
	$this.on({
		mouseenter: () => {
			$('body').append('<span class="gzo-tooltip" data-tooltip-container="true"></span>');
			$that = $($tooltipContainer);
			$that.append($this.attr('title'));
			const gap = 8; // set equal to default spacing unit size
			const link = edgeDetect($this);
			const tip = edgeDetect($that);

			// set left to center of link
			tip.left = ((link.left + (link.width / 2)) - (tip.width / 2));

			// not enough space in default location below link set above
			if ((link.currentBottom + gap + tip.height) > $(window).height()) {
				tip.top = (link.top - tip.height - gap);
			} else {
				tip.top = (link.bottom + gap);
			}

			$that.attr('style', 'left: ' + tip.left + 'px; top: ' + tip.top + 'px;');
			$that.toggleClass('is-active');
		},
		mouseleave: () => {
			$that.remove();
		},
	});
});
