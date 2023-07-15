<?php

namespace App\Http\Controllers;
use App\Models\PostgraduateStudy;
use App\Models\User;
use App\Models\ExternalPostgraduateStudy;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Trait\UploadMediaTrait;
use App\Models\Notification;
use Mail;
use App\Mail\NewPostgraduateApplicationMail;
use PDF;

class PostgraduateStudiesController extends Controller
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

    public function CreatePostgraduateApplication(Request $request)
    {
        $request->validate([
            'academic_year' => ['required', 'regex:/^[0-9]{4}-[0-9]{4}$/'],
            'gender' => 'required|string|in:male,Male,female,Female',
            'nationality' => 'required|string',
            'registration_date' => 'required|date_format:Y-m-d',
            'credit_hours' => 'required|numeric|between:0,999.99',
            'preliminary_date' => 'required|date_format:Y-m-d',
            'telephone_number' => 'required|string',
            'phone_number' => 'required|string',
            'employer' => 'nullable|string',
            'employer_address' => 'nullable|string',
            'bachelor_certificate' => 'required|file',
            'grade' => 'required|string',
            'faculty_name' => 'required|string',
            'graduation_date' => 'required|date_format:Y-m-d',
            'university_name' => 'required|string',
            'research_topic_AR' => 'required|string',
            'research_topic_EN' => 'required|string',
            'research_interest' => 'required|string',
            'target' => 'required|string',
            'specialization' => 'required|string',
            'field_of_research' => 'required|string',
            'internal_supervisor_names.*'=> 'required|string',
            'external_supervisor_names.*'=> 'nullable|string',
            'external_supervisor_titles.*'=> 'nullable|string',
            'attachment' => 'required|file'
        ]);

        $postgradApplication = new PostgraduateStudy;
        $postgradApplication->user_id = auth()->id();

        $user_id = $postgradApplication->user_id;
        $user_formail = User::where('user_id', $user_id)->first();

        $applicant = User::find($postgradApplication->user_id);

        $postgradApplication->dept_code = $applicant->dept_code;
        $postgradApplication->academic_year = $request->academic_year;

        $postgradApplication->student_name = $applicant->full_name;
        $postgradApplication->gender = $request->gender;
        $postgradApplication->nationality = $request->nationality;
        $postgradApplication->registration_date = $request->registration_date;
        $postgradApplication->credit_hours = $request->credit_hours;
        $postgradApplication->preliminary_date = $request->preliminary_date;
        $postgradApplication->telephone_number = $request->telephone_number;
        $postgradApplication->phone_number = $request->phone_number;


        if ($request->has('employer')) {
            $postgradApplication->employer = $request->input('employer');
        } else {
            $postgradApplication->employer = null;
        }

        if ($request->has('employer_address')) {
            $postgradApplication->employer_address = $request->input('employer_address');
        } else {
            $postgradApplication->employer_address = null;
        }

        try {
            $path = $this->uploadMedia($request, 'postgraduates_certificates','bachelor_certificate');
            $postgradApplication->bachelor_certificate = $path;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading bachelor certificate file.'], 422);
        }
        $cartificateUrl = asset('/media/' . $postgradApplication->bachelor_certificate);

        $postgradApplication->grade = $request->grade;
        $postgradApplication->faculty_name = $request->faculty_name;
        $postgradApplication->graduation_date = $request->graduation_date;
        $postgradApplication->university_name = $request->university_name;
        $postgradApplication->research_topic_AR = $request->research_topic_AR;
        $postgradApplication->research_topic_EN = $request->research_topic_EN;
        $postgradApplication->research_interest = $request->research_interest;
        $postgradApplication->target = $request->target;
        $postgradApplication->specialization = $request->specialization;
        $postgradApplication->field_of_research = $request->field_of_research;

        $postgradApplication->internal_supervisor_names = (array) $request->input('internal_supervisor_names');
        $internal_supervisor_names = $postgradApplication->internal_supervisor_names;
        $internal_supervisor_titles = [];
        foreach($internal_supervisor_names as $internal_supervisor_name)
        {
            $internal_professor = User::where('full_name', $internal_supervisor_name)->first();

            $internal_supervisor_titles []= $internal_professor->title;
        }

        ///get internal supervisors department
        $internal_supervisor_dept_codes = [];
        foreach($internal_supervisor_names as $internal_supervisor_name)
        {
            $internal_professor_code = User::where('full_name', $internal_supervisor_name)
            ->select('dept_code')
            ->first();
            $internal_supervisor_dept_codes []= $internal_professor_code->dept_code;

        }

        $numberOfIterations = isset($internal_supervisor_titles) ? count($internal_supervisor_titles) : 0;

        ///get internal supervisors ranks
        $ranks = [];
        for ($i=0; $i < $numberOfIterations; $i++) {

            if($internal_supervisor_titles[$i] == 'Professor'){
                $ranks []= 3;
            }
            else if($internal_supervisor_titles[$i] == 'Associate Professor'){
                $ranks []= 2;
            }
            else if($internal_supervisor_titles[$i] == 'Assistant Professor'){
                $ranks []= 1;
            }
        }

        ///check the highest is in my dept among internals
        $indexes = array_keys($ranks, max($ranks));
        $highest_rank_in_my_dept = $ranks[$indexes[0]];
        $numberOfIterations = isset($indexes) ? count($indexes) : 0;
        for ($i=0; $i < $numberOfIterations; $i++) {
            if($internal_supervisor_dept_codes[$indexes[$i]] != $applicant->dept_code){

                return response()->json([
                    'message'=>'The internal supervisor with the highest rank should be from your department',
                     ], 200);
            }
        }

        $postgradApplication->external_supervisor_names = $request->input('external_supervisor_names');
        $external_supervisor_names = $postgradApplication->external_supervisor_names;
        $postgradApplication->external_supervisor_titles = (array) $request->input('external_supervisor_titles');
        $external_supervisor_titles = $postgradApplication->external_supervisor_titles;

        $numberOfIterations = isset($external_supervisor_titles) ? count($external_supervisor_titles) : 0;

        ///get external supervisors ranks
        $externals_ranks = [];
        for ($i=0; $i < $numberOfIterations; $i++) {

            if($external_supervisor_titles[$i] == 'Professor'){
                $externals_ranks []= 3;
            }
            else if($external_supervisor_titles[$i] == 'Associate Professor'){
                $externals_ranks []= 2;
            }
            else if($external_supervisor_titles[$i] == 'Assistant Professor'){
                $externals_ranks []= 1;
            }
        }

        for ($i=0; $i < $numberOfIterations; $i++) {
            if($externals_ranks[$i] >= $highest_rank_in_my_dept){
                return response()->json([
                    'message'=>'You cannot have an external supervisor with a rank higher than the internal supervisor from your department',
                     ], 200);
            }
        }

        try {
            $path = $this->uploadMedia($request, 'postgraduates_attachments','attachment');
            $postgradApplication->attachment = $path;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading attachment file.'], 422);
        }

        $attachmentUrl = asset('/media/' . $postgradApplication->attachment);

        $postgradApplication->save();

        $notification = new Notification;
            $notification->target_user = $postgradApplication->user_id;
            $notification->title = 'New Postgrad Application Request';
            $notification->desc = 'Your request for a postgrad application is pending for approval, you will be notified with any update.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user_formail->main_email)->send(new NewPostgraduateApplicationMail($data));

        return response()->json([
            'message'=>'success',
            'postgraduate studies applications' => $postgradApplication,
            'bachelor_certificate_path' => $cartificateUrl,
            'attachmentPath' => $attachmentUrl
        ], 201);
    }

    public function showPostgraduateApplication(Request $request)
    {
        $postgradApplication = PostGraduateStudy::find($request->id);

        if (!$postgradApplication) {
            return response()->json(['error' => 'Postgraduate application is not found'], 404);
        }

        $bachelor_certificate_URL = asset('/media/' . $postgradApplication->bachelor_certificate);
        $attachmentUrl = asset('/media/' . $postgradApplication->attachment);

        $numberOfIterations = isset($postgradApplication->internal_supervisor_names) ? count($postgradApplication->internal_supervisor_names) : 0;

        $internal_supervisor_titles = [];
        foreach($postgradApplication->internal_supervisor_names as $internal_supervisor_name)
        {
            $internal_professor = User::where('full_name', $internal_supervisor_name)->first();
            $internal_supervisor_titles []= $internal_professor->title;

        }

        $internal_supervisors = [];

        for ($i=0; $i < $numberOfIterations ; $i++) {

            $internal_supervisors []= [
                "internal_supervisor_name"=>$postgradApplication->internal_supervisor_names[$i],
                "internal_supervisor_title"=>$internal_supervisor_titles[$i],
            ];
        }

        $numberOfIterations = isset($postgradApplication->external_supervisor_names) ? count($postgradApplication->external_supervisor_names) : 0;

        $external_supervisors = [];

        for ($i=0; $i < $numberOfIterations ; $i++) {

            $external_supervisors []= [
                "external_supervisor_name"=>$postgradApplication->external_supervisor_names[$i],
                "external_supervisor_title"=>$postgradApplication->external_supervisor_titles[$i],
            ];
        }

        $postgrad_array = [];
        $postgrad_array[] = [
            'id'=>$postgradApplication->id,
            'dept_code'=>$postgradApplication->dept_code,
            'academic_year'=>$postgradApplication->academic_year,
            'student_name'=>$postgradApplication->student_name,
            'gender'=>$postgradApplication->gender,
            'nationality'=>$postgradApplication->nationality,
            'registration_date'=>$postgradApplication->registration_date,
            'credit_hours'=>$postgradApplication->credit_hours,
            'preliminary_date'=>$postgradApplication->preliminary_date,
            'telephone_number'=>$postgradApplication->telephone_number,
            'phone_number'=>$postgradApplication->phone_number,
            'employer'=>$postgradApplication->employer,
            'employer_address'=>$postgradApplication->employer_address,
            'grade'=>$postgradApplication->grade,
            'faculty_name'=>$postgradApplication->faculty_name,
            'bachelor_certificate' => asset('/media/' . $postgradApplication->bachelor_certificate),
            'attachment' => asset('/media/' . $postgradApplication->attachment),
            'graduation_date'=>$postgradApplication->graduation_date,
            'university_name'=>$postgradApplication->university_name,
            'research_topic_AR'=>$postgradApplication->research_topic_AR,
            'research_topic_EN'=>$postgradApplication->research_topic_EN,
            'research_interest'=>$postgradApplication->research_interest,
            'target'=>$postgradApplication->target,
            'specialization'=>$postgradApplication->specialization,
            'field_of_research'=>$postgradApplication->field_of_research,
            'internal_supervisors' => $internal_supervisors,
            'external_supervisors' => $external_supervisors,
            'user_id' => $postgradApplication->user_id,
            'user_id' => $postgradApplication->user_id,
            'bachelor_certificate_path' => $bachelor_certificate_URL,
            'attachmentPath' => $attachmentUrl,
            'remember_token'=>$postgradApplication->remember_token,
            'created_at'=>$postgradApplication->created_at,
            'updated_at'=>$postgradApplication->updated_at
        ];

        return response()->json([
            'message'=>'success',
            'postgraduate studies applications' => $postgrad_array,
        ], 200);
    }

    public function showUserPostgraduateApplications(Request $request)
    {
        $user_id = auth()->id();

        $postgradApplications = PostGraduateStudy::where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

         $data = [];

         foreach ($postgradApplications as $postgradApplication) {

            $bachelor_certificate_URL = asset('/media/' . $postgradApplication->bachelor_certificate);
            $attachmentUrl = asset('/media/' . $postgradApplication->attachment);

            $numberOfIterations = isset($postgradApplication->internal_supervisor_names) ? count($postgradApplication->internal_supervisor_names) : 0;

            $internal_supervisor_titles = [];
            foreach($postgradApplication->internal_supervisor_names as $internal_supervisor_name)
            {
                $internal_professor = User::where('full_name', $internal_supervisor_name)->first();
                $internal_supervisor_titles []= $internal_professor->title;

            }

            $internal_supervisors = [];

            for ($i=0; $i < $numberOfIterations ; $i++) {

                $internal_supervisors []= [
                    "internal_supervisor_name"=>$postgradApplication->internal_supervisor_names[$i],
                    "internal_supervisor_title"=>$internal_supervisor_titles[$i],
                ];
            }

            $numberOfIterations = isset($postgradApplication->external_supervisor_names) ? count($postgradApplication->external_supervisor_names) : 0;

            $external_supervisors = [];

            for ($i=0; $i < $numberOfIterations ; $i++) {

                $external_supervisors []= [
                    "external_supervisor_name"=>$postgradApplication->external_supervisor_names[$i],
                    "external_supervisor_title"=>$postgradApplication->external_supervisor_titles[$i],
                ];
            }

            $postgrad_array = [];
            $postgrad_array[] = [
                'id'=>$postgradApplication->id,
                'dept_code'=>$postgradApplication->dept_code,
                'academic_year'=>$postgradApplication->academic_year,
                'student_name'=>$postgradApplication->student_name,
                'gender'=>$postgradApplication->gender,
                'nationality'=>$postgradApplication->nationality,
                'registration_date'=>$postgradApplication->registration_date,
                'credit_hours'=>$postgradApplication->credit_hours,
                'preliminary_date'=>$postgradApplication->preliminary_date,
                'telephone_number'=>$postgradApplication->telephone_number,
                'phone_number'=>$postgradApplication->phone_number,
                'employer'=>$postgradApplication->employer,
                'employer_address'=>$postgradApplication->employer_address,
                'grade'=>$postgradApplication->grade,
                'faculty_name'=>$postgradApplication->faculty_name,
                'bachelor_certificate' => asset('/media/' . $postgradApplication->bachelor_certificate),
                'attachment' => asset('/media/' . $postgradApplication->attachment),
                'graduation_date'=>$postgradApplication->graduation_date,
                'university_name'=>$postgradApplication->university_name,
                'research_topic_AR'=>$postgradApplication->research_topic_AR,
                'research_topic_EN'=>$postgradApplication->research_topic_EN,
                'research_interest'=>$postgradApplication->research_interest,
                'target'=>$postgradApplication->target,
                'specialization'=>$postgradApplication->specialization,
                'field_of_research'=>$postgradApplication->field_of_research,
                'internal_supervisors' => $internal_supervisors,
                'external_supervisors' => $external_supervisors,
                'user_id' => $postgradApplication->user_id,
                'user_id' => $postgradApplication->user_id,
                'bachelor_certificate_path' => $bachelor_certificate_URL,
                'attachmentPath' => $attachmentUrl,
                'remember_token'=>$postgradApplication->remember_token,
                'created_at'=>$postgradApplication->created_at,
                'updated_at'=>$postgradApplication->updated_at
            ];

            $data []= [
                $postgrad_array
            ];
        }

        return response()->json([
            'message'=>'success',
            'data' => $data,
        ], 200);
    }

    public function showAllPostgraduateApplications(Request $request)
    {
        $postgradApplications = PostgraduateStudy::orderBy('created_at', 'desc')->get();

        $data = [];

        foreach ($postgradApplications as $postgradApplication) {

            $bachelor_certificate_URL = asset('/media/' . $postgradApplication->bachelor_certificate);
            $attachmentUrl = asset('/media/' . $postgradApplication->attachment);

            $data[] = [
                'postgraduate studies application' => $postgradApplication,
                'bachelor certificate' => $bachelor_certificate_URL,
                'attachment' => $attachmentUrl,
            ];
        }

        return response()->json([
            'message'=>'success',
            'data' => $data,
        ], 200);
    }

    public function showAllMyPostgraduates(Request $request){

        $professor = new User;
        $professor->user_id = auth()->id();

        $professor_name = User::where('user_id', $professor->user_id )
        ->select('full_name')
        ->first();

        $internal_postgrads = PostgraduateStudy::all();
        $external_postgrads = ExternalPostgraduateStudy::all();

        $my_internals = [];
        foreach($internal_postgrads as $internal_postgrad)
        {
            $numberOfIterations = isset($internal_postgrad->internal_supervisor_names) ? count($internal_postgrad->internal_supervisor_names) : 0;

            for ($i=0; $i < $numberOfIterations; $i++) {
                if($internal_postgrad->internal_supervisor_names[$i] == $professor_name->full_name){
                    $my_internals[] = $internal_postgrad;
                }
            }
        }


        $my_externals = [];
        foreach($external_postgrads as $external_postgrad)
        {
            $numberOfIterations = isset($internal_postgrad->internal_supervisor_names) ? count($external_postgrad->internal_supervisor_names) : 0;

            for ($i=0; $i < $numberOfIterations; $i++) {
                if($external_postgrad->internal_supervisor_names[$i] == $professor_name->full_name){
                    $my_externals[] = $external_postgrad;
                }
            }
        }

        $all_postgrads = array_merge($my_internals, $my_externals);

        return response()->json([
            'message' => 'success',
            'all postgrads' => $all_postgrads,
        ], 200);

    }

    public function getMyPostgraduatesAverageGrades(Request $request){

        $request->validate([
            'academic_year' => ['required', 'regex:/^[0-9]{4}-[0-9]{4}$/']
        ]);

        $academic_year = $request->academic_year;

        $professor = new User;
        $professor->user_id = auth()->id();

        $professor_name = User::where('user_id', $professor->user_id )
        ->select('full_name')
        ->first();

        $internal_postgrads = PostgraduateStudy::where('academic_year', $academic_year)->get();
        $external_postgrads = ExternalPostgraduateStudy::where('academic_year', $academic_year)->get();

        $my_internals = [];
        foreach($internal_postgrads as $internal_postgrad)
        {
            $numberOfIterations = isset($internal_postgrad->internal_supervisor_names) ? count($internal_postgrad->internal_supervisor_names) : 0;

            for ($i=0; $i < $numberOfIterations; $i++) {
                if($internal_postgrad->internal_supervisor_names[$i] == $professor_name->full_name){
                    $my_internals[] = $internal_postgrad->grade;
                }
            }
        }

        $my_externals = [];
        foreach($external_postgrads as $external_postgrad)
        {
            $numberOfIterations = isset($internal_postgrad->internal_supervisor_names) ? count($external_postgrad->internal_supervisor_names) : 0;

            for ($i=0; $i < $numberOfIterations; $i++) {
                if($external_postgrad->internal_supervisor_names[$i] == $professor_name->full_name){
                    $my_externals[] = $external_postgrad->grade;
                }
            }
        }

        $all_postgrads = array_merge($my_internals, $my_externals);
        $average_grades;

        $numeric_grades = [
            'A+' => 4.0,
            'A' => 3.70,
            'B+' => 3.30,
            'B' => 3.00,
            'C+' => 2.70,
            'C' => 2.40,
            'D+' => 2.20,
            'D' => 2.00,
            'F' => 0.00,
        ];

        $sum_weighted_grades = 0;
        $sum_weights = 0;

        foreach ($all_postgrads as $all_postgrad) {
            if (isset($numeric_grades[$all_postgrad])) {
                $weight = 1;
                $sum_weighted_grades += $numeric_grades[$all_postgrad] * $weight;
                $sum_weights += $weight;
            }
        }

        if ($sum_weights > 0) {
            $average_grade = $sum_weighted_grades / $sum_weights;
            $average_grade = round($average_grade, 2);

            if($average_grade >= 3.6 && $average_grade <= 4){

                $average_grades = 'A+';

            }else if($average_grade >= 3.4 && $average_grade <= 3.6){

                $average_grades = 'A';

            }else if($average_grade >= 3.2 && $average_grade <= 3.4){

                $average_grades = 'B+';

            }else if($average_grade >= 3 && $average_grade <= 3.2){

                $average_grades = 'B';

            }else if($average_grade >= 2.8 && $average_grade <= 3){

                $average_grades = 'C+';

            }else if($average_grade >= 2.6 && $average_grade <= 2.8){

                $average_grades = 'C';

            }else if($average_grade >= 2.4 && $average_grade <= 2.6){

                $average_grades = 'D+';

            }else if($average_grade >= 2 && $average_grade <= 2.4){

                $average_grades = 'D';

            }else if( $average_grade < 2){

                $average_grades = 'F';

            }

            $average = [];
            $average [] = [
                'average_grade_in_letters' => $average_grade,
                'average_grad_in_numbers' => $average_grades,
            ];

            return response()->json([
                'message' => 'success',
                'average_grade' => $average
            ], 200);

        } else {
            $average = [];
            $average [] = [
                'average_grade_in_letters' => null,
                'average_grad_in_numbers' => 0,
            ];

            return response()->json([
                'message' => 'success',
                'average_grade' => $average,
            ], 200);
        }


    }

    public function viewPostgradPDF(Request $request)
    {
        $postgradApplication = PostGraduateStudy::find($request->id);

        if (!$postgradApplication) {
            return response()->json(['error' => 'Postgraduate application is not found'], 404);
        }

        $bachelor_certificate_URL = asset('/media/' . $postgradApplication->bachelor_certificate);
        $attachmentUrl = asset('/media/' . $postgradApplication->attachment);

        $numberOfIterations = isset($postgradApplication->internal_supervisor_names) ? count($postgradApplication->internal_supervisor_names) : 0;

        $internal_supervisor_titles = [];
        foreach($postgradApplication->internal_supervisor_names as $internal_supervisor_name)
        {
            $internal_professor = User::where('full_name', $internal_supervisor_name)->first();
            $internal_supervisor_titles []= $internal_professor->title;

        }

        $internal_supervisors = [];

        for ($i=0; $i < $numberOfIterations ; $i++) {

            $internal_supervisors []= [
                "internal_supervisor_name"=>$postgradApplication->internal_supervisor_names[$i],
                "internal_supervisor_title"=>$internal_supervisor_titles[$i],
            ];
        }

        $numberOfIterations = isset($postgradApplication->external_supervisor_names) ? count($postgradApplication->external_supervisor_names) : 0;

        $external_supervisors = [];

        for ($i=0; $i < $numberOfIterations ; $i++) {

            $external_supervisors []= [
                "external_supervisor_name"=>$postgradApplication->external_supervisor_names[$i],
                "external_supervisor_title"=>$postgradApplication->external_supervisor_titles[$i],
            ];
        }
        $postgrad_array = [
            'dept_code'=>$postgradApplication->dept_code,
            'academic_year'=>$postgradApplication->academic_year,
            'student_name'=>$postgradApplication->student_name,
            'gender'=>$postgradApplication->gender,
            'nationality'=>$postgradApplication->nationality,
            'registration_date'=>$postgradApplication->registration_date,
            'credit_hours'=>$postgradApplication->credit_hours,
            'preliminary_date'=>$postgradApplication->preliminary_date,
            'telephone_number'=>$postgradApplication->telephone_number,
            'phone_number'=>$postgradApplication->phone_number,
            'employer'=>$postgradApplication->employer,
            'employer_address'=>$postgradApplication->employer_address,
            'grade'=>$postgradApplication->grade,
            'faculty_name'=>$postgradApplication->faculty_name,
            'graduation_date'=>$postgradApplication->graduation_date,
            'university_name'=>$postgradApplication->university_name,
            // 'research_topic_AR'=>$postgradApplication->research_topic_AR,
            'research_topic_EN'=>$postgradApplication->research_topic_EN,
            'research_interest'=>$postgradApplication->research_interest,
            'target'=>$postgradApplication->target,
            'specialization'=>$postgradApplication->specialization,
            'field_of_research'=>$postgradApplication->field_of_research,
            // 'internal_supervisors' => $internal_supervisors,
            // 'external_supervisors' => $external_supervisors,
            'user_id' => $postgradApplication->user_id,
            'user_id' => $postgradApplication->user_id,
            'bachelor_certificate_path' => $bachelor_certificate_URL,
            'attachmentPath' => $attachmentUrl
        ];


        $pdf = PDF::loadView('pdf.postgradPDF', ['postgrad_array' => $postgrad_array])
        ->setPaper('a4','portrait');

        return $pdf->stream('postgrad.pdf');
    }


    public function exportPostgradPDF(Request $request)
    {
        $postgradApplication = PostGraduateStudy::find($request->id);

        if (!$postgradApplication) {
            return response()->json(['error' => 'Postgraduate application is not found'], 404);
        }

        $bachelor_certificate_URL = asset('/media/' . $postgradApplication->bachelor_certificate);
        $attachmentUrl = asset('/media/' . $postgradApplication->attachment);

        $numberOfIterations = isset($postgradApplication->internal_supervisor_names) ? count($postgradApplication->internal_supervisor_names) : 0;

        $internal_supervisor_titles = [];
        foreach($postgradApplication->internal_supervisor_names as $internal_supervisor_name)
        {
            $internal_professor = User::where('full_name', $internal_supervisor_name)->first();
            $internal_supervisor_titles []= $internal_professor->title;

        }

        $internal_supervisors = [];

        for ($i=0; $i < $numberOfIterations ; $i++) {

            $internal_supervisors []= [
                "internal_supervisor_name"=>$postgradApplication->internal_supervisor_names[$i],
                "internal_supervisor_title"=>$internal_supervisor_titles[$i],
            ];
        }

        $numberOfIterations = isset($postgradApplication->external_supervisor_names) ? count($postgradApplication->external_supervisor_names) : 0;

        $external_supervisors = [];

        for ($i=0; $i < $numberOfIterations ; $i++) {

            $external_supervisors []= [
                "external_supervisor_name"=>$postgradApplication->external_supervisor_names[$i],
                "external_supervisor_title"=>$postgradApplication->external_supervisor_titles[$i],
            ];
        }
        $postgrad_array = [
            'dept_code'=>$postgradApplication->dept_code,
            'academic_year'=>$postgradApplication->academic_year,
            'student_name'=>$postgradApplication->student_name,
            'gender'=>$postgradApplication->gender,
            'nationality'=>$postgradApplication->nationality,
            'registration_date'=>$postgradApplication->registration_date,
            'credit_hours'=>$postgradApplication->credit_hours,
            'preliminary_date'=>$postgradApplication->preliminary_date,
            'telephone_number'=>$postgradApplication->telephone_number,
            'phone_number'=>$postgradApplication->phone_number,
            'employer'=>$postgradApplication->employer,
            'employer_address'=>$postgradApplication->employer_address,
            'grade'=>$postgradApplication->grade,
            'faculty_name'=>$postgradApplication->faculty_name,
            'graduation_date'=>$postgradApplication->graduation_date,
            'university_name'=>$postgradApplication->university_name,
            // 'research_topic_AR'=>$postgradApplication->research_topic_AR,
            'research_topic_EN'=>$postgradApplication->research_topic_EN,
            'research_interest'=>$postgradApplication->research_interest,
            'target'=>$postgradApplication->target,
            'specialization'=>$postgradApplication->specialization,
            'field_of_research'=>$postgradApplication->field_of_research,
            // 'internal_supervisors' => $internal_supervisors,
            // 'external_supervisors' => $external_supervisors,
            'user_id' => $postgradApplication->user_id,
            'user_id' => $postgradApplication->user_id,
            'bachelor_certificate_path' => $bachelor_certificate_URL,
            'attachmentPath' => $attachmentUrl
        ];


        $pdf = PDF::loadView('pdf.postgradPDF', ['postgrad_array' => $postgrad_array])
        ->setPaper('a4','portrait');

        return $pdf->download($postgradApplication->student_name.'-'.$postgradApplication->created_at.'-postgrad.pdf');
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
