@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/billing.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div id="show-billing" class="panel panel-default info">
				<div class="panel-heading">Billing Information <i class="glyphicon glyphicon-pencil pull-right"></i></div>
				<div class="panel-body">
					@if (!is_null($cb_billing))
						{{ $cb_billing->firstName }} {{ $cb_billing->lastName }} <br>
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
							<label for="name_billing">First Name</label>
							<input type="text" class="form-control" id="name_billing" placeholder="Jane" value="{{ !is_null($cb_billing)? $cb_billing->firstName:'' }}">
						</div>
						<div class="form-group">
							<label for="address_1">Address 1</label>
							<input type="text" class="form-control" id="address_1" placeholder="# Street" value="{{ !is_null($cb_billing)? $cb_billing->line1:'' }}">
						</div>
						<div class="form-group">
							<label for="zipcode">Zip Code</label>
							<input type="text" class="form-control" id="zipcode" placeholder="12345" value="{{ !is_null($cb_billing)? $cb_billing->zip:'' }}">
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
							<label for="last_name_billing">Last Name</label>
							<input type="text" class="form-control" id="last_name_billing" placeholder="Doe" value="{{ !is_null($cb_billing)? $cb_billing->lastName:'' }}">
						</div>
						<div class="form-group">
							<label for="address_2">Address 2</label>
							<input type="text" class="form-control" id="address_2" placeholder="# Street" value="{{ !is_null($cb_billing)? $cb_billing->line2:'' }}">
						</div>
						<div class="form-group">
							<label for="city">City</label>
							<input type="text" class="form-control" id="city" placeholder="San Francisco" value="{{ !is_null($cb_billing)? $cb_billing->city:'' }}">
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
					@if (!is_null($cb_card))
						<div class="info">
							<div class="row">
								<div class="col-md-2 col-sm-3 col-xs-6"><b>First name</b></div>
								<div class="col-md-4 col-sm-4 col-xs-6 name">{{ $cb_card->firstName }}</div>
							</div>
							<div class="row">
								<div class="col-md-2 col-sm-3 col-xs-6"><b>Last name</b></div>
								<div class="col-md-4 col-sm-4 col-xs-6 lname">{{ $cb_card->lastName }}</div>
							</div>
							<div class="row">
								<div class="col-md-2 col-sm-3 col-xs-6"><b>Card type</b></div>
								<div class="col-md-4 col-sm-4 col-xs-6 ctype">{{ $cb_card->cardType }}</div>
							</div>
							<div class="row">
								<div class="col-md-2 col-sm-3 col-xs-6"><b>Card number</b></div>
								<div class="col-md-4 col-sm-4 col-xs-6 cnumber">{{ $cb_card->maskedNumber }}</div>
							</div>
							<div class="row">
								<div class="col-md-2 col-sm-3 col-xs-6"><b>Expire</b></div>
								<div class="col-md-4 col-sm-4 col-xs-6 cexpiry">{{ $cb_card->expiryMonth }}/{{ $cb_card->expiryYear }}</div>
							</div>
						</div>
					@else
						<br>
						No payment information available
					@endif
				</div>
			</div>
			<div id="edit-payment" class="panel panel-default edit" style="display: none;">
				<div class="panel-heading">Payment method information <i class="glyphicon glyphicon-remove pull-right"></i></div>
				<div id="card_form" class="panel-body row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="name_payment">First Name*</label>
							<input type="text" class="form-control" id="name_payment" placeholder="Jane" value="{{ !is_null($cb_card)? $cb_card->firstName:'' }}">
						</div>
						<div class="form-group">
							<label for="expiryMonth">Card Number*</label>
							<input type="text" class="form-control" id="card_number" placeholder="xxxx xxxx xxxx 1234" value="{{ !is_null($cb_card)? $cb_card->maskedNumber:'' }}">
						</div>
						<div class="form-group">
							<label for="expiry">Expiry Date*</label>
							<div class="row">
								<div class="col-xs-6">
									<select id="expiryMonth" class="form-control"	>
										@for ($i = 1; $i <= 12; $i++)
											<option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
										@endfor
									</select>
								</div>
								<div class="col-xs-6">
									<select id="expiryYear" class="form-control"	>
										@for ($i = 2018; $i <= 2050; $i++)
											<option value="{{ $i }}">{{ $i }}</option>
										@endfor
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="last_name">Last Name*</label>
							<input type="text" class="form-control" id="last_name_payment" placeholder="Doe" value="{{ !is_null($cb_card)? $cb_card->lastName:'' }}">
						</div>
						<div class="form-group">
							<label for="card_type">Card Type*</label>
							<div class="card_type">
								<div class="col-xs-2 v {{ !is_null($cb_card) && $cb_card->cardType == 'visa'? 'selected': '' }}" data-value="visa"></div>
								<div class="col-xs-2 m {{ !is_null($cb_card) && $cb_card->cardType == 'mastercard'? 'selected': '' }}" data-value="mastercard"></div>
								<div class="col-xs-2 a {{ !is_null($cb_card) && $cb_card->cardType == 'american_express'? 'selected': '' }}" data-value="american_express"></div>
								<div class="col-xs-2 d {{ !is_null($cb_card) && $cb_card->cardType == 'discover'? 'selected': '' }}" data-value="discover"></div>
							</div>
						</div>
						<div class="form-group">
							<label for="cvv">CVV*</label>
							<input type="text" class="form-control" id="cvv" placeholder="123">
						</div>
					</div>
					<div class="col-md-2 centered">
						<button class="btn btn-success col-md-12" onclick="save_payment()">Save</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/billing.js') }}"></script>
@endsection