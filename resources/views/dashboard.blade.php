@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-10">Complete plan</div>
						<div class="col-xs-2 text-right"><h4 style="margin: 0;">{{$plan}} Hours Total</h4></div>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-11">
							<div class="progress">
								<div class="progress-bar progress-bar-success" role="progressbar" style="width: {{$percentage_usage}}%" 
									aria-valuenow="{{$percentage_usage}}" aria-valuemin="0" aria-valuemax="100">{{$percentage_usage}}%</div>
							</div>
						</div>
						<div class="col-sm-1" style="padding: 0px;">
							<h5 style="margin: 0;"><small style="color: #adadad;"">{{$plan - $consumption}} hours left</small></h5>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-6">Time consumption</div>
						<div class="col-xs-2 text-right">Date Filter</div>
						<div class="col-xs-4">
							<div class="input-group input-group-sm">
								<span class="input-group-addon" id="icon-from"><i class="glyphicon glyphicon-calendar"></i></span>
							  <!-- <input type="text" class="form-control" id="date-from" placeholder="From" aria-describedby="icon-from" value="{{date('m/01/Y')}}"> -->
							  <select class="form-control" id="cycle" onchange="filter_usage()">
									@foreach ($billing_cycles as $key => $billing_cycle)
							  		<option value="{{$key}}">{{$billing_cycle}}</option>
							  	@endforeach
							  </select>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div id="usage-chart"></div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">Activities</div>
				<div class="panel-body">
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					Invoices
					<div class="pull-right"><a href="invoices">More</a></div>
				</div>
				<div class="panel-body">
					<table class="table table-hover">
						<thead>
							<tr>
								<th colspan="2	"></th>
								<th>Date From</th>
								<th>Date To</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($cb_invoices as $key => $cb_invoice)
								<?php $invoice = $cb_invoice->invoice(); ?>
								<?php $download = ChargeBee_Invoice::pdf($invoice->id)->download(); ?>
								<tr>
									<td><a href="{{$download->downloadUrl}}"><i class="glyphicon glyphicon-cloud-download"></i></a></td>
									<!-- <td><i class="glyphicon glyphicon-envelope"></i></td> -->
									<td>Invoice {{$invoice->id}}</td>
									<td>{{ date('d M Y', $invoice->lineItems[0]->dateFrom) }}</td>
									<td>{{ date('d M Y', $invoice->lineItems[0]->dateTo) }}</td>
									<td align="right">${{ number_format($invoice->total/100, 2) }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">VA Info</div>
						<div class="panel-body">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">Plan hours</div>
						<div class="panel-body">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	var usage_chart_data = [
		@foreach ($month as $key => $day)
			{
				"label": "{{$day['date']}}",
				"value": "{{$day['hours']}}",
			},
		@endforeach
	];
</script>
<script src="{{ asset('bower_components/fusioncharts/fusioncharts.js') }}"></script>
<script src="{{ asset('bower_components/fusioncharts/themes/fusioncharts.theme.fint.js') }}"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection