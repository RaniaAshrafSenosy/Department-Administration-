<?php

namespace App\Http\Controllers;
use App\Models\AssignedCourse;
use App\Models\User;
use App\Models\Course;
use App\Models\Notification;
use Illuminate\Http\Request;
use Mail;
use App\Mail\AssignedCourseMail;

class AssignedCoursesController extends Controller
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
    public function assignCourse(Request $request)
    {
        $request->validate([
            'dept_code' => 'required|String',
            'course_code' => 'required|String',
            'academic_year' => ['required', 'regex:/^[0-9]{4}-[0-9]{4}$/'],
            'semester' => 'required|string|in:first,second,summer,First,Second,Summer',
            'professors.*' => 'required|string',
            'teaching_Assistants.*' => 'nullable|string',
        ]);

        $professors = $request->input('professors');
        $numberOfProfessors = isset($professors) ? count($professors) : 0;

        $teachingAssistants = $request->input('teaching_Assistants');
        $numberOfteachingAssistants = isset($teachingAssistants) ? count($teachingAssistants) : 0;

        for ($i = 0; $i < $numberOfProfessors; $i++) {
            $full_name = $professors[$i];
            $user = User::where('full_name', $full_name)->first();

            if($user->is_active == false){
                return response()->json(['error' => 'this user is inctive at the moment'], 404);
            }

            if($user->is_archived == true){
                return response()->json(['error' => 'this user is archived'], 404);
            }

            $assignedCourse = new AssignedCourse;
            $assignedCourse->user_id = $user->user_id;
            $assignedCourse->course_code = $request->course_code;
            $assignedCourse->semester = $request->semester;
            $assignedCourse->academic_year = $request->academic_year;
            $assignedCourse->save();

            $course_name = Course::where('course_code', $assignedCourse->course_code)->pluck('course_name')->first();


            $notification = new Notification;
            $notification->target_user = $user->user_id;
            $notification->title = 'New Assigned Course';
            $notification->desc = 'You have been assigned a lecturer to the course '
            .$course_name
            .' for the '.$assignedCourse->semester.'  semester '
            .' in the academic year '.$assignedCourse->academic_year.', please check your Assigned Courses page.';


            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user->main_email)->send(new AssignedCourseMail($data));


        }

        for ($i = 0; $i < $numberOfteachingAssistants; $i++) {
            $full_name = $teachingAssistants[$i];
            $user = User::where('full_name', $full_name)->first();

            if($user->is_active == false){
                return response()->json(['error' => 'this user is inctive at the moment'], 404);
            }

            if($user->is_archived == true){
                return response()->json(['error' => 'this user is archived'], 404);
            }

            $assignedCourse = new AssignedCourse;
            $assignedCourse->user_id = $user->user_id;
            $assignedCourse->course_code = $request->course_code;
            $assignedCourse->semester = $request->semester;
            $assignedCourse->academic_year = $request->academic_year;
            $assignedCourse->save();

            $course_name = Course::where('course_code', $assignedCourse->course_code)->pluck('course_name')->first();

            $notification = new Notification;
            $notification->target_user = $user->user_id;
            $notification->title = 'New Assigned Course';
            $notification->desc = 'You have been assigned a lecturer to the course '
            .$course_name
            .' for the '.$assignedCourse->semester.'  semester '
            .' in the academic year '.$assignedCourse->academic_year.', please check your Assigned Courses page.';

            $notification->save();

            $data = [
                'title' => $notification->title,
                'desc' => $notification->desc
            ];

            Mail::to($user->main_email)->send(new AssignedCourseMail($data));

        }

        return response()->json([
            'message' => 'success'
        ], 201);
    }

    public function getMyAssignedCourses(Request $request)
    {
        $user_id = auth()->id();

        $assignedCourses = AssignedCourse::query()
            ->where('user_id', $user_id)
            ->orderByDesc('academic_year')
            ->orderByDesc('semester')
            ->distinct()
            ->get();

        $courseDetails = $assignedCourses->map(function ($assignedCourse) {
            $course = Course::where('course_code', $assignedCourse->course_code)->first();

            return [
                'user_id' => $assignedCourse->user_id,
                'course_code' => $assignedCourse->course_code,
                'academic_year' => $assignedCourse->academic_year,
                'semester' => $assignedCourse->semester,
                'created_at' => $assignedCourse->created_at,
                'updated_at' => $assignedCourse->updated_at,
                'course_id' => $course->id,
                'course_name' => $course->course_name,
                'credit_hours' => $course->credit_hours,
            ];
        });

        $groupedCourses = $courseDetails->groupBy(['academic_year', 'semester']);

        return response()->json([
            'message' => 'success',
            'assigned courses' => $groupedCourses
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
