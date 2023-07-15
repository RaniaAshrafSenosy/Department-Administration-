<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
//use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class LoginController extends Controller
{

    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'main_email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $credentials = $request->only('main_email', 'password');

    $user = User::where('main_email', $credentials['main_email'])->first();

    // if(!$user){
    //     return response()->json(['error' => 'invalid email'], 404);
    // }

    if ($user && $user->is_archived) {
        return response()->json(['error' => 'archived'], 404);
    }

    if($user && !$user->is_archived){
        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Password is not correct'], 200);
        }
        $attachmentUrl = asset('/media/' . $user->image);

        $token['token']=auth()->claims([
            'user_id'=>$user->user_id,
            'user_imagPath'=>$attachmentUrl,
            'full_name'=>$user->full_name,
            'dept_code'=>$user->dept_code,
            'privileged_user'=>$user->privileged_user
        ])->attempt($credentials);

        $token = JWTAuth::fromUser($user);
    }

    if (!$user) {
        $admin = Admin::where('email', $credentials['main_email'])->first();

        if ($admin) {
            if (!Hash::check($credentials['password'], $admin->password)) {
                return response()->json(['message' => 'Password is not correct'], 200);
            }
            $token['token']=auth()->claims([
                'user_id'=>$admin->id,
                'is_superAdmin'=>$admin->is_superAdmin,
                'full_name'=>$admin->name,
               // 'main_email'=>$user->main_email
            ])->attempt($credentials);

        $token = JWTAuth::fromUser($admin);
            return response()->json([
                'message' => 'Admin login successful.',
                'state' => 'success',
                'role' => 'Admin',
                'token' => $token
            ]);
        }
    }

    return response()->json([
        'message' => 'User login successful.',
        'state' => 'success',
        'role' => $user->role,
        'token' => $token
    ]);
}


public function logout() {
    auth()->logout();
    return response()->json(['status' => 'success', 'message' => 'User logged out successfully']);
}

public function refresh(Request $request)
{
    try {
        $token = JWTAuth::parseToken()->refresh();
    } catch (TokenExpiredException $e) {
        return response()->json(['error' => 'Token has expired'], 401);
    } catch (JWTException $e) {
        return response()->json(['error' => 'Token could not be refreshed'], 500);
    }

    return response()->json([
        'message' => 'Token refreshed.',
        'state' => 'success',
        'token' => $token
    ]);
}


}
