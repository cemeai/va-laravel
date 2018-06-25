<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use ChargeBee_Subscription;
use ChargeBee_Invoice;
use ChargeBee_HostedPage;
use Auth;
use App\User;
use App\State;

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

		return view('dashboard', compact('cb_sub', 'cb_card', 'cb_billing', 'cb_invoices'));
	}

	public function billing (Request $request) {
		$sub_id = Auth::user()->subscription->subscription_id;
		$cb_obj = ChargeBee_Subscription::retrieve($sub_id);
		$cb_sub = $cb_obj->subscription();
		$cb_card = $cb_obj->card();
		$cb_billing = $cb_obj->customer()->billingAddress;
		$states = State::all();
		return view('billing', compact('cb_sub', 'cb_card', 'cb_billing', 'states'));
	}

	public function usage (Request $request) {
		$curl = curl_init();
		$hv_entries = array();
		$harvest_id = Auth::user()->subscription->harvest_id;
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.harvestapp.com/v2/time_entries",
			CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_TIMEOUT => 30000,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				'Harvest-Account-ID: '.getenv('HARVEST_ACCOUNT_ID'),
				'Authorization: Bearer '.getenv('HARVEST_ACCESS_TOKEN'),
				'User-Agent: VA-Now (support@va.today)',
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$time_entries = json_decode($response, 1);
			foreach ($time_entries['time_entries'] as $key => $entry) {
				$entry['date'] = date('d M Y', strtotime(explode('T', $entry['created_at'])[0]));
				if ($entry['project']['id'] == $harvest_id) {
					$hv_entries[] = $entry;
				}
			}
		}
		return view('usage', compact('hv_entries'));
	}

	public function filter_usage (Request $request) {
		$curl = curl_init();
		$from = explode('/', $request->date_from);
		$to = explode('/', $request->date_to);
		$hv_entries = array();
		$harvest_id = Auth::user()->subscription->harvest_id;
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.harvestapp.com/v2/time_entries",
			CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_TIMEOUT => 30000,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				'Harvest-Account-ID: '.getenv('HARVEST_ACCOUNT_ID'),
				'Authorization: Bearer '.getenv('HARVEST_ACCESS_TOKEN'),
				'User-Agent: VA-Now (support@va.today)',
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$time_entries = json_decode($response, 1);
			foreach ($time_entries['time_entries'] as $key => $entry) {
				$entry['date'] = date('d M Y', strtotime(explode('T', $entry['created_at'])[0]));
				$entry['date_aux'] = date('Y-m-d', strtotime(explode('T', $entry['created_at'])[0]));
				if ($entry['project']['id'] == $harvest_id 
				    && $entry['date_aux'] >= date($from[2].'-'.$from[0].'-'.$from[1])
				    && $entry['date_aux'] <= date($to[2].'-'.$to[0].'-'.$to[1])) {
					$hv_entries[] = $entry;
				}
			}
		}
		echo json_encode($hv_entries);
	}

	public function invoices (Request $request) {
		$sub_id = Auth::user()->subscription->subscription_id;
		$cb_invoices = ChargeBee_Invoice::all(array(
			"subscriptionId[is]" => $sub_id,
			"status[notIn]" => array('voided', 'pending'),
			"sortBy[asc]" => "date"));
		$cb_invoices_15 = ChargeBee_Invoice::all(array(
			"subscriptionId[is]" => $sub_id,
			"limit" => 15,
			"status[notIn]" => array('voided', 'pending'),
			"sortBy[asc]" => "date"));
		return view('invoices', compact('cb_invoices', 'cb_invoices_15'));
	}

	public function filter_invoices (Request $request) {
		$sub_id = Auth::user()->subscription->subscription_id;
		$from = explode('/', $request->date_from);
		$to = explode('/', $request->date_to);
		$cb_invoices = ChargeBee_Invoice::all(array(
			"subscriptionId[is]" => $sub_id,
			"limit" => 15,
			"status[notIn]" => array('voided', 'pending'),
			"date[after]" => strtotime(date($from[2].'-'.$from[0].'-'.$from[1])),
			"date[before]" => strtotime(date($to[2].'-'.$to[0].'-'.$to[1])),
			"sortBy[asc]" => "date"));

		$data = array();
		foreach ($cb_invoices as $key => $cb_invoice) {
			$invoice = $cb_invoice->invoice();
			$data[] = [
				'id' => $invoice->id,
				'download' => ChargeBee_Invoice::pdf($invoice->id)->download()->downloadUrl,
				'date_from' => date('d M Y', $invoice->lineItems[0]->dateFrom),
				'date_to' => date('d M Y', $invoice->lineItems[0]->dateTo),
				'amount' => number_format($invoice->total/100, 2),
			];
		}
		echo json_encode($data);
	}
}
