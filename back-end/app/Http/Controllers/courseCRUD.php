<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Course;
use App\Models\Program;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\AssignedCourse;
use App\Trait\UploadMediaTrait;
use Illuminate\Support\Facades\Storage;

class courseCRUD extends Controller
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
    public function createCourse(Request $request)
    {
        $request->validate([
            'course_code' => 'required|string',
            'prerequisites.*' => 'nullable|string',
            'credit_hours' => 'required|numeric|between:0,999.99',
            'course_name' => 'required|string',
            'course_desc' => 'required|string',
            'course_specs' => 'required|file',
            'dept_code' => 'required|string',
            'program_name' => 'string',
        ]);

        $course = new Course;

        $course_codes = Course::where('is_archived', false)
        ->pluck('course_code')
        ->toArray();

        if(!(in_array($request->course_code, $course_codes)))
        {
            $course->course_code = $request->course_code;

        }else{
            return response()->json(['error' => 'course code exist'], 404);
        }


        $course->course_name = $request->course_name;
        $course->course_desc = $request->course_desc;
        $course->credit_hours = $request->credit_hours;
        $course->prerequisites = $request->input('prerequisites');

        $departments = Department::where('is_archived', false)
        ->pluck('dept_code')
        ->toArray();

        if(in_array($request->dept_code, $departments))
        {
            $course->dept_code = $request->dept_code;
        }else{
            return response()->json(['error' => 'department code does not exist'], 404);
        }

        $programs = Program::where('is_archived', false)
        ->pluck('program_name')
        ->toArray();

        if(in_array($request->program_name, $programs))
        {
            $course->program_name = $request->program_name;
        }
        elseif(empty($request->program_name)){
            $course->program_name = null;
        }
        else{
            return response()->json(['error' => 'program name does not exist'], 404);
        }

        try {
            $path = $this->uploadMedia($request, 'courses_specs','course_specs');
            $course->course_specs = $path;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading course specifications file.'], 422);
        }

        $course_specs_Url = asset('/media/' . $course->course_specs);

        $course->save();

        return response()->json([
            'message' => 'success',
            'data' => $course,
            'course_specs_tUrl' => $course_specs_Url,
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
    public function showCourse(Request $request)
    {
        $course = Course::find($request->id);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        if ($course->is_archived == true) {
            return response()->json(['error' => 'This course is archived'], 404);
        }

        $course_specs_Url = asset('/media/' . $course->course_specs);

        return response()->json([
            'message'=>'success',
            'course' => $course,
            'course_specs_tUrl' => $course_specs_Url
        ], 200);
    }

    public function getCourseDetails(Request $request)
    {
        $course = Course::find($request->id);

        $assignedCourses = AssignedCourse::where('course_code', $course->course_code)->get();

        $course_professors = [];
        $course_TAs = [];
        $course_users = [];
        foreach ($assignedCourses as $assignedCourse) {
            $course_user_id = $assignedCourse->user_id;
            $course_user = User::where('user_id', $course_user_id)->first();

            if ($course_user->role == 'Professor' || $course_user == 'Head of Department'
                || $course_user == 'Dean' || $course_user == 'Vice Dean') {
                $course_professor = $course_user;
                $course_professors[] = [
                    $course_professor->user_id,
                    $course_professor->full_name,
                ];
            } elseif ($course_user->role == 'Teaching Assistant') {
                $course_TA = $course_user;
                $course_TAs[] = [
                    $course_TA->user_id,
                    $course_TA->full_name,
                ];
            }
        }

        $course_specs_Url = asset('/media/' . $course->course_specs);

        $courseDetals[] = [
            'Course Name' => $course->course_name,
            'Course Prerequisites' => $course->prerequisites,
            'Professors' => $course_lecturers = $course_professors,
            'Teaching Assitants' => $course_teachingAssistants = $course_TAs,
            'Course Specifications' => $course_specs_Url,
            'Course Credit Hourse' => $course->credit_hours,
            'Course Description' => $course->course_desc,
            'Course Department Code' => $course->dept_code,
            'Course Program Name' => $course->program_name,
        ];

        return response()->json([
            'message' => 'success',
            'Course Details' => $courseDetals,
        ], 200);
    }

    public function getDistinctCourseCodes(Request $request, $dept_code)
    {
        $courses = Course::where('is_archived', false)
        ->where('dept_code', $dept_code)
        ->select('course_code')
        ->get();

        return response()->json([
            'message'=>'success',
            'courses' => $courses
        ], 200);
    }

    public function showAllCourses(Request $request)
    {
        $courses = Course::where('is_archived', false)->get();

        $data = [];

        foreach ($courses as $course) {

            $course_specs_Url = asset('/media/' . $course->course_specs);

            $data[] = [
                'courses' => $course,
                'course_specs' => $course_specs_Url
            ];
        }

        return response()->json([
            'message'=>'success',
            'course_specs' => $data,
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
    public function updateCourse(Request $request)
    {
        $request->validate([
            'prerequisites' => 'nullable|string',
            'credit_hours' => 'nullable|numeric|between:0,999.99',
            'course_desc' => 'nullable|string',
            'course_specs' => 'nullable|file',
        ]);

        $course = Course::find($request->id);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        if ($course->is_archived == true) {
            return response()->json(['error' => 'This course is archived'], 404);
        }

        if ($request->has('course_desc')) {
            $course->course_desc = $request->input('course_desc');
        }

        if ($request->has('credit_hours')) {
            $course->credit_hours = $request->input('credit_hours');
        }

        if ($request->has('prerequisites')) {
            $course->prerequisites = $request->input('prerequisites');
        }

        if ($request->hasFile('course_specs')) {
            $path = $this->uploadMedia($request, 'courses_specs','course_specs');
            $course->course_specs = $path;
            $course_specs_Url = asset('/media/' . $course->course_specs);
        }else{
            $course_specs_Url = asset('/media/' . $course->course_specs);
        }

        $course->save();

        return response()->json([
            //'message' => 'User profile updated successfully!',
            'message'=>'success',
            'data' => $course,
            'course_specs_Url' => $course_specs_Url
        ], 200);
    }

    public function archiveCourse(Request $request)
    {
        $course = new Course;
        $course = Course::find($request->id);

        if (!$course) {
            return response()->json(['error' => 'course not found'], 404);
        }

        if ($course->is_archived == false) {
            $course->is_archived = true;
            $course->save();
        }
        return response()->json([
            'message' => 'The course has been archived successfully!'
        ], 200);

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
