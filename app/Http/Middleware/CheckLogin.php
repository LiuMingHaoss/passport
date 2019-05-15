<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class CheckLogin
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
        $token=$request->input('token');
        $uid=$request->input('uid');

        //验证非空
        if(empty($token) || empty($uid)){
            $response=[
              'erron'=>50010,
              'msg'=>'参数不全',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $key='token:uid:'.$uid;
        $local_token=Redis::get($key);
        if($local_token){
            if($local_token!=$token){
                $response=[
                    'erron'=>50030,
                    'msg'=>'无效的token',
                ];
                die(json_encode($response,JSON_UNESCAPED_UNICODE));
            }else{
                $str=json_encode($_GET);
                //记录日志
                $log_str = date('Y-m-d H:i:s') . "\n" . $str . "\n";
                file_put_contents('logs/token.log',$log_str,FILE_APPEND);
            }
        }else{
            $response=[
                'erron'=>50020,
                'msg'=>'用户未授权',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        return $next($request);
    }
}
