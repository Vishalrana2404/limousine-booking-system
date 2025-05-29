@php
    use App\CustomHelper;
@endphp
@if ($hotelLinkageLogs->isEmpty())
    <div class="no-logs">
        <p>No logs found.</p>
    </div>
@else
    <div class="timeline">
        @foreach ($hotelLinkageLogs as $date => $logGroup)
            <div class="time-label">
                <span
                    class="bg-dark">{{ App\CustomHelper::parseDateTime(App\CustomHelper::formatSingaporeDate($date), 'd M, Y') }}</span>
            </div>
            @foreach ($logGroup as $log)
                <div>
                    <i class="fas fa-user bg-dark"></i>
                    <div class="timeline-item">
                        <div class="timeline-body">
                            @if ($log->message === 'as Head Office')
                                <span class="semibold">{{ $log->user->first_name ?? null }} {{ $log->user->last_name ?? null }}</span> 
                                Assigned Hotel <span class="semibold">{{ $log->hotel->name }}</span> {{ $log->message }}
                            @elseif($log->message === 'from Head Office' && ($log->head_office_id !== '' && $log->head_office_id !== NULL))
                                <span class="semibold">{{ $log->user->first_name ?? null }} {{ $log->user->last_name ?? null }}</span> 
                                UnLinked Hotel <span class="semibold">{{ $log->hotel->name }}</span> {{ $log->message }} <span class="semibold">{{ $log->headOffice->name }}</span>                             
                            @elseif($log->message === 'from Head Office')
                                <span class="semibold">{{ $log->user->first_name ?? null }} {{ $log->user->last_name ?? null }}</span> 
                                UnAssigned Hotel <span class="semibold">{{ $log->hotel->name }}</span> {{ $log->message }}                                
                            @elseif($log->message === 'to Head Office')
                                <span class="semibold">{{ $log->user->first_name ?? null }} {{ $log->user->last_name ?? null }}</span> 
                                Linked Hotel <span class="semibold">{{ $log->hotel->name }}</span> {{ $log->message }} <span class="semibold">{{ $log->headOffice->name }} </span>                               
                            @elseif($log->message === 'as POC')
                                <span class="semibold">{{ $log->user->first_name ?? null }} {{ $log->user->last_name ?? null }}</span> 
                                Assigned Client <span class="semibold">{{ $log->client->user->first_name ?? null }} {{ $log->client->user->last_name ?? null }}</span> {{ $log->message }} of Hotel <span class="semibold">{{ $log->hotel->name }}</span>                              
                            @else
                                <span class="semibold">{{ $log->user->first_name ?? null }} {{ $log->user->last_name ?? null }}</span> 
                                UnAssigned Client <span class="semibold">{{ $log->client->user->first_name ?? null }} {{ $log->client->user->last_name ?? null }}</span> {{ $log->message }} of Hotel <span class="semibold">{{ $log->hotel->name }}</span>                               
                            @endif
                            <p class="text-muted small text-right">
                            {{ App\CustomHelper::parseDateTime(App\CustomHelper::formatSingaporeDate($log->created_at), 'H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endif
