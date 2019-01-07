/*
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */
/**
 * Setup (required for Joomla! 3)
 */
if(typeof(akeeba) == 'undefined') {
	var akeeba = {};
}
if(typeof(akeeba.jQuery) == 'undefined') {
	akeeba.jQuery = window.jQuery.noConflict();
}

(function($) {
	$(window).load(function(){
		if($('#paymentForm')) {
			$('#paymentForm').submit();
		}
	});
})(akeeba.jQuery);
