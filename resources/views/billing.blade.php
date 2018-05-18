@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div id="show-billing" class="panel panel-default info">
				<div class="panel-heading">Billing Information <i class="glyphicon glyphicon-pencil pull-right"></i></div>
				<div class="panel-body">
					{{ Auth::user()->name }} {{ Auth::user()->last_name }} <br>
					@if (!is_null($cb_billing))
						<div class="info">
							{{ $cb_billing->line1 }}, {{ $cb_billing->zip }} <br>
							{{ $cb_billing->city }}, {{ $cb_billing->state }} <br>
							{{ $cb_billing->country }} <br>
						</div>
					@else
						<br>
						No billing information available
					@endif
				</div>
			</div>
			<div id="edit-billing" class="panel panel-default edit" style="display: none;">
				<div class="panel-heading">Billing Information <i class="glyphicon glyphicon-remove pull-right"></i></div>
				<div id="billing_form" class="panel-body row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="name">Name</label>
							<input type="text" class="form-control" id="name" placeholder="Jane" value="{{ Auth::user()->name }}">
						</div>
						<div class="form-group">
							<label for="address_1">Address 1</label>
							<input type="text" class="form-control" id="address_1" placeholder="# Street" value="{{ !is_null($cb_billing)?$cb_billing->line1:'' }}">
						</div>
						<div class="form-group">
							<label for="zipcode">Zip Code</label>
							<input type="text" class="form-control" id="zipcode" placeholder="12345" value="{{ !is_null($cb_billing)?$cb_billing->zip:'' }}">
						</div>
						<div class="form-group">
							<label for="state">State</label>
							<select class="form-control" id="state">
								@foreach ($states as $key => $state)
									<?php $selected = !is_null($cb_billing) && $cb_billing->state == $state->name? 'selected':''; ?>
									<option value="{{$state->id}}" {{$selected}}>{{$state->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="last_name">Last Name</label>
							<input type="text" class="form-control" id="last_name" placeholder="Doe" value="{{ Auth::user()->last_name }}">
						</div>
						<div class="form-group">
							<label for="address_2">Address 2</label>
							<input type="text" class="form-control" id="address_2" placeholder="# Street" value="{{ !is_null($cb_billing)?$cb_billing->line2:'' }}">
						</div>
						<div class="form-group">
							<label for="city">City</label>
							<input type="text" class="form-control" id="city" placeholder="San Francisco" value="{{ !is_null($cb_billing)?$cb_billing->city:'' }}">
						</div>
						<div class="form-group">
							<label for="Country">Country</label>
							<input type="text" class="form-control" id="country" placeholder="United states" value="United States" readonly>
						</div>
					</div>
					<div class="col-md-2 centered">
						<button class="btn btn-success col-md-12" onclick="save_billing()">Save</button>
					</div>
				</div>
			</div>
			<div id="show-payment" class="panel panel-default info">
				<div class="panel-heading">Payment method information <i class="glyphicon glyphicon-pencil pull-right"></i></div>
				<div class="panel-body">
				</div>
			</div>
			<div id="edit-payment" class="panel panel-default edit" style="display: none;">
				<div class="panel-heading">Payment method information <i class="glyphicon glyphicon-remove pull-right"></i></div>
				<div class="panel-body">
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/billing.js') }}"></script>
@endsection