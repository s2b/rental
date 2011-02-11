(function($){
	$.fn.extend({
		center: function (options) {
			var options = $.extend({
				'factorX': 1,
				'factorY': 1
			}, options);
			
			return this.each(function() {
				var top = (($(window).height() - $(this).outerHeight()) / 2) * options.factorY;
				var left = (($(window).width() - $(this).outerWidth()) / 2) * options.factorX;
				$(this).css({position:'absolute', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
			});
		}
	});
})(jQuery);