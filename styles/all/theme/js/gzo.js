$(function() { // Avoid conflicts with other libraries

	'use strict';

	$('.gzo-action').click(function() {
		const data_toggle = $(this).attr('data-toggle');
		const data_array = ['drawer-left', 'drawer-right', 'notification'];

		if (data_toggle) {
			$('#gzo-overlay').addClass('gzo-overlay');
			$('.gzo-'+ data_toggle).toggleClass('is-active');
		}

		if (data_array.includes(data_toggle)) {
			$('html').addClass('gzo-no-scroll');
		}
	});

	// Inner menus in sidebars
	$('.gzo-navigation-btn').click(function() {
		const data_toggle = $(this).attr('data-toggle');

		if (data_toggle) {
			$('.gzo-'+ data_toggle).toggleClass('is-active');
		}
	});

	// Clear overlay
	$('#gzo-overlay').click(function() {
		$('.gzo-action-toggle').removeClass('is-active');
		$('#gzo-overlay').removeClass('gzo-overlay');

		if ($('html').hasClass('gzo-no-scroll')) {
			$('html').removeClass('gzo-no-scroll');
		}
	});

}); // Avoid conflicts with other libraries
