$(function() {

	'use strict';

	const url = window.location + '';
	const path = url.replace(window.location.protocol + '//' + window.location.host + '/', '');
	const element = $('ul.gzo-area-navigation a').filter(function() {
		return this.href === url || this.href === path;
	});

	element.parentsUntil('.gzo-area-navigation').each(function() {
		if ($(this).is('li')) {
			if ($(this).children('a').length !== 0) {
				$(this).children('a').addClass('active');
			}
			else if ($(this).children('button').length !== 0) {
				$(this).children('button').addClass('active');
			}

			$(this).parent('ul.gzo-area-navigation').length === 0
				? $(this).addClass('active')
				: $(this).addClass('selected');
		}
		else if (!$(this).is('ul') && $(this).children('a').length === 0) {
			$(this).addClass('selected');
		}
		else if ($(this).is('ul')) {
			$(this).addClass('is-active');
		}
	});

	element.addClass('active');
	$('.gzo-area-navigation button').click(function(e) {
		if (!$(this).hasClass('active')) {
			// hide any open menus and remove all other classes
			$('ul', $(this).parents('ul:first')).removeClass('is-active');
			$('button', $(this).parents('ul:first')).removeClass('active');
			$('button', $(this).parents('ul:first')).removeClass('gzo-rotate');

			// open our new menu and add the open class
			$(this).next('ul').addClass('is-active');
			$(this).addClass('active');
		}
		else if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).parents('ul:first').removeClass('active');
			$(this).next('ul').removeClass('is-active');
		}

		// console.log(this);

		$(this).toggleClass('gzo-rotate');
		e.preventDefault();
	})
});
