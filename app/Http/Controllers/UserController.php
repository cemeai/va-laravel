<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use ChargeBee_Environment;
use ChargeBee_Customer;
use ChargeBee_Subscription;
use App\User;
use App\Subscription;

class UserController extends Controller
{
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
					
					$user = new User();
					$user->name = $customer->firstName;
					$user->last_name = $customer->lastName;
					$user->email = $customer->email;
					$user->phone = $customer->phone;
					$user->password = bcrypt('12345');
					$user->save();

					$subscription = new Subscription();
					$subscription->subscription_id = $sub->id;
					$subscription->plan_id = $sub->planId;
					$subscription->user_id = $user->id;
					$subscription->quantity = $sub->planQuantity;
					$subscription->save();
					print_r($c); echo '<br>';
				}
			}
		}
}
