@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Billing Address</div>
				<div class="panel-body">
					@if (false)
						{{ $cb_billing->line1 }} <br>
						{{ $cb_billing->city }} - 
						{{ $cb_billing->zip }} <br>
						{{ $cb_billing->stateCode }}, 
						{{ $cb_billing->country }}
					@endif
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">Card Information</div>
				<div class="panel-body">
					@if (!isset($cb_card))
						{{ $cb_card->firstName }}
						{{ $cb_card->lastName }} <br>
						{{ $cb_card->maskedNumber }} <br>
						{{ $cb_card->expiryMonth }} - 
						{{ $cb_card->expiryYear }} <br>
						{{ $cb_card->issuingCountry }}
					@endif
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">Card Information</div>
				<div class="panel-body">
					@if (!isset($cb_card))
						{{ $cb_card->firstName }}
						{{ $cb_card->lastName }} <br>
						{{ $cb_card->maskedNumber }} <br>
						{{ $cb_card->expiryMonth }} - 
						{{ $cb_card->expiryYear }} <br>
						{{ $cb_card->issuingCountry }}
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
