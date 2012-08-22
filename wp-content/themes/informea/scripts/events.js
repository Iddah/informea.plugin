$(document).ready(function(){
	$('.browse-button').click(function(e){
		e.preventDefault();
		button_class = $(this).attr('class').split(' ');
	});

	$('#free-text').focus(function(e){
		e.preventDefault();
		//focusToggleInputValue($('#free-text'), 'Type free text...');
		return false;
	});

	$('#free-text').blur(function(e){
		e.preventDefault();
		//blurToggleInputValue($('#free-text'), 'Type free text...');
		return false;
	});

	$('.toggle-matching-articles').click(function(e) {
		e.preventDefault();
		var div_id = $(this).attr('id').substr(7);
		if ($('#'+div_id).is(':visible')) {
			$('#'+$(this).attr('id')+' img').attr({
				'src':		images_dir + 'expand.gif',
				'title':	'Expand treaty content',
				'alt':		'Expand'
			});
			$('#'+div_id).slideUp('fast');
		} else {
			$('#'+$(this).attr('id')+' img').attr({
				'src':		images_dir + 'collapse.gif',
				'title':	'Compress treaty content',
				'alt':		'Compress'
			});
			$('#'+div_id).slideDown('normal');
		}
	});
});
