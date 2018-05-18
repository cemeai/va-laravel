$(document).ready( function() {
	$('.info .panel-heading i').click( function () {
		panel = $(this).closest('.panel');
		panel.hide();
		panel.next().show();
	});

	$('.edit .panel-heading i').click( function () {
		panel = $(this).closest('.panel');
		panel.hide();
		panel.prev().show();
	});
});

function save_billing ()  {
	data = {
		'_token': $('meta[name="csrf-token"]').attr('content'),
		'name': $('#name').val(),
		'last_name': $('#last_name').val(),
		'address_1': $('#address_1').val(),
		'address_2': $('#address_2').val(),
		'zipcode': $('#zipcode').val(),
		'city': $('#city').val(),
		'state': $('#state').val(),
		'country': $('#country').val(),
	};
	console.log(data);
	$.ajax({
		url: 'update_billing',
		method: 'POST',
		data: data,
		dataType: 'json',
		success: function (data) {
			console.log(data);
			$('#show-billing .info').html(''+
				data.address_1 +', '+ data.zipcode +'<br>'+
				data.city +', '+ data.state +'<br>'+
				data.country +'<br>');
			$('#edit-billing').hide();
			$('#show-billing').show();
		},
		error: function (data) {
			console.log(data);
		},
	});
}