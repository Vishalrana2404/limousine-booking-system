<?php

namespace App\Http\Controllers\Auth;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\ClientService;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Client;
use App\Models\BillingAgreement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Str;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;


/**
 * Class LoginController
 * 
 * @package  App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    public function __construct(
        private CustomHelper $helper,
        private UserService $userService,
        private ClientService $clientService,
    ) {
    }

    /**
     * Display the login form.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function index(Request $request)
    {
        return view('auth.login');
    }

    /**
     * Handle user login.
     *
     * @param LoginRequest $request The login request instance.
     * @return Response The HTTP response instance.
     */
    public function login(LoginRequest $request)
    {
        try {
            // Attempt to find the user by email
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                // $log_headers = $this->getHttpData($request);
    
                // $registerData = [];
                // $registerData['first_name'] = NULL;
                // $registerData['last_name'] = NULL;
                // $registerData['user_type_id'] = 4;
                // $registerData['status'] = 'ACTIVE';
                // $registerData['country_code'] = NULL;
                // $registerData['phone'] = NULL;
                // $registerData['email'] = $request->email;
                // $registerData['password'] = $request->password;
                // $registerData['created_by_id'] = NULL;
    
                // $userCreated = User::create($registerData);
    
                // $clientData['user_id'] = $userCreated->id;
                // $clientData['hotel_id'] = NULL;
                // $clientData['invoice'] = NULL;
                // $clientData['status'] = 'ACTIVE';
                // $clientData['entity'] = NULL;
                // $clientData['created_by_id'] = NULL;
    
                // $clientCreated = Client::create($clientData);
    
                // Auth::attempt($request->only('email', 'password'));
                // $this->helper->alertResponse(__('message.registered_and_logged_in_successfully'), 'success');
                // return redirect()->route('dashboard');
    
                $this->helper->alertResponse(__('message.invalid_email_or_password'), 'error');
                return redirect()->back();
            }
            // Check if the user's status is active
            if (strtolower($user->status) !== 'active') {
                $this->helper->alertResponse(__('message.account_not_active'), 'error');
                return redirect()->back();
            }
            // Attempt to authenticate the user
            if (!Auth::attempt($request->only('email', 'password'))) {
                $this->helper->alertResponse(__('message.invalid_email_or_password'), 'error');
                return redirect()->back();
            }
            $this->helper->alertResponse(__('message.logged_in_successfully'), 'success');
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            return back()->with('message', __('message.something_went_wrong'));
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            // Attempt to find the user by email
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                $log_headers = $this->getHttpData($request);
    
                // find for Indivisuals Corporate
                $indivisualsHotel = Hotel::where('name', 'Indivisuals')->first();

                if(!empty($indivisualsHotel))
                {
                    $hotelId = $indivisualsHotel->id;
                }else{
                    $corporateData = [];
                    $corporateData['name'] = 'Individuals';
                    $corporateData['term_conditions'] = 'Terms And Conditions';
                    $corporateData['created_by_id'] = NULL;

                    $hotelCreate = Hotel::create($corporateData);
                    $hotelId = $hotelCreate->id;

                    $billingAgreementData['hotel_id'] =  $hotelId;
                    $billingAgreementData['per_trip_delivery'] = NULL;
                    $billingAgreementData['peak_period_surcharge'] = NULL;
                    $billingAgreementData['fixed_multiplier_midnight_surcharge_23_seats'] = NULL;
                    $billingAgreementData['mid_night_surcharge_23_seats'] = NULL;
                    $billingAgreementData['fixed_multiplier_midnight_surcharge_greater_then_23_seats'] = NULL;
                    $billingAgreementData['midnight_surcharge_greater_then_23_seats'] = NULL;
                    $billingAgreementData['fixed_multiplier_arrivel_waiting_time'] = NULL;
                    $billingAgreementData['arrivel_waiting_time'] = NULL;
                    $billingAgreementData['fixed_multiplier_departure_and_transfer_waiting'] = NULL;
                    $billingAgreementData['departure_and_transfer_waiting'] = NULL;
                    $billingAgreementData['fixed_multiplier_last_min_request_23_seats'] = NULL;
                    $billingAgreementData['last_min_request_23_seats'] = NULL;
                    $billingAgreementData['fixed_multiplier_last_min_request_greater_then_23_seats'] = NULL;
                    $billingAgreementData['last_min_request_greater_then_23_seats'] = NULL;
                    $billingAgreementData['fixed_multiplier_outside_city_surcharge_23_seats'] = NULL;
                    $billingAgreementData['outside_city_surcharge_23_seats'] = NULL;
                    $billingAgreementData['fixed_multiplier_outside_city_surcharge_greater_then_23_seats'] = NULL;
                    $billingAgreementData['outside_city_surcharge_greater_then_23_seats'] = NULL;
                    $billingAgreementData['fixed_multiplier_additional_stop'] = NULL;
                    $billingAgreementData['additional_stop'] = NULL;
                    $billingAgreementData['fixed_multiplier_misc_charges'] = NULL;
                    $billingAgreementData['misc_charges'] = NULL;

                    BillingAgreement::create($billingAgreementData);
                }
                $password = Str::random(8);
                $registerData = [];
                $registerData['first_name'] = $request->first_name;
                $registerData['last_name'] = $request->last_name;
                $registerData['user_type_id'] = 4;
                $registerData['status'] = 'ACTIVE';
                $registerData['country_code'] = $request->country_code;
                $registerData['phone'] = $request->phone;
                $registerData['email'] = $request->email;
                $registerData['password'] =  $password;
                $registerData['created_by_id'] = NULL;
    
                $userCreated = User::create($registerData);
                $this->userService->sendPasswordEmail($userCreated, $registerData['password']);

                $clientData['user_id'] = $userCreated->id;
                $clientData['hotel_id'] = $hotelId;
                $clientData['invoice'] = NULL;
                $clientData['status'] = 'ACTIVE';
                $clientData['entity'] = NULL;
                $clientData['created_by_id'] = NULL;
                
                $clientCreated = Client::create($clientData);

                $loginCreds = [];
                $loginCreds['email'] = $request->email;
                $loginCreds['password'] = $password;

                Auth::attempt($loginCreds);

                $this->helper->alertResponse(__('message.registered_and_logged_in_successfully'), 'success');
                return redirect()->route('dashboard');
    
                $this->helper->alertResponse(__('message.invalid_email_or_password'), 'error');
                return redirect()->back();
            }
            // Check if the user's status is active
            if (strtolower($user->status) !== 'active') {
                $this->helper->alertResponse(__('message.account_not_active'), 'error');
                return redirect()->back();
            }
            // Attempt to authenticate the user
            if (!Auth::attempt($request->only('email', 'password'))) {
                $this->helper->alertResponse(__('message.invalid_email_or_password'), 'error');
                return redirect()->back();
            }
            $this->helper->alertResponse(__('message.logged_in_successfully'), 'success');
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            return back()->with('message', __('message.something_went_wrong'));
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    protected function loggedOut(Request $request)
    {
        Auth::logout();
        // Invalidate the session.
        $request->session()->invalidate();
        // Regenerate the CSRF token.
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
