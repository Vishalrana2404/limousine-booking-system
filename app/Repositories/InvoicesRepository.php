<?php

namespace App\Repositories;

use App\Models\Invoices;
use App\Models\InvoiceBookings;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Events;
use App\Models\EmailTemplates;
use App\Models\User;
use App\Repositories\Interfaces\InvoicesInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoicesRepository implements InvoicesInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected Invoices $model,
        protected InvoiceBookings $invoiceBookingsModel,
        protected Booking $bookingsModel,
        protected Hotel $hotelModel,
        protected Events $eventsModel,
        protected EmailTemplates $emailTemplatesModel,
        protected User $usersModel,
    )
    {
    }

    public function generateInvoice(array $data): Invoices
    {
        return $this->model->create($data);
    }

    public function createInvoiceBooking(array $data): InvoiceBookings
    {
        return $this->invoiceBookingsModel->create($data);
    }

    public function getNextInvoiceNumberForYear($year)
    {
        // Get the latest number for that year
        $lastInvoice = $this->model::where('year', $year)->orderByDesc('id')->first();

        // Extract last number
        if ($lastInvoice && preg_match('/E1\/' . $year . '\/(\d+)/', $lastInvoice->unique_invoice_number, $matches)) {
            $lastNumber = (int)$matches[1];
        } else {
            $lastNumber = 0;
        }

        // Increment and format
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        return "E1/{$year}/{$newNumber}";
    }

    public function getInvoiceByParams(array $params): ?Invoices
    {
        return $this->model->where($params)->first();
    }

    public function getInvoiceBookingByBookingId(int $bookingId): ?InvoiceBookings
    {
        return $this->model->where('booking_id', $bookingId)->first();
    }

    public function updateInvoice(Invoices $invoice, array $data): bool
    {
        return $invoice->update($data);
    }
}
