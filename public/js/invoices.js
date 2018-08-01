$(document).ready( function() {
	$('#date-from').datepicker();
	$('#date-to').datepicker();
});

function filter_invoices() {
	data = {
		'_token': $('meta[name="csrf-token"]').attr('content'),
		'date_from': $('#date-from').val(),
		'date_to': $('#date-to').val(),
	};
	$.ajax({
		url: 'filter_invoices',
		method: 'POST',
		data: data,
		dataType: 'json',
		success: function (data) {
			$('#invoices-table tbody').html('');
			$.each(data, function() {
				$('#invoices-table tbody').append(''+
					'<tr>'+
						'<td><a href="'+this.download+'"><i class="glyphicon glyphicon-cloud-download"></i></a></td>'+
						'<td>Invoice '+this.id+'</td>'+
						'<td>'+this.date_from+'</td>'+
						'<td>'+this.date_to+'</td>'+
						'<td align="right">$'+this.amount+'</td>'+
					'</tr>'+
				'');
			});
			// console.log(data);
		},
		error: function (data) {
			// console.log(data);
		},
	});
}