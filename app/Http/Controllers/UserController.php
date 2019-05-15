<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\UserModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
  public function userReg(){
      $json_str=file_get_contents('php://input');
      $data=json_decode($json_str,true);

      $email=DB::table('api_user')->where('email',$data['email'])->first();
      if($email){
          $response=[
              'errno'=>50001,
              'msg'=>'邮箱已注册',
          ];
          die(json_encode($response,JSON_UNESCAPED_UNICODE));
      }
      $info=[
          'username'=>$data['username'],
          'email'=>$data['email'],
          'pwd'=>$data['pwd']
      ];
      $res=DB::table('api_user')->insert($info);
      if($res){
          $response=[
              'errno'=>0,
              'msg'=>'注册成功',
          ];
          die(json_encode($response,JSON_UNESCAPED_UNICODE));

      }else{
          $response=[
              'errno'=>40001,
              'msg'=>'注册失败',
          ];
          die(json_encode($response,JSON_UNESCAPED_UNICODE));

      }
  }

  public function userLogin(){
      $json_str=file_get_contents('php://input');
      $data=json_decode($json_str,true);
      $arr=DB::table('api_user')->where('email',$data['email'])->first();
      if($arr){
          if($arr->pwd===$data['pwd']){

              $key='token:uid:'.$arr->id;
              $token=Redis::get($key);
              if(!$token){
                  $token=Str::random(8);
                  Redis::set($key,$token);
                  Redis::expire($key,604800);
              }
              $response=[
                  'errno'=>0,
                  'msg'=>'登录成功',
                  'token'=>$token,
                  'uid'=>$arr->id
              ];
              die(json_encode($response,JSON_UNESCAPED_UNICODE));
          }else{
              $response=[
                  'errno'=>50003,
                  'msg'=>'密码错误',
              ];
              die(json_encode($response,JSON_UNESCAPED_UNICODE));
          }
      }else{
          $response=[
              'errno'=>50002,
              'msg'=>'邮箱不存在',
          ];
          die(json_encode($response,JSON_UNESCAPED_UNICODE));
      }
  }
}
