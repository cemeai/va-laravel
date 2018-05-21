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

	$('.card_type div').click( function () {
		$('.card_type div').removeClass('selected');
		$(this).addClass('selected');
	});
});

function save_billing ()  {
	data = {
		'_token': $('meta[name="csrf-token"]').attr('content'),
		'name': $('#name_billing').val(),
		'last_name': $('#last_name_billing').val(),
		'address_1': $('#address_1').val(),
		'address_2': $('#address_2').val(),
		'zipcode': $('#zipcode').val(),
		'city': $('#city').val(),
		'state': $('#state').val(),
		'country': $('#country').val(),
	};
	$.ajax({
		url: 'update_billing',
		method: 'POST',
		data: data,
		dataType: 'json',
		success: function (data) {
			// console.log(data);
			$('#show-billing .info').html(''+
				data.address_1 +', '+ data.zipcode +'<br>'+
				data.city +', '+ data.state +'<br>'+
				data.country +'<br>');
			$('#edit-billing').hide();
			$('#show-billing').show();
		},
		error: function (data) {
			// console.log(data);
		},
	});
}

function save_payment ()  {
	data = {
		'_token': $('meta[name="csrf-token"]').attr('content'),
		'name': $('#name_payment').val(),
		'last_name': $('#last_name_payment').val(),
		'card_number': $('#card_number').val(),
		'expiryMonth': $('#expiryMonth').val(),
		'expiryYear': $('#expiryYear').val(),
		'cvv': $('#cvv').val(),
		'card_type': $('.card_type .selected').data('value'),
	};
	console.log(data);
	$.ajax({
		url: 'update_payment',
		method: 'POST',
		data: data,
		dataType: 'json',
		success: function (data) {
			console.log(data);
			$('#show-payment .name').html(data.name);
			$('#show-payment .lname').html(data.lname);
			$('#show-payment .ctype').html(data.ctype);
			$('#show-payment .cnumber').html(data.cnumber);
			$('#show-payment .cexpiry').html(data.cexpiry);
			$('#edit-payment').hide();
			$('#show-payment').show();
		},
		error: function (data) {
			console.log(data);
		},
	});
}