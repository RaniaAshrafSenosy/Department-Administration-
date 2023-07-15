<?php

namespace App\Http\Controllers;
use App\Models\ExternalPostgraduateStudy;
use Illuminate\Http\Request;
use App\Trait\UploadMediaTrait;
use App\Models\Notification;
use App\Models\User;
use Mail;
use PDF;
use App\Mail\NewExternalPostgraduateApplicationMail;


class ExternalPostgraduateStudyController extends Controller
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
    public function CreateExternalPostgraduateApplication(Request $request)
    {

        $request->validate([
            'department' => 'required|string',
            'student_name'=>'required|string',
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
            'attachment' => 'required|file',
            'email' => 'required|email'
        ]);

        $postgradApplication = new ExternalPostgraduateStudy;
        $postgradApplication->department = $request->department;
        $postgradApplication->academic_year = $request->academic_year;
        $postgradApplication->student_name = $request->student_name;
        $postgradApplication->gender = $request->gender;
        $postgradApplication->nationality = $request->nationality;
        $postgradApplication->registration_date = $request->registration_date;
        $postgradApplication->credit_hours = $request->credit_hours;
        $postgradApplication->preliminary_date = $request->preliminary_date;
        $postgradApplication->telephone_number = $request->telephone_number;
        $postgradApplication->phone_number = $request->phone_number;

        $postgradApplication->email = $request->email;


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
            $path = $this->uploadMedia($request, 'external_postgraduates_certificates','bachelor_certificate');
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

        $postgradApplication->internal_supervisor_names = $request->input('internal_supervisor_names');
            $postgradApplication->external_supervisor_names = $request->input('external_supervisor_names');
            $postgradApplication->external_supervisor_titles = $request->input('external_supervisor_titles');


        try {
            $path = $this->uploadMedia($request, 'external_postgraduates_attachments','attachment');
            $postgradApplication->attachment = $path;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading attachment file.'], 422);
        }

        $attachmentUrl = asset('/media/' . $postgradApplication->attachment);

        $postgradApplication->save();



        $postgrad_array= [
            'id'=>$postgradApplication->id,
            'department'=>$postgradApplication->department,
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
            'research_topic_EN'=>$postgradApplication->research_topic_EN,
            'research_interest'=>$postgradApplication->research_interest,
            'target'=>$postgradApplication->target,
            'specialization'=>$postgradApplication->specialization,
            'field_of_research'=>$postgradApplication->field_of_research,
            'bachelor_certificate_path' => $cartificateUrl,
            'attachmentPath' => $attachmentUrl
        ];

        $pdf = PDF::loadView('pdf.myExternalPostgradPDF' , ['postgrad_array' => $postgrad_array]);

        $data = [
            'title' => 'New Postgrad Application Request',
            'desc' => 'Your request for a postgrad application is pending for approval, you will be notified with any update.',
            'pdf' => $pdf
        ];

        Mail::to($postgradApplication->email)->send(new NewExternalPostgraduateApplicationMail($data));


        return response()->json([
            'message'=>'success',
            'external postgraduate studies applications' => $postgradApplication,
            'cartificateUrl' => $cartificateUrl,
            'attachmentUrl' => $attachmentUrl
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
    // public function showExternalPostgraduateApplication(Request $request)
    // {
    //     $postgradApplication = ExternalPostgraduateStudy::find($request->id);

    //     if (!$postgradApplication) {
    //         return response()->json(['error' => 'Postgraduate application is not found'], 404);
    //     }

    //     $bachelor_certificate_URL = asset('/media/' . $postgradApplication->bachelor_certificate);
    //     $attachmentUrl = asset('/media/' . $postgradApplication->attachment);

    //     return response()->json([
    //         'message'=>'success',
    //         'postgraduate studies applications' => $postgradApplication,
    //         'bachelor_certificate_path' => $bachelor_certificate_URL,
    //         'attachmentPath' => $attachmentUrl
    //     ], 200);
    // }

    public function showExternalPostgraduateApplication(Request $request)
    {
        $postgradApplication = ExternalPostgraduateStudy::find($request->id);

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
            'department'=>$postgradApplication->department,
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

    public function showAllExternalPostgraduateApplications(Request $request)
    {
        $postgradApplications = ExternalPostgraduateStudy::orderBy('created_at', 'desc')->get();

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

    public function calculateAverageGrade($grades) {
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

        foreach ($grades as $grade) {
            if (isset($numeric_grades[$grade])) {
                $weight = 1;
                $sum_weighted_grades += $numeric_grades[$grade] * $weight;
                $sum_weights += $weight;
            }
        }

        if ($sum_weights > 0) {
            $average_grade = $sum_weighted_grades / $sum_weights;
            $average_grade = round($average_grade, 2);
            return $average_grade;
        } else {
            return null;
        }
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
