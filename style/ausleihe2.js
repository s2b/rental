/*
 * Formulare vorbereiten bei onDomReady
 */
$(function () {
	prepareModal(true);
	setupAutoSubmit();
	prepareStatusSelects();
	prepareDates();
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
				if (data.status == 0) {
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

/*
	el = elModal.find('table.calendar');
	if (el.length > 0) {
		el.find('th.left a').click(function() {
			$('#black').remove();
			$.post(base_url + this.href, 'ajax=true', function (data) {
				if (data.status == 1) {
					openModal(data.content);
				}
			}, 'json');
			return false;
		});

		el.find('th.left a').click(function() {
			$('#black').remove();
			$.post(base_url + this.href, 'ajax=true', function (data) {
				if (data.status == 1) {
					openModal(data.content);
				}
			}, 'json');
			return false;
		});
	}
*/
}

/*
 * Formular nach Änderung automatisch abschicken und modalen Dialog öffnen
 */
function setupAutoSubmit() {
	$('.optbutton').hide();
	$('.autosubmit').change(function () {
		$.post($(this).parents('form').attr('action'), this.name + '=' + this.value + '&ajax=true', function (data) {
			if (typeof data.status != undefined && data.status != 0 && data.content) {
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
function prepareStatusSelects() {
	var el = $('.listing-action');
	if (el.length > 0) {
		var el2 = document.createElement('a');
		$(el2).attr('href', '.').click(function () {
			$(this).hide().siblings('select').show();
			return false;
		});

		el.hide().siblings('span').wrap(el2);
	}
}

function prepareDates() {
	var el = $('.date');
	if (el.length > 0) {
		var el2 = document.createElement('a');
		$(el2).attr('href', '.').click(function () {
			$.post(base_url + 'bookings/calendar/', $.param({'date': $(this).find('span').text()}) + '&ajax=true', function (data) {
				if (data.status == 1) {
					openModal(data.content);
				}
			}, 'json');
			return false;
		});

		el.wrap(el2);
	}
}