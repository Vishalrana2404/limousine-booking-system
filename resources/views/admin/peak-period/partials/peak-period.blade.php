@forelse($peakPeriodData as $key => $peakPeriod)
@php
$isChecked = $peakPeriod->status === "ACTIVE" ? "checked" : "";
@endphp
<tr data-peak-period-id="{{$peakPeriod->id}}">
    <td>
        <div class="custom-control custom-checkbox">
            <input class="custom-control-input peakPeriodTableCheckbox cellCheckbox" type="checkbox" id="bulkPeakPeriodAction_{{$peakPeriod->id}}">
            <label for="bulkPeakPeriodAction_{{$peakPeriod->id}}" class="custom-control-label"></label>
        </div>
    </td>
    <td class="text-truncate cell-width-200" title="{{ $peakPeriod->event ? $peakPeriod->event : 'N/A' }}">{{ $peakPeriod->event ? $peakPeriod->event : 'N/A' }}</td>
    <td class="text-truncate cell-width-200" title="{{ $peakPeriod->start_date ? $peakPeriod->start_date: 'N/A' }}">{{ $peakPeriod->start_date ? $peakPeriod->start_date: 'N/A' }}</td>
    <td class="text-truncate cell-width-200" title="{{ $peakPeriod->end_date ? $peakPeriod->end_date :'N/A' }}">{{ $peakPeriod->end_date ? $peakPeriod->end_date :'N/A' }}</td>

    <td>
        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
            <input type="checkbox" class="custom-control-input peakPeriodStatusToggal" id="peakPeriodStatusToggal_{{$peakPeriod->id}}" {{$isChecked}}>
            <label class="custom-control-label" for="peakPeriodStatusToggal_{{$peakPeriod->id}}"></label>
        </div>
    </td>
    
    <td>
        <a class="text-dark mx-1" href="{{ route('edit-peak-period', ['peakPeriod' => $peakPeriod->id]) }}" title="Edit"><i class="fas fa-pencil-alt mr-1"></i></a>
        <button title="Delete"><i  data-peak-period-id="{{$peakPeriod->id}}" class="fas fa-solid fa-trash text-danger mx-1"></i></button>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="text-center">No Record Found.</td>
</tr>
@endforelse