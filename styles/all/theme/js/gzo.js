$(function() { // Avoid conflicts with other libraries

	'use strict';

	$('.action').click(function() {
		const data_toggle = $(this).attr('data-toggle');
		const data_array = ['drawer-left', 'drawer-right', 'notification', 'create'];

		if (data_toggle) {
			$('#overlay').addClass('overlay');
			$('.gzo-'+ data_toggle).toggleClass('is-active');
		}

		if (data_array.includes(data_toggle)) {
			$('html').addClass('no-scroll');
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
	$('#overlay').click(function() {
		$('.action-toggle').removeClass('is-active');
		$('#overlay').removeClass('overlay');

		if ($('html').hasClass('no-scroll')) {
			$('html').removeClass('no-scroll');
		}
	});

}); // Avoid conflicts with other libraries
