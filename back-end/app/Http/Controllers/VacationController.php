<?php
namespace App\Http\Controllers;
use App\Models\Vacation;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Trait\UploadMediaTrait;
use App\Models\Notification;
use Mail;
use App\Mail\NewVacationMail;
use App\Mail\AcceptedVacationMail;
use App\Mail\RejectedVacationMail;
use Illuminate\Support\Facades\DB;
use PDF;


class VacationController extends Controller
{
    use UploadMediaTrait;


    public function createVacation(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'type' => 'required|string',
            'desc' => 'sometimes|string',
            'attachment' => 'sometimes|file'
        ]);

        $attachmentPath = null;

        $vacation = new Vacation;
        $vacation->user_id = auth()->id();
        $vacation->start_date = $request->start_date;
        $vacation->end_date = $request->end_date;
        $vacation->type = $request->type;

        $user_id = $vacation->user_id;
        $user_formail = User::where('user_id', $user_id)->first();

        $vacation->status = 'Pending';

        if ($request->has('desc')) {
            $vacation->desc = $request->input('desc');
        }

        if ($request->hasFile('attachment')) {
            try {
                $path = $this->uploadMedia($request, 'vacation_attachments','attachment');
                $vacation->attachment = $path;
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error uploading attachment file.'], 422);
            }

            $attachmentUrl = asset('/media/' . $vacation->attachment);
        }

        $vacation->save();

        $notification = new Notification;
            $notification->target_user = $vacation->user_id;
            $notification->title = 'New Vacation Request';
            $notification->desc = 'Your request for a vacation of type ('
            .$vacation->type.'), with start date ('
            .$vacation->start_date.'), and end date ('
            .$vacation->end_date.') is pending for approval, you will be notified with any update.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new NewVacationMail($data));

        return response()->json([
            'message'=>'success',
            'vacation' => $vacation,
            'attachmentPath' => $attachmentUrl
        ], 201);
    }

    public function showUserVacations(Request $request)
    {
        $user_id = auth()->id();

        $vacations = Vacation::where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

         $data = [];

        foreach ($vacations as $vacation) {

            $attachmentUrl = asset('/media/' . $vacation->attachment);

            $data[] = [
                'vacations' => $vacation,
                'attachment' => $attachmentUrl
            ];
        }

        return response()->json([
            'vacations' => $data,
        ], 200);
    }

    public function showVacation(Request $request)
    {
        $vacation = Vacation::find($request->id);

        if (!$vacation) {
            return response()->json(['error' => 'Vacation not found'], 404);
        }

        $attachmentUrl = asset('/media/' . $vacation->attachment);

        return response()->json([
            'vacation' => $vacation,
            'attachmentPath' => $attachmentUrl
        ], 200);
    }

    public function showAllVacations(Request $request)
    {
        $vacations = Vacation::orderBy('created_at', 'desc')->get();

        $data = [];

        foreach ($vacations as $vacation) {

            $attachmentUrl = asset('/media/' . $vacation->attachment);

            $data[] = [

                'vacations' => $vacation,
                'attachment' => $attachmentUrl
            ];
        }

        return response()->json([
            'vacations' => $data
        ], 200);
    }

    public function getVacationData(Request $request)//Secretary view
    {
        $vacation = Vacation::find($request->id);

        if ($vacation) {
            $user = User::find($vacation->user_id);
            $data = [
                'full_name' => $user->full_name,
                'phone_number' => $user->phone_number,
                'role' => $user->role,
                'status' => $vacation->status,
                'description' => $vacation->desc,
                'start_date' => $vacation->start_date,
                'end_date' => $vacation->end_date,
                'type' => $vacation->type,
                'attachment' => asset('/media/' . $vacation->attachment),
            ];
            return response()->json(['Vacation data' => $data]);
        } else {
            return response()->json(['message' => 'not found']);
        }
    }
    public function acceptVacation(Request $request)
    {
        $vacation = Vacation::find($request->id);
        $user_formail_id = $vacation->user_id;
        $user_formail = User::where('user_id', $user_formail_id)->first();

        if ($vacation) {
            $vacation->status = 'Accepted';
            $vacation->save();

            $notification = new Notification;
            $notification->target_user = $vacation->user_id;
            $notification->title = 'Accepted Vacation Request';
            $notification->desc = 'Your request for a vacation of type ('
            .$vacation->type.'), with start date ('
            .$vacation->start_date.'), and end date ('
            .$vacation->end_date.') has been accepted.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new AcceptedVacationMail($data));

            return response()->json(['message' => 'accepted']);
        } else {
            return response()->json(['message' => 'not found']);
        }
    }

    public function rejectVacation(Request $request)
    {
        $vacation = Vacation::find($request->id);
        $user_formail_id = $vacation->user_id;
        $user_formail = User::where('user_id', $user_formail_id)->first();

        if ($vacation) {
            $vacation->status = 'Rejected';
            $vacation->save();

            $notification = new Notification;
            $notification->target_user = $vacation->user_id;
            $notification->title = 'Rejected Vacation Request';
            $notification->desc = 'Your request for a vacation of type ('
            .$vacation->type.'), with start date ('
            .$vacation->start_date.'), and end date ('
            .$vacation->end_date.') has been rejected.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new RejectedVacationMail($data));

            return response()->json(['message' => 'rejected']);
        } else {
            return response()->json(['message' => 'not found']);
        }
    }
/*     public function updateVacationStatus(Request $request)//Secretary view
{
    $vacation = Vacation::find($request->id);
    $status = $request->route('status');


    if ($vacation) {
        $allowedStatuses = [ 'Accepted', 'Rejected'];
        $status = $request->status;
        if ($status && in_array($status, $allowedStatuses)) {
            $vacation->status = $status;
            $vacation->save();
            return response()->json(['message' => 'Vacation status has been updated']);
        } else {
            return response()->json(['message' => 'Invalid status value']);
        }
    } else {
        return response()->json(['message' => 'Vacation not found']);
    }
} */
    public function showVacationsDept(Request $request)//vacation of user to his secretary of dept takes the token of secratory that has logged in
    {
        $user = $request->user();


        if ($user->role != 'Secretary') {
            return response()->json(['message' => 'You are not authorized to view this resource'], 403);
        }

        $vacations = Vacation::whereHas('user', function($query) use ($user) {
            $query->where('dept_code', $user->dept_code);
        })->orderBy('created_at', 'desc')->get();

        $data = [];

        foreach ($vacations as $vacation) {

            $attachmentUrl = asset('/media/' . $vacation->attachment);
            $user = User::find($vacation->user_id);

            $data[] = [
                'full_name' => $user->full_name,
                'vacations' => $vacation,
                'attachment' => $attachmentUrl
            ];
        }

        return response()->json([
            'vacations' => $data
        ], 200);
    }

    public function viewVacationPDF(Request $request)
    {
        $vacation = Vacation::find($request->id);

        if (!$vacation) {
            return response()->json(['error' => 'Vacation not found'], 404);
        }

        $attachmentUrl = asset('/media/' . $vacation->attachment);

        $user = User::where('user_id', $vacation->user_id)->first();
        $user_name = $user->full_name;

        $vacationDetails =[
            'desc' => $vacation->desc,
            'start' => $vacation->start_date,
            'end' => $vacation->end_date,
            'type' => $vacation->type,
            'user' => $user_name,
            'attachmentPath' => $attachmentUrl
        ];
        // return response()->json([
        //     'vacation' => $vacationDetails
        // ], 200);

        $pdf = PDF::loadView('pdf.vacationPDF', ['vacationDetails' => $vacationDetails])
        ->setPaper('a4','portrait');
        return $pdf->stream('vacation.pdf');

    }

    public function exportVacationPDF(Request $request)
    {
        $vacation = Vacation::find($request->id);

        if (!$vacation) {
            return response()->json(['error' => 'Vacation not found'], 404);
        }

        $attachmentUrl = asset('/media/' . $vacation->attachment);

        $user = User::where('user_id', $vacation->user_id)->first();
        $user_name = $user->full_name;

        $vacationDetails =[
            'desc' => $vacation->desc,
            'start' => $vacation->start_date,
            'end' => $vacation->end_date,
            'type' => $vacation->type,
            'user' => $user_name,
            'attachmentPath' => $attachmentUrl
        ];

        $pdf = PDF::loadView('pdf.vacationPDF', ['vacationDetails' => $vacationDetails])
        ->setPaper('a4','portrait');

        return $pdf->download($user_name.'-'.$vacation->created_at.'-vacation.pdf');
    }
}
