$(document).ready( function() {
	truncate_event();
});

function truncate_event () {
	$('.truncated-text-td div').on('click', function(){
		$(this).parent().find('div').toggle();
	});
}