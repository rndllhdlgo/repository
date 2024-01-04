<?php

namespace App\Http\Middleware;
use App\Models\Ipaddress;
use Closure;
use Illuminate\Support\Facades\Config;

class CheckIpAddress
{
    public function handle($request, Closure $next){
        if(env('APP_MAINTENANCE') == 'true'){
            abort(503, 'THIS SITE IS UNDER MAINTENANCE');
        }

        $userIp = $request->ip();
        $checkIp = Ipaddress::where('ipaddress', $userIp)->first();
        if (!$checkIp) {
            Ipaddress::Create([
                'ipaddress' => $userIp,
                'repository' => now()
            ]);
        }
        else{
            $checkIp->update([
                'ipaddress' => $userIp,
                'repository' => now()
            ]);
        }

        $allowedIps = Config::get('ip_whitelist.allowed_ips');
        if(in_array($userIp, $allowedIps)){
            return $next($request);
        }
        abort(403, 'Unauthorized IP address');
    }
}
