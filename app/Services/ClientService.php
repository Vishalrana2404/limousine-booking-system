<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Repositories\Interfaces\ClientInterface;
use App\Repositories\Interfaces\UserInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 *
 * ClientService class
 * 
 */
class ClientService
{
    /**
     * ClientService constructor.
     *
     * @param UserInterface $userRepository The user repository instance.
     * @param ClientInterface $clientRepository The client repository instance.
     * @param UserService $userService The user service instance.
     * @param Auth $auth The authentication instance.
     *  @param ActivityLogService $activityLogService The activity log instance.
     */
    public function __construct(
        private UserInterface $userRepository,
        private ClientInterface $clientRepository,
        private UserService $userService,
        private Auth $auth,
        private ActivityLogService $activityLogService,
    ) {
    }


    /**
     * Retrieve client data based on the provided request data.
     *
     * @param array $requestData The request data containing filters and sorting parameters.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator The paginated client data.
     * @throws \Exception If an error occurs during the retrieval process.
     */
    public function getClientData(array $requestData = [])
    {
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();

            // Extract parameters from the request data or use default values
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'asc';
            $filterByUserType = $requestData['filterByUserType'] ?? null;
            $filterByClient = $requestData['filterByClient'] ?? null;
            // Get client data from the repository based on the provided parameters
            return $this->clientRepository->getClient($loggedUser, $filterByClient, $filterByUserType, $search, $page, $sortField, $sortDirection);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getClientsByHotel($hotel_id)
    {
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();

            // Get paginated client data using the client repository
            return $this->clientRepository->getClientsByHotel($loggedUser, $hotel_id);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    public function getHotelClients($hotel_id)
    {
        try {
            // Get paginated client data using the client repository
            return $this->clientRepository->getHotelClients($hotel_id);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    public function getClientsByLinkedHotel($hotel_id)
    {
        try {
            // Get paginated client data using the client repository
            return $this->clientRepository->getClientsByLinkedHotel($hotel_id);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create a new client.
     *
     * @param array $requestData The request data containing client information.
     * @return bool True if the client creation is successful, otherwise false.
     * @throws \Exception If an error occurs during the client creation process.
     */
    public function createClient($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            if(!empty(Auth::user()))
            {
                $loggedUserId = Auth::user()->id;
            }else{
                $loggedUserId = 1;
            }
            $userData = [];
            $clientData = [];
            // Prepare user data
            $userData['first_name'] = $requestData['first_name'];
            $userData['last_name'] = $requestData['last_name'];
            $userData['user_type_id'] = $requestData['client_type'];
            $userData['status'] = $requestData['status'];
            $userData['country_code'] = $requestData['country_code'];
            $userData['phone'] = $requestData['phone'];
            $userData['email'] = $requestData['email'];
            $userData['password'] = Str::random(8);
            $userData['created_by_id'] = $loggedUserId;
            $user = $this->userRepository->addUser($userData);
    
            // Prepare client data
            $clientData['user_id'] = $user->id;
            $clientData['hotel_id'] = $requestData['hotel_id'];
            $clientData['invoice'] = $requestData['invoice'];
            $clientData['status'] = $requestData['status'];
            $clientData['entity'] = $requestData['entity'];
            $clientData['created_by_id'] = $loggedUserId;
            $client = $this->clientRepository->addClient($clientData);
    
            $clientId = $client->id;
            // Prepare Client Multi Hotels Data
            if(!empty($requestData['multi_hotel_id']) && count($requestData['multi_hotel_id']) > 0)
            {
                foreach($requestData['multi_hotel_id'] as $client_hotel_data)
                {
                    if(!empty($client_hotel_data) && !empty($clientId)){
                        $clientHotelData['client_id'] = $clientId;
                        $clientHotelData['hotel_id'] = $client_hotel_data;
                        $clientHotelData['status'] = $requestData['status'];
                        $clientHotelData['created_by_id'] = $loggedUserId;
                        $clientHotel = $this->clientRepository->addClientHotel($clientHotelData);
                    }
                }
            }

            $this->activityLogService->addActivityLog('create', User::class, json_encode([]), json_encode($userData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            $this->activityLogService->addActivityLog('create', Client::class, json_encode([]), json_encode($clientData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);

            DB::commit();
            $this->userService->sendPasswordEmail($user, $userData['password']);
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update the status of multiple clients in bulk.
     *
     * @param array $requestData The request data containing client IDs and the new status.
     * @return bool True if the status update is successful.
     * @throws \Exception If an error occurs during the status update process.
     */
    public function updateBulkStatus($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $clientIds = $requestData['client_ids'];
            $status = $requestData['status'];
            // Update the status of clients in bulk
            $user = $this->clientRepository->updateBulkStatus($clientIds, $status, $loggedUserId);
            $this->activityLogService->addActivityLog('updateBulkStatus', 'App\Models\Client', json_encode([]), json_encode($requestData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Delete client(s) from the database.
     *
     * @param array $requestData The request data containing the IDs of the clients to delete.
     * @return bool True if the client(s) are successfully deleted.
     * @throws \Exception If an error occurs during the deletion process.
     */
    public function deleteClient($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $oldData = $this->clientRepository->getClientByIds($requestData['client_ids']);
            // Delete client(s) from the database
            $client = $this->clientRepository->deleteClient($requestData['client_ids']);
            $userIds = [];
            foreach ($oldData as $client) {
                if (isset($client->user)) {
                    $userIds[] = $client->user->id;
                }
            }
            if (count($userIds)) {
                $this->userRepository->deleteUser($userIds);
            }
            $this->activityLogService->addActivityLog('delete', Client::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $client;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Update a client's information in the database.
     *
     * @param array $requestData The request data containing the updated client information.
     * @param Client $client The client instance to update.
     * @return bool True if the client information is successfully updated.
     * @throws \Exception If an error occurs during the update process.
     */
    public function updateClient(array $requestData, Client $client, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $userData = [];
            $clientData = [];
            $user = $this->userRepository->getUserById($client->user_id);

            $userData['first_name'] = $requestData['first_name'];
            $userData['last_name'] = $requestData['last_name'];
            $userData['user_type_id'] = $requestData['client_type'];
            $userData['status'] = $requestData['status'];
            $userData['country_code'] = $requestData['country_code'];
            $userData['phone'] = $requestData['phone'];
            $userData['email'] = $requestData['email'];
            $userData['updated_by_id'] = $loggedUserId;
            $oldUserData = json_encode($user);
            $this->userRepository->updateUser($user, $userData,);

            $clientData['user_id'] = $user->id;
            $clientData['hotel_id'] = $requestData['hotel_id'];
            $clientData['invoice'] = $requestData['invoice'];
            $clientData['status'] = $requestData['status'];
            $clientData['entity'] = $requestData['entity'];
            $clientData['updated_by_id'] = $loggedUserId;
            $oldClientData = json_encode($client);
            $this->clientRepository->updateClient($client, $clientData);


            $clientId = $client->id;
            // Prepare Client Multi Hotels Data
            $this->clientRepository->syncClientHotels(
                $requestData['multi_hotel_id'] ?? [],
                $clientId,
                $loggedUserId,
                $requestData['status'] ?? 'active'
            );

            $this->activityLogService->addActivityLog('create', User::class, $oldUserData, json_encode($userData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            $this->activityLogService->addActivityLog('create', Client::class, $oldClientData, json_encode($clientData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function getActiveClients()
    {
        try {
            // Retrieve all clients from the repository
            return $this->clientRepository->getActiveClientsData();
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    public function getClientLinkageLogs($clientId)
    {
        try {
            // Retrieve all hotels from the repository
            return $this->clientRepository->getClientLinkageLogs($clientId);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }
}
