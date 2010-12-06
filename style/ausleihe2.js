/*
 * Formulare vorbereiten bei onDomReady
 */
$(function () {
	prepareModal(true);
	setupAutoSubmit();
	prepareListing();
	prepareCalendar();
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
	var el = $('.listing-action');
	if (el.length > 0) {
		var el2 = $('<a href="." />').click(function () {
			$(this).hide().siblings('select').show();
			return false;
		});

		el.hide().siblings('span').wrap(el2);
	}

	$('.edit-link').click(function () {
		$t = $(this);

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
		$t = $(this);

		$.post($t.attr('href'), 'ajax=true', function (data) {
			if (typeof data != undefined && data.status != 0 && data.content) {
				openModal(data.content, true);
			} else {
				alert('Bei der Übertragung der Daten ist ein Fehler aufgetreten.');
			}
		}, 'json');

		return false;
	});
}

/*
 * AJAX für zurück/vor-Buttons vom Kalender, Links zum Markieren von Buchungen
 */
function prepareCalendar() {
	$('.calendar_top .prev a, .calendar_top .next a').click(function () {
		$.post(this.href, 'ajax=true', function (data) {
			if (typeof data != undefined && data.status != 0 && data.content) {
				$('#calendar').empty().append(data.content);
				prepareCalendar();
			} else {
				alert('Bei der Übertragung der Daten ist ein Fehler aufgetreten.');
			}
		}, 'json');
		return false;
	});
	
	var $el = $('.booking-title');
	if ($el.length > 0) {
		$el.replaceWith(function () {
			$t = $(this);
			return $('<a href="." title="Termin hervorheben" data-id="' + $t.attr('data-id') + '">' + $t.text() + '</a>').click(function () {
				var className = 'booking-record-' + $(this).attr('data-id');
				var $records = $('*[class*="booking-record-"]');
				var $record;

				for (var i = 0; i < $records.length; i++) {
					$record = $($records[i]);
					if ($record.hasClass(className)) {
						$record.toggleClass('highlight');
					} else {
						$record.removeClass('highlight');
					}
				}
				return false;
			});
		});
	}

	$('*[class*="booking-record-"]').removeClass('highlight');
}