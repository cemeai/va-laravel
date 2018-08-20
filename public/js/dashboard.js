$(document).ready( function() {
	chart_config = {
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
		"rotateLabels": 0,
	};
	FusionCharts.ready(function() {
		usage_chart = new FusionCharts({
			"type": "area2D",
			"renderAt": "usage-chart",
			"width": "100%",
			"height": "300",
			"dataFormat": "json",
			"dataSource": {
				"chart": chart_config,
				"data": usage_chart_data
			}
    });
    usage_chart.render();
  });
});

function filter_usage() {
	data = {
		'_token': $('meta[name="csrf-token"]').attr('content'),
		'cycle': $('#cycle').val(),
	};
	$.ajax({
		url: 'filter_usage_dashboard',
		method: 'POST',
		data: data,
		dataType: 'json',
		success: function (data) {
			$('#usage-table tbody').html('');
			usage_chart_data = [];
			$.each(data, function() {
				usage_chart_data.push({
					"label": this['date'],
					"value": this['hours'],
				});
			});
			usage_chart.setJSONData({
				"chart": chart_config,
				"data": usage_chart_data
			});
			usage_chart.render();
			console.log(usage_chart_data);
		},
		error: function (data) {
			// console.log(data);
		},
	});
}