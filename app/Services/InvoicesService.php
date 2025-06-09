<?php

namespace App\Services;

use App\CustomHelper;
use App\Models\User;
use App\Repositories\Interfaces\InvoicesInterface;
use App\Repositories\Interfaces\BookingInterface;
use App\Repositories\Interfaces\EmailTemplatesInterface;
use App\Repositories\Interfaces\UserInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoicesService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private InvoicesInterface $invoicesRepository,
        private BookingInterface $bookingRepository,
        private EmailTemplatesInterface $emailTemplateRepository,
        private UserInterface $userRepository,
        private CustomHelper $helper,
        private ActivityLogService $activityLogService,
        private Auth $auth,
    )
    {
    }

    public function generateInvoice(array $requestData = [])
    {
        // Retrieve the logged-in user
        $loggedUser = Auth::user();
        $userTypeSlug = $loggedUser->userType->slug ?? null;

        $bookingId = $requestData['booking_id'];
        $emailTemplateId = $requestData['email_template_id'];

        if(!empty($bookingId) && !empty($emailTemplateId))
        {
            if($userTypeSlug === null ||  $userTypeSlug === 'admin' ||  $userTypeSlug === 'admin-staff')
            {
                if(in_array( $loggedUser->department, [null, 'Management', 'Finance']))
                {
                    // get the information of this booking id
                    $bookingDetails = $this->bookingRepository->getBookingById($bookingId);

                    if(!empty($bookingDetails))
                    {
                        // check if event exists in this booking
                        if(!empty($bookingDetails->event_id))
                        {
                            // check if booking's event really exists under booking's corporate
                            if($bookingDetails->client->hotel_id == $bookingDetails->event->hotel_id)
                            {
                                // check if booking's status is Completed or Cancelled With Charges
                                if($bookingDetails->status == 'COMPLETED' || $bookingDetails->status == 'CANCELLED WITH CHARGES')
                                {                                        
                                    // check if email template is active or not
                                    $emailTemplate = $this->emailTemplateRepository->getEmailTemplateById($emailTemplateId);

                                    if(!empty($emailTemplate) && $emailTemplate->status == 'ACTIVE')
                                    {
                                        $year = date('y', strtotime($bookingDetails->pickup_date));
                                        $month = date('m', strtotime($bookingDetails->pickup_date));
                                        // check if there is already a entry in invoice table for this corporate->year->month->event
                                        $params = [];
                                        $params['hotel_id'] = $bookingDetails->client->hotel_id;
                                        $params['event_id'] = $bookingDetails->event_id;
                                        $params['year'] = $year;
                                        $params['month'] = $month;

                                        $invoiceExists = $this->invoicesRepository->getInvoiceByParams($params);

                                        // if entry exists then only create entry in invoice-bookings table otherwise first create entry in invoices table
                                        if(!empty($invoiceExists))
                                        {
                                            if($invoiceExists->email_template_id !== $emailTemplateId)
                                            {
                                                $invoiceData = [];
                                                $invoiceData['email_template_id'] = $emailTemplateId;
                                                $invoiceData['updated_by_id'] = $loggedUser->id;

                                                $this->invoicesRepository->updateInvoice($invoiceExists, $invoiceData);
                                            }

                                            // check if booking already exists under this invoice
                                            $invoiceBookingExists = $this->invoicesRepository->getInvoiceBookingByBookingId($bookingId);

                                            // if exists then return otherwise create entry
                                            if(!empty($invoiceBookingExists))
                                            {
                                                return [
                                                    'success' => false,
                                                    'message' => 'Invoice has already been generated for this booking.',
                                                    'data' => []
                                                ];
                                            }else{
                                                $invoiceBookingData = [];
                                                $invoiceBookingData['invoice_id'] = $invoiceExists->id;
                                                $invoiceBookingData['booking_id'] = $bookingId;
                                                $invoiceBookingData['created_by_id'] = $loggedUser->id;

                                                $this->invoicesRepository->createInvoiceBooking($invoiceBookingData);

                                                return [
                                                    'success' => true,
                                                    'message' => 'Invoice generated successfully.',
                                                    'data' => [
                                                        'invoice_number' => $invoiceExists->unique_invoice_number
                                                    ]
                                                ];
                                            }

                                        }else{
                                            // check if booking already exists under this invoice
                                            $invoiceBookingExists = $this->invoicesRepository->getInvoiceBookingByBookingId($bookingId);

                                            // if exists then return otherwise create entry
                                            if(!empty($invoiceBookingExists))
                                            {
                                                return [
                                                    'success' => false,
                                                    'message' => 'Invoice has already been generated for this booking.',
                                                    'data' => []
                                                ];
                                            }else{
                                                $year = date('y', strtotime($bookingDetails->pickup_date));
                                                $month = date('m', strtotime($bookingDetails->pickup_date));

                                                $invoiceNumber = $this->generateInvoiceNumber($year);
                                                
                                                $invoiceData = [];
                                                $invoiceData['hotel_id'] = $bookingDetails->client->hotel_id;
                                                $invoiceData['event_id'] = $bookingDetails->event_id;
                                                $invoiceData['email_template_id'] = $emailTemplateId;
                                                $invoiceData['year'] = $year;
                                                $invoiceData['month'] = $month;
                                                $invoiceData['unique_invoice_number'] = $invoiceNumber;
                                                $invoiceData['created_by_id'] = $loggedUser->id;

                                                $generateInvoice = $this->invoicesRepository->generateInvoice($invoiceData);

                                                if($generateInvoice)
                                                {
                                                    $invoiceBookingData = [];
                                                    $invoiceBookingData['invoice_id'] = $invoiceExists->id;
                                                    $invoiceBookingData['booking_id'] = $bookingId;
                                                    $invoiceBookingData['created_by_id'] = $loggedUser->id;
    
                                                    $invoiceBooking = $this->invoicesRepository->createInvoiceBooking($invoiceBookingData);

                                                    if($invoiceBooking)
                                                    {
                                                        return [
                                                            'success' => true,
                                                            'message' => 'Invoice generated successfully.',
                                                            'data' => [
                                                                'invoice_number' => $invoiceExists->unique_invoice_number
                                                            ]
                                                        ];
                                                    }else{
                                                        return [
                                                            'success' => false,
                                                            'message' => 'Unable to submit booking details.',
                                                            'data' => []
                                                        ];
                                                    }
                                                }else{
                                                    return [
                                                        'success' => false,
                                                        'message' => 'Invoice generation failed.',
                                                        'data' => []
                                                    ];
                                                }
                                            }
                                        }
                                    }else{
                                        return [
                                            'success' => false,
                                            'message' => 'Email Template is not active or does not exists.',
                                            'data' => []
                                        ];
                                    }

                                }else{
                                    return [
                                        'success' => false,
                                        'message' => 'Booking status should be either Completed or Cancelled With Charges.',
                                        'data' => []
                                    ];
                                }

                            }else{
                                return [
                                    'success' => false,
                                    'message' => 'Booking Corporate and Event Corporate does not matches.',
                                    'data' => []
                                ];
                            }
                        }else{
                            return [
                                'success' => false,
                                'message' => 'No event assigned to this booking.',
                                'data' => []
                            ];
                        }
                    }else{
                        return [
                            'success' => false,
                            'message' => 'Unable to submit booking details.',
                            'data' => []
                        ];
                    }
                }else{
                    return [
                        'success' => false,
                        'message' => 'Permission denied.',
                        'data' => []
                    ];
                }
            }else{
                return [
                    'success' => false,
                    'message' => 'Permission denied.',
                    'data' => []
                ];
            }
        }else{
            return [
                'success' => false,
                'message' => 'Something went wrong.',
                'data' => []
            ];
        }
        try {
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    public function generateInvoiceNumber($year)
    {
        return $this->invoicesRepository->getNextInvoiceNumberForYear($year);
    }
}
