@php
    use App\CustomHelper;
@endphp
@if ($logs->isEmpty())
    <div class="no-logs">
        <p>No logs found.</p>
    </div>
@else
    <div class="timeline">
        @foreach ($logs as $date => $logGroup)
            @php
                $filteredLogGroup = collect($logGroup);

                if (!empty(Auth::user()->userType) && Auth::user()->userType->type === 'client') {
                    $filteredLogGroup = $filteredLogGroup->filter(function ($log) {
                        return !str_contains($log->message, 'Admin added a comment');
                    });
                }
            @endphp

            @if ($filteredLogGroup->isNotEmpty())
                <div class="time-label">
                    <span class="bg-dark">
                        {{ App\CustomHelper::parseDateTime(App\CustomHelper::formatSingaporeDate($date), 'd M, Y') }}
                    </span>
                </div>

                @foreach ($filteredLogGroup as $log)
                    <div>
                        <i class="fas fa-user bg-dark"></i>
                        <div class="timeline-item">
                            <div class="timeline-body">
                                @if ($log->message === 'Deleted')
                                    <span class="semibold">{{ $log->user->first_name ?? null }} {{ $log->user->last_name ?? null }}</span>
                                    {{ $log->message }} Booking #{{ $log->booking_id }}
                                @elseif($log->message === 'Created')
                                    <span class="semibold">{{ $log->user->first_name ?? null }} {{ $log->user->last_name ?? null }}</span>
                                    {{ $log->message }} Booking
                                    <a class="mx-1" href="{{ route('edit-booking', ['booking' => $log->booking_id]) }}" title="Edit Booking">
                                        #{{ $log->booking_id }}
                                    </a>
                                @else
                                    <span class="semibold">{{ $log->user->first_name ?? null }} {{ $log->user->last_name ?? null }}</span>
                                    {{
                                        (str_contains($log->message, 'from 00:00') &&
                                        (!empty($log->booking->pickup_time) && $log->booking->pickup_time != '00:00:00'))
                                        ? str_replace('from 00:00', 'from To Be Adviced', $log->message)
                                        : $log->message
                                    }}
                                    For Booking
                                    <a class="mx-1" href="{{ route('edit-booking', ['booking' => $log->booking_id]) }}" title="Edit Booking">
                                        #{{ $log->booking_id }}
                                    </a>
                                @endif

                                <p class="text-muted small text-right">
                                    {{ App\CustomHelper::parseDateTime(App\CustomHelper::formatSingaporeDate($log->created_at), 'H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach
    </div>
@endif
