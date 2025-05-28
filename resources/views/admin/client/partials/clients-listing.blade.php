<div class="card-body p-0">
    <div class="table-responsive custom-table table-scroll-height">
        <table id="clientTable" class="table table-head-fixed text-nowrap table-hover m-0">
            <thead>
                <tr>
                    <th style="width: 10px">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input clientTableCheckbox" type="checkbox" id="bulkUserAction">
                            <label for="bulkUserAction" class="custom-control-label"></label>
                        </div>
                    </th>
                    <th>Corporate<i class="fa fa-sort ml-1 text-dark" id="sortClient" aria-hidden="true"></i></th>
                    <th>Contact<i class="fa fa-sort ml-1 text-dark" id="sortPhone" aria-hidden="true"></i></th>
                    <th>Email<i class="fa fa-sort ml-1 text-dark" id="sortEmail" aria-hidden="true"></i></th>
                    <th>Contact Person<i class="fa fa-sort ml-1 text-dark" id="sortContactPerson"
                            aria-hidden="true"></i></th>
                    <th>Invoice<i class="fa fa-sort ml-1 text-dark" id="sortInvoice" aria-hidden="true"></i></th>
                    <th>Client Type<i class="fa fa-sort ml-1 text-dark" id="sortClientType" aria-hidden="true"></i></th>
                    <th>Status<i class="fa fa-sort ml-1 text-dark" id="sortStatus" aria-hidden="true"></i></th>
                    <th style="width: 40px">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    use App\CustomHelper;
                @endphp
                @forelse($clientData as $key => $client)
                    @php
                        $isChecked = $client->status === 'ACTIVE' ? 'checked' : '';
                        $fullName = CustomHelper::getFullName($client->user->first_name, $client->user->last_name);
                    @endphp
                    <tr data-id="{{ $client->id }}">
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input clientTableCheckbox cellCheckbox" type="checkbox"
                                    id="bulkUserAction_{{ $client->id }}">
                                <label for="bulkUserAction_{{ $client->id }}" class="custom-control-label"></label>
                            </div>
                        </td>
                        <td class="text-truncate cell-width-200" title="{{ $client->hotel->name ?? 'N/A' }}">
                            {{ $client->hotel->name ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200"
                            title="{{ $client->user->country_code ? '+' . $client->user->country_code : '' }}{{ $client->user->phone ?? 'N/A' }}
">
                            {{ $client->user->country_code ? '+' . $client->user->country_code : '' }}{{ $client->user->phone ?? 'N/A' }}
                        </td>
                        <td class="text-truncate cell-width-200" title="{{ $client->user->email ?? 'N/A' }}">
                            {{ $client->user->email ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200" title="{{ $fullName ?? 'N/A' }}">
                            {{ $fullName ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200" title="{{ $client->invoice ?? 'N/A' }}">
                            {{ $client->invoice ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200" title="{{ $client->user->userType->name ?? 'N/A' }}">
                            {{ $client->user->userType->name ?? 'N/A' }}</td>
                        <td>
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input clientStatusToggal"
                                    id="clientStatusToggal_{{ $client->id }}" {{ $isChecked }}>
                                <label class="custom-control-label"
                                    for="clientStatusToggal_{{ $client->id }}"></label>
                            </div>
                        </td>
                        <td>
                            <a class="text-dark mx-1" href="{{ route('edit-client', ['client' => $client->id]) }}"
                                title="Edit"><i class="fas fa-pencil-alt mr-1"></i></a>
                            @if ($client->user->id !== Auth::user()->id)
                                <button title="Delete"><i data-id="{{ $client->id }}"
                                        class="fas fa-solid fa-trash text-danger mx-1"></i></button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">No Record Found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="card-footer clearfix" id="clientPagination">
    {{ $clientData->links('pagination::bootstrap-5') }}
</div>
