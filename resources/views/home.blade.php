@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Billing Address</div>
				<div class="panel-body">
					{{ $cb_sub->shippingAddress->line1 }} <br>
					{{ $cb_sub->shippingAddress->city }} - 
					{{ $cb_sub->shippingAddress->zip }} <br>
					{{ $cb_sub->shippingAddress->stateCode }}, 
					{{ $cb_sub->shippingAddress->country }}
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">Card Information</div>
				<div class="panel-body">
					{{ $cb_card->firstName }}
					{{ $cb_card->lastName }} <br>
					{{ $cb_card->maskedNumber }} <br>
					{{ $cb_card->expiryMonth }} - 
					{{ $cb_card->expiryYear }} <br>
					{{ $cb_card->issuingCountry }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
