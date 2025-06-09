<?php

namespace App\Repositories\Interfaces;

use App\Models\Invoices;
use App\Models\InvoiceBookings;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface InvoicesInterface
{
    public function generateInvoice(array $data): Invoices;

    public function createInvoiceBooking(array $data): InvoiceBookings;

    public function getInvoiceByParams(array $data): ?Invoices;

    public function getInvoiceBookingByBookingId(int $bookingId): ?InvoiceBookings;

    public function updateInvoice(Invoices $invoice, array $data): bool;
}
