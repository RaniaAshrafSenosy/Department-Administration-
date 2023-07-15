<?php

namespace App\Http\Controllers;

use App\Models\HasA;
use App\Models\Department;
use App\Models\Announcement;
use Illuminate\Http\Request;

class HasAController extends Controller
{
    public function createInstances()
    {
        // Retrieve all departments
        $departments = Department::all();

        // Loop through each department and retrieve its accepted announcements
        foreach ($departments as $department) {
            $acceptedAnnouncements = Announcement::where('status', 'accepted')
                ->where('is_archived', false)
                ->where(function ($query) use ($department) {
                    $query->whereJsonContains('target_dept', $department->dept_code)
                        ->orWhereNull('target_dept')
                        ->orWhereRaw("FIND_IN_SET(?, target_dept) > 0", [$department->dept_code]);
                })
                ->get();

            // Create an instance of each accepted announcement for the department
            foreach ($acceptedAnnouncements as $announcement) {
                $hasA = new HasA();
                $hasA->dept_code =$department->dept_code;
                $hasA->announcement_id = $announcement->announcement_id;
                $hasA->save();
            }
        }

        return response()->json([
            'message' => 'Instances of accepted announcements created successfully for all departments'
        ], 200);
    }
}
