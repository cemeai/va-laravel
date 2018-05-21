<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use ChargeBee_Environment;
use ChargeBee_Customer;
use ChargeBee_Subscription;
use Auth;
use App\User;
use App\State;
use App\Subscription;

class UserController extends Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function get_cb_users () {
		ChargeBee_Environment::configure(getenv('CHARGEBEE_SITE'), getenv('CHARGEBEE_KEY'));
		// $customers = ChargeBee_Customer::all(array(
		// 	"sortBy[desc]" => "created_at"));
		$subs = ChargeBee_Subscription::all(array(
			"status[is]" => "active",
			"planId[is]" => "va-now-40",
			"sortBy[asc]" => "created_at"));
		if (count($subs) > 0) {
			foreach ($subs as $sub) {
				$customer = $sub->customer();
				$sub = $sub->subscription();
				
				if (is_null(User::where('email', 'LIKE', $customer->email)->first())) {
					$user = new User();
					$user->name = $customer->firstName;
					$user->last_name = $customer->lastName;
					$user->email = $customer->email;
					$user->phone = $customer->phone;
					$user->password = bcrypt('123456');
					$user->save();

					$subscription = new Subscription();
					$subscription->subscription_id = $sub->id;
					$subscription->plan_id = $sub->planId;
					$subscription->user_id = $user->id;
					$subscription->quantity = $sub->planQuantity;
					$subscription->save();
				}
				// print_r($c); echo '<br>';
			}
		}
	}

	public function update_billing (Request $request) {
		// print_r($request['state']); exit();
		$sub_id = Auth::user()->subscription->subscription_id;
		$cb_obj = ChargeBee_Subscription::update($sub_id, array(
			"billingAddress" => array(
				"firstName" => $request['name'], 
				"lastName" => $request['last_name'], 
				"line1" => $request['address_1'], 
				"line2" => $request['address_2'], 
				"city" => $request['city'], 
				"state" => State::find($request['state'])->name, 
				"zip" => $request['zipcode'], 
				"country" => 'US', 
		)));
		$cb_sub = $cb_obj->subscription();
		$cb_customer = $cb_obj->customer()->billingAddress;
		$data = [
			'name' => $cb_customer->firstName,
			'last_name' => $cb_customer->last_name,
			'address_1' => $cb_customer->line1,
			'address_2' => $cb_customer->line2,
			'city' => $cb_customer->city,
			'state' => $cb_customer->state,
			'zipcode' => $cb_customer->zip,
			'country' => $cb_customer->country,
		];

		echo json_encode($data);
		// print_r($cb_obj->customer()->billingAddress->firstName);
	}

		public function update_payment (Request $request) {
		// print_r($request['state']); exit();
		$sub_id = Auth::user()->subscription->subscription_id;
		$cb_obj = ChargeBee_Subscription::update($sub_id, array(
			"card" => array(
				"firstName" => $request['name'], 
				"lastName" => $request['last_name'], 
				"number" => $request['card_number'], 
				"expiryMonth" => $request['expiryMonth'], 
				"expiryYear" => $request['expiryYear'], 
				"cvv" => $request['city'], 
				"cardType" => $request['card_type'], 
		)));
		$cb_sub = $cb_obj->subscription();
		$cb_card = $cb_obj->card();
		$data = [
			'name' => $cb_card->firstName,
			'lname' => $cb_card->lastName,
			'ctype' => $cb_card->cardType,
			'cnumber' => $cb_card->maskedNumber,
			'cexpiry' => $cb_card->expiryMonth .'/'. $cb_card->expiryYear,
		];

		echo json_encode($data);
		// print_r($cb_obj->customer()->billingAddress->firstName);
	}

	public function googleLogin (Request $request) {
		$google_redirect_url = route('googleLogin');
		$gClient = new \Google_Client();
		$gClient->setApplicationName(config('services.google.app_name'));
		$gClient->setClientId(config('services.google.client_id'));
		$gClient->setClientSecret(config('services.google.client_secret'));
		$gClient->setRedirectUri($google_redirect_url);
		$gClient->setDeveloperKey(config('services.google.api_key'));
		$gClient->setScopes(array(
			'https://www.googleapis.com/auth/plus.me',
			'https://www.googleapis.com/auth/userinfo.email',
			'https://www.googleapis.com/auth/userinfo.profile',
		));
		$google_oauthV2 = new \Google_Service_Oauth2($gClient);

		if ($request->get('code')) {
			$gClient->authenticate($request->get('code'));
			$request->session()->put('token', $gClient->getAccessToken());
		}

		if ($request->session()->get('token')) {
			$gClient->setAccessToken($request->session()->get('token'));
		}

		if ($gClient->getAccessToken()) {
			//For logged in user, get details from google using access token
			$guser = $google_oauthV2->userinfo->get();  
				 
			$request->session()->put('name', $guser['name']);
			if ($user = User::where('email',$guser['email'])->first()) {
				//logged your user via auth login
			} else {
				//register your user with response data
			}               
			return redirect()->route('user.glist');          
		} else {
			//For Guest user, get google login url
			$authUrl = $gClient->createAuthUrl();
			return redirect()->to($authUrl);
		}
	}
}
