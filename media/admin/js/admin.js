$(document).ready(function(){

	if ( $('.toggle_switcher').length)
	{
		$.each($('.toggle_switcher'), function(index, element){
			var _element = $(element);
			var _switch_group = '.' + _element.attr('name');

			if (_element.is(':checked') != false)
			{
				$('.hide_toggle'+_switch_group).each(function(i, e){
					$(e).hide();
				});
			}
			else
			{
				$('.hide_toggle_invert'+_switch_group).each(function(i, e){
					$(e).hide();
				});
			}
		});
	}

	$('.toggle_switcher').click(function(){
		var _switch_group = '.' + $(this).attr('name');

		$('.hide_toggle'+_switch_group+', .hide_toggle_invert'+_switch_group).each(function(i, e){
			$(e).toggle(1000);
		});
	});

	$('.hidden').each(function(i, e){
		$(e).hide();
	});

	$("a.js-photo-gallery").flyout();

	jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ru']);
	jQuery.datepicker.setDefaults({ dateFormat: 'yy/mm/dd' });

	$.timepicker.regional['ru'] = {
		timeOnlyTitle: 'Выберите время',
		timeText: 'Время',
		hourText: 'Часы',
		minuteText: 'Минуты',
		secondText: 'Секунды',
		millisecText: 'миллисекунды',
		currentText: 'Сейчас',
		closeText: 'Закрыть',
		ampm: false
	};
	$.timepicker.setDefaults($.timepicker.regional['ru']);

	$( "input:submit, a.button, button").button();

	$('body > .container').css('min-height', ($(window).height() - 30) + 'px');
});

$(document).ready(function(){
	$('button.kr-dyn-creator').live("click", function(){
		var _li = $(this).closest('.kr-dyn-list');
		var _li_clone = _li.clone();

		$('select', _li_clone).val('');
		$('input', _li_clone).val('');

		_li.after(_li_clone);
	});
	$('button.kr-dyn-deleter').live("click", function(){
		if (confirm("Вы действительно хотите удалить выбранный элемент?")) {
			$(this).closest('.kr-dyn-list')
				.remove();
		}
	});

	$('.btn.delete_button').live("click", function(){
		return confirm("Вы действительно хотите удалить выбранный элемент?");
	});
	$('.btn[name="cancel"]').live("click", function(){
		return confirm("Выйти без сохранения?");
	});
});

$(function(){
	var el = $("#js-multiupload-holder");

	if ( ! el.length) return;

	el.pluploadQueue({
		// General settings
		runtimes : 'gears,flash,silverlight,html5',
		url : el.attr('data-url'),
		max_file_size : '10mb',
		chunk_size : '1mb',
//		unique_names : true,

		// Resize images on clientside if we can
		resize : {width : 1200, height : 1000, quality : 90},

		// Specify what files to browse for
		filters : [
			{title : "Image files", extensions : "jpg,jpeg,gif,png"}
		],

		// Flash settings
		flash_swf_url : '/media/admin/vendor/plupload/plupload.flash.swf',

		// Silverlight settings
		silverlight_xap_url : '/media/admin/vendor/plupload/plupload.silverlight.xap',

		// PreInit events, bound before any internal events
		preinit : {
			Init: function(up, info) {
//				console.log('[Init]', 'Info:', info, 'Features:', up.features);
			},

			UploadFile: function(up, file) {
//				console.log('[UploadFile]', arguments);

				up.settings.multipart_params = {
						category_id : $('#album_select').val(),
						add_to_head : $('#add_to_head:checked').length
				};

				// You can override settings before the file is uploaded
				// up.settings.url = 'upload.php?id=' + file.id;
				// up.settings.multipart_params = {param1 : 'value1', param2 : 'value2'};
			},
			ChunkUploaded: function(up, file, chunkArgs) {
				var data = $.parseJSON(chunkArgs.response);
//				console.log('[ChunkUploaded] Response - ' + chunkArgs.response, data, arguments);
				if (data && data.error) {
					chunkArgs.cancelled = true;
//					console.log('[Error] ' + file.id + ' : ' + data.error.message);
					up.trigger("Error", {message: "'" + data.error.message + "'", file: file});

					window.setTimeout(function(){
						$('#'+file.id)
							.find('.plupload_file_name')
							.after($('<span class="plupload_error_message" />').text(data.error.message));
					}, 100);
					return false;
				}
			},
			FileUploaded: function(up, file, response) {
				var data = $.parseJSON(response.response);
//				console.log('[FileUploaded] Response - ' + response.response, data, arguments);
				if (data && data.error) {
//					console.log('[Error] ' + file.id + ' : ' + data.error.message);
					up.trigger("Error", {message: "'" + data.error.message + "'", file: file});
					$('#'+file.id).find('.plupload_file_name').append($('<span class="plupload_error_message" />').text(data.error.message));
					return false;
				}
			}
		}
	});
});

