<?php

namespace App\Http\Controllers;
use Mail;

use Carbon\Carbon;
use App\Models\HasA;
use App\Models\User;
use App\Models\Admin;
use App\Models\Department;
use App\Models\Announcement;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Trait\UploadMediaTrait;
use App\Mail\NewAnnouncementMail;
use Illuminate\Support\Facades\Cache;
use App\Mail\AcceptedAnnouncementMail;
use App\Mail\AudienceAnnouncementMail;
use App\Mail\RejectedAnnouncementMail;

class AnnouncementController extends Controller
{
    use UploadMediaTrait;

    public function postAnnouncement(Request $request)
    {

        $user = auth()->user();
        $user_id = $user->user_id;
        $user_formail = User::where('user_id', $user_id)->first();
        $userRole = $user->role;
        $userDept = $user->dept_code;

        $request->validate([
            'title' => 'string',
            'body' => 'required|string',
            'file' => 'nullable|file',
            'target_role.*' => 'nullable|string',
            'target_dept.*' => 'nullable|string'
        ]);

        $announcement = new Announcement;
        $announcement->user_id = $user_id;
        $announcement->title = $request->title;
        $announcement->body = $request->body;
        $announcement->target_role = $request->input('target_role') ?? [];

        $announcement->target_dept = $request->input('target_dept') ?? [];
        $announcement->is_archived = false;

        if (in_array($userRole, ['Secretary', 'Dean', 'Vice Dean'])) {
            $announcement->target_dept = $request->target_dept;
        }
        else {
            if (empty($announcement->target_dept)) {
                $announcement->target_dept = [];
            }

            if (!is_array($announcement->target_dept)) {
                $announcement->target_dept = [$announcement->target_dept];
            }

            if (in_array($userDept, $announcement->target_dept)) {
                // Allow professor to post announcement for their own department
                $announcement->target_dept = [$userDept];
            }
                 else {
                    return response()->json([
                        'message' => 'You are authorized to post announcements only for your department'
                    ], 401);
                }
            }
        if ($request->hasFile('file')) {
            $path = $this->uploadMedia($request, 'announcement_files','file');
            $announcement->file = $path;
            $file_Url = asset('/media/' . $announcement->file);
        }else{
            $file_Url = $announcement->file ? asset('/media/' . $announcement->file) : null;
        }

        if (!is_array($announcement->target_role)) {
            $announcement->target_role = [$announcement->target_role];
        }

        if (in_array('Professor', $announcement->target_role)) {

            $announcement->target_role = array_merge($announcement->target_role, ['Head of Department', 'Dean', 'Vice Dean']);
        }
            $announcement->save();

            $notification = new Notification;
            $notification->target_user = $user_id;
            $notification->title = 'New Announcement';
            $notification->desc = 'Your Announcement ('.$announcement->title.') is pending for approval, you will be notified with any update.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new NewAnnouncementMail($data));

        return response()->json([
            'message' => 'Announcement has been stored successfully',
            'announcement' => $announcement,
            'file'=>  $file_Url   ], 200);
}


public function getAllAnnouncementsForApproval(Request $request)
{
    $userId = auth()->id();
    $user = User::findOrFail($userId);

    $allowedRoles = ['Head of Department', 'Dean', 'Vice Dean','Secretary'];
    if (in_array($user->role, $allowedRoles)) {

        $announcements = Announcement::where('status', 'pending')
            ->where('is_archived', false)
            ->orderByDesc('created_at')
            ->get();

            return response()->json([
                'announcements' => $announcements->map(function ($announcement) {
                    return [
                        'announcement_id' => $announcement->announcement_id,
                        'title' => $announcement->title,
                        'body' => $announcement->body,
                        'target_role' => $announcement->target_role,
                        'target_dept' => $announcement->target_dept,
                        'datetime'=>$announcement->datetime,
                        'created_at' => $announcement->created_at,
                        'updated_at' => $announcement->updated_at,
                        'user_id' => $announcement->user_id,
                        'user_full_name' => User::findOrFail($announcement->user_id)->full_name,
                        'user_img_url'=>asset('/media/' . User::findOrFail($announcement->user_id)->image),
                        'file'=> asset('/media/' . $announcement->file)
                    ];
                }),
                'userId' => $userId
            ]);

    } else {
        return response()->json([
            'message' => 'You are not authorized to view these announcements'
        ], 401);
    }
}
public function approveAnnouncement(Request $request, $announcementId)
{
    $userId = auth()->id();
    $user = User::findOrFail($userId);

    $user_formail_id = $userId;
    $user_formail = User::where('user_id', $user_formail_id)->first();

    $allowedRoles = ['Head of Department', 'Dean', 'Vice Dean','Secretary'];
    if (in_array($user->role, $allowedRoles)) {

        $announcement = Announcement::findOrFail($announcementId);

        $announcement->status = 'accepted';
        $announcement->save();

        if($announcement->target_dept ==[] ){
            $departments = Department::all();
            foreach ($departments as $department){
                $hasA = new HasA();
                $hasA->dept_code =$department->dept_code;
                $hasA->announcement_id = $announcement->announcement_id;
                $hasA->save();
            }
        }
        else{
        foreach( $announcement->target_dept as $dept){
                $hasA = new HasA();
                $hasA->dept_code =$dept;
                $hasA->announcement_id = $announcement->announcement_id;
                $hasA->save();
        }
                $notification = new Notification;
                $notification->target_user = $announcement->user_id;
                $notification->title = 'Accepted Announcement';
                $notification->desc = 'Your request for publishing an announcement with the title ('
                .$announcement->title.'), at ('
                .$announcement->created_at.'), has been accepted.';

                $notification->save();

                $data = [
                    'title' => $notification->title,
                    'desc' => $notification->desc
                ];

                Mail::to($user_formail->main_email)->send(new AcceptedAnnouncementMail($data));
        }

        $roles = $announcement->target_role;
        $depts = $announcement->target_dept;

        $users = User::all();

        foreach($users as $target_user){
            if(in_array($target_user->role, $roles)){

                if(in_array($target_user->dept_code, $depts)){

                    $notification = new Notification;
                    $notification->target_user = $target_user->user_id;
                    $notification->title = 'New Available Announcement';
                    $notification->desc = 'A new announcement has been published by '
                    .$user->full_name.' check your announcements page.';

                    $notification->save();
                    $data = [
                        'title' => $notification->title,
                        'desc' => $notification->desc
                    ];
                    Mail::to($user_formail->main_email)->send(new AudienceAnnouncementMail($data));
                }
            }
        }
        // return response()->json([
        //     'message' => $roles,
        //     'hjdsnc' => $depts,
        // ], 200);

        return response()->json([
            'message' => 'Announcement approved successfully'
        ], 200);
    } else {
        return response()->json([
            'message' => 'You are not authorized to approve announcements '
        ], 401);
    }
}

public function rejectAnnouncement(Request $request, $announcementId)
{
    $userId = auth()->id();
    $user = User::findOrFail($userId);

    $user_formail_id = $userId;
    $user_formail = User::where('user_id', $user_formail_id)->first();

    $allowedRoles = ['Head of Department', 'Dean', 'Vice Dean','Secretary'];
    if (in_array($user->role, $allowedRoles)) {

        $announcement = Announcement::findOrFail($announcementId);

        $announcement->status = 'rejected';
        $announcement->save();

        $notification = new Notification;
            $notification->target_user = $announcement->user_id;
            $notification->title = 'Rejected Announcement';
            $notification->desc = 'Your request for publishing an announcement with the title ('
            .$announcement->title.'), at ('
            .$announcement->created_at.'), has been rejected.';

            $notification->save();
            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];
            Mail::to($user_formail->main_email)->send(new RejectedAnnouncementMail($data));
        return response()->json([
            'message' => 'Announcement rejected successfully'
        ], 200);

    } else {
        return response()->json([
            'message' => 'You are not authorized to reject announcements '
        ], 401);
    }
}
public function getUserAnnouncements(Request $request)
{
    $userId = auth()->id();
    $user = User::findOrFail($userId);
    $userRole = $user->role;
    $userDeptCode = $user->dept_code;

    $announcements = Announcement::where('status', 'accepted')
       ->where('is_archived', false)
       ->where(function ($query) use ($userRole, $userDeptCode) {
       $query->where(function ($query) use ($userRole, $userDeptCode) {
            $query->WhereJsonContains('target_dept', $userDeptCode)
                  ->WhereJsonContains('target_role', $userRole);
        })
        ->orWhereJsonLength('target_role', 0)
            ->where(function ($query) use ($userDeptCode) {
        $query->where('target_dept', [$userDeptCode])
              ->orWhereJsonContains('target_dept', $userDeptCode)
              ->orWhereNull('target_dept');
    })
        ->orWhereNull('target_dept')
            ->where(function ($query) use ($userRole) {
        $query->where('target_role', [$userRole])
              ->orWhereJsonContains('target_role', $userRole)
              ->orWhereNull('target_role');
    })
        ;
        })
    ->orderByDesc('datetime')
    ->get();

    return response()->json([
        'message'=>'success',
        'announcements' => $announcements->map(function ($announcement) {
            return [
                'announcement_id' => $announcement->announcement_id,
                'title' => $announcement->title,
                'body' => $announcement->body,
                'target_role' => $announcement->target_role,
                'target_dept' => $announcement->target_dept,
                'datetime'=>$announcement->datetime,
                'created_at' => $announcement->created_at,
                'updated_at' => $announcement->updated_at,
                'user_id' => $announcement->user_id,
                'user_full_name' => User::findOrFail($announcement->user_id)->full_name,
                'user_img_url'=>asset('/media/' . User::findOrFail($announcement->user_id)->image),
                'file'=> asset('/media/' . $announcement->file)
            ];
        }),
        'userId' => $userId
    ]);
}

public function updateAnnouncement(Request $request, $id)
{
    $announcement = Announcement::findOrFail($id);

    if ($announcement->user_id != auth()->id()) {
        return response()->json(['error' => 'You are not authorized to update this announcement'], 401);
    }

    $request->validate([
        'title' => 'nullable|string',
        'body' => 'nullable|string',
        'file' => 'nullable|file'
    ]);

    $announcement->title = $request->title;
    $announcement->body = $request->body;

    if (!$announcement) {
        return response()->json(['error' => 'announcement not found'], 404);
    }

    if ($announcement->is_archived == true) {
        return response()->json(['error' => 'This announcement is archived'], 404);
    }

    if ($request->has('title')) {
        $announcement->title = $request->title;
    }
    if ($request->hasFile('file')) {
        $path = $this->uploadMedia($request, 'announcement_files','file');
        $announcement->file = $path;
        $file_Url = asset('/media/' . $announcement->file);
    }else{
        $file_Url = asset('/media/' . $announcement->file);
    }

    $announcement->save();

    $file_Url = asset('/media/' . $announcement->file);

    return response()->json([
        'message' => 'Announcement has been updated successfully',
        'announcement' => $announcement,
        'file'=>  $file_Url
    ], 200);
}

public function archiveAnnouncement(Request $request)
{

    $announcement = Announcement::find($request->id);
    $userId = auth()->id();

    if (!$announcement) {
        return response()->json(['error' => 'announcement not found'], 404);
    }
    if($announcement->user_id != $userId ){
        return response()->json(['error' => 'You are not authorized to archive this announcement'], 404);
    }

    if ($announcement->user_id == $userId && $announcement->is_archived == false) {
        $announcement->is_archived = true;
            $announcement->save();


    return response()->json([
        'message' => 'The announcement has been archived successfully!'
    ], 200);}

}
public function getMyAnnouncements(Request $request)
{
    $userId = auth()->id();

        $announcements = Announcement::where('status', 'accepted')
            ->where('is_archived', false)
            ->where('user_id',$userId)
            ->orderByDesc('created_at')
            ->get();

            return response()->json([
                'message'=>'success',
                'announcements' => $announcements->map(function ($announcement) {
                    return [
                        'announcement_id' => $announcement->announcement_id,
                        'title' => $announcement->title,
                        'body' => $announcement->body,
                        'target_role' => $announcement->target_role,
                        'target_dept' => $announcement->target_dept,
                        'datetime'=>$announcement->datetime,
                        'created_at' => $announcement->created_at,
                        'updated_at' => $announcement->updated_at,
                        'user_id' => $announcement->user_id,
                        'user_full_name' => User::findOrFail($announcement->user_id)->full_name,
                        'user_img_url'=>asset('/media/' . User::findOrFail($announcement->user_id)->image),
                        'file'=> asset('/media/' . $announcement->file)
                    ];
                })
                        ]);


}
public function AdminArchiveAnnouncement(Request $request)
{
    $announcement = Announcement::find($request->id);

    if (!$announcement) {
        return response()->json(['error' => 'announcement not found'], 404);
    }

    if ($announcement->is_archived == false) {
        $announcement->is_archived = true;
            $announcement->save();

    return response()->json([
        'message' => 'The announcement has been archived successfully!'
    ], 200);}
}

public function getAnnouncementByID(Request $request)
{
    $announcement = Announcement::find($request->id);

        $announcement = Announcement::where('status', 'accepted')
            ->where('is_archived', false)
            ->where('announcement_id',$request->id)
            ->get();

            return response()->json([
                'announcement' => $announcement->map(function ($announcement) {
                    return [
                        'announcement_id' => $announcement->announcement_id,
                        'title' => $announcement->title,
                        'body' => $announcement->body,
                        'target_role' => $announcement->target_role,
                        'target_dept' => $announcement->target_dept,
                        'datetime'=>$announcement->datetime,
                        'created_at' => $announcement->created_at,
                        'updated_at' => $announcement->updated_at,
                        'user_id' => $announcement->user_id,
                        'user_full_name' => User::findOrFail($announcement->user_id)->full_name,
                        'user_img_url'=>asset('/media/' . User::findOrFail($announcement->user_id)->image),
                        'file'=> asset('/media/' . $announcement->file)
                    ];
                })
              ]);


}
public function AdminGetAnnounncements(Request $request)
{

        $announcements = Announcement::where('status', 'accepted')
            ->where('is_archived', false)
            ->orderByDesc('created_at')
            ->get();

            return response()->json([
                'message'=>'success',
                'announcement' => $announcements->map(function ($announcement) {
                    return [
                        'announcement_id' => $announcement->announcement_id,
                        'title' => $announcement->title,
                        'body' => $announcement->body,
                        'target_role' => $announcement->target_role,
                        'target_dept' => $announcement->target_dept,
                        'datetime'=>$announcement->datetime,
                        'created_at' => $announcement->created_at,
                        'updated_at' => $announcement->updated_at,
                        'user_id' => $announcement->user_id,
                        'user_full_name' => User::findOrFail($announcement->user_id)->full_name,
                        'user_img_url'=>asset('/media/' . User::findOrFail($announcement->user_id)->image),
                        'file'=> asset('/media/' . $announcement->file)
                    ];
                })
              ]);


}
}
