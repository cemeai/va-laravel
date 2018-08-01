@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Complate plan</div>
				<div class="panel-body">
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">Time consumption</div>
				<div class="panel-body">
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
