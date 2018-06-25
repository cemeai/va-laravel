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
						<div class="col-xs-6">Usage</div>
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
							  <input type="text" class="form-control" id="date-to" onchange="filter_usage()" placeholder="To" aria-describedby="icon-from">
							</div>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<table id="usage-table" class="table table-hover">
						<thead>
							<tr>
								<th>Date</th>
								<th>Task</th>
								<th>Notes</th>
								<th>Hours</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($hv_entries as $key => $entry)
								<tr>
									<td>{{$entry['date']}}</td>
									<td>{{$entry['task']['name']}}</td>
									<td class="truncated-text-td">
										<div class="table-truncated-text">{{$entry['notes']}}</div>
										<div class="table-non-truncated-text">{{$entry['notes']}}</div>
									</td>
									<td>{{$entry['hours']}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>

					@if (count($hv_entries) > 15)
					<nav aria-label="Page navigation">
						<ul class="pagination">
							<li>
								<a href="#" aria-label="Previous">
									<span aria-hidden="true">&laquo;</span>
								</a>
							</li>
							@for ($i = 0, $j = 1; $i < count($hv_entries); $i+=15, $j++)
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
<script src="{{ asset('js/usage.js') }}"></script>
@endsection