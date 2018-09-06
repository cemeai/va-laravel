<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\User;
use App\Subscription;
use Auth;
use Validator;
use Session;
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

		$subscripton = null;
		$customer_id = $portal->portalSession()->customerId;
		$cb_subscriptions = ChargeBee_Subscription::all(array(
			"limit" => 1,
			"customerId[is]" => $customer_id));
		foreach ($cb_subscriptions as $cb_subscription) {
			$subscription = $cb_subscription->subscription();
		}

		if (isset($subscription)) {
			Session::set('auth_session_id', $request->auth_session_id);
			Session::set('auth_session_token', $request->auth_session_token);
			$subscription = Subscription::where('subscription_id', '=', $subscription->id)->first();
			$user = User::where('id', '=', $subscription->user_id)->first();
			Auth::login($user);

			return redirect('dashboard');
		}

		return redirect('no_sub');
	}

// https://portal.virtualassistants.today/api_register?api_key=DK2H30D27C&fname=Krsitan&lname=Widjaja&email=kristian@jonajo.com&phone=234234&harvest_id=5344089&subscription_id=NA&plan_id=free-trial
	public function api_register (Request $request) {
		$data = array();
		if ($request->api_key == getenv('API_KEY')) {
			$user = User::where('phone', '=', $request->phone)->first();
			if (empty($user)) {
				$user = new User();
			}
			$user->name = $request->fname;
			$user->last_name = $request->lname;
			$user->email = $request->email;
			$user->phone = $request->phone;
			$user->password = bcrypt('123456');
			$user->save();

			if (!$user) {
				$data['mssg'] = 'The user could not be created';
				$data['success'] = false;
				echo json_encode($data); exit();
			}

			$sub_id = ($request->subscription_id == 'NA')? str_random(16).'-trial': $request->subscription_id;
			$subscription = Subscription::where('user_id', '=', $user->id)->first();
			if (empty($subscription)) {
				$subscription = new Subscription();
				$subscription->harvest_id = $request->harvest_id;
				$subscription->user_id = $user->id;
				$subscription->created_at = date('Y-m-d H:i:s');
				$subscription->updated_at = date('Y-m-d H:i:s');
			}
			$subscription->subscription_id = $sub_id;
			$subscription->plan_id = $request->plan_id;
			$subscription->save();

			if (!$subscription) {
				$data['mssg'] = 'The subscription could not be created';
				$data['success'] = false;
				echo json_encode($data); exit();
			}

			$data['mssg'] = 'User registered correctly in the portal!';
			$data['success'] = true;
		} else {
			$data['mssg'] = 'API key is incorrect';
			$data['success'] = false;
		}
		echo json_encode($data); exit();
	}

	public function logout () {
		if (Session::get('auth_session_id') !== null) {
			ChargeBee_PortalSession::logout(Session::get('auth_session_id'));
		}
		Auth::logout();
		return redirect('dashboard');
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	// protected function create(array $data)
	// {
	// 	$cb_obj = ChargeBee_Customer::all(array(
	// 		"limit" => 1,
	// 		"email[is]" => $data['email'],
	// 	));
	// 	foreach ($cb_obj as $cb_customer) {
	// 		$customer = $cb_customer->customer();
	// 		$cb_subs = ChargeBee_Subscription::all(array(
	// 			"limit" => 1, 
	// 			"customerId[is]" => $customer->id,
	// 			"status[is]" => 'active',
	// 		));
	// 		if (count($cb_subs) > 0) {
	// 			foreach ($cb_subs as $cb_sub) {
	// 				$sub = $cb_sub->subscription();
	// 			}
	// 		} else {
	// 			$errors = ['Chargebee customer does not exists!'];
	// 			return view('register', compact('errors'));
	// 		}
	// 	}

	// 	// return User::create([
	// 	//     'name' => $data['name'],
	// 	//     'last_name' => $data['last_name'],
	// 	//     'email' => $data['email'],
	// 	//     'phone' => $data['phone'],
	// 	//     'password' => bcrypt($data['password']),
	// 	// ]);
	// }
}
