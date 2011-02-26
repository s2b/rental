(function ($) {

if (typeof calendarLinks == 'undefined') {
	var calendarLinks = calendarLinks || true;
}

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
		links: true,
		clickAction: false
	}, options);
	
	var $this = $(this);
	
	$this.find('.calendar_top .prev a, .calendar_top .next a').click(function () {
		$.post(this.href, 'ajax=true', function (data) {
			if (typeof data != undefined && data.status != 0 && data.content) {
				$this.empty().append(data.content).Calendar(options);
			} else {
				alert('Bei der Übertragung der Daten ist ein Fehler aufgetreten.');
			}
		}, 'json');
		return false;
	});
	
	if (options['links']) {
		var $el = $('.booking-title');
		if ($el.length > 0) {
			$el.replaceWith(function () {
				$t = $(this);
				return $('<a href="." title="Termin hervorheben" data-id="' + $t.attr('data-id') + '" data-tab="' + $t.attr('data-tab') + '">' + $t.text() + '</a>').click(function () {
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
	}
	
	if (options['clickAction']) {
	
	}
});

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

		// Formularvalidierung per AJAX im Dialog (z. B. beim Login)
		elModal.parents('form').submit(function () {
			return $.post(this.action, $(this).serialize() + '&ajax=true', function (data) {
				if (typeof data != undefined && data.status == 0) {
					$('#modal .errors').empty().append(data.content);
					return false;
				}
			}, 'json');
		});
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
		var el2 = $('<a href="." />').click(function () {
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
	$(':checkbox').click(function (e) {
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
	
	$('ul.listing.toggle').each(function () {
		$(this).hide().parent().prepend($('<a href="." class="listing-button" title="Details">Details</a>').click(function () {
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

	$tabbar.children('.tab').wrapInner($('<a href=".">').click(function () {
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

})(jQuery);