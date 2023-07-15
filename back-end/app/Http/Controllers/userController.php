<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller{

    public function createUser(Request $request)
{
    $request->validate([
        'full_name' => 'required|string',
        'main_email' => 'required|string|email',
        'role' => 'required|string',
        'password' => 'required|string',
        'dept_code' => 'required|string',
        'start_date' => 'sometimes|date',
        'title' => 'required|string',
    ]);

    $user = new User;
    $user->full_name = $request->full_name;
    // $user->main_email = $request->main_email;
    $main_emails = User::where('is_archived', false)
        ->pluck('main_email')
        ->toArray();

        if(!(in_array($request->main_email, $main_emails)))
        {
            $user->main_email = $request->main_email;
        }else{
            return response()->json(['message' => 'Main Email Already Exist'], 200);
        }
    $user->password = Hash::make($request->password);
    $user->title = $request->title;

    $user->additional_email = '';
    $user->start_date = $request->input('start_date', Carbon::now()->format('Y-m-d'));

    $validRoles = ['Professor', 'Teaching Assistant', 'Dean', 'Vice Dean', 'Secretary'];
    $titlesRoles= ['Professor', 'Associate Professor',"Assistant Professor", "Teaching Assistant","Assintant Lecturer","Secretary"];
    if (!in_array($request->role, $validRoles)) {
        return response()->json(['error' => 'this user role is not valid'], 404);
    } else {
        $user->role = $request->role;
    }

    if (!in_array($request->title, $titlesRoles)) {
        return response()->json(['error' => 'this user title is not valid'], 404);
    } else {
        $user->title = $request->title;
    }
    $departments = Department::where('is_archived', false)
    ->pluck('dept_code')
    ->toArray();

    if(in_array($request->dept_code, $departments))
    {
        $user->dept_code = $request->dept_code;

        $department_head = Department::where('dept_code', $user->dept_code)->first();
        if($department_head->head == $user->full_name){
            $user->role = 'Head of Department';
        }
    }else{
        return response()->json(['error' => 'department code does not exist'], 404);
    }

    // Set default values for other fields
    $user->user_name = '';
    $user->profile_links = '';
    $user->additional_email = null;
    //$user->title = 'user';
    $user->office_location = '';
    $user->day_time = '';
    $user->time_range = [];
    $user->is_active = true;
    $user->end_date = Carbon::now()->addYears(1)->format('Y-m-d');
    $user->image = '';
    $user->phone_number = '';
    $user->relative_number = '';
    $user->reason='';
    $user->relative_name='';

    $user->save();

    $notify = new Notification;
    $notify->target_user = $user->user_id;
    $notify->title = 'Hello ' . $user->full_name;
    $notify->desc = 'Welcome to the department administration system';

    $notify->save();

    return response()->json([
        'message' => 'success',
        'data' => $user
    ], 201);
}
public function updateUser(Request $request)
{
    $request->validate([
        'full_name' => 'nullable|string',
        'main_email' => 'nullable|string|email',
        'role' => 'nullable|string|in:Professor,Head,Teaching Assistant,Dean,Vice Dean,Secretary',
        'password' => 'nullable|string',
        'dept_code' => 'nullable|string',
        'start_date' => 'nullable|date',
        'title' => 'required|string|in:Professor,Associate Professor,Assistant Professor,Teaching Assistant,Assintant Lecturer',
    ]);

    $user = User::find($request->id);

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    if ($user->is_archived == true) {
        return response()->json(['message' => 'This user is archived'], 200);
    }

    if ($request->has('password')) {
        $user->password = Hash::make($request->input('password'));
    }

    if ($request->has('full_name')) {
        $user->full_name = $request->input('full_name');
    }

    if ($request->has('main_email')) {
        $user->main_email = $request->input('main_email');
    }

    if ($request->has('role')) {
        $user->role = $request->input('role');
    }

    if ($request->has('title')) {
        $user->title = $request->input('title');
    }

    if ($request->has('dept_code')) {
        $user->dept_code = $request->input('dept_code');
    }

    if ($request->has('is_active')) {
        $user->is_active = $request->input('is_active');
    }

    if ($request->has('start_date')) {
        $user->start_date = $request->input('start_date');
    }

    if ($request->has('end_date')) {
        $user->end_date = $request->input('end_date');
    }

    $user->save();

    return response()->json([
        'message' => 'User profile updated successfully!',
        'data' => $user
    ], 200);
}

public function archiveUser(Request $request)
{
    $user = new User;
    $user = User::find($request->id);

    if (!$user) {
        return response()->json(['error' => 'user not found'], 404);
    }

    if ($user->is_archived == false) {
        $user->is_archived = true;
        $user->save();
    }
    return response()->json([
        'message' => 'User has been archived successfully!'
    ], 200);

}

public function showAllUsers(Request $request)
{
    $users = User::where('is_archived', false)
                 ->orderByRaw("FIELD(dept_code, 'CS', 'IT', 'IS', 'DS', 'AI')")
                 ->get();
    $data = [];

    foreach ($users as $user) {
        $imageUrl = asset('/media/' . $user->image);

        $data[] = [
            'users' => $user,
            'images' => $imageUrl
        ];
    }

    return response()->json([
        'users' => $data
    ], 200);
}

public function getAllProfessorsFullNames(Request $request){

    $users = User::where('is_archived', false)
        ->whereIn('role', ['Professor', 'Head of Department', 'Dean', 'Vice Dean'])
        ->select('Full_name')
        ->orderBy('Full_name')
        ->get();

    return response()->json([
        'users_full_names' => $users,
        'message' => 'success'
    ], 200);

}

public function getAllProfessorsFullNamesAndTitles(Request $request){

    $users = User::where('is_archived', false)
        ->whereIn('role', ['Professor', 'Head of Department', 'Dean', 'Vice Dean'])
        ->orderBy('Full_name')
        ->get();

    $users_array = [];
    foreach($users as $user){
        $users_array [] =[
            'full_name' => $user->full_name,
            'title' => $user->title,
            'department' => $user->dept_code
        ];
    }

    return response()->json([
        'users_full_names' => $users_array,
        'message' => 'success'
    ], 200);

}

public function getProfessorsFullNames(Request $request, $dept_code)
{
    $users = User::where('is_archived', false)
        ->where('is_active', true)
        ->where('dept_code', $dept_code)
        ->whereIn('role', ['Professor', 'Head of Department', 'Dean', 'Vice Dean'])
        ->select('Full_name')
        ->orderBy('Full_name')
        ->get();

    return response()->json([
        'users_full_names' => $users,
        'message' => 'success'
    ], 200);
}

    public function getTAsFullNames(Request $request, $dept_code)
    {
        $users = User::where('is_archived', false)
            ->where('is_active', true)
            ->where('dept_code', $dept_code)
            ->whereIn('role', ['Teaching Assistant'])
            ->select('Full_name')
            ->orderBy('Full_name')
            ->get();

        return response()->json([
            'users_full_names' => $users,
            'message' => 'success'
        ], 200);
    }

}
