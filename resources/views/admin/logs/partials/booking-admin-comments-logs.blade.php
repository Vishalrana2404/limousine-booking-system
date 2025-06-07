@php
    use App\CustomHelper;

    $groupedLogs = $booking->bookings_admin_communication_log->groupBy(function ($log) {
        return \Carbon\Carbon::parse($log->created_at)->toDateString(); // Group by date (Y-m-d)
    });
@endphp

@if ($groupedLogs->isEmpty())
    <div class="no-logs">
        <p>No logs found.</p>
    </div>
@else
    <div class="timeline">
        @foreach ($groupedLogs as $date => $logGroup)
            <div class="time-label">
                <span class="bg-dark">
                    {{ CustomHelper::parseDateTime(CustomHelper::formatSingaporeDate($date), 'd M, Y') }}
                </span>
            </div>

            @foreach ($logGroup as $log)
                <div>
                    <i class="fas fa-user bg-dark"></i>
                    <div class="timeline-item">
                        <div class="timeline-body">
                            <span class="semibold">
                                {{ $log->createdBy->first_name ?? 'N/A' }}
                                {{ $log->createdBy->last_name ?? '' }}
                            </span>
                            {{ $log->comment }} For Booking 
                            <a class="mx-1" href="{{ route('edit-booking', ['booking' => $log->booking_id]) }}"
                                    title="Edit Booking"> #{{ $log->booking_id }}</a>
                            <p class="text-muted small text-right">
                                {{ CustomHelper::parseDateTime(CustomHelper::formatSingaporeDate($log->created_at), 'H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endif
