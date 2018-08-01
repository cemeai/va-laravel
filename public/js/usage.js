$(document).ready( function() {
	$('#date-from').datepicker();
	$('#date-to').datepicker();
});

function filter_usage() {
	data = {
		'_token': $('meta[name="csrf-token"]').attr('content'),
		'date_from': $('#date-from').val(),
		'date_to': $('#date-to').val(),
	};
	$.ajax({
		url: 'filter_usage',
		method: 'POST',
		data: data,
		dataType: 'json',
		success: function (data) {
			$('#usage-table tbody').html('');
			$.each(data, function() {
				$('#usage-table tbody').append(''+
					'<tr>'+
						'<td>'+this.date+'</td>'+
						'<td>'+this.task.name+'</td>'+
						'<td class="truncated-text-td">'+
							'<div class="table-truncated-text">'+this.notes+'</div>'+
							'<div class="table-non-truncated-text">'+this.notes+'</div>'+
						'</td>'+
						'<td>'+this.hours+'</td>'+
					'</tr>'+
				'');
			});
			truncate_event();
			// console.log(data);
		},
		error: function (data) {
			// console.log(data);
		},
	});
}