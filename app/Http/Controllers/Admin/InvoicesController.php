<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoices;
use App\Services\InvoicesService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Auth;

class InvoicesController extends Controller
{
    /**
     * Initialize controller dependencies.
     */
    public function __construct(
        private InvoicesService $invoicesService,
        private CustomHelper $helper
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $data = [];
        $data['booking_id'] = $request->booking_id;
        $data['email_template_id'] = $request->email_template_id;

        $generateInvoice = $this->invoicesService->generateInvoice($data);
        return $generateInvoice;
        if ($generateInvoice['success']) {
            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => $generateInvoice['message']
                ],
                'data' => $generateInvoice['data'] ?? []
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => [
                'code' => 400,
                'message' => $generateInvoice['message']
            ],
            'data' => []
        ], Response::HTTP_BAD_REQUEST);
        try {


        } catch (\Exception $e) {
            return response()->json([
                'status' => [
                    'code' => 500,
                    'message' => $e->getMessage()
                ],
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
