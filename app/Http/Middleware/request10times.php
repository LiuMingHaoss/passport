<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class request10times
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        echo date('Y-m-d H:i:s');echo '<hr>';
        $ip=$_SERVER['REMOTE_ADDR'];
        $token=$request->input('token');
        $key=$ip.'request10times'.$token;
        $c=Redis::get($key);

        if($c>=10){
            die('超出次数限制');
        }
        echo '次数：'.$c;echo '<hr>';
        Redis::incr($key);
        Redis::expire($key,10);
        return $next($request);
    }
}
