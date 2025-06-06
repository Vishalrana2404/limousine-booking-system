<div class="card-body p-0">
    <div class="table-responsive custom-table">
        <table id="eventsTable" class="table table-head-fixed text-nowrap table-hover m-0">
            <thead>
                <tr>
                    <th style="width: 10px">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input eventTableCheckbox" type="checkbox" id="bulkEventAction">
                            <label for="bulkEventAction" class="custom-control-label"></label>
                        </div>
                    </th>
                    @php
                        $user = Auth::user();
                        $userTypeSlug = $user->userType->slug ?? null;
                    @endphp
                    @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                    @endif
                    <th>Corporate <i class="fa fa-sort ml-1 theme-color" id="sortCorporate" aria-hidden="true"></i></th>
                    <th>Name <i class="fa fa-sort ml-1 theme-color" id="sortName" aria-hidden="true"></i></th>
                    <th>Status<i class="fa fa-sort ml-1 theme-color" id="sortStatus" aria-hidden="true"></i></th>
                    <th style="width: 40px">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eventData as $key => $event)
                    @php
                        $isChecked = $event->status === 'ACTIVE' ? 'checked' : '';
                    @endphp
                    <tr data-id="{{ $event->id }}">
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input eventTableCheckbox cellCheckbox" type="checkbox"
                                    id="bulkEventAction_{{ $event->id }}">
                                <label for="bulkEventAction_{{ $event->id }}"
                                    class="custom-control-label"></label>
                            </div>
                        </td>
                        @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                        @endif
                        <td class="text-truncate cell-width-200" title="{{ $event->hotel->name ?? 'N/A' }}">
                            {{ $event->hotel->name ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200" title="{{ $event->name ?? 'N/A' }}">
                            {{ $event->name ?? 'N/A' }}</td>
                        <td>
                            <div
                                class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input eventStatusToggal"
                                    id="eventStatusToggal_{{ $event->id }}" {{ $isChecked }}>
                                <label class="custom-control-label"
                                    for="eventStatusToggal_{{ $event->id }}"></label>
                            </div>
                        </td>

                        <td>
                            <a class="text-dark mx-1" href="{{ route('edit-event', ['event' => $event->id]) }}"
                                title="Edit"><i class="fas fa-pencil-alt mr-1"></i></a>
                                @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                                    <button title="Delete"><i data-id="{{ $event->id }}"
                                            class="fas fa-solid fa-trash text-danger mr-2 mx-1"></i></button>
                                @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No Record Found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<!-- /.card-body -->
<div class="card-footer clearfix" id="eventPagination">
    {!! $eventData->links('pagination::bootstrap-5') !!}
</div>
