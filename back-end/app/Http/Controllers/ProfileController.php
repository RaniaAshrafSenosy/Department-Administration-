<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use App\Rules\StrongPassword;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Trait\UploadMediaTrait;

class ProfileController extends Controller
{
    use UploadMediaTrait;
    public function show(Request $request)
    {
        $user = User::find($request->id);
        if ($user->is_archived == true) {
            return response()->json(['error' => 'This user is archived'], 404);
        }

        if (!$user) {
            return response()->json(['error' => 'user not found'], 404);
        }

        $imageUrl = asset('/media/' . $user->image);

        if ($request->has('time_range')) {
            $timeRange = $request->input('time_range');
            foreach ($timeRange as &$range) {
                if (isset($range['day_time'])) {
                    $range['day_time'] = ucfirst(strtolower($range['day_time']));
                }
            }
            $user->time_range = $timeRange;
        }
        return response()->json([
            'user' => $user,
            'imageUrl' => $imageUrl
        ], 200);
    }
    public function update(Request $request)
    {
        $user = User::find($request->id);


        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->is_archived == true) {
            return response()->json(['error' => 'This user is archived'], 404);
        }

        if ($request->has('user_name')) {
            $user->user_name = $request->input('user_name');
        }

        if ($request->has('phone_number')) {
            $user->phone_number = $request->input('phone_number');
        }

        if ($request->has('relative_number')) {
            $user->relative_number = $request->input('relative_number');
        }

        if ($request->has('relative_name')) {
            $user->relative_name = $request->input('relative_name');
        }

        if ($request->has('additional_email')) {
            $user->additional_email = $request->input('additional_email');
        }

        if ($request->has('profile_links')) {
            $user->profile_links = $request->input('profile_links');
        }

        if ($request->has('title')) {
            $user->title = $request->input('title');
        }

        if ($request->has('office_location')) {
            $user->office_location = $request->input('office_location');
        }

        if ($request->has('time_range')) {
            $timeRange = $request->input('time_range');
            foreach ($timeRange as &$range) {
                if (isset($range['day_time'])) {
                    $range['day_time'] = ucfirst(strtolower($range['day_time']));
                }
            }
            $user->time_range = $timeRange;
        }

        $user->save();

        return response()->json([
            'message' => 'User profile updated successfully!',
            'data' => $user,
        ], 200);
    }
    public function updateImage(Request $request)
    {
        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if ($user->is_archived == true) {
            return response()->json(['error' => 'This user is archived'], 404);
        }
        $path = $this->uploadMedia($request, 'users', 'image');
        $user->image = $path;
        $user->save();
        $imageUrl = asset('/media/' . $user->image);
        return response()->json([
            'message' => ' success',
            'imageUrl' => $imageUrl
        ], 200);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/login');
    }
    public function changePassword(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validatedData = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirmed_password' => 'required|same:new_password',
        ]);

        $current_password = $user->password;
        if (Hash::check($request->current_password, $current_password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json(['success' => 'Password updated successfully'], 200);
        } else {
            return response()->json(['error' => 'Current password does not match'], 401);
        }
    }

    public function getPhoneNumber(Request $request, $id)
    {
        $user = User::find($request->id);
        $phone_number = $user->phone_number;

        return response()->json([
            'message' => 'success',
             'phone_number' => $phone_number
         ], 200);

    }

    public function search(Request $request)
    {
        $full_name = $request->route('full_name');
        $users = DB::table('users')
            ->whereRaw("CONCAT(full_name) LIKE ?", ['%' . $full_name . '%'])
            ->where('is_archived', false)
            ->get();
        if ($users->isEmpty()) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user_array = [];
        foreach ($users as $user) {
            $user_array[] = [
                'user_id'=>$user->user_id,
                'full_name'=>$user->full_name,
                'phone_number'=>$user->phone_number,
                'relative_number'=>$user->relative_number,
                'main_email'=>$user->main_email,
                'additional_email'=>$user->additional_email,
                'profile_links'=>$user->profile_links,
                'role'=>$user->role,
                'title'=>$user->title,
                'office_location'=>$user->office_location,
                'time_range'=>json_decode($user->time_range),
                'is_active'=>$user->is_active,
                'dept_code'=>$user->dept_code,
                'start_date'=>$user->start_date,
                'end_date'=>$user->end_date,
                'image' => asset('/media/' . $user->image),
                'remember_token'=>$user->remember_token,
                'created_at'=>$user->created_at,
                'updated_at'=>$user->updated_at
            ];
        }
    return response()->json($user_array);
    }
}
