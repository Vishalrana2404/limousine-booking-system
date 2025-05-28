<?php

namespace App\Repositories\Interfaces;

use App\Models\OutsideCitySurcharge;

/**
 * InterfaceCitySurchargeInterface
 * 
 * @package App\Repositories\Interfaces
 */
interface CitySurchargeInterface
{
    /**
     * Add a new city surcharge using the provided data.
     *
     * @param array $data The data for the new city surcharge.
     * @return OutsideCitySurcharge The newly created city surcharge instance.
     */
    public function addCitySurcharge(array $data): OutsideCitySurcharge;

    /**
     * Retrieve all saved city surcharges from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of saved city surcharges.
     */
    public function getSavedCitiesSurcharges();
    public function updateCordinates(OutsideCitySurcharge $outsideCitySurcharge, array $data): bool;
    public function getCordinatesById(int $id): ?OutsideCitySurcharge;
    public function delete(int $id): bool;
}
