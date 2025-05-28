<div class="card-body p-0">
    <div class="table-responsive custom-table">
        <table id="vehicleClassTable" class="table table-head-fixed text-nowrap table-hover">
            <thead>
                <tr>
                    <th style="width: 10px">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input vehicleClassTableCheckbox" type="checkbox"
                                id="bulkVehicleClassAction">
                            <label for="bulkVehicleClassAction" class="custom-control-label"></label>
                        </div>
                    </th>
                    <th>Class <i class="fa fa-sort ml-1 text-dark" id="sortClass" aria-hidden="true"></i></th>
                    <th>Seating Capacity<i class="fa fa-sort ml-1 text-dark" id="sortSeating" aria-hidden="true"></i>
                    </th>
                    <th>No. of Pax<i class="fa fa-sort ml-1 text-dark" id="sortPax" aria-hidden="true"></i></th>
                    <th>No. of Luggages<i class="fa fa-sort ml-1 text-dark" id="sortLuggages" aria-hidden="true"></i>
                    </th>
                    <th>Status<i class="fa fa-sort ml-1 text-dark" id="sortStatus" aria-hidden="true"></i></th>
                    <th style="width: 40px">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicleClassData as $key => $vehicleClass)
                    @php
                        $isChecked = $vehicleClass->status === 'ACTIVE' ? 'checked' : '';
                    @endphp
                    <tr data-vehicle-class-id="{{ $vehicleClass->id }}">
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input vehicleClassTableCheckbox cellCheckbox"
                                    type="checkbox" id="bulkVehicleClassAction_{{ $vehicleClass->id }}">
                                <label for="bulkVehicleClassAction_{{ $vehicleClass->id }}"
                                    class="custom-control-label"></label>
                            </div>
                        </td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $vehicleClass->name ? $vehicleClass->name : 'N/A' }}">
                            {{ $vehicleClass->name ? $vehicleClass->name : 'N/A' }}</td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $vehicleClass->seating_capacity ? $vehicleClass->seating_capacity : 'N/A' }}">
                            {{ $vehicleClass->seating_capacity ? $vehicleClass->seating_capacity : 'N/A' }}</td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $vehicleClass->total_pax ? $vehicleClass->total_pax : 'N/A' }}">
                            {{ $vehicleClass->total_pax ? $vehicleClass->total_pax : 'N/A' }}</td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $vehicleClass->total_luggage ? $vehicleClass->total_luggage : 'N/A' }}">
                            {{ $vehicleClass->total_luggage ? $vehicleClass->total_luggage : 'N/A' }}</td>

                        <td>
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input vehicleClassStatusToggal"
                                    id="vehicleClassStatusToggal_{{ $vehicleClass->id }}" {{ $isChecked }}>
                                <label class="custom-control-label"
                                    for="vehicleClassStatusToggal_{{ $vehicleClass->id }}"></label>
                            </div>
                        </td>

                        <td>
                            <a class="text-dark mx-1"
                                href="{{ route('edit-vehicle-class', ['vehicleClass' => $vehicleClass->id]) }}"
                                title="Edit"><i class="fas fa-pencil-alt mr-1"></i></a>
                            <button title="Delete"><i data-vehicle-class-id="{{ $vehicleClass->id }}"
                                    class="fas fa-solid fa-trash text-danger mx-1"></i></button>
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
<div class="card-footer clearfix" id="vehicleClassPagination">
    {{ $vehicleClassData->links('pagination::bootstrap-5') }}
</div>
