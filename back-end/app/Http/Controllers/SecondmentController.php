<?php

namespace App\Http\Controllers;
use App\Models\Secondment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Trait\UploadMediaTrait;
use App\Models\Notification;
use Mail;
use App\Models\Department;
use Carbon\Carbon;
use App\Mail\NewSecondmentMail;
use App\Mail\AcceptedSecondmentMail;
use App\Mail\RejectedSecondmentMail;
use PDF;

class SecondmentController extends Controller
{
    use UploadMediaTrait;

    public function createSecondment(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'type' => 'required|string',
            'desc' => 'nullable|string',
            'attachment' => 'required|file',
            'country' => 'nullable|string'
        ]);

        $attachmentPath = null;

        $secondment = new Secondment;
        $secondment->user_id = auth()->id();
        $secondment->start_date = $request->start_date;
        $secondment->end_date = $request->end_date;
        $secondment->type = $request->type;
        $secondment->country = $request->country;
        $secondment->status = 'Pending';

        $user_id = $secondment->user_id;
        $user_formail = User::where('user_id', $user_id)->first();

        if ($request->has('desc')) {
            $secondment->desc = $request->input('desc');
        }

        try {
            $path = $this->uploadMedia($request, 'secondment_attachments','attachment');
            $secondment->attachment = $path;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading attachment file.'], 422);
        }

        $attachmentUrl = asset('/media/' . $secondment->attachment);


        $secondment->save();

        $notification = new Notification;
            $notification->target_user = $secondment->user_id;
            $notification->title = 'New Secondment Request';
            $notification->desc = 'Your request for a secondment of type ('
            .$secondment->type.'), with start date ('
            .$secondment->start_date.'), and end date ('
            .$secondment->end_date.') is pending for approval, you will be notified with any update.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new NewSecondmentMail($data));

        return response()->json([
            'secondment' => $secondment,
            'attachmentPath' => $attachmentUrl,
            'message'=>'success'

        ], 201);
    }


    public function showSecondment(Request $request)
    {
        $secondment = Secondment::find($request->id);

        if (!$secondment) {
            return response()->json(['error' => 'Secondment not found'], 404);
        }

        $attachmentUrl = asset('/media/' . $secondment->attachment);

        return response()->json([
            'secondment' => $secondment,
            'attachmentPath' => $attachmentUrl,
            'message'=>'success'

        ], 200);
    }


    public function showAllSecondments(Request $request)
    {
        $secondments = Secondment::orderBy('created_at', 'desc')->get();

        $data = [];

        foreach ($secondments as $secondment) {

            $attachmentUrl = asset('/media/' . $secondment->attachment);

            $data[] = [
                'secondments' => $secondment,
                'attachment' => $attachmentUrl
            ];
        }

        return response()->json([
            'secondments' => $data,
            'message'=>'success'
        ], 200);
    }


    public function showUserSecondments(Request $request)
    {
        $user_id = auth()->id();

        $secondments = Secondment::where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

         $data = [];

        foreach ($secondments as $secondment) {

            $attachmentUrl = asset('/media/' . $secondment->attachment);

            $data[] = [
                'secondments' => $secondment,
                'attachment' => $attachmentUrl
            ];
        }

        return response()->json([
            'secondments' => $data,
            'message'=>'success'
        ], 200);
    }
    public function getSecondmentData(Request $request)//Secretary view
    {
        $secondment = Secondment::find($request->id);

        if ($secondment) {
            $user = User::find($secondment->user_id);
            $data = [
                'full_name' => $user->full_name,
                'phone_number' => $user->phone_number,
                'role' => $user->role,
                'status' => $secondment->status,
                'description' => $secondment->desc,
                'start_date' => $secondment->start_date,
                'end_date' => $secondment->end_date,
                'type' => $secondment->type,
                'attachment' => asset('/media/' . $secondment->attachment),
                'country' => $secondment->country,
            ];
            return response()->json(['Secondment data' => $data]);
        } else {
            return response()->json(['message' => 'not found']);
        }
    }
    public function acceptSecondment(Request $request)
    {
        $secondment = Secondment::find($request->id);
        $user_formail_id = $secondment->user_id;
        $user_formail = User::where('user_id', $user_formail_id)->first();

        if ($secondment) {
            $secondment->status = 'Accepted';
            $secondment->save();

            $notification = new Notification;
            $notification->target_user = $secondment->user_id;
            $notification->title = 'Accepted Secondment Request';
            $notification->desc = 'Your request for a secondment of type ('
            .$secondment->type.'), with start date ('
            .$secondment->start_date.'), and end date ('
            .$secondment->end_date.') has been accepted.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new AcceptedSecondmentMail($data));

            return response()->json(['message' => 'accepted']);
        } else {
            return response()->json(['message' => 'not found']);
        }
    }

    public function rejectSecondment(Request $request)
    {
        $secondment = Secondment::find($request->id);
        $user_formail_id = $secondment->user_id;
        $user_formail = User::where('user_id', $user_formail_id)->first();

        if ($secondment) {
            $secondment->status = 'Rejected';
            $secondment->save();

            $notification = new Notification;
            $notification->target_user = $secondment->user_id;
            $notification->title = 'Rejected Secondment Request';
            $notification->desc = 'Your request for a secondment of type ('
            .$secondment->type.'), with start date ('
            .$secondment->start_date.'), and end date ('
            .$secondment->end_date.') has been rejected.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new RejectedSecondmentMail($data));

            return response()->json(['message' => 'rejected']);
        } else {
            return response()->json(['message' => 'not found']);
        }
    }
/*     public function updateSecondmentStatus(Request $request)//Secretary view
{
    $secondment = Secondment::find($request->id);
    $status = $request->route('status');

    if ($secondment) {
        $allowedStatuses = [ 'Accepted', 'Rejected'];
        $status = $request->status;
        if ($status && in_array($status, $allowedStatuses)) {
            $secondment->status = $status;
            $secondment->save();
            return response()->json(['message' => 'Secondment status has been updated']);
        } else {
            return response()->json(['message' => 'Invalid status value']);
        }
    } else {
        return response()->json(['message' => 'Secondment not found']);
    }
} */

    public function showSecondmentDept(Request $request)//secondment of user to his secretary of dept takes the token of secratory that has logged in
    {
        $user = $request->user();


        if ($user->role != 'Secretary') {
            return response()->json(['message' => 'You are not authorized to view this resource'], 403);
        }

        $secondments = Secondment::whereHas('user', function($query) use ($user) {
            $query->where('dept_code', $user->dept_code);
        })->orderBy('created_at', 'desc')->get();

        $data = [];

        foreach ($secondments as $secondment) {
            $user = User::find($secondment->user_id);

            $attachmentUrl = asset('/media/' . $secondment->attachment);

            $data[] = [
                'full_name' => $user->full_name,
                'Secondment' => $secondment,
                'attachment' => $attachmentUrl
            ];
        }

        return response()->json([
            'secondments' => $data
        ], 200);
    }
    public function getSecondmentStatistics(Request $request, $deptCode)
    {
        // Get the secondments for the specified department in the last 6 years
        $secondments = Secondment::query()
        ->whereHas('user', function ($query) use ($deptCode) {
            $query->where('dept_code', $deptCode);
        })
        ->where('status', 'Accepted')
        ->where('start_date', '>=', Carbon::now()->subYears(6))
        ->selectRaw('YEAR(start_date) as year, COUNT(*) as num_secondments')
        ->groupBy('year')
        ->get();

        // Initialize the statistics array
        $stats = [];

        // Populate the statistics array with secondment data
        $years = range(date('Y')-5, date('Y'));
        foreach ($years as $year) {
        $numSecondments = 0;
        foreach ($secondments as $secondment) {
            if ($secondment->year == $year) {
                $numSecondments = $secondment->num_secondments;
                break;
            }
        }
        $stats[] = [
            'Year' => $year,
            'NumberofSecondments' => $numSecondments
        ];
        }

        return response()->json($stats, 200);
}

    public function viewSecondmentPDF(Request $request)
    {
        $secondment = Secondment::find($request->id);

        if (!$secondment) {
            return response()->json(['error' => 'Secondment not found'], 404);
        }

        $attachmentUrl = asset('/media/' . $secondment->attachment);

        $user = User::where('user_id', $secondment->user_id)->first();
        $user_name = $user->full_name;

        $secondmentDetails =[
            'desc' => $secondment->desc,
            'start' => $secondment->start_date,
            'end' => $secondment->end_date,
            'type' => $secondment->type,
            'country' => $secondment->country,
            'user' => $user_name,
            'attachmentPath' => $attachmentUrl
        ];

        $pdf = PDF::loadView('pdf.secondmentPDF', ['secondmentDetails' => $secondmentDetails])
        ->setPaper('a4','portrait');

        return $pdf->stream('secondment.pdf');


    }

    public function exportSecondmentPDF(Request $request)
    {
        $secondment = Secondment::find($request->id);

        if (!$secondment) {
            return response()->json(['error' => 'Secondment not found'], 404);
        }

        $attachmentUrl = asset('/media/' . $secondment->attachment);

        $user = User::where('user_id', $secondment->user_id)->first();
        $user_name = $user->full_name;

        $secondmentDetails =[
            'desc' => $secondment->desc,
            'start' => $secondment->start_date,
            'end' => $secondment->end_date,
            'type' => $secondment->type,
            'country' => $secondment->country,
            'user' => $user_name,
            'attachmentPath' => $attachmentUrl
        ];

        $pdf = PDF::loadView('pdf.secondmentPDF', ['secondmentDetails' => $secondmentDetails])
        ->setPaper('a4','portrait');

        return $pdf->download($user_name.'-'.$secondment->created_at.'-secondment.pdf');
    }
}
