<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\User;
use App\Models\UserType;
use App\Repositories\Interfaces\BookingInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Class BookingRepository
 * 
 * This class implements the BookingInterface and provides methods to interact with booking.
 */
class BookingRepository implements BookingInterface
{
    /**
     * Create a new instance of the BookingRepository.
     *
     * @param Booking $model The model instance for bookings.
     */
    public function __construct(
        protected Booking $model,
        protected User $userModel
    ) {
    }


    /**
     * Add a new booking.
     *
     * Adds a new booking to the database.
     *
     * @param array $data The data of the booking to be added.
     * @return \App\Models\Booking The newly created booking instance.
     */
    public function addBooking(array $data): Booking
    {
        return $this->model->create($data);
    }

    /**
     * Delete bookings by their IDs.
     *
     * Deletes bookings from the database based on their IDs.
     *
     * @param array $bookingIds The IDs of the bookings to be deleted.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteBooking(array $bookingIds): bool
    {
        return $this->model->whereIn('id', $bookingIds)->delete();
    }

    /**
     * Update a booking.
     *
     * Updates the data of an existing booking in the database.
     *
     * @param \App\Models\Booking $booking The booking instance to be updated.
     * @param array               $data    The updated data for the booking.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateBooking(Booking $booking, array $data): bool
    {
        return $booking->update($data);
    }

    
    public function restoreBooking(Booking $booking, array $data): bool
    {
        return $booking->update($data);
    }

    
    public function cancelBooking(Booking $booking, array $data): bool
    {
        return $booking->update($data);
    }

    /**
     * Retrieve a booking by its ID.
     *
     * Retrieves a booking from the database based on its ID.
     *
     * @param int $bookingId The ID of the booking to be retrieved.
     * @return \App\Models\Booking|null The retrieved booking instance, or null if not found.
     */
    public function getBookingById(int $bookingId): ?Booking
    {
        return $this->model->find($bookingId);
    }

    
    public function getBookingByIdToRestore(int $bookingId): ?Booking
    {
        return $this->model->withTrashed()->find($bookingId);
    }

    
    public function getBookingByIdsToPermanentDelete(array $bookingIds, string $status = null): Collection
    {
        // Start the query with the base condition
        $query = $this->model->withTrashed()->whereIn('id', $bookingIds);
        // Add the status condition if it's provided
        if (!empty($status)) {
            $query->where('status', $status);
        }
        // Execute the query and return the result
        return $query->get();
    }
    
    public function permanentDeleteBooking(array $bookingIds): bool
    {
        if(!empty($bookingIds))
        {
            foreach($bookingIds as $bId)
            {
                $data = [];
                $data['completely_deleted'] = 'yes';

                $this->model->withTrashed()->where('id', $bId)->update($data);
            }
        }
        return $this->model->whereIn('id', $bookingIds)->delete();
    }


    /**
     * Retrieve all booking data from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of booking data.
     */
    public function getBookingData()
    {
        return Booking::all();
    }

    /**
     * Retrieve paginated and sorted booking data based on provided parameters.
     *
     * Retrieves paginated and sorted booking data based on the logged-in user,
     * search query, pagination parameters, and sorting criteria.
     *
     * @param \App\Models\User $loggedUser    The logged-in user instance.
     * @param string           $search        The search query to filter bookings (default: '').
     * @param int              $page          The page number for pagination (default: 1).
     * @param string           $sortField     The field to sort bookings by (default: 'id').
     * @param string           $sortDirection The direction for sorting ('asc' or 'desc') (default: 'asc').
     * @return \Illuminate\Pagination\LengthAwarePaginator A paginated list of sorted bookings.
     */
    public function getBookings(User $loggedUser,  $startDate, $endDate, string $search = '', string $searchByBookingId = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc', $driverId = null, $noPagination = false, $isDriverSchedule = false)
    {
        // Filter Booking based on the provided parameters
        $bookings = $this->filterBookingResult($loggedUser, $startDate, $endDate, $search, $searchByBookingId, $driverId, $isDriverSchedule)->get();
        if ($noPagination) {
            return  $bookings;
        }
        // Sort the bookings based on the specified field and direction
        $sortedCollection = $this->sortBookings($bookings, $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    
    public function getBookingsArchive(User $loggedUser,  $startDate, $endDate, string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc', $driverId = null, $noPagination = false, $isDriverSchedule = false)
    {
        // Filter Booking based on the provided parameters
        $bookings = $this->filterBookingArchiveResult($loggedUser, $startDate, $endDate, $search, $driverId, $isDriverSchedule)->get();
        if ($noPagination) {
            return  $bookings;
        }
        // Sort the bookings based on the specified field and direction
        $sortedCollection = $this->sortBookingsArchive($bookings, $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }


    /**
     * Filter booking results based on the logged-in user and search query.
     *
     * Filters booking results based on the logged-in user's ID and a search query.
     * Builds the query with eager loading relationships and applies search filters.
     *
     * @param \App\Models\User $loggedUser The logged-in user instance.
     * @param string           $search     The search query to filter bookings (default: '').
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance with applied filters.
     */
    private function filterBookingResult(User $loggedUser, $startDate, $endDate, string $search = '', string $searchByBookingId = '', $driverId = null, $isDriverSchedule = false)
    {
        $loggedUserId = $loggedUser->id;
        $loggedUserHotel = $loggedUser->client->hotel_id ?? null;
        $loggedUserType = $loggedUser->userType->type ?? null;

        // Start building the query with eager loading relationships
        $query = $this->model
            ->with(['serviceType', 'event', 'pickUpLocation', 'dropOffLocation', 'vehicleType', 'client.user', 'client.hotel', 'vehicle', 'driver', 'createdBy', 'updatedBy']);

        if ($loggedUserType === UserType::CLIENT) {
            $query->where(function ($query) use ($loggedUserId, $loggedUserHotel) {
                $query->where('created_by_id', $loggedUserId)
                      ->orWhereHas('client', function ($query) use ($loggedUserHotel) {
                          $query->where('hotel_id', $loggedUserHotel);
                      })
                      ->orWhere(function ($query) use ($loggedUserId) {  // Fixed `->orWhere`
                          $query->whereRaw("linked_clients REGEXP ?", ["(^|,)$loggedUserId(,|$)"]);
                      });
            });
        }
        
        if ($isDriverSchedule) {
            $query->whereNotIn('status', [Booking::COMPLETED, Booking::CANCELLED]);
        }

        if ($searchByBookingId) {
            $query->where('id', $searchByBookingId);
        }

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw("CONCAT(pickup_date, ' ', pickup_time)"), [$startDate, $endDate]);
        }

        if (!empty($driverId)) {
            $query->where('driver_id', $driverId);
        }

        if ($loggedUserType === UserType::CLIENT) {
            $query->where(function ($query) use ($loggedUserId) {
                $query->whereRaw("linked_clients REGEXP ?", ["(^|,)$loggedUserId(,|$)"])
                      ->orWhere('created_by_id', $loggedUserId);
            });
        }        
        
        // if (!empty($clientId)) {
        //     $query->where('created_by_id', $clientId);
        // }

        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search, $loggedUserType) {
                $query->whereRaw("LOWER(`client_instructions`) like ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`id`) like ?", ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`drop_of_location`) like ?", ['%' . $search . '%'])
                    ->orWhereRaw("CONCAT(`country_code`, `phone`) LIKE ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`guest_name`) like ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`pick_up_location`) like ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`status`) like ?",  ['%' . $search . '%']);
                if ($loggedUserType === null || $loggedUserType === UserType::ADMIN) {
                    $query->orWhereRaw("LOWER(`driver_remark`) like ?",  ['%' . $search . '%'])
                        ->orWhereRaw("LOWER(`internal_remark`) like ?",  ['%' . $search . '%']);
                }

                $query->orWhereHas('serviceType', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('event', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('pickUpLocation', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?", ['%' . $search . '%']);
                })->orWhereHas('dropOffLocation', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('driver', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('vehicle', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`model`) like ?", ['%' . $search . '%']);
                });
                if ($loggedUserType === null || $loggedUserType === UserType::ADMIN) {
                    $query->orWhereHas('client.hotel', function ($query) use ($search) {
                        $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                    })->orWhereHas('updatedBy', function ($query) use ($search) {
                        $query->whereRaw("CONCAT(LOWER(`first_name`), ' ', LOWER(`last_name`)) like ?", ['%' . $search . '%']);
                    });
                }
            });
        }

        $query->where('completely_deleted', 'no');
        // $query->withTrashed();

        return $query;
    }

    
    private function filterBookingArchiveResult(User $loggedUser, $startDate, $endDate, string $search = '', $driverId = null, $isDriverSchedule = false)
    {
        $loggedUserId = $loggedUser->id;
        $loggedUserHotel = $loggedUser->client->hotel_id ?? null;
        $loggedUserType = $loggedUser->userType->type ?? null;

        // Start building the query with eager loading relationships
        $query = $this->model
            ->with(['serviceType', 'event', 'pickUpLocation', 'dropOffLocation', 'vehicleType', 'client.user', 'client.hotel', 'vehicle', 'driver', 'createdBy', 'updatedBy']);

        if ($loggedUserType === UserType::CLIENT) {
            $query->where(function ($query) use ($loggedUserId, $loggedUserHotel) {
                $query->where('created_by_id', $loggedUserId)
                    ->orWhereHas('client', function ($query) use ($loggedUserHotel) {
                        $query->where('hotel_id', $loggedUserHotel);
                    });
            });
        }
        if ($isDriverSchedule) {
            $query->whereNotIn('status', [Booking::COMPLETED, Booking::CANCELLED]);
        }

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw("CONCAT(pickup_date, ' ', pickup_time)"), [$startDate, $endDate]);
        }

        if (!empty($driverId)) {
            $query->where('driver_id', $driverId);
        }

        // if (!empty($clientId)) {
        //     $query->where('created_by_id', $clientId);
        // }
        
        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search, $loggedUserType) {
                $query->whereRaw("LOWER(`client_instructions`) like ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`id`) like ?", ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`drop_of_location`) like ?", ['%' . $search . '%'])
                    ->orWhereRaw("CONCAT(`country_code`, `phone`) LIKE ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`guest_name`) like ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`pick_up_location`) like ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`status`) like ?",  ['%' . $search . '%']);
                if ($loggedUserType === null || $loggedUserType === UserType::ADMIN) {
                    $query->orWhereRaw("LOWER(`driver_remark`) like ?",  ['%' . $search . '%'])
                        ->orWhereRaw("LOWER(`internal_remark`) like ?",  ['%' . $search . '%']);
                }

                $query->orWhereHas('serviceType', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('event', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('pickUpLocation', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?", ['%' . $search . '%']);
                })->orWhereHas('dropOffLocation', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('driver', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('vehicle', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`model`) like ?", ['%' . $search . '%']);
                });
                if ($loggedUserType === null || $loggedUserType === UserType::ADMIN) {
                    $query->orWhereHas('client.hotel', function ($query) use ($search) {
                        $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                    })->orWhereHas('updatedBy', function ($query) use ($search) {
                        $query->whereRaw("CONCAT(LOWER(`first_name`), ' ', LOWER(`last_name`)) like ?", ['%' . $search . '%']);
                    });
                }
            });
        }

        $query->where('completely_deleted', 'no');
        $query->onlyTrashed();

        return $query;
    }



    /**
     * Sort bookings collection based on the specified field and direction.
     *
     * Sorts the bookings collection based on the specified field and direction.
     *
     * @param \Illuminate\Support\Collection $bookings      The collection of bookings to be sorted.
     * @param string                         $sortField     The field to sort bookings by (default: 'id').
     * @param string                         $sortDirection The direction for sorting ('asc' or 'desc') (default: 'desc').
     * @return \Illuminate\Support\Collection The sorted collection of bookings.
     */
    private function sortBookings(Collection $bookings, string $sortField = 'id', string $sortDirection = 'desc')
    {
        // Determine the sorting function based on the sort direction
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $bookings->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortLastEdit':
                    $value = strtolower($innerQuery->updatedBy->name ?? 'zzzz');
                    break;
                case 'sortComment':
                    $value = strtolower($innerQuery->comment ?? 'zzzz');
                    break;
                case 'sortDriverRemark':
                    $value = strtolower($innerQuery->driver_remarks ?? 'zzzz');
                    break;
                case 'sortInstructions':
                    $value = strtolower($innerQuery->client_instructions ?? 'zzzz');
                    break;
                case 'sortStatus':
                    $value = strtolower($innerQuery->status ?? 'zzzz');
                    break;
                case 'sortVehicleType':
                    $value = strtolower($innerQuery->vehicleType->name ?? 'zzzz');
                    break;
                case 'sortDriver':
                    $value = strtolower($innerQuery->driver->name ?? 'zzzz');
                    break;
                case 'sortContact':
                    $value = strtolower($innerQuery->country_code . $innerQuery->phone ?? 'zzzz');
                    break;
                case 'sortClient':
                    $value = strtolower($innerQuery->client->hotel->name ?? 'zzzz');
                    break;
                case 'sortDropOf':
                    $dropOffLocationId = $innerQuery->drop_off_location_id ?? null;
                    $dropOffLocation = null;
                    if ($innerQuery->service_type_id === 3) {
                        $dropOffLocation = $innerQuery->flight_detail;
                    } else {
                        if ($dropOffLocationId && $dropOffLocationId !== 8) {
                            $dropOffLocation = $innerQuery->dropOffLocation->name ?? null;
                        } else {
                            $dropOffLocation = $innerQuery->drop_of_location;
                        }
                    }
                    $value = strtolower($dropOffLocation ?? 'zzzz');
                    break;
                case 'sortPikUp':
                    $pickUpLocationId = $innerQuery->pick_up_location_id ?? null;
                    $pickUpLocation = null;
                    if ($innerQuery->service_type_id === 1) {
                        $pickUpLocation = $innerQuery->flight_detail;
                    } else {
                        if ($pickUpLocationId && $pickUpLocationId !== 8) {
                            $pickUpLocation = $innerQuery->pickUpLocation->name ?? null;
                        } else {
                            $pickUpLocation = $innerQuery->pick_up_location;
                        }
                    }
                    $value = strtolower($pickUpLocation ?? 'zzzz');
                    break;
                case 'sortType':
                    $value = strtolower($innerQuery->serviceType->name ?? 'zzzz');
                    break;
                case 'sortTime':
                    $value = $innerQuery->pickup_time;
                    break;
                case 'sortBooking':
                    $value = strtolower($innerQuery->id ?? 'zzzz');
                    break;
                case 'sortPickUpDate':
                    $value = $innerQuery->pickup_date ?? 'zzzz';
                    break;
                case 'sortBookingDate':
                    $value = $innerQuery->created_at ?? 'zzzz';
                    break;
                default:
                    $value = strtolower($innerQuery->id ?? 'zzzz');
                    break;
            }
            return $value;
        });
    }

    
    private function sortBookingsArchive(Collection $bookings, string $sortField = 'id', string $sortDirection = 'desc')
    {
        // Determine the sorting function based on the sort direction
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $bookings->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortLastEdit':
                    $value = strtolower($innerQuery->updatedBy->name ?? 'zzzz');
                    break;
                case 'sortComment':
                    $value = strtolower($innerQuery->comment ?? 'zzzz');
                    break;
                case 'sortDriverRemark':
                    $value = strtolower($innerQuery->driver_remarks ?? 'zzzz');
                    break;
                case 'sortInstructions':
                    $value = strtolower($innerQuery->client_instructions ?? 'zzzz');
                    break;
                case 'sortStatus':
                    $value = strtolower($innerQuery->status ?? 'zzzz');
                    break;
                case 'sortVehicleType':
                    $value = strtolower($innerQuery->vehicleType->name ?? 'zzzz');
                    break;
                case 'sortDriver':
                    $value = strtolower($innerQuery->driver->name ?? 'zzzz');
                    break;
                case 'sortContact':
                    $value = strtolower($innerQuery->country_code . $innerQuery->phone ?? 'zzzz');
                    break;
                case 'sortClient':
                    $value = strtolower($innerQuery->client->hotel->name ?? 'zzzz');
                    break;
                case 'sortDropOf':
                    $dropOffLocationId = $innerQuery->drop_off_location_id ?? null;
                    $dropOffLocation = null;
                    if ($innerQuery->service_type_id === 3) {
                        $dropOffLocation = $innerQuery->flight_detail;
                    } else {
                        if ($dropOffLocationId && $dropOffLocationId !== 8) {
                            $dropOffLocation = $innerQuery->dropOffLocation->name ?? null;
                        } else {
                            $dropOffLocation = $innerQuery->drop_of_location;
                        }
                    }
                    $value = strtolower($dropOffLocation ?? 'zzzz');
                    break;
                case 'sortPikUp':
                    $pickUpLocationId = $innerQuery->pick_up_location_id ?? null;
                    $pickUpLocation = null;
                    if ($innerQuery->service_type_id === 1) {
                        $pickUpLocation = $innerQuery->flight_detail;
                    } else {
                        if ($pickUpLocationId && $pickUpLocationId !== 8) {
                            $pickUpLocation = $innerQuery->pickUpLocation->name ?? null;
                        } else {
                            $pickUpLocation = $innerQuery->pick_up_location;
                        }
                    }
                    $value = strtolower($pickUpLocation ?? 'zzzz');
                    break;
                case 'sortType':
                    $value = strtolower($innerQuery->serviceType->name ?? 'zzzz');
                    break;
                case 'sortTime':
                    $value = $innerQuery->pickup_time;
                    break;
                case 'sortBooking':
                    $value = strtolower($innerQuery->id ?? 'zzzz');
                    break;
                case 'sortPickUpDate':
                    $value = $innerQuery->pickup_date ?? 'zzzz';
                    break;
                case 'sortBookingDate':
                    $value = $innerQuery->created_at ?? 'zzzz';
                    break;
                default:
                    $value = strtolower($innerQuery->id ?? 'zzzz');
                    break;
            }
            return $value;
        });
    }

    /**
     * Paginate a collection of results.
     *
     * Paginates the provided collection based on the specified page size and page number.
     *
     * @param mixed $collection The collection of results to be paginated.
     * @param int $pageSize The number of items per page.
     * @param int $page The current page number (optional, default is 1).
     * @return LengthAwarePaginator The paginated collection of results.
     */
    private function paginateResults($collection, $pageSize, $page = 1): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $collection->values()->forPage($page, $pageSize), // Paginate the collection for the specified page and page size
            $collection->count(), // Total count of items in the collection
            $pageSize, // Number of items per page
            $page, // Current page number
            ['path' => LengthAwarePaginator::resolveCurrentPath()] // Path for generating pagination links
        );
    }

    /**
     * Retrieve the last pending bookings that are older than the specified threshold.
     *
     * @param int $threshold The threshold in minutes to consider bookings as pending.
     * @return \Illuminate\Database\Eloquent\Collection The collection of last pending bookings.
     */
    public function getLastPendingBookings($threshold): Collection
    {
        return $this->model->with(['vehicleType'])
            ->where('status', Booking::PENDING)
            ->where('is_cross_border', 0)
            ->where('created_at', '<', $threshold)
            ->where('pick_up_location_id', '!=', 8)
            ->get();
    }

    public function getNextDayBookings($startDate, $startTime, $endDate, $endTime): Collection
    {
        return $this->model->with(['driver'])->where(function ($query) use ($startDate, $startTime, $endDate, $endTime) {
            $query->where(function ($query) use ($startDate, $startTime) {
                $query->where('pickup_date', $startDate)
                    ->where('pickup_time', '>=', $startTime);
            })->orWhere(function ($query) use ($endDate, $endTime) {
                $query->where('pickup_date', $endDate)
                    ->where('pickup_time', '<', $endTime);
            });
        })->whereHas('driver', function ($query) {
            $query->where('driver_type', 'INHOUSE');
        })->get();
    }

      /**
     * Retrieves a collection of bookings based on the given array of booking IDs and an optional status.
     *
     * This function queries the database to fetch all bookings whose IDs are in the provided array.
     * Optionally, it filters the bookings by their status if a status is provided.
     *
     * @param array $bookingIds An array of booking IDs to retrieve.
     * @param string|null $status An optional status to filter the bookings by. Defaults to null.
     * 
     * @return \Illuminate\Support\Collection A collection of bookings that match the given IDs and status.
     *
     */
    public function getBookingByIds(array $bookingIds, string $status = null): Collection
    {
        // Start the query with the base condition
        $query = $this->model->whereIn('id', $bookingIds);
        // Add the status condition if it's provided
        if (!empty($status)) {
            $query->where('status', $status);
        }
        // Execute the query and return the result
        return $query->get();
    }

    public function getBookingsForReports(User $loggedUser,  $startDate, $endDate, string $search = '', string $searchByBookingId = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc', $driverId = null, $hotelId = null, $eventId = null, $userId = null, $noPagination = false, $isDriverSchedule = false)
    {
        // Filter Booking based on the provided parameters
        $bookings = $this->filterBookingResultForReports($loggedUser, $startDate, $endDate, $search, $searchByBookingId, $driverId, $hotelId, $eventId, $userId, $isDriverSchedule)->get();
        if ($noPagination) {
            return  $bookings;
        }

        foreach($bookings as $booking)
        {
            $linkedClients = null;

            $linkedClients = explode(',', $booking->linked_clients);
            
            $booking->linkedClients = $this->model->linkedClients($linkedClients);
        }
        
        // Sort the bookings based on the specified field and direction
        $sortedCollection = $this->sortBookingsForReports($bookings, $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }
    
    private function filterBookingResultForReports(User $loggedUser, $startDate, $endDate, string $search = '', string $searchByBookingId = '', $driverId = null, $hotelId = null, $eventId = null, $userId = null, $isDriverSchedule = false)
    {
        $loggedUserId = $loggedUser->id;
        $loggedUserHotel = $loggedUser->client->hotel_id ?? null;
        $loggedUserType = $loggedUser->userType->type ?? null;

        // Start building the query with eager loading relationships
        $query = $this->model
            ->with(['serviceType', 'event', 'pickUpLocation', 'dropOffLocation', 'vehicleType', 'client', 'client.user', 'client.hotel', 'vehicle', 'driver', 'createdBy', 'updatedBy']);

        if ($loggedUserType === UserType::CLIENT) {
            $query->where(function ($query) use ($loggedUserId, $loggedUserHotel) {
                $query->where('created_by_id', $loggedUserId)
                    ->orWhereHas('client', function ($query) use ($loggedUserHotel) {
                        $query->where('hotel_id', $loggedUserHotel);
                    });
            });
        }


        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw("CONCAT(pickup_date, ' ', pickup_time)"), [$startDate, $endDate]);
        }

        if (!empty($searchByBookingId)) {
            $query->where('id', $searchByBookingId);
        }

        if (!empty($driverId)) {
            $query->where('driver_id', $driverId);
        }

        if (!empty($hotelId)) {
            $query->whereHas('client', function ($query) use ($hotelId) {
                $query->where('hotel_id', $hotelId);
            });
        }

        if (!empty($userId)) {
            $query->where('created_by_id', $userId);
        }

        if (!empty($eventId)) {
            $query->where('event_id', $eventId);
        }

        if ($loggedUserType === UserType::CLIENT) {
            $query->where(function ($query) use ($loggedUserId) {
                $query->whereRaw("
                    FIND_IN_SET(?, REPLACE(REPLACE(REPLACE(linked_clients, '\"', ''), '[', ''), ']', ''))
                ", [$loggedUserId])
                ->orWhere('created_by_id', $loggedUserId);
            });
        }
        
        
        
        // if (!empty($clientId)) {
        //     $query->where('created_by_id', $clientId);
        // }

        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search, $loggedUserType) {
                $query->whereRaw("LOWER(`client_instructions`) like ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`id`) like ?", ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`drop_of_location`) like ?", ['%' . $search . '%'])
                    ->orWhereRaw("CONCAT(`country_code`, `phone`) LIKE ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`guest_name`) like ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`pick_up_location`) like ?",  ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`status`) like ?",  ['%' . $search . '%']);
                if ($loggedUserType === null || $loggedUserType === UserType::ADMIN) {
                    $query->orWhereRaw("LOWER(`driver_remark`) like ?",  ['%' . $search . '%'])
                        ->orWhereRaw("LOWER(`internal_remark`) like ?",  ['%' . $search . '%']);
                }

                $query->orWhereHas('serviceType', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('event', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('pickUpLocation', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?", ['%' . $search . '%']);
                })->orWhereHas('dropOffLocation', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('driver', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                })->orWhereHas('vehicle', function ($query) use ($search) {
                    $query->whereRaw("LOWER(`model`) like ?", ['%' . $search . '%']);
                });
                if ($loggedUserType === null || $loggedUserType === UserType::ADMIN) {
                    $query->orWhereHas('client.hotel', function ($query) use ($search) {
                        $query->whereRaw("LOWER(`name`) like ?",  ['%' . $search . '%']);
                    })->orWhereHas('updatedBy', function ($query) use ($search) {
                        $query->whereRaw("CONCAT(LOWER(`first_name`), ' ', LOWER(`last_name`)) like ?", ['%' . $search . '%']);
                    });
                }
            });
        }
        $query->where('completely_deleted', 'no');
        // $query->withTrashed();

        return $query;
    }
    private function sortBookingsForReports(Collection $bookings, string $sortField = 'id', string $sortDirection = 'desc')
    {
        // Determine the sorting function based on the sort direction
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $bookings->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortLastEdit':
                    $value = strtolower($innerQuery->updatedBy->name ?? 'zzzz');
                    break;
                case 'sortComment':
                    $value = strtolower($innerQuery->comment ?? 'zzzz');
                    break;
                case 'sortDriverRemark':
                    $value = strtolower($innerQuery->driver_remarks ?? 'zzzz');
                    break;
                case 'sortInstructions':
                    $value = strtolower($innerQuery->client_instructions ?? 'zzzz');
                    break;
                case 'sortStatus':
                    $value = strtolower($innerQuery->status ?? 'zzzz');
                    break;
                case 'sortVehicleType':
                    $value = strtolower($innerQuery->vehicleType->name ?? 'zzzz');
                    break;
                case 'sortDriver':
                    $value = strtolower($innerQuery->driver->name ?? 'zzzz');
                    break;
                case 'sortContact':
                    $value = strtolower($innerQuery->country_code . $innerQuery->phone ?? 'zzzz');
                    break;
                case 'sortCorporate':
                    $value = strtolower($innerQuery->client->hotel->name ?? 'zzzz');
                    break;
                case 'sortBookedBy':
                    $value = strtolower($innerQuery->createdBy->first_name . ' ' . $innerQuery->createdBy->last_name ?? 'zzzz');
                    break;
                case 'sortEvent':
                    $value = strtolower($innerQuery->event->name ?? 'zzzz');
                    break;
                case 'sortDropOf':
                    $dropOffLocationId = $innerQuery->drop_off_location_id ?? null;
                    $dropOffLocation = null;
                    if ($innerQuery->service_type_id === 3) {
                        $dropOffLocation = $innerQuery->flight_detail;
                    } else {
                        if ($dropOffLocationId && $dropOffLocationId !== 8) {
                            $dropOffLocation = $innerQuery->dropOffLocation->name ?? null;
                        } else {
                            $dropOffLocation = $innerQuery->drop_of_location;
                        }
                    }
                    $value = strtolower($dropOffLocation ?? 'zzzz');
                    break;
                case 'sortAdditionalStops':
                    $additionalStops = $innerQuery->additional_stops ?? '';

                    $stopsArray = explode('||', $additionalStops);
                
                    $value = strtolower(trim($stopsArray[0] ?? 'zzzz'));
                    break;
                case 'sortPikUp':
                    $pickUpLocationId = $innerQuery->pick_up_location_id ?? null;
                    $pickUpLocation = null;
                    if ($innerQuery->service_type_id === 1) {
                        $pickUpLocation = $innerQuery->flight_detail;
                    } else {
                        if ($pickUpLocationId && $pickUpLocationId !== 8) {
                            $pickUpLocation = $innerQuery->pickUpLocation->name ?? null;
                        } else {
                            $pickUpLocation = $innerQuery->pick_up_location;
                        }
                    }
                    $value = strtolower($pickUpLocation ?? 'zzzz');
                    break;
                case 'sortType':
                    $value = strtolower($innerQuery->serviceType->name ?? 'zzzz');
                    break;
                case 'sortTime':
                    $value = $innerQuery->pickup_time;
                    break;
                case 'sortBooking':
                    $value = strtolower($innerQuery->id ?? 'zzzz');
                    break;
                case 'sortPickUpDate':
                    $value = $innerQuery->pickup_date ?? 'zzzz';
                    break;
                case 'sortBookingDate':
                    $value = $innerQuery->created_at ?? 'zzzz';
                    break;
                case 'sortAccessGivenClients':
                    $firstLinkedClient = !empty($innerQuery->linkedClients) ? $innerQuery->linkedClients->first() : null;

                    $value = $firstLinkedClient && !empty($firstLinkedClient)
                        ? ($firstLinkedClient->first_name . ' ' . $firstLinkedClient->last_name) 
                        : 'zzzz';

                    break;
                default:
                    $value = strtolower($innerQuery->id ?? 'zzzz');
                    break;
            }
            return $value;
        });
    }

    public function getBookingsForDashboardForPieChart($startDate, $endDate, $loggedUser)
    {
        // Filter Booking based on the provided parameters
        $bookings = $this->filterBookingResultForDashboardForPieChart($startDate, $endDate, $loggedUser)->get();

        return  $bookings;
    }
    
    private function filterBookingResultForDashboardForPieChart($startDate, $endDate, $loggedUser)
    {
        // Start building the query with eager loading relationships
        $query = $this->model->withTrashed()->whereBetween('pickup_date', [$startDate, $endDate]);

        $userTypeSlug = $loggedUser->userType->slug ?? null;
        if($userTypeSlug === 'client-staff' ||  $userTypeSlug === 'client-admin')
        {
            $query->where('created_by_id', $loggedUser->id);
        }
        return $query;
    }

    public function getBookingsForDashboardForLineChart($startDate, $endDate, $loggedUser)
    {
        // Filter Booking based on the provided parameters
        $bookings = $this->filterBookingResultForDashboardForLineChart($startDate, $endDate, $loggedUser)->get();

        return  $bookings;
    }
    
    private function filterBookingResultForDashboardForLineChart($startDate, $endDate, $loggedUser)
    {
        // Start building the query with eager loading relationships
        $query = $this->model->withTrashed()->selectRaw('DATE(pickup_date) as date, COUNT(*) as count')
        ->whereBetween('pickup_date', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date', 'ASC');

        $userTypeSlug = $loggedUser->userType->slug ?? null;
        if($userTypeSlug === 'client-staff' ||  $userTypeSlug === 'client-admin')
        {
            $query->where('created_by_id', $loggedUser->id);
        }

        return $query;
    }

    public function getBookingsForDashboardForLineChartCancellation($startDate, $endDate, $loggedUser)
    {
        // Filter Booking based on the provided parameters
        $bookings = $this->filterBookingResultForDashboardForLineChartForCancellation($startDate, $endDate, $loggedUser)->get();

        return  $bookings;
    }
    
    private function filterBookingResultForDashboardForLineChartForCancellation($startDate, $endDate, $loggedUser)
    {
        // Start building the query with eager loading relationships
        $query = $this->model->withTrashed()->selectRaw('DATE(pickup_date) as date, COUNT(*) as count')
        ->where('status', 'CANCELLED')
        ->whereBetween('pickup_date', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date', 'ASC');

        $userTypeSlug = $loggedUser->userType->slug ?? null;
        if($userTypeSlug === 'client-staff' ||  $userTypeSlug === 'client-admin')
        {
            $query->where('created_by_id', $loggedUser->id);
        }

        return $query;
    }
}
