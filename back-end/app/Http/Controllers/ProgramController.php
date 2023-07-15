<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Program;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Trait\UploadMediaTrait;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    use UploadMediaTrait;
    public function createProgram(Request $request)
    {
        $request->validate([

            'program_name' => 'string',
            'program_desc' => 'required|string',
            'program_head' => 'string',
            'booklet' => 'required|file',
            'bylaw' => 'required|file',
            'dept_code' => 'required|string'
        ]);
        $program = new Program;

        $departments = Department::where('is_archived', false)
        ->pluck('dept_code')
        ->toArray();

        if(in_array($request->dept_code, $departments))
        {
            $program->dept_code = $request->dept_code;
        }else{
            return response()->json(['error' => 'department code does not exist'], 200);
        }

        // $program->program_name = $request->program_name;
        $programs_names = Program::where('is_archived', false)
        ->pluck('program_name')
        ->toArray();

        if(!(in_array($request->program_name, $programs_names)))
        {
            $program->program_name = $request->program_name;
        }else{
            return response()->json(['message' => 'Program Name Already Exist'], 200);
        }
        $program->program_desc = $request->program_desc;
        $program->program_head = '';

        try {
            $path = $this->uploadMedia($request, 'program_booklets','booklet');
            $program->booklet = $path;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading program booklet file.'], 200);
        }

        $booklet_Url = asset('/media/' . $program->booklet);

        try {
            $path = $this->uploadMedia($request, 'program_bylaws','bylaw');
            $program->bylaw = $path;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading program bylaw file.'], 200);
        }

        $bylaw_Url = asset('/media/' . $program->bylaw);

        $program->save();


        return response()->json([
            'message'=>'success',
            'data' => $program,
            'booklet_Url' => $booklet_Url,
            'bylaw_Url' => $bylaw_Url
        ], 201);
    }

    public function updateProgram(Request $request)
    {
        $request->validate([
            'program_name' => 'nullable|string',
            'program_desc' => 'nullable|string',
            'program_head' => 'nullable|string',
            'booklet' => 'nullable|file',
            'bylaw' => 'nullable|file'
        ]);

        $program = Program::find($request->id);

        if (!$program) {
            return response()->json(['error' => 'program not found'], 200);
        }

        if ($program->is_archived == true) {
            return response()->json(['error' => 'This program is archived'], 200);
        }

        if ($request->has('program_desc')) {
            $program->program_desc = $request->input('program_desc');
        }
        if ($request->has('program_head')) {
            $program->program_head = $request->input('program_head');
            $head_user = User::where('full_name', $request->program_head)->first();
            if($head_user){
                $head_user->role = 'Head of program';
                $head_user->save();
                $program->head_id = $head_user->user_id;

            }
        }

        if ($request->hasFile('booklet')) {
            $path = $this->uploadMedia($request, 'dept_booklets','booklet');
            $program->booklet = $path;
            $booklet_Url = asset('/media/' . $program->booklet);
        }else{
            $booklet_Url = asset('/media/' . $program->booklet);
        }

        if ($request->hasFile('bylaw')) {
            $path = $this->uploadMedia($request, 'dept_bylaws','bylaw');
            $program->bylaw = $path;
            $bylaw_Url = asset('/media/' . $program->bylaw);
        }else{
            $bylaw_Url = asset('/media/' . $program->bylaw);
        }


        $program->save();

        return response()->json([
            'message'=>'success',
            'data' => $program,
            'booklet_Url' => $booklet_Url,
            'bylaw_Url' => $bylaw_Url
        ], 200);
    }

    public function showProgram(Request $request)
    {
        $program = Program::find($request->id);

        if (!$program) {
            return response()->json(['error' => 'Program not found'], 200);
        }

        if ($program->is_archived == true) {
            return response()->json(['error' => 'This Program is archived'], 404);
        }

        $booklet_URL = asset('/media/' . $program->booklet);
        $bylaw_URL = asset('/media/' . $program->bylaw);

        return response()->json([
            'message'=>'success',
            'program' => $program,
            'bookletURL' => $booklet_URL,
            'bylawURL' => $bylaw_URL,
        ], 200);
    }

    public function getDistinctProgramName(Request $request)
    {
        $programs = Program::where('is_archived', false)
        ->select('program_name')
        ->get();

        return response()->json([
            'message'=>'success',
            'programs_codes' => $programs,
        ], 200);
    }

    public function showAllPrograms(Request $request)
    {
        $programs = Program::where('is_archived', false)
        //->select('id', 'program_name', 'program_desc', 'program_head')
         ->orderByRaw('program_name')
         ->get();

        return response()->json([
            'message'=>'success',
            'programs' => $programs
        ], 200);
    }

    public function getDeptCourses(Request $request,$id){

        $program = DB::table('programs')
        ->where('programs.id',$request->id)
        ->where('programs.is_archived', false)
        ->leftJoin('courses', function ($join) {
            $join->on('programs.program_name', '=', 'courses.program_name')
                ->where('courses.is_archived', false);
        })
        ->select('courses.course_name','courses.id as course_id')
        ->orderByRaw('courses.course_id')
        ->get();

        return response()->json([
            'courses' => $program
        ], 200);
    }

    public function archiveProgram(Request $request)
    {
        $program = new Program;
        $program = Program::find($request->id);

        if (!$program) {
            return response()->json(['error' => 'program not found'], 404);
        }

        if ($program->is_archived == false) {
            $program->is_archived = true;
            $program->save();
        }
        return response()->json([
            'message' => 'success'
        ], 200);

    }
}
