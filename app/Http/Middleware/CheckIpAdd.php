<?php

namespace App\Http\Middleware;
use Closure;
use Carbon\Carbon;
use App\Models\IpAdd;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Jenssegers\Agent\Agent;

class CheckIpAdd
{
    public function handle($request, Closure $next){
        // return $next($request);
        $exceptRoutes = ['login', 'register'];
        $userIp = $request->ip();
        $checkIp = IpAdd::where('ipaddress', $userIp)->first();
        $agent = new Agent();
        if ($request->route()) {
            if (!$checkIp) {
                IpAdd::Create([
                    'ipaddress' => $userIp,
                    'device' => $agent->device(),
                    'browser' =>$agent->browser(),
                    'platform' => $agent->platform(),
                    'name' => 'visitor'
                ]);
            }
            else{
                if (!Auth::check()) {
                    // dd('test');
                    $checkIp->update([
                        'ipaddress' => $userIp,
                        'updated_at' => Carbon::now(),
                        'device' => $agent->device(),
                        'browser' =>$agent->browser(),
                        'platform' => $agent->platform(),
                        'name' => 'visitor'
                    ]);
                }
                else {
                    $checkIp->update([
                        'ipaddress' => $userIp,
                        'updated_at' => Carbon::now(),
                        'name' => Auth::user()->firstname.' '.Auth::user()->lastname,
                        'device' => $agent->device(),
                        'platform' => $agent->platform(),
                        'browser' =>$agent->browser()
                    ]);
                }

            }
        }

        // $allowedIps = Config::get('ip_whitelist.allowed_ips');
        // if(in_array($userIp, $allowedIps)){
        //     return $next($request);
        // }
        return $next($request);
    }
}
