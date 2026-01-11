$(function() { // Avoid conflicts with other libraries

	'use strict';

	phpbb.addAjaxCallback('zebra', function(response) {

		if (response.success) {
			if (response.refresh) {
				setTimeout(function () {
					location.reload(true);
				}, 3100);

				// location.reload();
			}
		}
	});

}); // Avoid conflicts with other libraries
