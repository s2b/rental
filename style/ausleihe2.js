(function ($) {

/*
 * Formulare vorbereiten bei onDomReady
 */
$(document).ready(function () {
	$('#no-js').hide();
	
	prepareModal(true);
	setupAutoSubmit();
	prepareListing();
	prepareTabs();
});

/*
 * AJAX für zurück/vor-Buttons vom Kalender, Links zum Markieren von Buchungen
 */
$.fn.Calendar = (function (options) {
	var options = $.extend({
		bookingLinks: true,
		dateSelection: false
	}, options);
	
	var $calendar = $(this);
	
	init = function () {
		initHeader();
		
		if (options['bookingLinks']) {
			bookingLinks();
		}
		
		if (options['dateSelection']) {
			dateSelection();
		}
	},
	
	initHeader = function () {
		$calendar.find('.calendar_top .prev a, .calendar_top .next a').click(function () {
			$.post(this.href, 'ajax=true', function (data) {
				if (typeof data != undefined && data.status != 0 && data.content) {
					$calendar.empty().append(data.content).Calendar(options);
				} else {
					alert('Bei der Übertragung der Daten ist ein Fehler aufgetreten.');
				}
			}, 'json');
			return false;
		});
	},
	
	bookingLinks = function () {
		var $el = $('.booking-title');
		if ($el.length > 0) {
			$el.replaceWith(function () {
				$t = $(this);
				return $('<a title="Termin hervorheben" data-id="' + $t.attr('data-id') + '" data-tab="' + $t.attr('data-tab') + '">' + $t.text() + '</a>').click(function () {
					$t = $(this);

					$('.tab[data-tab="' + $t.attr('data-tab') + '"] a').click();
	
					var className = 'booking-record-' + $t.attr('data-id');
					$('*[class*="booking-record-"]').each(function () {
						$t = $(this);
						$t.toggleClass('highlight', $t.hasClass(className));
					});
					
					return false;
				});
			});
		}
	
		$('*[class*="booking-record-"]').removeClass('highlight');
	},
	
	dateSelection = function () {
		$calendar.find('td[class!=empty]').css('cursor', 'pointer').click(function () {
			var $this = $(this);
			    
			if ($this.hasClass('selected')) {
				return;
			}
			
			$this.addClass('selected highlight');
			
			var html = '<select><option value="">-</option><option value="start">von</option><option value="end">bis</option></select>';
			var $select = $(html).change(function (e) {
				var $this = $(this),
				    selected = $this.val(),
				    date = $this.parent().attr('data-stamp');
				
				if (selected) {
					$('#calendar-result').find('input[name=' + $this.val() + ']').attr({
						value: date,
						'data-human': $this.parent().attr('data-human')
					});
				} else {
					$('#calendar-result').find('input').each(function () {
						if ($(this).val() == date) {
							$(this).attr({
								value: '',
								'data-human': ''
							});
						}
					});
				}
				
				$this.remove();
				
				dateSelectionRefresh();
				
				//e.stopPropagation();
			});
			
			$this.append($select);
		});
		dateSelectionRefresh();
	},
	
	dateSelectionRefresh = function () {
		var $parent = $('#calendar-result'),
		    startDate = $parent.find('input[name=start]').val(),
		    endDate = $parent.find('input[name=end]').val();
		
		var $days = $calendar.find('td[class!=empty]');
		$days.removeClass('selected highlight').find('select').remove();
				
		if (startDate || endDate) {	
			if (startDate && endDate && startDate > endDate) {
				$days.find('select').remove();
				
				$parent.find('input').attr('value', '');
				return;
			}
			
			var firstDate = $days.first().attr('data-stamp'),
			    highlight = ((!startDate || startDate < firstDate) && (!endDate || endDate > firstDate));
			$days.each(function () {
				var $this = $(this),
				    date = $this.attr('data-stamp');
				
				if (date == endDate || date == startDate) {
					highlight = (date != endDate);
					$this.addClass('highlight');
					return;
				}
				
				if (highlight) {
					$this.addClass('highlight');
				}
			});
		}
		
		$parent.find('#calendar-start').find('span').replaceWith('<span>' + ($parent.find('input[name=start]').attr('data-human') || '') + '</span>');
		$parent.find('#calendar-end').find('span').replaceWith('<span>' + ($parent.find('input[name=end]').attr('data-human') || '') + '</span>');
	};
	
	init();
	
	return $calendar;
});

})(jQuery);

/*
 * Modalen Dialog anzeigen
 */
function openModal(content, isForm) {
	$('#content').append(content);
	$('#black').hide().fadeIn();

	prepareModal(isForm);
}

/*
 * Modalen Dialog vorbereiten
 */
function prepareModal(isForm) {
	var elModal = $('#modal');

	if (isForm) {
		// Fokus auf erstes Formularfeld setzen
		elModal.find(':input:visible:enabled:first').focus();
	}

	// Abbrechen per JavaScript
	var el = elModal.find("input[name='cancel']");
	if (el.length > 0 || $('#black.closeable').length > 0) {
		var callback = function () {
			$('#black').fadeOut('', function () {
				$(this).remove();
			});
			return false;
		};

		el.click(callback);

		// Abbrechen beim Klick außerhalb des Dialogs
		$('#black').click(function (event) {
			if ($(event.target).closest('#modal').length == 0) {
				callback();
			}
		});
	}

	elModal.center({'factorY': 0.75});
	$(window).bind('resize', function() {
		elModal.center({'factorY': 0.75});
	});
}

/*
 * Formular nach Änderung automatisch abschicken und modalen Dialog öffnen
 */
function setupAutoSubmit() {
	$('.optbutton').hide();
	$('.autosubmit').change(function () {
		$.post($(this).parents('form').attr('action'), this.name + '=' + this.value + '&ajax=true', function (data) {
			if (typeof data != undefined && data.status != 0 && data.content) {
				openModal(data.content, true);
			} else {
				alert('Bei der Übertragung der Daten ist ein Fehler aufgetreten.');
			}
		}, 'json');
	});
}

/*
 * Versteckt die Status-Auswahlfelder in Auflistungen (z. B. Buchungen)
 */
function prepareListing() {
	/* Table listings */
	var el = $('.listing-action');
	if (el.length > 0) {
		var el2 = $('<a />').click(function () {
			$(this).hide().siblings('select').show();
			return false;
		});

		el.hide().siblings('span').wrap(el2);
	}

	$('.edit-link').click(function () {
		var $t = $(this);

		$.post($t.attr('href'), 'ajax=true', function (data) {
			if (typeof data != undefined && data.status != 0 && data.content) {
				$t.text(data.content);
			} else {
				alert('Bei der Übertragung der Daten ist ein Fehler aufgetreten.');
			}
		}, 'json');

		return false;
	});

	$('.modal-link').click(function () {
		var $t = $(this);

		$.post($t.attr('href'), 'ajax=true', function (data) {
			if (typeof data != undefined && data.status != 0 && data.content) {
				openModal(data.content, true);
			} else {
				alert('Bei der Übertragung der Daten ist ein Fehler aufgetreten.');
			}
		}, 'json');

		return false;
	});
	
	/* UL listings */
	$('ul.listing > li').find(':checkbox').click(function (e) {
		var $this = $(this);
		$('input[name="' + $this.attr('name') + '"]').attr('checked', $this.attr('checked'));
		
		e.stopPropagation();
	});
	
	$('ul.listing > li').each(function () {
		var $this = $(this);
		
		$checkbox = $this.children('.listing-checkbox').find(':checkbox');
		if ($checkbox.length > 0) {
			$this.css('cursor', 'pointer').click(function () {
				$checkbox = $this.children('.listing-checkbox').find(':checkbox');
				
				/* strange bugfix */
				$checkbox.attr('checked', !$checkbox.attr('checked'))
				         .click()
				         .attr('checked', !$checkbox.attr('checked'));
			});
		}
	});
	
	$('ul.listing .select-all').click(function () {
		$(this).closest('ul.listing').find(':checkbox').not($(this)).each(function () {
			$this = $(this);
			if (!$this.attr('checked')) {
				/* strange bugfix */
				$this.attr('checked', !$this.attr('checked'))
				     .click()
				     .attr('checked', !$this.attr('checked'));
			}
		});
		return false;
	});
	
	$('ul.listing .select-invert').click(function () {
		$(this).closest('ul.listing').find(':checkbox').not($(this)).each(function () {
			$this = $(this);
			/* strange bugfix */
			$this.attr('checked', !$this.attr('checked'))
			     .click()
			     .attr('checked', !$this.attr('checked'));
		});
		return false;
	});
	
	$('ul.listing.toggle').each(function () {
		$(this).hide().parent().prepend($('<a class="listing-button" title="Details">Details</a>').click(function () {
			var $this = $(this);
			
			if ($this.hasClass('opened')) {
				$this.nextAll('ul.listing.toggle').slideUp(function () {
					$this.removeClass('opened');
				});
			} else {
				$this.nextAll('ul.listing.toggle').slideDown(function () {
					$this.addClass('opened');
				});
			}
			
			return false;
		}));
	});
}

function prepareTabs() {
	var $tabs = $('.tabs');

	var $tabbar = $('<div class="tabbar" />');
	$tabbar.append($tabs.children('h3').addClass('tab')).append('<div class="clear" />');

	var $tabcontent = $('<div class="tabcontent" />');
	$tabcontent.append($tabs.children('.listing').addClass('tab-content'));

	$tabbar.children('.tab').wrapInner($('<a>').click(function () {
		var $parent = $(this).parent();
		var tab = $parent.attr('data-tab');

		$tabcontent = $('.tabs .tabcontent');
		$tabcontent.children('.tab-content[data-tab!="' + tab + '"]').hide();
		$tabcontent.children('.tab-content[data-tab="' + tab + '"]').show();

		$parent.addClass('tab-selected');
		$parent.siblings('.tab').removeClass('tab-selected');
		
		return false;
	}));

	var $firstTab = $tabbar.children('.tab').first();
	$firstTab.addClass('tab-selected');
	$tabcontent.children('.tab-content[data-tab!="' + $firstTab.attr('data-tab') + '"]').hide();

	$tabs.append($tabbar).append($tabcontent);
}