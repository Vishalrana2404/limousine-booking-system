<?php

namespace App\Repositories;

use App\Models\OutsideCitySurcharge;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\CitySurchargeInterface;

/**
 * Class CitySurchargeRepository
 * 
 * @package App\Repositories
 */
class CitySurchargeRepository implements CitySurchargeInterface
{
    /**
     * Constructor for the OutsideCitySurchargeService class.
     *
     * @param OutsideCitySurcharge $model The OutsideCitySurcharge model instance.
     */
    public function __construct(
        protected OutsideCitySurcharge $model
    ) {
    }

    /**
     * Add a new city surcharge using the provided data.
     *
     * @param array $data The data for the new city surcharge.
     * @return OutsideCitySurcharge The newly created city surcharge instance.
     */
    public function addCitySurcharge(array $data): OutsideCitySurcharge
    {
        return $this->model->create($data);
    }

    /**
     * Retrieve all saved city surcharges from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of saved city surcharges.
     */
    public function getSavedCitiesSurcharges(): Collection
    {
        return  $this->model->get();
    }
    public function updateCordinates(OutsideCitySurcharge $outsideCitySurcharge, array $data): bool
    {
        return $outsideCitySurcharge->update($data);
    }
    public function  getCordinatesById(int $id): ?OutsideCitySurcharge
    {
        return $this->model->find($id);
    }
    public function delete(int $id): bool
    {
        return $this->model->where('id',$id)->delete();
    }
}
