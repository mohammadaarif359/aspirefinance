<?php namespace App\Traits;

use Log;
use Auth;
use App\Models\User;
use Carbon\Carbon;
trait AuthCode
{
    public function getUserRole($user_id){
        $user=User::with('roles')->where('id',$user_id)->first();
        $roles = $user->roles;
        $arr = [];
        foreach($roles as $row){
            array_push($arr,$row['name']);
        }
        return $arr;
    }
    public function loginUser($username,$password,$loginType){
        $authAttemptArr=[];
        if($loginType == 0){
            $authAttemptArr = [
                "email"=>$username,
            ];
        }else if($loginType == 1){
            $authAttemptArr = [
                "username"=>$username,
            ];
        }else if($loginType == 2){
            $authAttemptArr = [
                "mobile"=>$username,
            ];
        }
        $authAttemptArr['password'] = $password;
        if(Auth::attempt($authAttemptArr)){
            $user = Auth::user();
            return $user;
        }else{
            return false;
        }
    }

    public function generateAppToken($user,$device_id='',$remember_me=false){
        $t = $user->id."_".date('YmdHis');
		$tokenResult = $user->createToken('PersonalAccessToken_'.$t)->accessToken;

        return $tokenResult;
    }
	public function generateUniqueForgotToken(){
        do{
            $str = md5(uniqid(rand(), true));    
        }while(User::where('forgotpassword_token', '=', $str)->count() > 0);
        return $str;
    }
	public function checkForgotTokenTime($datetime){
		$mytime = Carbon::now();
		$diff_in_hour = $mytime->diffInHours($datetime);
		if ($diff_in_hour > 24) {
            $response['msg'] = 'Reset password link is expired';
            $response['code'] = 400;
        } else {
			$response['msg'] = '';
            $response['code'] = 200;
		}
		return $response;
    
	}
}
?>