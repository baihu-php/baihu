$(function() { // Avoid conflicts with other libraries

	'use strict';

	phpbb.addAjaxCallback('zebra', function(response) {

		if (response.success && response.refresh) {
			setTimeout(function () {
				location.reload(true);
			}, 3100);
		}
	});

}); // Avoid conflicts with other libraries
