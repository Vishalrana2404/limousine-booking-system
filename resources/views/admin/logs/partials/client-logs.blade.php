@php
    use App\CustomHelper;
@endphp
@if ($clientLinkageLogs->isEmpty())
    <div class="no-logs">
        <p>No logs found.</p>
    </div>
@else
    <div class="timeline">
        @foreach ($clientLinkageLogs as $date => $logGroup)
            <div class="time-label">
                <span
                    class="bg-dark">{{ App\CustomHelper::parseDateTime(App\CustomHelper::formatSingaporeDate($date), 'd M, Y') }}</span>
            </div>
            @foreach ($logGroup as $log)
                <div>
                    <i class="fas fa-user bg-dark"></i>
                    <div class="timeline-item">
                        <div class="timeline-body">
                            <span class="semibold">{{ $log->user->first_name ?? null }} {{ $log->user->last_name ?? null }}</span> 
                            {{ $log->message }} client <span class="semibold">{{ $log->client->user->first_name ?? null }} {{ $log->client->user->last_name ?? null }}</span> {{ $log->log_type }} Hotel <span class="semibold">{{ $log->hotel->name }}</span>
                            <p class="text-muted small text-right">
                            {{ App\CustomHelper::parseDateTime(App\CustomHelper::formatSingaporeDate($log->created_at), 'H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endif
