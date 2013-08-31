$(function () {

	$('#qr').qrcode({
		text: document.URL,
		radius: 0.5,
		size: 107
	});

	$(".scroll").click(function() {
		$('html, body').animate({
			scrollTop: $("#scroll").offset().top
		}, 1000);
	});

	// Disable all update buttons before edit
	/*$('input[name="update"]').attr({
		'disabled': 'disabled'
	});*/

	if ($.nette != undefined) {
		if ($('#image-uploader').length != 0) {
			$.nette.ext('uploader', {
				complete: function () {
					var uploader = $('#image-uploader').fineUploader({
						request: {
							endpoint: 'product/default/' + $('#image-uploader').data('id') + '?do=upload'
						},
						text: {
							uploadButton: 'Klikněte, nebo Přetáhněte obrázky',
							cancelButton: 'zrušit',
							failUpload: 'Nahrání obrázku se nezdařilo',
							dragZone: 'Přetáhněte soubory sem',
							dropProcessing: 'Zpracovávám přetažené soubory...',
							formatProgress: '{percent}% z {total_size}',
							waitingForResponse: 'Zpracovávám...'
						},
						autoUpload: false,
						failedUploadTextDisplay: {
							mode: 'custom',
							maxChars: 70,
							responseProperty: 'error',
							enableTooltip: true
						}
					});
					$('#triggerUpload').click(function () {
						uploader.fineUploader('uploadStoredFiles');
					});
				}
			});
		}

		// generuje URL na zaklade zadavaneho titulku
		$.nette.ext('nodiac', {
			complete: function () {
				$('input[data-slug-to]').keyup(function () {
					var slugId = $(this).data('slug-to');
					var val = $(this).val();
					$('#' + slugId).val(make_url(val));
				});
			}
		});

		// generuje cenu s DPH
		$.nette.ext('dph-to', {
			complete: function () {
				var input = $('input[data-dph-to]');
				$('#' + input.data('dph-to')).val((input.val() * (1 + input.data('dph') / 100)).toFixed(2));
				input.bind("keyup change", function () {
					var dphId = $(this).data('dph-to');
					var val = $(this).val();
					$('#' + dphId).val((val * (1 + input.data('dph') / 100)).toFixed(2));
				});
			}
		});

		// obsluhuje wait kurzor při AJAX požadavcích
		$.nette.ext('spinner', {
			start: function () {
				$('html').addClass('wait');
			},
			complete: function () {
				$('html').removeClass('wait');
			}
		});

		// odkazy, ktere musi byt potvrzeny
		$.nette.ext('confirm', {
			before: function (xhr, settings) {
				if (!settings.nette) {
					return;
				}
				var question = settings.nette.el.data('confirm');
				if (question) {
					return confirm(question);
				}
			}
		});

		$.nette.init();
	}

	// generuje URL na zaklade zadavaneho titulku
	$('input[data-slug-to]').keyup(function () {
		var slugId = $(this).data('slug-to');
		var val = $(this).val();
		$('#' + slugId).val(make_url(val));
	});

	// generuje cenu s DPH
	var input = $('input[data-dph-to]');
	$('#' + input.data('dph-to')).val((input.val() * (1 + input.data('dph') / 100)).toFixed(2));
	input.bind("keyup change", function () {
		var dphId = $(this).data('dph-to');
		var val = $(this).val();
		$('#' + dphId).val((val * (1 + input.data('dph') / 100)).toFixed(2));
	});

	// odkazy, ktere musi byt potvrzeny
	$('body').on('click', 'a[data-confirm]', function (e) {
		var question = $(this).data('confirm');
		if (!confirm(question)) {
			e.stopImmediatePropagation();
			e.preventDefault();
		}
	});

});

var nodiac = { 'á': 'a', 'č': 'c', 'ď': 'd', 'é': 'e', 'ě': 'e', 'í': 'i', 'ň': 'n', 'ó': 'o', 'ř': 'r', 'š': 's', 'ť': 't', 'ú': 'u', 'ů': 'u', 'ý': 'y', 'ž': 'z' };
/** Vytvoření přátelského URL
 * @param string řetězec, ze kterého se má vytvořit URL
 * @return string řetězec obsahující pouze čísla, znaky bez diakritiky, podtržítko a pomlčku
 * @copyright Jakub Vrána, http://php.vrana.cz/
 */
function make_url(s) {
	s = s.toLowerCase();
	var s2 = '';
	for (var i = 0; i < s.length; i++) {
		s2 += (typeof nodiac[s.charAt(i)] != 'undefined' ? nodiac[s.charAt(i)] : s.charAt(i));
	}
	return s2.replace(/[^a-z0-9_]+/g, '-').replace(/^-|-$/g, '');
}

function number_format(number, decimals, dec_point, thousands_sep) {
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',
		toFixedFix = function (n, prec) {
			var k = Math.pow(10, prec);
			return '' + Math.round(n * k) / k;
		};
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
}