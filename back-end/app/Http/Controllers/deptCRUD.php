<?php

namespace App\Http\Controllers;
use App\Models\Department;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Trait\UploadMediaTrait;

class deptCRUD extends Controller
{
    use UploadMediaTrait;
    public function createDepartment(Request $request)
    {
        $request->validate([
            'dept_code' => 'required|string',
            'dept_name' => 'required|string',
            'desc' => 'required|string',
            // 'head' => 'required|string',
            'booklet' => 'required|file',
            'bylaw' => 'required|file',
        ]);

        $department = new Department;

        $department_code = Department::where('is_archived', false)
        ->pluck('dept_code')
        ->toArray();

        if(!(in_array($request->dept_code, $department_code)))
        {
            $department->dept_code = $request->dept_code;
        }else{
            return response()->json(['message' => 'department code exist'], 200);
        }

        $department_names = Department::where('is_archived', false)
        ->pluck('dept_name')
        ->toArray();

        if(!(in_array($request->dept_name, $department_names)))
        {
            $department->dept_name = $request->dept_name;
        }else{
            return response()->json(['message' => 'department name exist'], 200);
        }

        $department->desc = $request->desc;
        $department->head = '';

        // $head_user = User::where('full_name', $request->head)->first();
        // if($head_user){
        //     $head_user->role = 'Head of Department';
        //     $head_user->save();
        // }

        try {
            $path = $this->uploadMedia($request, 'dept_booklets','booklet');
            $department->booklet = $path;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading department booklet file.'], 422);
        }

        $booklet_Url = asset('/media/' . $department->booklet);

        try {
            $path = $this->uploadMedia($request, 'dept_bylaws','bylaw');
            $department->bylaw = $path;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading department bylaw file.'], 422);
        }

        $bylaw_Url = asset('/media/' . $department->bylaw);

        $department->save();

       // return redirect()->back()->with('message','File Uploaded Successfully');

        return response()->json([
            'message'=>'success',
            'data' => $department,
            'booklet_Url' => $booklet_Url,
            'bylaw_Url' => $bylaw_Url
        ], 201);
    }

    public function updateDepartment(Request $request)
    {
        $request->validate([
            'desc' => 'nullable|string',
            'head' => 'nullable|string',
            'booklet' => 'nullable|file',
            'bylaw' => 'nullable|file',
        ]);

        $department = Department::find($request->id);

        if (!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }

        if ($department->is_archived == true) {
            return response()->json(['error' => 'This department is archived'], 404);
        }

        if ($request->has('desc')) {
            $department->desc = $request->input('desc');
        }
        if ($request->has('head')) {
            $department->head = $request->input('head');
            $head_user = User::where('full_name', $request->head)->first();
            if($head_user){
                $head_user->role = 'Head of Department';
                $head_user->save();
                $department->head_id = $head_user->user_id;

            }
        }

        if ($request->hasFile('booklet')) {
            $path = $this->uploadMedia($request, 'dept_booklets','booklet');
            $department->booklet = $path;
            $booklet_Url = asset('/media/' . $department->booklet);
        }else{
            $booklet_Url = asset('/media/' . $department->booklet);
        }

        if ($request->hasFile('bylaw')) {
            $path = $this->uploadMedia($request, 'dept_bylaws','bylaw');
            $department->bylaw = $path;
            $bylaw_Url = asset('/media/' . $department->bylaw);
        }else{
            $bylaw_Url = asset('/media/' . $department->bylaw);
        }


        $department->save();

        return response()->json([
            //'message' => 'User profile updated successfully!',
            'message'=>'success',
            'data' => $department,
            'booklet_Url' => $booklet_Url,
            'bylaw_Url' => $bylaw_Url
        ], 200);
    }
    public function showDepartment(Request $request)
    {
        $department = Department::find($request->id);

        if (!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }

        if ($department->is_archived == true) {
            return response()->json(['error' => 'This department is archived'], 404);
        }

        $booklet_URL = asset('/media/' . $department->booklet);
        $bylaw_URL = asset('/media/' . $department->bylaw);

        return response()->json([
            'message'=>'success',
            'department' => $department,
            'bookletURL' => $booklet_URL,
            'bylawURL' => $bylaw_URL,
        ], 200);
    }

    public function getDistinctDeptCodes(Request $request)
    {
        $departments = Department::where('is_archived', false)
        ->select('dept_code')
        ->get();

        return response()->json([
            'message'=>'success',
            'departments_codes' => $departments,
        ], 200);
    }

    public function showAllDepts(Request $request)
    {
        $departments = Department::where('is_archived', false)
        ->select('id', 'dept_code', 'dept_name', 'head')
         ->orderByRaw("FIELD(dept_code, 'CS', 'IT', 'IS', 'DS', 'AI')")
         ->get();

        return response()->json([
            'message'=>'success',
            'departments' => $departments
        ], 200);
    }

    public function getDeptCourses(Request $request){
        $departments = DB::table('departments')
        ->where('departments.is_archived', false)
        ->leftJoin('courses', function ($join) {
            $join->on('departments.dept_code', '=', 'courses.dept_code')
                ->where('courses.is_archived', false);
        })
        ->select('departments.dept_name', 'courses.id as course_id', 'courses.course_name', 'departments.dept_code')
        ->orderByRaw("FIELD(departments.dept_code, 'CS', 'IT', 'IS', 'DS', 'AI')")
        ->orderBy('departments.dept_name')
        ->orderBy('courses.course_name')
        ->get();
        $departmentArrays = [];
        $lastDeptCode = null;
        foreach ($departments as $department) {
        if ($department->dept_name != $lastDeptCode) {
        $departmentArrays[] = [
            $department->dept_name => [],
        ];
        $lastDeptCode = $department->dept_name;
        }
        $course = [
        'course_id' => $department->course_id,
        'course_name' => $department->course_name,
        ];
        $departmentCount = count($departmentArrays);
        $departmentArrays[$departmentCount - 1][$department->dept_name][] = $course;
        }

        return $departmentArrays;
    }

    public function getDeptAcademic(Request $request){
        $departments = DB::table('departments')
        ->where('departments.is_archived', false)
        ->leftJoin('users', function ($join) {
            $join->on('departments.dept_code', '=', 'users.dept_code')
                 ->where('users.is_archived', false);
        })
        ->select(
            'departments.dept_name',
            'users.user_id',
            'users.full_name',
            'users.image',
            'departments.dept_code',
            DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN users.role IN ("Professor", "Dean", "Vice Dean", "Head of Department") THEN users.full_name END) as professor_names'),
            DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN users.role = "Teaching Assistant" THEN users.full_name ELSE NULL END) as ta_names'),
            DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN users.role = "Secretary" THEN users.full_name ELSE NULL END) as secretary_names')
        )
        ->orderByRaw("FIELD(departments.dept_code, 'CS', 'IT', 'IS', 'DS', 'AI')")
        ->groupBy('departments.dept_name', 'users.user_id', 'users.full_name','users.image','departments.dept_code')
        ->orderBy('departments.dept_name')
        ->orderBy('users.full_name')
        ->get();
        $departmentArrays = [];
        $lastDeptName = null;
        foreach ($departments as $department) {
            if ($department->dept_name != $lastDeptName) {
                $departmentArrays[] = [
                    $department->dept_name => [
                        'professor_names' => $department->professor_names ? [] : [],
                        'ta_names' => $department->ta_names ? [] : [],
                        'secretary_names' => $department->secretary_names ? [] : [],
                    ],
                ];
                $lastDeptName = $department->dept_name;
            }
            $user = [
                'user_id' => $department->user_id,
                'full_name' => $department->full_name,
                'image'=>asset('/media/' . $department->image),
            ];
            if ($department->professor_names) {
                $departmentCount = count($departmentArrays);
                $departmentArrays[$departmentCount - 1][$department->dept_name]['professor_names'][] = $user;
            }
            if ($department->ta_names) {
                $departmentCount = count($departmentArrays);
                $departmentArrays[$departmentCount - 1][$department->dept_name]['ta_names'][] = $user;
            }
            if ($department->secretary_names) {
                $departmentCount = count($departmentArrays);
                $departmentArrays[$departmentCount - 1][$department->dept_name]['secretary_names'][] = $user;
            }
        }

        return $departmentArrays;
        }

    public function archiveDepartment(Request $request)
    {
        $department = new Department;
        $department = Department::find($request->id);

        if (!$department) {
            return response()->json(['error' => 'department not found'], 404);
        }

        if ($department->is_archived == false) {
            $department->is_archived = true;
            $department->save();
        }
        return response()->json([
            'message' => 'success'
        ], 200);

    }
    public function getDeptInfoUser(Request $request)
    {

        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->is_archived == true) {
            return response()->json(['error' => 'This user is archived'], 404);
        }

        $departments = DB::table('departments')
            ->leftJoin('users', function ($join) {
                $join->on('departments.dept_code', '=', 'users.dept_code')
                    ->where('users.is_archived', false);
            })
            ->leftJoin('courses', function ($join) {
                $join->on('departments.dept_code', '=', 'courses.dept_code')
                    ->where('courses.is_archived', false);
            })
            ->where('departments.dept_code', '=', $user->dept_code)
            ->where('departments.is_archived', false)
            ->select(
                'departments.dept_name',
                'departments.desc',
                'departments.dept_code',
                'departments.head',
                'departments.head_id',
                DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN users.role IN ("Professor", "Dean", "Vice Dean", "Head of Department") THEN CONCAT(users.user_id, ":", users.full_name) ELSE NULL END) as professor_names'),
                DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN users.role = "Teaching Assistant" THEN CONCAT(users.user_id, ":", users.full_name) ELSE NULL END) as ta_names'),
                DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN users.role = "Secretary" THEN CONCAT(users.user_id, ":", users.full_name) ELSE NULL END) as secretary_names'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT( courses.course_name, ":", courses.id)) as course_names')
            )
            ->orderByRaw("FIELD(departments.dept_code, 'CS', 'IT', 'IS', 'DS', 'AI')")
            ->groupBy('departments.dept_name','departments.dept_code', 'departments.desc', 'departments.head','departments.head_id')
            ->get();

        $result = [];

        foreach ($departments as $department) {
            $professor_names = [];
            $ta_names = [];
            $secretary_names = [];
            $course_names = [];

            if ($department->professor_names) {
                $professors = explode(',', $department->professor_names);
                foreach ($professors as $professor) {
                    list($id, $name) = explode(':', $professor);
                    $professor_names[] = [
                        'User_id' => $id,
                        'User_name' => $name
                    ];
                }
            }

            if ($department->ta_names) {
                $tas = explode(',', $department->ta_names);
                foreach ($tas as $ta) {
                    list($id, $name) = explode(':', $ta);
                    $ta_names[] = [
                        'User_id' => $id,
                        'User_name' => $name
                    ];
                }
            }

            if ($department->secretary_names) {
                $secretaries = explode(',', $department->secretary_names);
                foreach ($secretaries as $secretary) {
                    list($id, $name) = explode(':', $secretary);
                    $secretary_names[] = [
                        'User_id' => $id,
                        'User_name' => $name
                    ];
                }
            }

            if ($department->course_names) {
                $courses = explode(',', $department->course_names);
                foreach ($courses as $course) {
                    list($name, $id) = explode(':', $course);
                    $course_names[] = [
                        'Course_id' => $id,
                        'Course_name' => $name
                    ];
                }
            }

            $result[] = [
                'dept_name' => $department->dept_name,
                'desc' => $department->desc,
                'head' => $department->head,
                'head_id' => $department->head_id,
                'dept_code'=>$department->dept_code,
                'professor_names' => $professor_names,
                'ta_names' => $ta_names,
                'secretary_names' => $secretary_names,
                'course_names' => $course_names
            ];
        }

        return $result;
}

public function getDeptInfoDept(Request $request)
{
    $department_check = Department::find($request->id);

    if (!$department_check) {
        return response()->json(['error' => 'Department not found'], 404);
    }

    if ($department_check->is_archived == true) {
        return response()->json(['error' => 'This Department is archived'], 404);
    }

    $department = DB::table('departments')
        ->leftJoin('users', function ($join) {
            $join->on('departments.dept_code', '=', 'users.dept_code')
                ->where('users.is_archived', false)
                ->where(function ($query) {
                    $query->where('users.role', 'Professor')
                        ->orWhere('users.role', 'Dean')
                        ->orWhere('users.role', 'Vice Dean')
                        ->orWhere('users.role', 'Head of Department')
                        ->orWhere('users.role', 'Teaching Assistant')
                        ->orWhere('users.role', 'Secretary');
                });
        })
        ->leftJoin('courses', function ($join) {
            $join->on('departments.dept_code', '=', 'courses.dept_code')
                ->where('courses.is_archived', false);
        })
        ->where('departments.id', '=', $request->id)
        ->select(
            'departments.dept_name',
            'departments.desc',
            'departments.head',
            'departments.head_id',
            'departments.dept_code',
            DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN users.role IN ("Professor", "Dean", "Vice Dean", "Head of Department") THEN CONCAT(users.user_id, ":", users.full_name) ELSE NULL END) as professor_names'),
            DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN users.role = "Teaching Assistant" THEN CONCAT(users.user_id, ":", users.full_name) ELSE NULL END) as ta_names'),
            DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN users.role = "Secretary" THEN CONCAT(users.user_id, ":", users.full_name) ELSE NULL END) as secretary_names'),
            DB::raw('GROUP_CONCAT(DISTINCT CONCAT( courses.course_name, ":", courses.id)) as course_names')
        )
        ->orderByRaw("FIELD(departments.dept_code, 'CS', 'IT', 'IS', 'DS', 'AI')")
        ->groupBy('departments.dept_name', 'departments.desc', 'departments.head','departments.head_id','departments.dept_code')
        ->first(); // use "first" instead of "get" to retrieve a single result

    if ($department) {
        $professor_names = [];
        $ta_names = [];
        $secretary_names = [];
        $course_names = [];

        if ($department->professor_names) {
            $professors = explode(',', $department->professor_names);
            foreach ($professors as $professor) {
                list($id, $name) = explode(':', $professor);
                $professor_names[] = [
                    'User_id' => $id,
                    'User_name' => $name
                ];
            }
        }

        if ($department->ta_names) {
            $tas = explode(',', $department->ta_names);
            foreach ($tas as $ta) {
                list($id, $name) = explode(':', $ta);
                $ta_names[] = [
                    'User_id' => $id,
                    'User_name' => $name
                ];
            }
        }

        if ($department->secretary_names) {
            $secretaries = explode(',', $department->secretary_names);
            foreach ($secretaries as $secretary) {
                list($id, $name) = explode(':', $secretary);
                $secretary_names[] = [
                    'User_id' => $id,
                    'User_name' => $name
                ];
            }
        }

        if ($department->course_names) {
            $courses = explode(',', $department->course_names);
            foreach ($courses as $course) {
                list( $name, $id) = explode(':', $course);
                $course_names[] = [
                    'course_id' => $id,
                    'course_name' => $name,
                ];
            }
        }

        $department_media = Department::find($request->id);

        $booklet_URL = asset('/media/' . $department_media->booklet);
        $bylaw_URL = asset('/media/' . $department_media->bylaw);

        $result_departments = [
            'dept_name' => $department->dept_name,
            'desc' => $department->desc,
            'head' => $department->head,
            'head_id'=>$department->head_id,
            'professor_names' => $professor_names,
            'ta_names' => $ta_names,
            'secretary_names' => $secretary_names,
            'course_names' => $course_names,
            'bookletURL' => $booklet_URL,
            'bylawURL' => $bylaw_URL,
        ];

        return $result_departments;
    } else {
        return response()->json(['error' => 'Department not found'], 404);
    }
}

}
