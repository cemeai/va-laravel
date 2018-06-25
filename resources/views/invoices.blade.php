@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/invoices.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-6">Invoices</div>
						<div class="col-xs-2 text-right">Date Filter</div>
						<div class="col-xs-2">
							<div class="input-group input-group-sm">
								<span class="input-group-addon" id="icon-from"><i class="glyphicon glyphicon-calendar"></i></span>
							  <input type="text" class="form-control" id="date-from" placeholder="From" aria-describedby="icon-from">
							</div>
						</div>
						<div class="col-xs-2">
							<div class="input-group input-group-sm">
								<span class="input-group-addon" id="icon-from"><i class="glyphicon glyphicon-calendar"></i></span>
							  <input type="text" class="form-control" id="date-to" onchange="filter_invoices()" placeholder="To" aria-describedby="icon-from">
							</div>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<table id="invoices-table" class="table table-hover">
						<thead>
							<tr>
								<th colspan="2">Name</th>
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

					@if (count($cb_invoices_15) > 15)
					<nav aria-label="Page navigation">
						<ul class="pagination">
							<li>
								<a href="#" aria-label="Previous">
									<span aria-hidden="true">&laquo;</span>
								</a>
							</li>
							@for ($i = 0, $j = 1; $i < count($cb_invoices); $i+=15, $j++)
							<li><a href="#">{{ $j }}</a></li>
							@endfor
							<li>
								<a href="#" aria-label="Next">
									<span aria-hidden="true">&raquo;</span>
								</a>
							</li>
						</ul>
					</nav>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/invoices.js') }}"></script>
@endsection