<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\AuthCode;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Hash;

class AuthController extends Controller
{
	use AuthCode;
	public function register(Request $request) {
		$validator = Validator::make($request->all(),[
            'name' => 'required|regex:/^[a-zA-Z ]*$/',
			'username' => 'required|regex:/^[a-zA-Z ]*$/',
			'email' => 'required|email|unique_field:User',
            'mobile' => 'required|digits_between:10,12|unique_field:User',
			'password' => 'required|min:6|max:12|confirmed',
            'password_confirmation' => 'required|min:6|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Bad request',
                'response' => [],
                'validation_error_responce' => $validator->errors(),
                'code' => 404,
            ]);
        }
		$input = $request->all();
        $user = User::create([
            'name' => $input['name'],
			'username' => $input['username'],
            'email' => $input['email'],
			'mobile'=> $input['mobile'],
            'password' => Hash::make($input['password']),
			'is_active'=>1,
			'is_admin'=>0
        ]);
		if($user) {
			return response()->json([
				'code' => 200,
				'status' => 'user register successfully',
				'response' => []
			]);
		} else {
			return response()->json([
				'code' => 500,
				'status' => 'something went wrong',
				'response' => []
			]);
		}
	}
	public function login(Request $request) {
		$validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Bad request',
                'response' => [],
                'validation_error_responce' => $validator->errors(),
                'code' => 404,
            ]);
        }
		$input = $request->all();
        $user = User::where('email',$input['email'])->first();
		if(!empty($user)) {
			if(!$user->is_active){  
				return response()->json([
					'user_is_active' => 0,
					'code' => 406,
					'status' => 'Your account is not actived',
					'response' => [],
				]);
			}
		}
		$loginUser = $this->loginUser($input['email'],$input['password'],0);
		if($loginUser){
			$userData = $loginUser->only(['id', 'name','username', 'email', 'mobile','is_active','is_admin']);
			$tokenResult = $this->generateAppToken($loginUser);
			return response()->json([
				'code' => 200,
				'status' => 'Ok',
				'response' => $userData,
				'token' => $tokenResult,
			]);
		} else {
			return response()->json([
				'code' => 401,
				'status' => 'Invalid email or password',
				'response' => []
			]);
		}
	}
}
