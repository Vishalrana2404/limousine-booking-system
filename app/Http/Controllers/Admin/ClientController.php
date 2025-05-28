<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddClientRequest;
use App\Http\Requests\BulkClientStatusUpdateRequest;
use App\Http\Requests\DeleteClientRequest;
use App\Http\Requests\EditClientRequest;
use App\Models\Client;
use App\Models\UserType;
use App\Services\ClientService;
use App\Services\HotelService;
use App\Services\UserService;
use App\Services\UserTypeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ClientController
 * 
 * @package  App\Http\Controllers\Admin
 */
class ClientController extends Controller
{
    /**
     * Constructor for the ClientController class.
     *
     * Initializes ClientController with necessary services and dependencies.
     *
     * @param UserService    $userService    The UserService instance to handle user-related operations.
     * @param UserTypeService $userTypeService The UserTypeService instance to handle user type-related operations.
     * @param ClientService  $clientService  The ClientService instance to handle client-related operations.
     * @param HotelService  $hotelService  The HotelService instance to handle hotel-related operations.
     * @param CustomHelper   $helper         The CustomHelper instance to provide additional utility functions.
     */
    public function __construct(
        private UserService $userService,
        private UserTypeService $userTypeService,
        private ClientService $clientService,
        private HotelService $hotelService,
        private CustomHelper $helper
    ) {
    }
    /**
     * Display the client index page.
     *
     * Retrieves client data and user type data, then renders the client index view.
     *
     * @param Request $request The HTTP request object containing query parameters.
     * 
     * @return \Illuminate\Contracts\View\View The rendered view for the client index page.
     */
    public function index(Request $request)
    {
        try {
            $clientData = $this->clientService->getClientData($request->query());
            $userTypeData = $this->userTypeService->getUserType(UserType::CLIENT);
            $hotels = $this->hotelService->getHotels();
            return view('admin.client.client', compact('clientData', 'userTypeData','hotels'));
        } catch (\Exception $e) {
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            return redirect()->back();
        }
    }
    /**
     * Display the form for creating a new client.
     *
     * Retrieves user type data and renders the create client view.
     *
     * @param Request $request The HTTP request object.
     * 
     * @return \Illuminate\Contracts\View\View The rendered view for creating a new client.
     */
    public function create(Request $request)
    {
        $hotels = $this->hotelService->getHotels()->sortBy(fn($hotel) => strtolower($hotel->name));
        // Retrieve entities  data from constatnts
        $entities = config('constants.entities');
        $userTypeData = $this->userTypeService->getUserType(UserType::CLIENT);
        return view('admin.client.create-client', compact('userTypeData', 'entities','hotels'));
    }
    /**
     * Save a new client.
     *
     * Attempts to create a new client using the provided request data.
     *
     * @param AddClientRequest $request The HTTP request object containing the client data.
     * 
     * @return \Illuminate\Http\RedirectResponse A redirect response after successful client creation.
     */
    public function save(AddClientRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            $this->clientService->createClient($request->all(), $log_headers);
            $this->helper->alertResponse(__('message.client_created'), 'success');
            return redirect('clients');
        } catch (\Exception $e) {
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Filter clients based on the provided criteria.
     *
     * Retrieves client data based on the provided query parameters and returns
     * a partial HTML view for updating the client listing.
     *
     * @param Request $request The HTTP request object containing the filter criteria.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the updated client listing HTML.
     */
    public function filterClients(Request $request)
    {
        try {
            // Retrieve client data based on the filter criteria
            $clientData = $this->clientService->getClientData($request->query());
            // Render the client listing partial view with the filtered data
            $data = ['html' => view('admin.client.partials.clients-listing', compact('clientData'))->render()];
            // Return a JSON response with the updated client listing HTML
            return $this->handleResponse($data, __("message.client_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
    /**
     * Update status for multiple users in bulk.
     *
     * Updates the status of multiple users based on the provided request data.
     *
     * @param BulkClientStatusUpdateRequest $request The HTTP request object containing the bulk status update data.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the updated user data.
     */
    public function updateBulkStatus(BulkClientStatusUpdateRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the status of multiple users based on the request data
            $userData = $this->clientService->updateBulkStatus($request->all(), $log_headers);
            // Generate and return a successful response with the updated user data
            return $this->handleResponse($userData, __("message.client_status_updated"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }


    /**
     * Delete a client.
     *
     * Deletes a client based on the provided request data.
     *
     * @param DeleteClientRequest $request The HTTP request object containing the client data to be deleted.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the updated user data after deletion.
     */
    public function delete(DeleteClientRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Delete the client based on the request data
            $userData = $this->clientService->deleteClient($request->all(), $log_headers);
            // Generate and return a successful response with the updated user data
            return $this->handleResponse($userData, __("message.client_deleted"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }


    /**
     * Display the form for editing a client.
     *
     * Retrieves client data including associated user  then renders the update client view.
     *
     * @param Client $client The client instance to be edited.
     * 
     * @return \Illuminate\Contracts\View\View The rendered view for updating a client.
     */
    public function edit(Client $client)
    {
        // Load client data including associated user
        $clientData = $client->load(['user.userType', 'multiCorporates']);
        
        $hotels = $this->hotelService->getHotels()->sortBy(fn($hotel) => strtolower($hotel->name));

        // Retrieve entities  data from constatnts
        $entities = config('constants.entities');
        // Retrieve user type data
        $userTypeData = $this->userTypeService->getUserType(UserType::CLIENT);
        // Return the view for updating a client with the necessary data
        return view('admin.client.update-client', compact('userTypeData', 'clientData', 'entities','hotels'));
    }



    /**
     * Update a client.
     *
     * Updates a client using the provided request data and client instance.
     *
     * @param EditClientRequest $request The HTTP request object containing the updated client data.
     * @param Client $client The client instance to be updated.
     * 
     * @return \Illuminate\Http\RedirectResponse A redirect response after successful client update.
     */
    public function update(EditClientRequest $request, Client $client)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the client using the request data and client instance
            $this->clientService->updateClient($request->all(), $client, $log_headers);
            // Display a success message and redirect to the clients page
            $this->helper->alertResponse(__('message.client_updated'), 'success');
            return redirect('clients');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    public function getClientsByCorporateId(Request $request)
    {
        $clientsData = $this->clientService->getClientsByHotel($request->hotel_id);

        if(!empty($clientsData))
        {
            return $this->handleResponse($clientsData, __("message.clients_fetched_successfully"), Response::HTTP_OK);
        }else{
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
