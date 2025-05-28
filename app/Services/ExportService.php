<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\Export;

/**
 * This class provides export functionality for PDF and Excel files.
 */
class ExportService
{

    /**
     * The path to the storage directory.
     *
     * This variable holds the path to the storage directory where files are stored or retrieved from.
     *
     * @var string|null
     */
    protected $storagePath = null;

    /**
     * Sets the storage path.
     *
     * This method sets the path to the storage directory where files will be stored or retrieved from.
     *
     * @param string $path The path to the storage directory.
     * @return void
     */
    public function setPath($path)
    {
        $this->storagePath = $path;
    }

    /**
     * Export data to PDF format.
     *
     * @param string $view The name of the Blade view file to render.
     * @param array $data The data to pass to the view.
     * @param string $fileName The name of the exported file.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse The PDF download response.
     */
    public function exportToPDF($view, $data)
    {
        try {

            $pdf = PDF::loadView($view, $data);
            return  Storage::disk('public')->put($this->storagePath, $pdf->output());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Export data to Excel format.
     *
     * @param array $data The data to export to Excel.
     * @param array $columns The column headers for the Excel file.
     * @param string $fileName The name of the exported Excel file.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse The Excel download response.
     */
    public function exportToExcel($data, $columns, $fileName)
    {
        try {
            $export = new Export($data, $columns);
            $excelFile = Excel::download($export, $fileName)->getFile(); 
            return Storage::disk('public')->put($this->storagePath . '/' . $fileName, file_get_contents($excelFile));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
