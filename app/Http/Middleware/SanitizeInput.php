<?php

namespace App\Http\Middleware;

use Closure;
use HTMLPurifier;
use HTMLPurifier_Config;

class SanitizeInput
{
    public function handle($request, Closure $next)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Output.Newline', "\r\n");
        $purifier = new HTMLPurifier($config);
        // Loop through all input data and purify if it's a string
        $input = $request->all();
        array_walk_recursive($input, function (&$value) use ($purifier) {
            if (is_string($value)) {
                $purifiedValue = $purifier->purify($value);
                $decodedValue = html_entity_decode($purifiedValue, ENT_QUOTES, 'UTF-8');
                // If the purified and decoded value is different from the original, set it to null
                if ($decodedValue !== $value) {
                    $value = null;
                } else {
                    $value = $decodedValue;
                }
            }
        });

        // Replace the request input with the purified input
        $request->merge($input);
        return $next($request);
    }
}
