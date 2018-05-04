<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use ChargeBee_Subscription;
use Auth;

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
		return view('home', compact('cb_sub', 'cb_card'));
	}
}
