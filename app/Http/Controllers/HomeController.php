<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use ChargeBee_Subscription;
use ChargeBee_Invoice;
use ChargeBee_HostedPage;
use Auth;
use App\User;

class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index (Request $request) {
		$sub_id = Auth::user()->subscription->subscription_id;
		$cb_obj = ChargeBee_Subscription::retrieve($sub_id);
		$cb_sub = $cb_obj->subscription();
		$cb_card = $cb_obj->card();
		$cb_billing = $cb_sub->shippingAddress;
		// print_r($cb_sub); exit();
		return view('home', compact('cb_sub', 'cb_card', 'cb_billing'));
	}

	public function test (Request $request) {
		// return ChargeBee_HostedPage::checkoutExisting([
	 //      'subscription' => [
	 //          'id' => 'Hr5512dQjSk7LgXxr'
	 //      ],
	 //      'embed' => true,
	 //      'redirectUrl' => null,
	 //      'cancelledUrl' => null,
	 //  ])->hostedPage()->url;
	}

	public function dashboard (Request $request) {
		$sub_id = Auth::user()->subscription->subscription_id;
		$cb_obj = ChargeBee_Subscription::retrieve($sub_id);
		$cb_sub = $cb_obj->subscription();
		$cb_card = $cb_obj->card();
		$cb_billing = $cb_sub->shippingAddress;
		$cb_invoices = ChargeBee_Invoice::all(array(
			"subscriptionId[is]" => $cb_sub->id,
			"status[notIn]" => array('voided', 'pending'),
			"sortBy[asc]" => "date"));

		$result = ChargeBee_Invoice::pdf("draft_inv_Hxit8JFQraJEQ74eu5");
		$download = $result->download();
		return view('dashboard', compact('cb_sub', 'cb_card', 'cb_billing', 'cb_invoices'));
	}
}
