<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $admin = new Admin;
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);

        $admin->save();

        return response()->json([
            'message' => 'success',
            'data' => $admin
        ], 201);
    }

    public function updateAdmin(Request $request)
    {
        $admin = Admin::find($request->id);

        if ($request->has('email')) {
            $admin->email = $request->input('email');
        }

        if ($request->has('name')) {
            $admin->name = $request->input('name');
        }

        if ($request->has('password')) {
            $admin->password = Hash::make($request->input('password'));
        }

        $admin->save();

        return response()->json([
            'data' => $admin
        ], 201);
    }

    public function getProfile(Request $request)
    {
        $admin = Admin::find($request->id);

        if (!$admin) {
            return response()->json(['error' => 'admin not found'], 404);
        }

        return response()->json([
            'message' => 'success',
            'admin' => $admin,

        ], 200);
    }

    public function getAllAdmins(Request $request)
    {
        $admins = Admin::all();
        
        return response()->json([
            'message' => 'success',
            'admins' => $admins
        ], 200);
    }

    public function promoteAdmin(Request $request)
    {
        $admin = Admin::find($request->id);
        if($admin->is_superAdmin == false)
        {
            $admin->is_superAdmin = true;
            $admin->save();
        }
        return response()->json([
            'msg' => 'admin has been assigned super admin successfully'
        ], 201);
    }

    public function promoteUser(Request $request)
    {
        $user = User::find($request->id);
        if($user->privileged_user == false)
        {
            $user->privileged_user = true;
            $user->save();
        }
        return response()->json([
            'msg' => 'user has been assigned admin successfully'
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
