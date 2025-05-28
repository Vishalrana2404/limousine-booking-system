<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Export implements FromArray, WithHeadings
{

    /**
     * The data to be exported.
     *
     * @var array
     */
    protected $data;

    /**
     * The columns for the export file.
     *
     * @var array
     */
    protected $columns;



    /**
     * Create a new export instance.
     *
     * @param array $data
     * @param array $columns
     * @return void
     */
    public function __construct($data, $columns)
    {
        $this->data = $data;
        $this->columns = $columns;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return $this->columns;
    }
}
