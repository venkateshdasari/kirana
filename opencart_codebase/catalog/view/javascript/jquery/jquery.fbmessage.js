$(document).ready(function(){
	$('body').prepend('<div id="fb-root"></div>');
	
	$('body').on('click', function(e){
		if ($(e.target).hasClass('fbm-trigger')) {
			FBMShowMessageArea();
		} else if ($('#ocx-facebook-message-chat').hasClass('fbm-opened')) {
			FBMHideMessageArea();
		}
	});
});

function FBMShowMessageArea() {
	var display_mode = $('#ocx-facebook-message').attr('data-display-mode');
	var display_position = $('#ocx-facebook-message').attr('data-display-position');
	var width = $('#ocx-facebook-message').attr('data-width');
	var height = $('#ocx-facebook-message').attr('data-height');
	
	$('#ocx-facebook-message #ocx-facebook-message-icon').hide();
	$('#ocx-facebook-message #ocx-facebook-message-chat').addClass('fbm-opened fbm-' + display_position).css({'width': width + 'px', 'height': height + 'px' });

	if (display_mode == 'default') {
		$('#ocx-facebook-message-header').css({'width': width + 'px' });
	}
}

function FBMHideMessageArea() {
	var display_position = $('#ocx-facebook-message').attr('data-display-position');
	
	$('#ocx-facebook-message #ocx-facebook-message-icon').show();
	$('#ocx-facebook-message #ocx-facebook-message-chat').removeClass('fbm-opened fbm-' + display_position);
}