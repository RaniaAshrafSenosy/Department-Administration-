<?php

namespace App\Http\Controllers;
use App\Models\Notification;

use Illuminate\Http\Request;

class NotificationController extends Controller
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
    public function create()
    {
        //
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
    public function getMyNotifications(Request $request)
    {
        $user_id = auth()->id();
        $notifications = Notification::where('target_user', $user_id)
                    ->where('is_archived',false)
                    ->orderBy('created_at', 'desc')
                    ->get();

        $data = [];

        foreach($notifications as $notification)
        {
            if($notification->is_seen == false)
            {
                $notification->is_seen = true;
                $notification->save();
            }

            $data[] = [
                'notification' => $notification
            ];
        }

        return response()->json([
            'notifications' => $data
        ], 201);
    }

    public function getUnreadNotificationsCountForUser(Request $request)
    {
        $user_id = auth()->id();
        $notification_count = Notification::where('target_user', $user_id)
        ->where('is_seen',false)
        ->where('is_archived', false)
        ->count();

        return response()->json([
            'number_of_unread_notifications' => $notification_count
        ], 201);
    }

    public function archiveNotification(Request $request){
        $notification = new Notification;
        $notification = Notification::find($request->id);

        return response()->json([
            'message' => $notification
        ], 200);

        if (!$notification) {
            return response()->json(['error' => 'notification not found'], 404);
        }

        if ($notification->is_archived == false) {
            $notification->is_archived = true;
            $notification->save();
        }
        return response()->json([
            'message' => 'Notification has been archived successfully!'
        ], 200);
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
