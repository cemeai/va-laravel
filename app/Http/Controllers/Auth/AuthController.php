<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use ChargeBee_Subscription;
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
