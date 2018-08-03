$(document).ready( function() {
	$('#date-from').datepicker();
	$('#date-to').datepicker();

	FusionCharts.ready(function() {
		var usage_chart = new FusionCharts({
			"type": "area2D",
			"renderAt": "usage-chart",
			"width": "100%",
			"height": "300",
			"dataFormat": "json",
			"dataSource": {
				"chart": {
					"xAxisName": "Day",
					"yAxisName": "Hours",
					"theme": "fint",
					"valueAlpha": 0,
					"lineColor": "#2c82be",
					"showPlotBorder": 1,
					"plotBorderThickness": "1",
					"plotFillColor": "#cae0ef",
					"drawAnchors": 1,
					"plottooltext": "$name<br> $value Hours",
					"toolTipBgColor": "#5a6574",
					"labelDisplay": "rotate",
					"slantLabel": "1",
				},
				"data": usage_chart_data
			}
    });
    usage_chart.render();
  });
});

function filter_usage() {
	data = {
		'_token': $('meta[name="csrf-token"]').attr('content'),
		'date_from': $('#date-from').val(),
		'date_to': $('#date-to').val(),
	};
	$.ajax({
		url: 'filter_usage_dashboard',
		method: 'POST',
		data: data,
		dataType: 'json',
		success: function (data) {
			$('#usage-table tbody').html('');
			var usage_chart_data = [];
			$.each(data, function() {
				usage_chart_data.push({
					"label": this['date'],
					"value": this['hours'],
				});
			});
			console.log(usage_chart_data);
		},
		error: function (data) {
			// console.log(data);
		},
	});
}