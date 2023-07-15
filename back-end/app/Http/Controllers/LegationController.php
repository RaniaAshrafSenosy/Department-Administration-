<?php


namespace App\Http\Controllers;
use App\Models\Legation;
use App\Models\User;
use App\Models\Department;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Trait\UploadMediaTrait;
use App\Models\Notification;
use Mail;
use App\Mail\NewLegationMail;
use App\Mail\AcceptedLegationMail;
use App\Mail\RejectedLegationMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class LegationController extends Controller
{
    use UploadMediaTrait;
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
    public function createLegation(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'type' => 'required|string',
            'desc' => 'nullable|string',
            'attachment' => 'required|file'
        ]);

        $attachmentPath = null;

        $legation = new Legation;
        $legation->user_id = auth()->id();

        $legation->start_date = $request->start_date;
        $legation->end_date = $request->end_date;
        $legation->type = $request->type;
        $legation->status = 'Pending';

        $user_id = $legation->user_id;
        $user_formail = User::where('user_id', $user_id)->first();


        if ($request->has('desc')) {
            $legation->desc = $request->input('desc');
        }

        try {
            $path = $this->uploadMedia($request, 'legations_attachments','attachment');
            $legation->attachment = $path;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading attachment file.'], 422);
        }

        $attachmentUrl = asset('/media/' . $legation->attachment);


        $legation->save();

        $notification = new Notification;
            $notification->target_user = $legation->user_id;
            $notification->title = 'New Legation Request';
            $notification->desc = 'Your request for a legation of type ('
            .$legation->type.'), with start date ('
            .$legation->start_date.'), and end date ('
            .$legation->end_date.') is pending for approval, you will be notified with any update.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new NewLegationMail($data));

        return response()->json([
            'message'=>'success',
            'legation' => $legation,
            'attachmentPath' => $attachmentUrl
        ], 201);
    }

    public function showLegation(Request $request)
    {
        $legation = Legation::find($request->id);

        if (!$legation) {
            return response()->json(['error' => 'Legation not found'], 404);
        }

        $attachmentUrl = asset('/media/' . $legation->attachment);

        return response()->json([
            'legation' => $legation,
            'attachmentPath' => $attachmentUrl
        ], 200);
    }

    public function showUserLegations(Request $request)
    {
        $user_id = auth()->id();

        $legations = Legation::where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

         $data = [];

        foreach ($legations as $legation) {

            $attachmentUrl = asset('/media/' . $legation->attachment);

            $data[] = [
                'legations' => $legation,
                'attachment' => $attachmentUrl
            ];
        }

        return response()->json([
            'legations' => $data,
        ], 200);
    }

    public function showAllLegations(Request $request)
    {
        $legations = Legation::orderBy('created_at', 'desc')->get();

        $data = [];

        foreach ($legations as $legation) {

            $attachmentUrl = asset('/media/' . $legation->attachment);

            $data[] = [
                'legations' => $legation,
                'attachment' => $attachmentUrl
            ];
        }

        return response()->json([
            'legations' => $data
        ], 200);
    }

    public function getLegationData(Request $request)//Secretary view
    {
        $legation = Legation::find($request->id);

        if ($legation) {
            $user = User::find($legation->user_id);
            $data = [
                'full_name' => $user->full_name,
                'phone_number' => $user->phone_number,
                'role' => $user->role,
                'status' => $legation->status,
                'description' => $legation->desc,
                'start_date' => $legation->start_date,
                'end_date' => $legation->end_date,
                'type' => $legation->type,
                'attachment' => asset('/media/' . $legation->attachment),
            ];
            return response()->json(['Legation data' => $data]);
        } else {
            return response()->json(['message' => 'not found']);
        }
    }
    public function acceptLegation(Request $request)
    {
        $legation = Legation::find($request->id);
        $user_formail_id = $legation->user_id;
        $user_formail = User::where('user_id', $user_formail_id)->first();

        if ($legation) {
            $legation->status = 'Accepted';
            $legation->save();

            $notification = new Notification;
            $notification->target_user = $legation->user_id;
            $notification->title = 'Accepted Legation Request';
            $notification->desc = 'Your request for a legation of type ('
            .$legation->type.'), with start date ('
            .$legation->start_date.'), and end date ('
            .$legation->end_date.') has been accepted.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new AcceptedLegationMail($data));

            return response()->json(['message' => 'accepted']);
        } else {
            return response()->json(['message' => 'not found']);
        }
    }

    public function rejectLegation(Request $request)
    {
        $legation = Legation::find($request->id);

        $user_formail_id = $legation->user_id;
        $user_formail = User::where('user_id', $user_formail_id)->first();

        if ($legation) {
            $legation->status = 'Rejected';
            $legation->save();

            $notification = new Notification;
            $notification->target_user = $legation->user_id;
            $notification->title = 'Rejected Legation Request';
            $notification->desc = 'Your request for a legation of type ('
            .$legation->type.'), with start date ('
            .$legation->start_date.'), and end date ('
            .$legation->end_date.') has been rejected.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new RejectedLegationMail($data));

            return response()->json(['message' => 'rejected']);
        } else {
            return response()->json(['message' => 'not found']);
        }
    }

/*     public function updateLegationStatus(Request $request)//Secretary view
{
    $legation = Legation::find($request->id);
    $status = $request->route('status');

    if ($legation) {
        $allowedStatuses = [ 'Accepted', 'Rejected'];
        $status = $request->status;
        if ($status && in_array($status, $allowedStatuses)) {
            $legation->status = $status;
            $legation->save();
            return response()->json(['message' => 'Legation status has been updated']);
        } else {
            return response()->json(['message' => 'Invalid status value']);
        }
    } else {
        return response()->json(['message' => 'Legation not found']);
    }
} */
    public function showLegationDept(Request $request)//Legation of user to his secretary of dept takes the token of secratory that has logged in
    {
        $user = $request->user();


        if ($user->role != 'Secretary') {
            return response()->json(['message' => 'You are not authorized to view this resource'], 403);
        }

        $legations = Legation::whereHas('user', function($query) use ($user) {
            $query->where('dept_code', $user->dept_code);
        })->orderBy('created_at', 'desc')->get();

        $data = [];

        foreach ($legations as $legation) {
            $user = User::find($legation->user_id);

            $attachmentUrl = asset('/media/' . $legation->attachment);

            $data[] = [
                'full_name' => $user->full_name,
                'Legations' => $legation,
                'attachment' => $attachmentUrl
            ];
        }

        return response()->json([
            'Legations' => $data
        ], 200);
    }
    public function getLegationStatistics(Request $request, $deptCode)
    {
        // Get the legations for the specified department in the last 6 years
        $legations = Legation::query()
        ->whereHas('user', function ($query) use ($deptCode) {
            $query->where('dept_code', $deptCode);
        })
        ->where('status', 'Accepted')
        ->where('start_date', '>=', Carbon::now()->subYears(6))
        ->selectRaw('YEAR(start_date) as year, COUNT(*) as num_legations')
        ->groupBy('year')
        ->get();

        // Initialize the statistics array
        $stats = [];

        // Populate the statistics array with legation data
        $years = range(date('Y')-5, date('Y'));
        foreach ($years as $year) {
            $numLegations = 0;
            foreach ($legations as $legation) {
                if ($legation->year == $year) {
                    $numLegations = $legation->num_legations;
                    break;
                }
        }
        $stats[] = [
            'Year' => $year,
            'NumberofLegations' => $numLegations
        ];
        }

        return response()->json($stats, 200);
        }

    public function viewLegationPDF(Request $request)
    {
        $legation = Legation::find($request->id);

        if (!$legation) {
            return response()->json(['error' => 'Legation not found'], 404);
        }

        $attachmentUrl = asset('/media/' . $legation->attachment);

        $user = User::where('user_id', $legation->user_id)->first();
        $user_name = $user->full_name;

        $legationDetails =[
            'desc' => $legation->desc,
            'start' => $legation->start_date,
            'end' => $legation->end_date,
            'type' => $legation->type,
            'user' => $user_name,
            'attachmentPath' => $attachmentUrl
        ];

        $pdf = PDF::loadView('pdf.legationPDF', ['legationDetails' => $legationDetails])
        ->setPaper('a4','portrait');

        return $pdf->stream('legation.pdf');


    }

    public function exportLegationPDF(Request $request)
    {
        $legation = Legation::find($request->id);

        if (!$legation) {
            return response()->json(['error' => 'Legation not found'], 404);
        }

        $attachmentUrl = asset('/media/' . $legation->attachment);

        $user = User::where('user_id', $legation->user_id)->first();
        $user_name = $user->full_name;

        $legationDetails =[
            'desc' => $legation->desc,
            'start' => $legation->start_date,
            'end' => $legation->end_date,
            'type' => $legation->type,
            'user' => $user_name,
            'attachmentPath' => $attachmentUrl
        ];

        $pdf = PDF::loadView('pdf.legationPDF', ['legationDetails' => $legationDetails])
        ->setPaper('a4','portrait');

        return $pdf->download($user_name.'-'.$legation->created_at.'-legation.pdf');
    }
}
