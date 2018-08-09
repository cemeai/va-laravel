<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use ChargeBee_Subscription;
use ChargeBee_Invoice;
use ChargeBee_Plan;
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

	private function HV_entires () {
		$curl = curl_init();
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
			return null;
		} else {
			return $response;
		}
	}

	public function dashboard (Request $request) {
		// Invoices
		$sub_id = Auth::user()->subscription->subscription_id;
		$cb_obj = ChargeBee_Subscription::retrieve($sub_id);
		$cb_sub = $cb_obj->subscription();
		$cb_card = $cb_obj->card();
		$cb_billing = $cb_sub->shippingAddress;
		$cb_invoices = ChargeBee_Invoice::all(array(
			"subscriptionId[is]" => $cb_sub->id,
			"status[notIn]" => array('voided', 'pending'),
			"sortBy[asc]" => "date"));

		// Progress bar
		$plan = ChargeBee_Plan::retrieve($cb_sub->planId)->plan()->cfHours;
		$consumption = 0;

		// Usage entries
		$harvest_id = Auth::user()->subscription->harvest_id;
		$response = $this->HV_entires();
		$month = array();
		$srt_term = strtotime(date('Y-m-d 00:00:00', $cb_sub->currentTermStart));
		$end_term = strtotime(date('Y-m-d 23:59:59', $cb_sub->currentTermEnd));
		for ($i = $srt_term; $i <= $end_term; $i+= 86400) { 
			$month[$i] = array();
			$month[$i]['date'] = ($i%2 == 0)? date('d-m', $i): '';
			$month[$i]['hours'] = 0;
		}
		$month[$srt_term]['date'] = date('d-m', $srt_term);
		$month[strtotime(date('Y-m-d 00:00:00', $cb_sub->currentTermEnd))]['date'] = date('d-m', $end_term);

		if (isset($response)) {
			// print_r($response);
			$time_entries = json_decode($response, 1);
			foreach ($time_entries['time_entries'] as $key => $entry) {
				if ($entry['project']['id'] == $harvest_id 
					&& $entry['spent_date'] >= date('Y-m-d', $srt_term) && $entry['spent_date'] <= date('Y-m-d', $end_term)) {
					$index = strtotime($entry['spent_date']);
					// $month[$index]['date'] = date('d-m', strtotime($entry['spent_date']));
					$consumption = $consumption + $entry['hours'];
					$month[$index]['hours'] = $month[$index]['hours'] + $entry['hours'];
				}
			}
		}
		$percentage_usage = $consumption*100 / $plan;

		return view('dashboard', compact('cb_sub', 'cb_card', 'cb_billing', 'cb_invoices', 'month', 'plan', 'consumption', 'percentage_usage'));
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
		$harvest_id = Auth::user()->subscription->harvest_id;
		$hv_entries = array();
		$response = $this->HV_entires();
		if (isset($response)) {
			$time_entries = json_decode($response, 1);
			foreach ($time_entries['time_entries'] as $key => $entry) {
				$entry['date'] = date('d M Y', strtotime(explode('T', $entry['spent_date'])[0]));
				if ($entry['project']['id'] == $harvest_id) {
					$hv_entries[] = $entry;
				}
			}
		}
		return view('usage', compact('hv_entries'));
	}

	public function filter_usage (Request $request) {
		$from = explode('/', $request->date_from);
		$to = explode('/', $request->date_to);
		$hv_entries = array();
		$harvest_id = Auth::user()->subscription->harvest_id;
		$response = $this->HV_entires();
		if (isset($response)) {
			$time_entries = json_decode($response, 1);
			foreach ($time_entries['time_entries'] as $key => $entry) {
				$entry['date'] = date('d M Y', strtotime(explode('T', $entry['spent_date'])[0]));
				$entry['date_aux'] = date('Y-m-d', strtotime(explode('T', $entry['spent_date'])[0]));
				if ($entry['project']['id'] == $harvest_id 
				    && $entry['date_aux'] >= date($from[2].'-'.$from[0].'-'.$from[1])
				    && $entry['date_aux'] <= date($to[2].'-'.$to[0].'-'.$to[1])) {
					$hv_entries[] = $entry;
				}
			}
		}
		echo json_encode($hv_entries);
	}

	public function filter_usage_dashboard (Request $request) {
		$from = explode('/', $request->date_from);
		$to = explode('/', $request->date_to);
		$harvest_id = Auth::user()->subscription->harvest_id;
		$response = $this->HV_entires();
		$month = array();
		for ($i = 1; $i <= intval(date('t')); $i++) { 
			$month[$i] = array();
			$month[$i]['date'] = ($i%2 == 0)? date('d-m', strtotime('2018-'.date('m').'-'.$i)): '';
			$month[$i]['hours'] = 0;
		}
		$month[1]['date'] = date('d-m', strtotime('2018-'.date('m').'-1'));
		$month[count($month)]['date'] = date('d-m', strtotime('2018-'.date('m-t')));

		if (isset($response)) {
			$time_entries = json_decode($response, 1);
			foreach ($time_entries['time_entries'] as $key => $entry) {
				if ($entry['project']['id'] == $harvest_id 
					&& $entry['spent_date'] >= date('Y-m-01') && $entry['spent_date'] <= date('Y-m-t')) {
					$index = intval(date('d-m', strtotime($entry['spent_date'])));
					$month[$index] = array();
					$month[$index]['date'] = date('d-m', strtotime($entry['spent_date']));
					$month[$index]['hours'] = $month[$index]['hours'] + $entry['hours'];
				}
			}
		}
		echo json_encode($month);
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
