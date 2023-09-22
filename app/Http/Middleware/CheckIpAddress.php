<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class CheckIpAddress
{
    public function handle($request, Closure $next)
    {
        // Get the user's IP address
        $userIp = $request->ip();

        // Get the list of allowed IP addresses from the configuration
        $allowedIps = Config::get('ip_whitelist.allowed_ips');

        // Check if the user's IP address is in the allowed list
        if (in_array($userIp, $allowedIps)) {
            return $next($request);
        }
        abort(403, 'Unauthorized IP address');
    }
}
