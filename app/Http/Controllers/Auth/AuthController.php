<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\User;
use App\Subscription;
use Auth;
use Validator;
use ChargeBee_Subscription;
use ChargeBee_PortalSession;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers, ThrottlesLogins;

	/**
	 * Where to redirect users after login / registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/';

	/**
	 * Create a new authentication controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->middleware($this->guestMiddleware(), ['except' => 'logout']);
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data)
	{
		return Validator::make($data, [
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|min:6|confirmed',
		]);
	}

	public function authenticate_CB (Request $request) {
		$portal = ChargeBee_PortalSession::activate($request->auth_session_id, array(
				"token" => $request->auth_session_token));

		$customer_id = $portal->portalSession()->linkedCustomers[0]->email;
		print_r($customer_id); echo '<br>'; exit();
		$cb_subscriptions = ChargeBee_Subscription::all(array(
			// "limit" => 1,
			// "planId[is]" => "va-now-40", 
			"customerId[is]" => $customer_id));
		foreach ($cb_subscriptions as $cb_subscription) {
			$subscription = $cb_subscription->subscription();
		}
		$user = Subscription::where('subscription_id', '=', $subscription->id)->first();
		Auth::login($user);

		return redirect('dashboard');
	}

	// public function logout () {
	// 	ChargeBee_PortalSession::logout($request->auth_session_id);
	// }

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	protected function create(array $data)
	{
		$cb_obj = ChargeBee_Customer::all(array(
			"limit" => 1,
			"email[is]" => $data['email'],
		));
		foreach ($cb_obj as $cb_customer) {
			$customer = $cb_customer->customer();
			$cb_subs = ChargeBee_Subscription::all(array(
				"limit" => 1, 
				"customerId[is]" => $customer->id,
				"status[is]" => 'active',
			));
			if (count($cb_subs) > 0) {
				foreach ($cb_subs as $cb_sub) {
					$sub = $cb_sub->subscription();
				}
			} else {
				$errors = ['Chargebee customer does not exists!'];
				return view('register', compact('errors'));
			}
		}

		// return User::create([
		//     'name' => $data['name'],
		//     'last_name' => $data['last_name'],
		//     'email' => $data['email'],
		//     'phone' => $data['phone'],
		//     'password' => bcrypt($data['password']),
		// ]);
	}
}
