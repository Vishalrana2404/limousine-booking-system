<div class="card-body p-0">
    <div class="table-responsive custom-table table-scroll-height">
        <table id="driverTable" class="table table-head-fixed text-nowrap table-hover m-0">
            <thead>
                <tr>
                    <th style="width: 10px">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input driverTableCheckbox" type="checkbox" id="bulkDriverAction">
                            <label for="bulkDriverAction" class="custom-control-label"></label>
                        </div>
                    </th>
                    <th>Name <i class="fa fa-sort ml-1 theme-color" id="sortName" aria-hidden="true"></i></th>
                    <th>Contact<i class="fa fa-sort ml-1 theme-color" id="sortPhone" aria-hidden="true"></i></th>
                    <th>Default Vehicle<i class="fa fa-sort ml-1 theme-color" id="sortVehicle" aria-hidden="true"></i>
                    </th>
                    <th>Class<i class="fa fa-sort ml-1 theme-color" id="sortClass" aria-hidden="true"></i></th>
                    <th>Race<i class="fa fa-sort ml-1 theme-color" id="sortRace" aria-hidden="true"></i></th>
                    <th>Driver Type<i class="fa fa-sort ml-1 theme-color" id="sortDriverType" aria-hidden="true"></i>
                    </th>
                    <th>Telegram Chat Id</th>
                    <th style="width: 40px">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    use App\CustomHelper;
                @endphp
                @forelse($driverData as $key => $driver)
                    @php
                        $isChecked = $driver->status === 'ACTIVE' ? 'checked' : '';
                    @endphp
                    <tr data-id="{{ $driver->id }}">
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input driverTableCheckbox cellCheckbox" type="checkbox"
                                    id="bulkDriverAction_{{ $driver->id }}">
                                <label for="bulkDriverAction_{{ $driver->id }}" class="custom-control-label"></label>
                            </div>
                        </td>
                        <td class="text-truncate cell-width-200" title="{{ $driver->name ?? 'N/A' }}">
                            {{ $driver->name ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $driver->country_code ? '+' . $driver->country_code : '' }}{{ $driver->phone ?? 'N/A' }}">
                            {{ $driver->country_code ? '+' . $driver->country_code : '' }}{{ $driver->phone ?? 'N/A' }}
                        </td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $driver->vehicle->vehicle_number ?? 'N/A' }}">
                            {{ $driver->vehicle->vehicle_number ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $driver->vehicle->vehicleClass->name ?? 'N/A' }}">
                            {{ $driver->vehicle->vehicleClass->name ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200" title="{{ $driver->race ?? 'N/A' }}">
                            {{ $driver->race ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200" title="{{ $driver->driver_type ?? 'N/A' }}">
                            {{ $driver->driver_type ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200" title="{{ $driver->chat_id ?? 'N/A' }}">
                            {{ $driver->chat_id ?? 'N/A' }}</td>
                        <td>
                            <a class="text-dark mx-1" href="{{ route('edit-driver', ['driver' => $driver->id]) }}"
                                title="Edit"><i class="fas fa-pencil-alt mr-1"></i></a>
                            <button title="Delete"><i data-id="{{ $driver->id }}"
                                    class="fas fa-solid fa-trash text-danger mx-1"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No Record Found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<!-- /.card-body -->
<div class="card-footer clearfix" id="driverPagination">
    {{ $driverData->links('pagination::bootstrap-5') }}
</div>
