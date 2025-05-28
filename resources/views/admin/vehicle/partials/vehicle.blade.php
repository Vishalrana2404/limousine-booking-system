<div class="card-body p-0">
    <div class="table-responsive custom-table table-scroll-height">
        <table id="vehicleTable" class="table table-head-fixed text-nowrap table-hover m-0">
            <thead>
                <tr>
                    <th style="width: 10px">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input vehicleTableCheckbox" type="checkbox"
                                id="bulkVehicleAction">
                            <label for="bulkVehicleAction" class="custom-control-label"></label>
                        </div>
                    </th>
                    <th>Vehicle Class<i class="fa fa-sort ml-1 text-dark" id="sortClass" aria-hidden="true"></i></th>
                    <th>Vehicle Number<i class="fa fa-sort ml-1 text-dark" id="sortNumber" aria-hidden="true"></i></th>
                    <th>Brand<i class="fa fa-sort ml-1 text-dark" id="sortBrand" aria-hidden="true"></i></th>
                    <th>Model<i class="fa fa-sort ml-1 text-dark" id="sortModel" aria-hidden="true"></i></th>
                    <th>Status<i class="fa fa-sort ml-1 text-dark" id="sortStatus" aria-hidden="true"></i></th>
                    <th style="width: 40px">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicleData as $key => $vehicle)
                    @php
                        $isChecked = $vehicle->status === 'ACTIVE' ? 'checked' : '';
                    @endphp
                    <tr data-vehicle-id="{{ $vehicle->id }}">
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input vehicleTableCheckbox cellCheckbox" type="checkbox"
                                    id="bulkVehicleAction_{{ $vehicle->id }}">
                                <label for="bulkVehicleAction_{{ $vehicle->id }}" class="custom-control-label"></label>
                            </div>
                        </td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $vehicle->vehicleClass->name ? $vehicle->vehicleClass->name : 'N/A' }}">
                            {{ $vehicle->vehicleClass->name ? $vehicle->vehicleClass->name : 'N/A' }}</td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $vehicle->vehicle_number ? $vehicle->vehicle_number : 'N/A' }}">
                            {{ $vehicle->vehicle_number ? $vehicle->vehicle_number : 'N/A' }}</td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $vehicle->brand ? $vehicle->brand : 'N/A' }}">
                            {{ $vehicle->brand ? $vehicle->brand : 'N/A' }}</td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $vehicle->model ? $vehicle->model : 'N/A' }}">
                            {{ $vehicle->model ? $vehicle->model : 'N/A' }}</td>

                        <td>
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input vehicleStatusToggal"
                                    id="vehicleStatusToggal_{{ $vehicle->id }}" {{ $isChecked }}>
                                <label class="custom-control-label"
                                    for="vehicleStatusToggal_{{ $vehicle->id }}"></label>
                            </div>
                        </td>

                        <td>
                            <a class="text-dark mx-1" href="{{ route('edit-vehicle', ['vehicle' => $vehicle->id]) }}"
                                title="Edit"><i class="fas fa-pencil-alt mr-1"></i></a>
                            <button title="Delete"><i data-vehicle-id="{{ $vehicle->id }}"
                                    class="fas fa-solid fa-trash text-danger mr-2 mx-1"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No Record Found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<!-- /.card-body -->
<div class="card-footer clearfix" id="vehiclePagination">
    {{ $vehicleData->links('pagination::bootstrap-5') }}
</div>
