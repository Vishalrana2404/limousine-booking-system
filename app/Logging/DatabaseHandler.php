<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use App\Models\Log;
use Auth;

class DatabaseHandler extends AbstractProcessingHandler
{
    /**
     * @inheritDoc
     */
    protected function write($record): void
    {
        Log::create([
            'level' => $record['level'],
            'type' => 'Unhandled Exception',
            'user_id' => Auth::check() ? Auth::id() : 'guest',
            'environment' => env('APP_ENV', 'local'),
            'status' => true,
            'level_name' => $record['level_name'],
            'message' => $record['message'],
            'context' => json_encode($record['context']),
            'extra' => json_encode($record['extra']),
        ]);
    }
}
